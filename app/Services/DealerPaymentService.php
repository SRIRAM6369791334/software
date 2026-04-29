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
        $dealer->decrement('pending_amount', abs($data['amount']));
        
        // Prevent negative balance
        if ($dealer->pending_amount < 0) {
            $dealer->update(['pending_amount' => 0]);
        }

        $data['pending_balance_after'] = $dealer->pending_amount;
        return DealerPayment::create($data);
    }

    public function allForExport(): \Illuminate\Database\Eloquent\Collection
    {
        return DealerPayment::with('dealer')->orderByDesc('date')->get();
    }
}
