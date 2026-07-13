<?php

namespace App\Services;

use App\Models\Dealer;
use App\Models\WeeklyBill;
use App\Models\DealerPurchase;
use App\Models\DealerPayment;
use App\Services\Tax\GSTCalculator;
use App\Services\InvoiceNumberService;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;

class WeeklyBillingService
{
    public function __construct(
        private InvoiceNumberService $invoiceService,
        private StockService $stockService
    ) {}

    /**
     * Create a daily/individual Dealer Purchase.
     */
    public function createPurchase(array $data, ?int $createdBy = null): DealerPurchase
    {
        return DB::transaction(function () use ($data, $createdBy) {
            $itemsData = $data['items'];
            $subtotal = 0;
            foreach ($itemsData as $item) {
                $subtotal += $item['qty'] * $item['rate'];
            }

            $gstData = GSTCalculator::calculate($subtotal, 18);

            $purchase = DealerPurchase::create([
                'dealer_id'      => $data['dealer_id'],
                'date'           => $data['date'],
                'invoice_no'     => $this->invoiceService->generateUnique('DPUR', 'dealer_purchases'),
                'amount'         => $subtotal,
                'gst_percentage' => 18,
                'gst_amount'     => $gstData['total_gst'],
                'net_amount'     => $gstData['net_amount'],
            ]);

            // Increment dealer's pending_amount
            $dealer = Dealer::find($data['dealer_id']);
            if ($dealer) {
                $dealer->increment('pending_amount', $gstData['net_amount']);
            }

            foreach ($itemsData as $item) {
                $base = $item['qty'] * $item['rate'];
                $tax  = round($base * 18 / 100, 2);

                $purchase->items()->create([
                    'item_name'    => $item['name'],
                    'quantity_kg'  => $item['qty'],
                    'rate_per_kg'  => $item['rate'],
                    'tax_amount'   => $tax,
                    'total_amount' => $base + $tax,
                ]);

                // Reduce stock immediately on purchase
                $this->stockService->recordOut([
                    'item_name'      => $item['name'],
                    'quantity'       => $item['qty'],
                    'rate'           => $item['rate'],
                    'reference_type' => DealerPurchase::class,
                    'reference_id'   => $purchase->id,
                    'date'           => $purchase->date,
                    'created_by'     => $createdBy ?? auth()->id() ?? 1,
                ]);
            }

            return $purchase;
        });
    }

