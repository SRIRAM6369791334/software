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
        return Purchase::create($data);
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
