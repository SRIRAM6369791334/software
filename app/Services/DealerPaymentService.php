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
        
        $dealer->decrement('pending_amount', abs($amount));
        
        // Prevent negative balance
        if ($dealer->pending_amount < 0) {
            $dealer->update(['pending_amount' => 0]);
        }

        $data['amount'] = $amount;
        $data['cash_amount'] = $cashAmount;
        $data['bank_amount'] = $bankAmount;
        $data['pending_balance_after'] = $dealer->pending_amount;
        
        $payment = DealerPayment::create($data);
        
        // Recalculate cash/bank ledger
        app(CashBankLedgerService::class)->recalculateForDate(\Carbon\Carbon::parse($payment->date));
        
        return $payment;
    }

    public function allForExport(): \Illuminate\Database\Eloquent\Collection
    {
        return DealerPayment::with('dealer')->orderByDesc('date')->get();
    }
}