    /**
     * Calculate weekly billing totals for a dealer.
     */
    public function calculateWeeklyTotals(int $dealerId, string $startDate, string $endDate): array
    {
        $dealer = Dealer::findOrFail($dealerId);

        // 1. Sum of all uninvoiced daily purchases
        $purchasesQuery = DealerPurchase::where('dealer_id', $dealerId)
            ->whereBetween('date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereNull('weekly_bill_id');

        $totalPurchases = (float) $purchasesQuery->sum('net_amount');
        $purchasesList = $purchasesQuery->with('items')->get();

        // 2. Sum of all dealer payments made during this week
        $paymentsQuery = DealerPayment::where('dealer_id', $dealerId)
            ->whereBetween('date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        $totalPayments = (float) $paymentsQuery->sum('amount');

        // 3. Outstanding balance before this week
        $currentPending = (float) $dealer->pending_amount;
        $previousOutstanding = $currentPending - $totalPurchases + $totalPayments;
        if ($previousOutstanding < 0) {
            $previousOutstanding = 0.0;
        }

        // Net Invoice Amount = Previous Outstanding + Purchases - Payments
        $netInvoiceAmount = $previousOutstanding + $totalPurchases - $totalPayments;
        if ($netInvoiceAmount < 0) {
            $netInvoiceAmount = 0.0;
        }

        return [
            'dealer' => $dealer,
            'previous_outstanding' => $previousOutstanding,
            'total_purchases' => $totalPurchases,
            'total_payments' => $totalPayments,
            'net_invoice_amount' => $netInvoiceAmount,
            'purchases' => $purchasesList,
        ];
    }

    /**
     * Generate weekly bill with Monday/Friday split payment schedules.
     */
    public function generateWeeklyBill(array $data, ?int $createdBy = null): WeeklyBill
    {
        return DB::transaction(function () use ($data, $createdBy) {
            $dealerId = $data['dealer_id'];
            $startDate = $data['period_start'];
            $endDate = $data['period_end'];

            $totals = $this->calculateWeeklyTotals($dealerId, $startDate, $endDate);
            $netAmount = $totals['net_invoice_amount'];

            $baseAmount = round($netAmount / 1.18, 2);
            $gstAmount = round($netAmount - $baseAmount, 2);

            // Split: 50% Monday, 50% Friday
            $mondayPayment = round($netAmount / 2, 2);
            $fridayPayment = $netAmount - $mondayPayment;

            $bill = WeeklyBill::create([
                'dealer_id'             => $dealerId,
                'period_start'          => $startDate,
                'period_end'            => $endDate,
                'invoice_no'            => $this->invoiceService->generateUnique('INV-W', 'weekly_bills'),
                'amount'                => $baseAmount,
                'gst_percentage'        => 18,
                'gst_amount'            => $gstAmount,
                'net_amount'            => $netAmount,
                'status'                => $netAmount > 0 ? 'Pending' : 'Paid',
                'payment_mode'          => 'Credit',
                'monday_payment_amount' => $mondayPayment,
                'monday_payment_status' => $mondayPayment > 0 ? 'Pending' : 'Paid',
                'friday_payment_amount' => $fridayPayment,
                'friday_payment_status' => $fridayPayment > 0 ? 'Pending' : 'Paid',
                'previous_outstanding'  => $totals['previous_outstanding'],
                'payments_during_week'  => $totals['total_payments'],
            ]);

            // Link daily purchases to weekly bill and copy items
            foreach ($totals['purchases'] as $purchase) {
                foreach ($purchase->items as $item) {
                    $bill->items()->create([
                        'item_name'    => $item->item_name,
                        'quantity_kg'  => $item->quantity_kg,
                        'rate_per_kg'  => $item->rate_per_kg,
                        'tax_amount'   => $item->tax_amount,
                        'total_amount' => $item->total_amount,
                    ]);
                }
                $purchase->update(['weekly_bill_id' => $bill->id]);
            }

            if ($totals['purchases']->isEmpty() && $netAmount > 0) {
                $bill->items()->create([
                    'item_name'    => 'Outstanding Balance Carried Forward',
                    'quantity_kg'  => 1,
                    'rate_per_kg'  => $baseAmount,
                    'tax_amount'   => $gstAmount,
                    'total_amount' => $netAmount,
                ]);
            }

            return $bill;
        });
    }

    /**
     * Record payment for a split part (Monday or Friday).
     */
    public function recordSplitPayment(int $billId, string $part, array $paymentData): WeeklyBill
    {
        return DB::transaction(function () use ($billId, $part, $paymentData) {
            $bill = WeeklyBill::findOrFail($billId);
            $dealer = Dealer::findOrFail($bill->dealer_id);

            $amount = 0.0;
            if ($part === 'monday') {
                if ($bill->monday_payment_status === 'Paid') {
                    throw new \Exception("Monday split payment is already paid.");
                }
                $amount = (float) $bill->monday_payment_amount;
                $bill->monday_payment_status = 'Paid';
            } elseif ($part === 'friday') {
                if ($bill->friday_payment_status === 'Paid') {
                    throw new \Exception("Friday split payment is already paid.");
                }
                $amount = (float) $bill->friday_payment_amount;
                $bill->friday_payment_status = 'Paid';
            }

            if ($bill->monday_payment_status === 'Paid' && $bill->friday_payment_status === 'Paid') {
                $bill->status = 'Paid';
            }
            $bill->save();

            // Record Dealer Payment
            DealerPayment::create([
                'dealer_id'          => $bill->dealer_id,
                'date'               => $paymentData['date'] ?? now()->format('Y-m-d'),
                'amount'             => $amount,
                'payment_mode'       => $paymentData['payment_mode'] ?? 'Cash',
                'cash_amount'        => $paymentData['cash_amount'] ?? $amount,
                'bank_amount'        => $paymentData['bank_amount'] ?? 0.00,
                'bank_transfer_type' => $paymentData['bank_transfer_type'] ?? null,
                'notes'              => $paymentData['notes'] ?? "Split payment ({$part}) for Invoice #{$bill->invoice_no}",
            ]);

            // Recalculate cash/bank ledger
            app(CashBankLedgerService::class)->recalculateForDate(\Carbon\Carbon::parse($paymentData['date'] ?? now()));

            // Decrement dealer outstanding
            $dealer->decrement('pending_amount', $amount);
            if ($dealer->pending_amount < 0) {
                $dealer->update(['pending_amount' => 0]);
            }

            return $bill;
        });
    }

    /**
     * Create a single Weekly Bill with itemised lines (Deprecated / Legacy Manual Mode).
     */
    public function create(array $data, ?int $createdBy = null): WeeklyBill
    {
        return DB::transaction(function () use ($data, $createdBy) {
            $itemsData   = $data['items'];
            $paymentMode = $data['payment_mode'];
            $status      = $data['status'];

            $subtotal = 0;
            foreach ($itemsData as $item) {
                $subtotal += $item['qty'] * $item['rate'];
            }

            $gstData = GSTCalculator::calculate($subtotal, 18);

            $bill = WeeklyBill::create([
                'dealer_id'    => $data['dealer_id'],
                'period_start'   => $data['period_start'],
                'period_end'     => $data['period_end'],
                'invoice_no'     => $this->invoiceService->generateUnique('INV-W', 'weekly_bills'),
                'amount'         => $subtotal,
                'gst_percentage' => 18,
                'gst_amount'     => $gstData['total_gst'],
                'net_amount'     => $gstData['net_amount'],
                'status'         => $status,
                'payment_mode'   => $paymentMode,
            ]);

            if ($paymentMode === 'Credit' || $status === 'Pending') {
                $dealer = Dealer::find($data['dealer_id']);
                if ($dealer) {
                    $dealer->increment('pending_amount', $gstData['net_amount']);
                }
            }

            foreach ($itemsData as $item) {
                $base = $item['qty'] * $item['rate'];
                $tax  = round($base * 18 / 100, 2);

                $billItem = $bill->items()->create([
                    'item_name'    => $item['name'],
                    'quantity_kg'  => $item['qty'],
                    'rate_per_kg'  => $item['rate'],
                    'tax_amount'   => $tax,
                    'total_amount' => $base + $tax,
                ]);

                $this->stockService->recordOut([
                    'item_name'      => $billItem->item_name,
                    'quantity'       => $billItem->quantity_kg,
                    'rate'           => $billItem->rate_per_kg,
                    'reference_type' => WeeklyBill::class,
                    'reference_id'   => $bill->id,
                    'date'           => $bill->period_end,
                    'created_by'     => $createdBy ?? auth()->id() ?? 1,
                ]);
            }

            if ($paymentMode === 'Pay later(EMI)' && isset($data['emis']) && is_array($data['emis'])) {
                foreach ($data['emis'] as $emiData) {
                    \App\Models\Emi::create([
                        'emi_type'   => 'Dealer',
                        'entity_id'  => $bill->dealer_id,
                        'loan_name'  => 'Sales EMI - ' . $bill->invoice_no,
                        'bank_name'  => 'Weekly Bill',
                        'amount'     => $emiData['amount'],
                        'due_date'   => $emiData['due_date'],
                        'status'     => 'Upcoming',
                    ]);
                }
            }

            return $bill;
        });
    }

    /**
     * Create a Weekly Bill for each dealer in bulk (Legacy Flat-rate).
     */
    public function bulkCreate(array $dealerIds, array $data): int
    {
        return DB::transaction(function () use ($dealerIds, $data) {
            $paymentMode = $data['payment_mode'];
            $status      = $data['status'];
            $count       = 0;

            foreach ($dealerIds as $did) {
                $gstData = GSTCalculator::calculate($data['amount'], 18);

                $bill = WeeklyBill::create([
                    'invoice_no'     => $this->invoiceService->generateUnique('INV-W', 'weekly_bills'),
                    'dealer_id'      => $did,
                    'period_start'   => $data['period_start'],
                    'period_end'     => $data['period_end'],
                    'amount'         => $data['amount'],
                    'gst_percentage' => 18,
                    'gst_amount'     => $gstData['total_gst'],
                    'net_amount'     => $gstData['net_amount'],
                    'status'         => $status,
                    'payment_mode'   => $paymentMode,
                ]);

                if ($paymentMode === 'Credit' || $status === 'Pending') {
                    $dealer = Dealer::find($did);
                    if ($dealer) {
                        $dealer->increment('pending_amount', $gstData['net_amount']);
                    }
                }

                $bill->items()->create([
                    'item_name'    => 'Weekly Poultry Settlement',
                    'quantity_kg'  => 1,
                    'rate_per_kg'  => $data['amount'],
                    'tax_amount'   => $gstData['total_gst'],
                    'total_amount' => $gstData['net_amount'],
                ]);

                $count++;
            }

            return $count;
        });
    }

    /**
     * Delete a weekly bill.
     */
    public function deleteWeeklyBill(WeeklyBill $bill): void
    {
        DB::transaction(function () use ($bill) {
            $hasPurchases = DealerPurchase::where('weekly_bill_id', $bill->id)->exists();

            if ($hasPurchases) {
                // Unlink daily purchases
                DealerPurchase::where('weekly_bill_id', $bill->id)->update(['weekly_bill_id' => null]);
            } else {
                // If it was manually created, revert dealer balance
                if ($bill->payment_mode === 'Credit' || $bill->status === 'Pending') {
                    $dealer = Dealer::find($bill->dealer_id);
                    if ($dealer) {
                        $dealer->decrement('pending_amount', $bill->net_amount);
                        if ($dealer->pending_amount < 0) {
                            $dealer->update(['pending_amount' => 0]);
                        }
                    }
                }
            }

            // Delete weekly bill items
            $bill->items()->delete();

            // Delete the bill itself
            $bill->delete();
        });
    }
}
