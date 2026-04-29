<?php

namespace App\Services;

use App\Models\StockMovement;
use App\Models\StockSummary;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function recordIn(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $movement = StockMovement::create(array_merge($data, ['type' => 'purchase_in']));
            $this->updateSummary($data['item_name'], $data['quantity'], $data['unit'] ?? 'kg');
            return $movement;
        });
    }

    public function recordOut(array $data): StockMovement
    {
        return DB::transaction(function () use ($data) {
            $movement = StockMovement::create(array_merge($data, ['type' => 'sale_out']));
            $this->updateSummary($data['item_name'], -$data['quantity'], $data['unit'] ?? 'kg');
            return $movement;
        });
    }

    public function getCurrentStock(string $itemName): float
    {
        return (float) StockSummary::where('item_name', $itemName)->value('current_stock') ?? 0.0;
    }

    public function getLowStockItems(float $threshold = 10.0): Collection
    {
        return StockSummary::where('current_stock', '<', $threshold)->get();
    }

    public function getStockMovements(string $from, string $to): Collection
    {
        return StockMovement::whereBetween('date', [$from, $to])->orderBy('date', 'desc')->get();
    }

    private function updateSummary(string $itemName, float $quantityChange, string $unit): void
    {
        $summary = StockSummary::firstOrCreate(
            ['item_name' => $itemName],
            ['current_stock' => 0, 'unit' => $unit]
        );

        $summary->current_stock += $quantityChange;
        $summary->last_updated = now();
        $summary->save();
    }
}
