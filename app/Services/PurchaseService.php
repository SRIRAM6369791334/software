<?php

namespace App\Services;

use App\Models\Purchase;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PurchaseService
{
    public function paginated(?string $query, int $perPage = 15): LengthAwarePaginator
    {
        return Purchase::with('vendor')
            ->search($query)
            ->latest('date')
            ->paginate($perPage);
    }

    public function create(array $data): Purchase
    {
        return \Illuminate\Support\Facades\DB::transaction(function () use ($data) {
            $purchase = Purchase::create($data);
            
            // Auto-trigger stock movement
            app(\App\Services\StockService::class)->recordIn([
                'item_name'      => $purchase->item ?? 'Poultry',
                'quantity'       => $purchase->quantity ?? 0,
                'rate'           => $purchase->rate ?? 0,
                'reference_type' => Purchase::class,
                'reference_id'   => $purchase->id,
                'date'           => $purchase->date,
                'created_by'     => auth()->id(),
            ]);
            
            return $purchase;
        });
    }

    public function find($id): Purchase
    {
        return Purchase::findOrFail($id);
    }

    public function update(Purchase $purchase, array $data): bool
    {
        return $purchase->update($data);
    }

    public function delete(Purchase $purchase): bool
    {
        return $purchase->delete();
    }

    public function allForExport(): Collection
    {
        return Purchase::orderByDesc('date')->get();
    }
}
