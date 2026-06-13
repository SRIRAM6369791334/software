<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\WeeklyBill;
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
     * Create a single Weekly Bill with itemised lines.
     *
     * @param array $data  Validated request data
     * @param int|null $createdBy
     * @return WeeklyBill
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
                'customer_id'    => $data['customer_id'],
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
                $customer = Customer::find($data['customer_id']);
                if ($customer) {
                    $customer->increment('balance', $gstData['net_amount']);
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

            return $bill;
        });
    }

    /**
     * Create a Weekly Bill for each customer in bulk (flat-rate settlement).
     *
     * @param array $customerIds
     * @param array $data   Validated request data (period_start, period_end, amount, status, payment_mode)
     * @return int  Number of bills created
     */
    public function bulkCreate(array $customerIds, array $data): int
    {
        return DB::transaction(function () use ($customerIds, $data) {
            $paymentMode = $data['payment_mode'];
            $status      = $data['status'];
            $count       = 0;

            foreach ($customerIds as $cid) {
                $gstData = GSTCalculator::calculate($data['amount'], 18);

                $bill = WeeklyBill::create([
                    'invoice_no'     => $this->invoiceService->generateUnique('INV-W', 'weekly_bills'),
                    'customer_id'    => $cid,
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
                    $customer = Customer::find($cid);
                    if ($customer) {
                        $customer->increment('balance', $gstData['net_amount']);
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
}
