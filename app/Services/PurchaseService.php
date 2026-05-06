<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function paginated(?string $query, int $perPage = 15): LengthAwarePaginator
    {
        return Purchase::with(['vendor', 'items'])
            ->search($query)
            ->latest('date')
            ->paginate($perPage);
    }

    public function create(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];
            unset($data['items']);
            
            // Calculate grand total from items if not provided
            $gstPercentage = $data['gst_percentage'] ?? 18;
            $subtotal = 0;
            $totalTax = 0;
            
            foreach ($items as $item) {
                $base = $item['qty'] * $item['rate'];
                $subtotal += $base;
            }
            
            $totalTax = round($subtotal * $gstPercentage / 100, 2);
            $data['gst_amount'] = $totalTax;
            $data['total_amount'] = $subtotal + $totalTax;

            $purchase = Purchase::create($data);
            
            foreach ($items as $itemData) {
                $base = $itemData['qty'] * $itemData['rate'];
                $tax = round($base * $gstPercentage / 100, 2);
                
                $item = $purchase->items()->create([
                    'item_name'    => $itemData['name'],
                    'quantity'     => $itemData['qty'],
                    'unit'         => $itemData['unit'] ?? 'kg',
                    'rate'         => $itemData['rate'],
                    'tax_amount'   => $tax,
                    'total_amount' => $base + $tax,
                ]);

                // Record stock movement for each item
                app(StockService::class)->recordIn([
                    'item_name'      => $item->item_name,
                    'quantity'       => $item->quantity,
                    'rate'           => $item->rate,
                    'reference_type' => Purchase::class,
                    'reference_id'   => $purchase->id,
                    'date'           => $purchase->date,
                    'created_by'     => auth()->id() ?? 1,
                ]);
            }
            
            return $purchase;
        });
    }

    public function find($id): Purchase
    {
        return Purchase::with('items')->findOrFail($id);
    }

    public function update(Purchase $purchase, array $data): bool
    {
        return DB::transaction(function () use ($purchase, $data) {
            $items = $data['items'] ?? [];
            unset($data['items']);
            
            // For simplicity in this step, we clear and recreate items on update
            $purchase->items()->delete();
            
            $gstPercentage = $data['gst_percentage'] ?? 18;
            $subtotal = 0;
            
            foreach ($items as $item) {
                $subtotal += $item['qty'] * $item['rate'];
            }
            
            $data['gst_amount'] = round($subtotal * $gstPercentage / 100, 2);
            $data['total_amount'] = $subtotal + $data['gst_amount'];

            $purchase->update($data);

            foreach ($items as $itemData) {
                $base = $itemData['qty'] * $itemData['rate'];
                $tax = round($base * $gstPercentage / 100, 2);
                
                $purchase->items()->create([
                    'item_name'    => $itemData['name'],
                    'quantity'     => $itemData['qty'],
                    'unit'         => $itemData['unit'] ?? 'kg',
                    'rate'         => $itemData['rate'],
                    'tax_amount'   => $tax,
                    'total_amount' => $base + $tax,
                ]);
            }
            
            return true;
        });
    }

    public function delete(Purchase $purchase): bool
    {
        return $purchase->delete();
    }

    public function allForExport(): Collection
    {
        return Purchase::with('items')->orderByDesc('date')->get();
    }
}
