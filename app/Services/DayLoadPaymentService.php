<?php

namespace App\Services;

use App\Models\DayLoadBatch;
use App\Models\DayLoadEntry;
use App\Models\DayLoadInvoice;
use App\Models\DealerPayment;
use App\Models\PaymentAdjustmentLog;
use App\Models\VendorPayment;
use Illuminate\Support\Facades\DB;

class DayLoadPaymentService
{
    public function __construct(
        private CashBankLedgerService $cashBankLedgerService,
    ) {}

    public function recordDealerPayment(DayLoadEntry $entry, array $data): DealerPayment
    {
        return DB::transaction(function () use ($entry, $data) {
            // Ensure the batch relationship is fresh (invoice_id may have been set after entry creation)
            $entry->load('batch');

            $cashAmount = (float) ($data['cash_amount'] ?? $data['amount']);
            $bankAmount = (float) ($data['bank_amount'] ?? 0);

            // Legacy `amount` must always equal cash + bank to prevent drift
            $legacyAmount = round($cashAmount + $bankAmount, 2);

            // Update entry-level collected
            $entry->increment('dealer_collected', $legacyAmount);
            $this->refreshDealerPaymentStatus($entry);

            // Create payment record with split amounts
            $payment = DealerPayment::create([
                'dealer_id'         => $entry->dealer_id,
                'day_load_entry_id' => $entry->id,
                'invoice_id'        => $entry->batch?->invoice_id,
                'date'              => $data['date'],
                'amount'            => $legacyAmount,
                'payment_mode'      => $data['payment_mode'],
                'cash_amount'       => $cashAmount,
                'bank_amount'       => $bankAmount,
                'bank_transfer_type' => $data['bank_transfer_type'] ?? null,
                'reference_number'  => $data['reference_number'] ?? null,
                'notes'             => $data['notes'] ?? null,
            ]);

            // Update invoice-level aggregated payment
            $this->refreshInvoicePayment($entry->batch?->invoice);

            // Update batch-level financials
            $this->refreshBatchFinancials($entry->batch);

            // Recalculate cash/bank ledger for the payment date
            $this->cashBankLedgerService->recalculateForDate(now());

            return $payment;
        });
    }

    public function updateDealerPayment(DealerPayment $payment, array $data, string $reason): DealerPayment
    {
        return DB::transaction(function () use ($payment, $data, $reason) {
            $oldValues = $payment->toArray();

            $cashAmount = (float) ($data['cash_amount'] ?? $payment->cash_amount);
            $bankAmount = (float) ($data['bank_amount'] ?? $payment->bank_amount);
            $legacyAmount = round($cashAmount + $bankAmount, 2);

            $payment->update([
                'date'              => $data['date'] ?? $payment->date,
                'amount'            => $legacyAmount,
                'payment_mode'      => $data['payment_mode'] ?? $payment->payment_mode,
                'cash_amount'       => $cashAmount,
                'bank_amount'       => $bankAmount,
                'bank_transfer_type' => $data['bank_transfer_type'] ?? $payment->bank_transfer_type,
                'reference_number'  => $data['reference_number'] ?? $payment->reference_number,
                'notes'             => $data['notes'] ?? $payment->notes,
            ]);

            PaymentAdjustmentLog::create([
                'payment_id'   => $payment->id,
                'action_type'  => 'Edit',
                'old_values'   => $oldValues,
                'new_values'   => $payment->fresh()->toArray(),
                'reason'       => $reason,
                'adjusted_by'  => auth()->id(),
            ]);

            // Recalculate entry-level collected from all payments for this entry
            $entry = $payment->dayLoadEntry;
            if ($entry) {
                $totalCollected = (float) $entry->dealerPayments()->sum('amount');
                $entry->updateQuietly(['dealer_collected' => $totalCollected]);
                $this->refreshDealerPaymentStatus($entry);
                $this->refreshBatchFinancials($entry->batch);
                $this->refreshInvoicePayment($entry->batch?->invoice);
            }

            $this->cashBankLedgerService->recalculateForDate(now());

            return $payment->fresh();
        });
    }

    public function recordVendorPayment(DayLoadEntry $entry, array $data): VendorPayment
    {
        return DB::transaction(function () use ($entry, $data) {
            $cashAmount = isset($data['cash_amount']) ? (float) $data['cash_amount'] : 0.00;
            $bankAmount = isset($data['bank_amount']) ? (float) $data['bank_amount'] : 0.00;
            
            // Backward compatibility fallback
            if (!isset($data['cash_amount']) && !isset($data['bank_amount'])) {
                $amount = (float) ($data['amount'] ?? 0);
                if (($data['payment_mode'] ?? 'Cash') === 'Cash') {
                    $cashAmount = $amount;
                    $bankAmount = 0.00;
                } else {
                    $cashAmount = 0.00;
                    $bankAmount = $amount;
                }
            }

            $amount = round($cashAmount + $bankAmount, 2);
            $entry->increment('vendor_paid', $amount);

            $this->refreshVendorPaymentStatus($entry);
            $this->refreshBatchFinancials($entry->batch);

            $payment = VendorPayment::create([
                'vendor_id'        => $entry->vendor_id,
                'day_load_entry_id'=> $entry->id,
                'date'             => $data['date'],
                'amount'           => $amount,
                'payment_mode'     => $data['payment_mode'],
                'cash_amount'      => $cashAmount,
                'bank_amount'      => $bankAmount,
                'bank_transfer_type'=> $data['bank_transfer_type'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'notes'            => $data['notes'] ?? null,
            ]);

            $this->cashBankLedgerService->recalculateForDate(now());

            return $payment;
        });
    }

    public function refreshInvoicePayment(?DayLoadInvoice $invoice): void
    {
        if (!$invoice) {
            return;
        }

        $amountPaid = (float) $invoice->dealerPayments()->sum('amount');
        $totalAmount = (float) $invoice->total_amount;

        if ($amountPaid <= 0) {
            $paymentStatus = 'Pending';
        } elseif ($amountPaid >= $totalAmount && $totalAmount > 0) {
            $paymentStatus = 'Paid';
        } elseif ($amountPaid > 0 && $amountPaid < $totalAmount) {
            $paymentStatus = 'Partial';
        } else {
            // Edge case: total_amount is 0 but there's a payment
            $paymentStatus = 'Paid';
        }

        $invoice->updateQuietly([
            'amount_paid'    => $amountPaid,
            'payment_status' => $paymentStatus,
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
