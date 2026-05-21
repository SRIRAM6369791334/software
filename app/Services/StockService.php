<?php

namespace App\Services;

use App\Models\BirdBatch;
use App\Models\StockItem;
use App\Models\StockTransaction;
use App\Models\StockLedger;
use App\Models\Item;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class StockService
{
    public function recordIn(array $data): StockTransaction
    {
        return DB::transaction(function () use ($data) {
            $data = $this->prepareTransactionData($data, 'IN');
            
            // Create legacy stock transaction
            $movement = StockTransaction::create($data);
            
            // Create poultry stock ledger if item_id is linked
            if (!empty($data['item_id'])) {
                StockLedger::create([
                    'item_id'          => $data['item_id'],
                    'batch_id'         => $data['batch_id'] ?? null,
                    'warehouse_id'     => $data['warehouse_id'] ?? null,
                    'quantity'         => $data['quantity'],
                    'type'             => 'IN',
                    'source_type'      => $data['source_type'] ?? 'Adjustment',
                    'source_id'        => $data['source_id'] ?? $movement->id,
                    'unit'             => $data['unit'] ?? 'kg',
                    'transaction_date' => $data['date'],
                    'remarks'          => $data['notes'] ?? null,
                ]);
            }

            $this->updateSummary($data['item_name'], $data['quantity'], $data['unit'] ?? 'kg');
            return $movement;
        });
    }

    public function recordOut(array $data): StockTransaction
    {
        return DB::transaction(function () use ($data) {
            $data = $this->prepareTransactionData($data, 'OUT');
            
            // Create legacy stock transaction
            $movement = StockTransaction::create($data);
            
            // Create poultry stock ledger if item_id is linked
            if (!empty($data['item_id'])) {
                StockLedger::create([
                    'item_id'          => $data['item_id'],
                    'batch_id'         => $data['batch_id'] ?? null,
                    'warehouse_id'     => $data['warehouse_id'] ?? null,
                    'quantity'         => $data['quantity'],
                    'type'             => 'OUT',
                    'source_type'      => $data['source_type'] ?? 'Adjustment',
                    'source_id'        => $data['source_id'] ?? $movement->id,
                    'unit'             => $data['unit'] ?? 'kg',
                    'transaction_date' => $data['date'],
                    'remarks'          => $data['notes'] ?? null,
                ]);
            }

            $this->updateSummary($data['item_name'], -$data['quantity'], $data['unit'] ?? 'kg');
            return $movement;
        });
    }

    public function adjustStock(array $data): StockTransaction
    {
        return DB::transaction(function () use ($data) {
            $data = $this->prepareTransactionData($data, 'ADJUST');
            
            // Create legacy stock transaction
            $movement = StockTransaction::create($data);
            
            // Create poultry stock ledger if item_id is linked
            if (!empty($data['item_id'])) {
                $type = $data['quantity'] >= 0 ? 'IN' : 'OUT';
                StockLedger::create([
                    'item_id'          => $data['item_id'],
                    'batch_id'         => $data['batch_id'] ?? null,
                    'warehouse_id'     => $data['warehouse_id'] ?? null,
                    'quantity'         => abs($data['quantity']),
                    'type'             => $type,
                    'source_type'      => $data['source_type'] ?? 'Adjustment',
                    'source_id'        => $data['source_id'] ?? $movement->id,
                    'unit'             => $data['unit'] ?? 'kg',
                    'transaction_date' => $data['date'],
                    'remarks'          => $data['notes'] ?? null,
                ]);
            }

            $this->updateSummary($data['item_name'], $data['quantity'], $data['unit'] ?? 'kg');
            return $movement;
        });
    }

    public function revertMovement(string $referenceType, int $referenceId): void
    {
        DB::transaction(function () use ($referenceType, $referenceId) {
            $transactions = StockTransaction::where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->get();

            foreach ($transactions as $transaction) {
                // Revert summary stock item amount
                $change = $transaction->txn_type === 'IN' ? -$transaction->quantity : $transaction->quantity;
                $this->updateSummary($transaction->item_name, $change, $transaction->unit);
                $transaction->delete();
            }

            // Also delete associated stock ledger entries
            StockLedger::where('source_type', $this->mapReferenceToSource($referenceType))
                ->where('source_id', $referenceId)
                ->delete();
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

    public function getCurrentStock(int|string $item): float
    {
        if (is_numeric($item)) {
            $itemModel = Item::find($item);
            return $itemModel ? (float) $itemModel->current_stock : 0.0;
        }

        return (float) StockItem::where('item_name', $item)->value('current_stock') ?? 0.0;
    }

    public function getLowStockItems(): Collection
    {
        return StockItem::whereColumn('current_stock', '<', 'reorder_level')->get();
    }

    public function getStockMovements(string $from, string $to): Collection
    {
        return StockTransaction::whereBetween('date', [$from, $to])->orderBy('date', 'desc')->get();
    }

    private function prepareTransactionData(array $data, string $txnType): array
    {
        $data['txn_type'] = $txnType;
        
        // Populate legacy 'type' field to satisfy NOT NULL constraints on stock_transactions
        if ($txnType === 'IN') {
            $data['type'] = 'purchase_in';
        } elseif ($txnType === 'OUT') {
            $data['type'] = 'sale_out';
        } else {
            $data['type'] = 'adjustment';
        }

        // Resolve item_name if only item_id is provided
        if (empty($data['item_name']) && !empty($data['item_id'])) {
            $item = Item::find($data['item_id']);
            $data['item_name'] = $item ? $item->name : 'Unknown Item';
        }

        // Resolve date if transaction_date is provided
        if (empty($data['date']) && !empty($data['transaction_date'])) {
            $data['date'] = $data['transaction_date'];
        } elseif (empty($data['date'])) {
            $data['date'] = now()->format('Y-m-d');
        }

        // Resolve notes if remarks is provided
        if (empty($data['notes']) && !empty($data['remarks'])) {
            $data['notes'] = $data['remarks'];
        }

        // Resolve reference fields for polymorphism
        if (empty($data['reference_type']) && !empty($data['source_type'])) {
            if ($data['source_type'] === 'Purchase') {
                $data['reference_type'] = \App\Models\PurchaseItem::class;
            } elseif ($data['source_type'] === 'Consumption') {
                $data['reference_type'] = \App\Models\Consumption::class;
            } else {
                $data['reference_type'] = $data['source_type'];
            }
        }
        if (empty($data['reference_id']) && !empty($data['source_id'])) {
            $data['reference_id'] = $data['source_id'];
        }

        if (empty($data['created_by']) && auth()->check()) {
            $data['created_by'] = auth()->id();
        }

        return $data;
    }

    private function mapReferenceToSource(string $referenceType): string
    {
        if ($referenceType === \App\Models\PurchaseItem::class) {
            return 'Purchase';
        }
        if ($referenceType === \App\Models\Consumption::class) {
            return 'Consumption';
        }
        return $referenceType;
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

