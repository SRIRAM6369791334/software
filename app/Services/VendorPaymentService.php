<?php

namespace App\Services;

use App\Models\DayLoadEntry;
use App\Models\VendorPayment;
use App\Models\Vendor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class VendorPaymentService
{
    public function paginated(
        ?string $search,
        ?int    $vendorId,
        ?string $dateFrom,
        ?string $dateTo,
        ?string $paymentMode,
        int     $perPage = 15
    ): LengthAwarePaginator {
        return VendorPayment::with('vendor')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($nested) use ($search) {
                    $nested->whereHas('vendor', fn($v) => $v->where('firm_name', 'like', "%{$search}%"))
                           ->orWhere('reference_number', 'like', "%{$search}%");
                });
            })
            ->when($vendorId, fn($q) => $q->where('vendor_id', $vendorId))
            ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->when($paymentMode, fn($q) => $q->where('payment_mode', $paymentMode))
            ->latest('date')
            ->paginate($perPage);
    }

    public function record(array $data): VendorPayment
    {
        return DB::transaction(function () use ($data) {
            $vendor = Vendor::findOrFail($data['vendor_id']);

            $cashAmount = isset($data['cash_amount']) ? (float) $data['cash_amount'] : 0.00;
            $bankAmount = isset($data['bank_amount']) ? (float) $data['bank_amount'] : 0.00;

            $totalAmount = round($cashAmount + $bankAmount, 2);
            $remainingTotal = $totalAmount;

            $createdPayments = [];

            $dayLoadService = app(DayLoadPaymentService::class);

            // 1. Allocate to selected day-load entries (FIFO)
            if (!empty($data['selected_entry_ids'])) {
                $entries = DayLoadEntry::where('vendor_id', $vendor->id)
                    ->whereIn('id', $data['selected_entry_ids'])
                    ->where('status', '!=', 'Cancelled')
                    ->with('batch')
                    ->get()
                    ->sortBy(function($e) {
                        return $e->batch ? $e->batch->billing_date->timestamp : $e->created_at->timestamp;
                    });

                foreach ($entries as $entry) {
                    $balance = round((float) $entry->vendor_cost - (float) $entry->vendor_paid, 2);
                    if ($balance <= 0) {
                        continue;
                    }

                    $alloc = min($remainingTotal, $balance);
                    $entry->increment('vendor_paid', $alloc);
                    $dayLoadService->refreshVendorPaymentStatus($entry);
                    $dayLoadService->refreshBatchFinancials($entry->batch);

                    $recordCash = $totalAmount > 0 ? round($alloc * ($cashAmount / $totalAmount), 2) : 0.00;
                    $recordBank = round($alloc - $recordCash, 2);

                    $payment = VendorPayment::create([
                        'vendor_id'        => $vendor->id,
                        'day_load_entry_id'=> $entry->id,
                        'date'             => $data['date'],
                        'amount'           => $alloc,
                        'cash_amount'      => $recordCash,
                        'bank_amount'      => $recordBank,
                        'payment_mode'     => $data['payment_mode'],
                        'bank_transfer_type' => $data['bank_transfer_type'] ?? null,
                        'reference_number' => $data['reference_number'] ?? null,
                        'notes'            => ($data['notes'] ?? '') ?: 'Auto-allocated to entry #' . $entry->id,
                    ]);

                    $createdPayments[] = $payment;
                    $remainingTotal = round($remainingTotal - $alloc, 2);

                    if ($remainingTotal <= 0) {
                        break;
                    }
                }
            }

            // 2. Any remaining amount — record against vendor balance directly
            if ($remainingTotal > 0) {
                $payment = VendorPayment::create([
                    'vendor_id'        => $vendor->id,
                    'day_load_entry_id'=> null,
                    'date'             => $data['date'],
                    'amount'           => $remainingTotal,
                    'cash_amount'      => $totalAmount > 0 ? round($remainingTotal * ($cashAmount / $totalAmount), 2) : 0.00,
                    'bank_amount'      => $totalAmount > 0 ? round($remainingTotal * ($bankAmount / $totalAmount), 2) : 0.00,
                    'payment_mode'     => $data['payment_mode'],
                    'bank_transfer_type' => $data['bank_transfer_type'] ?? null,
                    'notes'            => ($data['notes'] ?? '') ?: 'Vendor payment',
                ]);

                $createdPayments[] = $payment;
            }

            if (empty($createdPayments)) {
                $payment = VendorPayment::create([
                    'vendor_id'        => $vendor->id,
                    'date'             => $data['date'],
                    'amount'           => $totalAmount,
                    'cash_amount'      => $cashAmount,
                    'bank_amount'      => $bankAmount,
                    'payment_mode'     => $data['payment_mode'],
                    'bank_transfer_type' => $data['bank_transfer_type'] ?? null,
                    'notes'            => $data['notes'] ?? 'Vendor payment',
                ]);
                $createdPayments[] = $payment;
            }

            // Update pending_balance_after on all created payments
            $finalOutstanding = $vendor->fresh()->outstanding_balance;
            foreach ($createdPayments as $p) {
                $p->updateQuietly([
                    'pending_balance_after' => $finalOutstanding
                ]);
            }

            // Recalculate cash/bank ledger for the payment date
            $paymentDate = $createdPayments[0]->date;
            app(CashBankLedgerService::class)->recalculateForDate(\Carbon\Carbon::parse($paymentDate));

            return $createdPayments[0];
        });
    }

    public function allForExport(): \Illuminate\Database\Eloquent\Collection
    {
        return VendorPayment::with('vendor')->orderByDesc('date')->get();
    }
}
