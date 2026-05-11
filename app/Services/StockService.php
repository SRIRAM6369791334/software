<?php

namespace App\Services;

use App\Models\BirdBatch;
use App\Models\StockItem;
use App\Models\StockTransaction;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function recordIn(array $data): StockTransaction
    {
        return DB::transaction(function () use ($data) {
            $movement = StockTransaction::create(array_merge($data, ['txn_type' => 'IN']));
            $this->updateSummary($data['item_name'], $data['quantity'], $data['unit'] ?? 'kg');
            return $movement;
        });
    }

    public function recordOut(array $data): StockTransaction
    {
        return DB::transaction(function () use ($data) {
            $movement = StockTransaction::create(array_merge($data, ['txn_type' => 'OUT']));
            $this->updateSummary($data['item_name'], -$data['quantity'], $data['unit'] ?? 'kg');
            return $movement;
        });
    }

    public function adjustStock(array $data): StockTransaction
    {
        return DB::transaction(function () use ($data) {
            $movement = StockTransaction::create(array_merge($data, ['txn_type' => 'ADJUST']));
            $this->updateSummary($data['item_name'], $data['quantity'], $data['unit'] ?? 'kg');
            return $movement;
        });
    }

    public function recordMortality(BirdBatch $batch, int $count, string $reason, string $date): void
    {
        DB::transaction(function () use ($batch, $count, $reason, $date) {
            $batch->current_count -= $count;
            $batch->save();

            // Also record in stock transactions for "Birds" category
            StockTransaction::create([
                'txn_type' => 'OUT',
                'item_name' => 'Birds (' . $batch->batch_name . ')',
                'quantity' => $count,
                'unit' => 'pcs',
                'date' => $date,
                'notes' => 'Mortality: ' . $reason,
                'created_by' => auth()->id(),
                'reference_type' => BirdBatch::class,
                'reference_id' => $batch->id,
            ]);

            // Update birds stock summary
            $this->updateSummary('Birds (' . $batch->batch_name . ')', -$count, 'pcs', 'Birds');
        });
    }

    public function getCurrentStock(string $itemName): float
    {
        return (float) StockItem::where('item_name', $itemName)->value('current_stock') ?? 0.0;
    }

    public function getLowStockItems(): Collection
    {
        return StockItem::whereColumn('current_stock', '<', 'reorder_level')->get();
    }

    public function getStockMovements(string $from, string $to): Collection
    {
        return StockTransaction::whereBetween('date', [$from, $to])->orderBy('date', 'desc')->get();
    }

    private function updateSummary(string $itemName, float $quantityChange, string $unit, string $category = 'Feed'): void
    {
        $summary = StockItem::firstOrCreate(
            ['item_name' => $itemName],
            ['current_stock' => 0, 'unit' => $unit, 'category' => $category]
        );

        $summary->current_stock += $quantityChange;
        $summary->save();
    }
}
