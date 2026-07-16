<?php

namespace App\Services;

use App\Models\DealerPayment;
use App\Models\Dealer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class DealerPaymentService
{
    public function paginated(?string $query, int $perPage = 15): LengthAwarePaginator
    {
        return DealerPayment::with('dealer')
            ->search($query)
            ->latest('date')
            ->paginate($perPage);
    }

    public function record(array $data): DealerPayment
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $dealer = Dealer::findOrFail($data['dealer_id']);
            
            $cashAmount = isset($data['cash_amount']) ? (float) $data['cash_amount'] : 0.00;
            $bankAmount = isset($data['bank_amount']) ? (float) $data['bank_amount'] : 0.00;
            
            // Fallback for old tests / seeds
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
            $remainingAmount = $amount;
            
            $paymentGroupId = (string) \Illuminate\Support\Str::uuid();
            $createdPayments = [];
            
            // 1. Allocate to base pending_amount
            $pendingAmount = (float) $dealer->pending_amount;
            if ($pendingAmount > 0) {
                $deduct = min($remainingAmount, $pendingAmount);
                $dealer->decrement('pending_amount', $deduct);
                
                // Create a payment record for the base pending amount deduction
                $paymentData = array_merge($data, [
                    'amount' => $deduct,
                    'cash_amount' => round($deduct * ($cashAmount / $amount), 2),
                    'bank_amount' => round($deduct - round($deduct * ($cashAmount / $amount), 2), 2),
                    'payment_group_id' => $paymentGroupId,
                    'day_load_entry_id' => null,
                    'invoice_id' => null,
                    'notes' => ($data['notes'] ?? '') ?: 'Allocated to base pending balance',
                ]);
                $createdPayments[] = DealerPayment::create($paymentData);
                
                $remainingAmount = round($remainingAmount - $deduct, 2);
            }
            
            // 2. Allocate to active day load entries (FIFO)
            if ($remainingAmount > 0) {
                $dayLoadPaymentService = app(DayLoadPaymentService::class);
                
                $entries = \App\Models\DayLoadEntry::where('dealer_id', $dealer->id)
                    ->where('status', '!=', 'Cancelled')
                    ->with(['dealerPayments', 'batch'])
                    ->get()
                    ->sortBy(function($entry) {
                        return $entry->batch ? $entry->batch->billing_date->timestamp : $entry->created_at->timestamp;
                    });
                
                foreach ($entries as $entry) {
                    $collected = (float) $entry->dealer_collected;
                    $due = round((float) $entry->amount - $collected, 2);
                    if ($due <= 0) {
                        continue;
                    }
                    
                    $alloc = min($remainingAmount, $due);
                    $entry->increment('dealer_collected', $alloc);
                    $dayLoadPaymentService->refreshDealerPaymentStatus($entry);
                    $dayLoadPaymentService->refreshBatchFinancials($entry->batch);
                    $dayLoadPaymentService->refreshInvoicePayment($entry->batch?->invoice);
                    
                    // Create payment record for this entry allocation
                    $paymentData = array_merge($data, [
                        'amount' => $alloc,
                        'cash_amount' => round($alloc * ($cashAmount / $amount), 2),
                        'bank_amount' => round($alloc - round($alloc * ($cashAmount / $amount), 2), 2),
                        'payment_group_id' => $paymentGroupId,
                        'day_load_entry_id' => $entry->id,
                        'invoice_id' => $entry->batch?->invoice_id,
                        'notes' => ($data['notes'] ?? '') ?: 'Auto-allocated to entry #' . $entry->id,
                    ]);
                    $createdPayments[] = DealerPayment::create($paymentData);
                    
                    $remainingAmount = round($remainingAmount - $alloc, 2);
                    if ($remainingAmount <= 0) {
                        break;
                    }
                }
            }
            
            // 3. Any excess amount is treated as advance
            if ($remainingAmount > 0) {
                $paymentData = array_merge($data, [
                    'amount' => $remainingAmount,
                    'cash_amount' => round($remainingAmount * ($cashAmount / $amount), 2),
                    'bank_amount' => round($remainingAmount - round($remainingAmount * ($cashAmount / $amount), 2), 2),
                    'payment_group_id' => $paymentGroupId,
                    'day_load_entry_id' => null,
                    'invoice_id' => null,
                    'notes' => ($data['notes'] ?? '') ?: 'Unallocated advance',
                ]);
                $createdPayments[] = DealerPayment::create($paymentData);
            }
            
            // If no payment was created (amount was 0), fallback
            if (empty($createdPayments)) {
                $paymentData = array_merge($data, [
                    'amount' => $amount,
                    'cash_amount' => $cashAmount,
                    'bank_amount' => $bankAmount,
                    'day_load_entry_id' => null,
                    'invoice_id' => null,
                ]);
                $createdPayments[] = DealerPayment::create($paymentData);
            }
            
            // Update pending_balance_after on all created payments
            $finalOutstanding = $dealer->fresh()->displayed_outstanding;
            foreach ($createdPayments as $p) {
                $p->updateQuietly([
                    'pending_balance_after' => $finalOutstanding
                ]);
            }
            
            // Recalculate cash/bank ledger
            $firstPayment = $createdPayments[0];
            app(CashBankLedgerService::class)->recalculateForDate(\Carbon\Carbon::parse($firstPayment->date));
            
            return $firstPayment;
        });
    }

    public function allForExport(): \Illuminate\Database\Eloquent\Collection
    {
        return DealerPayment::with('dealer')->orderByDesc('date')->get();
    }
}
