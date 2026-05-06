<?php

namespace App\Services;

use App\Models\StockLedger;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Record an "IN" movement (Purchase, Production, Adjustment)
     */
    public function recordIn(array $data): StockLedger
    {
        return DB::transaction(function () use ($data) {
            return StockLedger::create([
                'item_id' => $data['item_id'],
                'batch_id' => $data['batch_id'] ?? null,
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'quantity' => $data['quantity'],
                'type' => 'IN',
                'source_type' => $data['source_type'],
                'source_id' => $data['source_id'],
                'unit' => $data['unit'],
                'transaction_date' => $data['transaction_date'] ?? now(),
                'remarks' => $data['remarks'] ?? null,
            ]);
        });
    }

    /**
     * Record an "OUT" movement (Consumption, Mortality, Sale, Adjustment)
     */
    public function recordOut(array $data): StockLedger
    {
        return DB::transaction(function () use ($data) {
            return StockLedger::create([
                'item_id' => $data['item_id'],
                'batch_id' => $data['batch_id'] ?? null,
                'warehouse_id' => $data['warehouse_id'] ?? null,
                'quantity' => $data['quantity'],
                'type' => 'OUT',
                'source_type' => $data['source_type'],
                'source_id' => $data['source_id'],
                'unit' => $data['unit'],
                'transaction_date' => $data['transaction_date'] ?? now(),
                'remarks' => $data['remarks'] ?? null,
            ]);
        });
    }

    /**
     * Get current stock of an item
     */
    public function getCurrentStock(int $itemId): float
    {
        $in = StockLedger::where('item_id', $itemId)->where('type', 'IN')->sum('quantity');
        $out = StockLedger::where('item_id', $itemId)->where('type', 'OUT')->sum('quantity');
        return (float) ($in - $out);
    }
}
