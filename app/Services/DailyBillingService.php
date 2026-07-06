<?php

namespace App\Services;

use App\Models\DailyBill;
use App\Models\Customer;
use App\Services\Tax\GSTCalculator;
use App\Services\InvoiceNumberService;
use App\Services\StockService;
use Illuminate\Support\Facades\DB;

class DailyBillingService
{
    public function __construct(
        private InvoiceNumberService $invoiceService,
        private StockService $stockService
    ) {}

    /**
     * Create a new Daily Bill.
     *
     * @param array $data
     * @param int|null $createdBy
     * @return DailyBill
     */
    public function create(array $data, ?int $createdBy = null): DailyBill
    {
        return DB::transaction(function () use ($data, $createdBy) {
            $itemsData = $data['items'];
            $gstPercent = $data['gst_percentage'];
            $paymentMode = $data['payment_mode'] ?? 'Cash';
            $status = $data['status'];

            $subtotal = 0;
            foreach ($itemsData as $item) {
                $subtotal += $item['qty'] * $item['rate'];
            }

            $gstData = GSTCalculator::calculate($subtotal, $gstPercent);

            $bill = DailyBill::create([
                'customer_id'    => $data['customer_id'],
                'date'           => $data['date'],
                'invoice_no'     => $this->invoiceService->generateUnique('INV-D', 'daily_bills'),
                'amount'         => $subtotal,
                'gst_percentage' => $gstPercent,
                'gst_amount'     => $gstData['total_gst'],
                'net_amount'     => $gstData['net_amount'],
                'status'         => $status,
                'payment_mode'   => $paymentMode,
            ]);

            // Update Customer Balance if Credit or Pending
            if ($paymentMode === 'Credit' || $status === 'Pending') {
                $customer = Customer::find($data['customer_id']);
                if ($customer) {
                    $customer->increment('balance', $gstData['net_amount']);
                }
            }

            foreach ($itemsData as $item) {
                $base = $item['qty'] * $item['rate'];
                $tax = round($base * $gstPercent / 100, 2);

                $billItem = $bill->items()->create([
                    'item_name'    => $item['name'],
                    'quantity_kg'  => $item['qty'],
                    'rate_per_kg'  => $item['rate'],
                    'tax_amount'   => $tax,
                    'total_amount' => $base + $tax,
                    'unit'         => $item['unit'] ?? 'kg',
                ]);

                // Auto-trigger stock movement
                $this->stockService->recordOut([
                    'item_name'      => $billItem->item_name,
                    'quantity'       => $billItem->quantity_kg,
                    'rate'           => $billItem->rate_per_kg,
                    'reference_type' => DailyBill::class,
                    'reference_id'   => $bill->id,
                    'date'           => $bill->date,
                    'created_by'     => $createdBy ?? auth()->id() ?? 1,
                ]);
            }

            // Create EMI schedules if payment mode is Pay later(EMI)
            if ($paymentMode === 'Pay later(EMI)' && isset($data['emis']) && is_array($data['emis'])) {
                foreach ($data['emis'] as $emiData) {
                    \App\Models\Emi::create([
                        'emi_type'   => 'Customer',
                        'entity_id'  => $bill->customer_id,
                        'loan_name'  => 'Sales EMI - ' . $bill->invoice_number,
                        'bank_name'  => 'Daily Bill',
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
     * Update a Daily Bill.
     */
    public function update(DailyBill $bill, array $data, ?int $updatedBy = null): DailyBill
    {
        return DB::transaction(function () use ($bill, $data, $updatedBy) {
            $itemsData = $data['items'];
            $gstPercent = $data['gst_percentage'];
            $paymentMode = $data['payment_mode'];
            $status = $data['status'];

            $subtotal = 0;
            foreach ($itemsData as $item) {
                $subtotal += $item['qty'] * $item['rate'];
            }

            $gstData = GSTCalculator::calculate($subtotal, $gstPercent);

            // Revert old customer balance if was credit
            $oldCustomer = Customer::find($bill->customer_id);
            if ($oldCustomer && ($bill->payment_mode === 'Credit' || $bill->status === 'Pending')) {
                $oldCustomer->decrement('balance', $bill->net_amount);
            }

            $bill->update([
                'customer_id'    => $data['customer_id'],
                'date'           => $data['date'],
                'amount'         => $subtotal,
                'gst_percentage' => $gstPercent,
                'gst_amount'     => $gstData['total_gst'],
                'net_amount'     => $gstData['net_amount'],
                'status'         => $status,
                'payment_mode'   => $paymentMode,
            ]);

            // Apply new customer balance
            if ($paymentMode === 'Credit' || $status === 'Pending') {
                $customer = Customer::find($data['customer_id']);
                if ($customer) {
                    $customer->increment('balance', $gstData['net_amount']);
                }
            }

            // Delete old items and recreate
            $bill->items()->delete();
            foreach ($itemsData as $item) {
                $base = $item['qty'] * $item['rate'];
                $tax = round($base * $gstPercent / 100, 2);

                $bill->items()->create([
                    'item_name'    => $item['name'],
                    'quantity_kg'  => $item['qty'],
                    'rate_per_kg'  => $item['rate'],
                    'tax_amount'   => $tax,
                    'total_amount' => $base + $tax,
                    'unit'         => $item['unit'] ?? 'kg',
                ]);
            }

            return $bill->fresh();
        });
    }

    /**
     * Delete a Daily Bill and associated records.
     *
     * @param DailyBill $bill
     * @return bool
     */
    public function delete(DailyBill $bill): bool
    {
        return DB::transaction(function () use ($bill) {
            // Delete stock transactions related to this daily bill
            DB::table('stock_transactions')
                ->where('reference_type', DailyBill::class)
                ->where('reference_id', $bill->id)
                ->delete();

            // Delete the items
            $bill->items()->delete();

            // Delete the bill itself
            return (bool) $bill->delete();
        });
    }
}
