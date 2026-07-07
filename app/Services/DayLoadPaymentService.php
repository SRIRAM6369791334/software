<?php

namespace App\Services;

use App\Models\DayLoadBatch;
use App\Models\DayLoadEntry;
use App\Models\DealerPayment;
use App\Models\VendorPayment;

class DayLoadPaymentService
{
    public function recordDealerPayment(DayLoadEntry $entry, array $data): DealerPayment
    {
        $amount = (float) $data['amount'];
        $entry->increment('dealer_collected', $amount);

        $this->refreshDealerPaymentStatus($entry);
        $this->refreshBatchFinancials($entry->batch);

        return DealerPayment::create([
            'dealer_id'        => $entry->dealer_id,
            'day_load_entry_id'=> $entry->id,
            'date'             => $data['date'],
            'amount'           => $amount,
            'payment_mode'     => $data['payment_mode'],
            'reference_number' => $data['reference_number'] ?? null,
            'notes'            => $data['notes'] ?? null,
        ]);
    }

    public function recordVendorPayment(DayLoadEntry $entry, array $data): VendorPayment
    {
        $amount = (float) $data['amount'];
        $entry->increment('vendor_paid', $amount);

        $this->refreshVendorPaymentStatus($entry);
        $this->refreshBatchFinancials($entry->batch);

        return VendorPayment::create([
            'vendor_id'        => $entry->vendor_id,
            'day_load_entry_id'=> $entry->id,
            'date'             => $data['date'],
            'amount'           => $amount,
            'payment_mode'     => $data['payment_mode'],
            'reference_number' => $data['reference_number'] ?? null,
            'notes'            => $data['notes'] ?? null,
        ]);
    }

    public function refreshDealerPaymentStatus(DayLoadEntry $entry): void
    {
        $income = $entry->dealer_income;
        $collected = (float) $entry->dealer_collected;

        if ($collected <= 0) {
            $status = 'Pending';
        } elseif ($collected >= $income) {
            $status = 'Paid';
        } elseif ($collected > 0 && $collected < $income) {
            $status = 'Partial';
        } else {
            $status = 'Overpaid';
        }

        if ($collected > $income) {
            $status = 'Overpaid';
        }

        $entry->updateQuietly(['dealer_payment_status' => $status]);
    }

    public function refreshVendorPaymentStatus(DayLoadEntry $entry): void
    {
        $cost = $entry->vendor_cost;
        $paid = (float) $entry->vendor_paid;

        if ($paid <= 0) {
            $status = 'Pending';
        } elseif ($paid >= $cost) {
            $status = 'Paid';
        } elseif ($paid > 0 && $paid < $cost) {
            $status = 'Partial';
        } else {
            $status = 'Overpaid';
        }

        if ($paid > $cost) {
            $status = 'Overpaid';
        }

        $entry->updateQuietly(['vendor_payment_status' => $status]);
    }

    public function refreshBatchFinancials(DayLoadBatch $batch): void
    {
        $entries = $batch->entries()->where('status', '!=', 'Cancelled')->get();

        $batch->update([
            'total_dealer_income'    => $entries->sum(fn($e) => $e->dealer_income),
            'total_vendor_cost'      => $entries->sum(fn($e) => $e->vendor_cost),
            'total_dealer_collected' => $entries->sum(fn($e) => (float) $e->dealer_collected),
            'total_vendor_paid'      => $entries->sum(fn($e) => (float) $e->vendor_paid),
        ]);
    }
}
