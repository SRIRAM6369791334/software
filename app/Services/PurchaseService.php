<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Item;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function paginated(?string $query, int $perPage = 15): LengthAwarePaginator
    {
        return Purchase::with(['vendor', 'items.item'])
            ->search($query)
            ->latest('date')
            ->paginate($perPage);
    }

    public function create(array $data): Purchase
    {
        return DB::transaction(function () use ($data) {
            $items = $data['items'] ?? [];
            unset($data['items']);
            
            // Grand totals are handled by client-side or calculated here
            $purchase = Purchase::create($data);
            
            foreach ($items as $itemData) {
                // Fetch item details if item_id is provided
                $itemName = $itemData['name'] ?? 'Unknown';
                if (!empty($itemData['item_id'])) {
                    $itemMaster = Item::find($itemData['item_id']);
                    $itemName = $itemMaster ? $itemMaster->name : $itemName;
                }

                $purchaseItem = $purchase->items()->create([
                    'item_id'      => $itemData['item_id'] ?? null,
                    'batch_id'     => $itemData['batch_id'] ?? null,
                    'warehouse_id' => $itemData['warehouse_id'] ?? null,
                    'item_name'    => $itemName,
                    'quantity'     => $itemData['qty'],
                    'unit'         => $itemData['unit'] ?? 'kg',
                    'rate'         => $itemData['rate'],
                    'tax_amount'   => $itemData['tax_amount'] ?? 0,
                    'total_amount' => $itemData['total_amount'] ?? ($itemData['qty'] * $itemData['rate']),
                ]);

                // Record stock movement if item_id is linked
                if ($purchaseItem->item_id) {
                    app(StockService::class)->recordIn([
                        'item_id'          => $purchaseItem->item_id,
                        'batch_id'         => $purchaseItem->batch_id,
                        'warehouse_id'     => $purchaseItem->warehouse_id,
                        'quantity'         => $purchaseItem->quantity,
                        'unit'             => $purchaseItem->unit,
                        'source_type'      => 'Purchase',
                        'source_id'        => $purchaseItem->id,
                        'transaction_date' => $purchase->date,
                        'remarks'          => "Purchase from {$purchase->vendor_name} (Inv: {$purchase->invoice_no})",
                    ]);
                }
            }
            
            return $purchase;
        });
    }

    public function find($id): Purchase
    {
        return Purchase::with('items.item')->findOrFail($id);
    }

    public function update(Purchase $purchase, array $data): bool
    {
        return DB::transaction(function () use ($purchase, $data) {
            $items = $data['items'] ?? [];
            unset($data['items']);
            
            // Important: On update, we need to handle existing stock ledger entries
            // For simplicity in this ERP, we'll clear and recreate (caution in production)
            DB::table('stock_ledgers')->where('source_type', 'Purchase')
                ->whereIn('source_id', $purchase->items->pluck('id'))
                ->delete();
                
            $purchase->items()->delete();
            $purchase->update($data);

            foreach ($items as $itemData) {
                $itemName = $itemData['name'] ?? 'Unknown';
                if (!empty($itemData['item_id'])) {
                    $itemMaster = Item::find($itemData['item_id']);
                    $itemName = $itemMaster ? $itemMaster->name : $itemName;
                }

                $purchaseItem = $purchase->items()->create([
                    'item_id'      => $itemData['item_id'] ?? null,
                    'batch_id'     => $itemData['batch_id'] ?? null,
                    'warehouse_id' => $itemData['warehouse_id'] ?? null,
                    'item_name'    => $itemName,
                    'quantity'     => $itemData['qty'],
                    'unit'         => $itemData['unit'] ?? 'kg',
                    'rate'         => $itemData['rate'],
                    'tax_amount'   => $itemData['tax_amount'] ?? 0,
                    'total_amount' => $itemData['total_amount'] ?? ($itemData['qty'] * $itemData['rate']),
                ]);

                if ($purchaseItem->item_id) {
                    app(StockService::class)->recordIn([
                        'item_id'          => $purchaseItem->item_id,
                        'batch_id'         => $purchaseItem->batch_id,
                        'warehouse_id'     => $purchaseItem->warehouse_id,
                        'quantity'         => $purchaseItem->quantity,
                        'unit'             => $purchaseItem->unit,
                        'source_type'      => 'Purchase',
                        'source_id'        => $purchaseItem->id,
                        'transaction_date' => $purchase->date,
                        'remarks'          => "Updated Purchase from {$purchase->vendor_name}",
                    ]);
                }
            }
            
            return true;
        });
    }

    public function delete(Purchase $purchase): bool
    {
        return DB::transaction(function () use ($purchase) {
            DB::table('stock_ledgers')->where('source_type', 'Purchase')
                ->whereIn('source_id', $purchase->items->pluck('id'))
                ->delete();
            return $purchase->delete();
        });
    }

    public function allForExport(): Collection
    {
        return Purchase::with('items')->orderByDesc('date')->get();
    }
}
