<?php

namespace Tests\Unit\Models;

use App\Models\Batch;
use App\Models\Item;
use App\Models\StockLedger;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockLedgerTest extends TestCase
{
    use RefreshDatabase;

    public function test_belongs_to_item()
    {
        $item = Item::create(['name' => 'Ledger Item', 'type' => 'Feed', 'base_unit' => 'kg', 'conversion_rate' => 1.0]);
        $ledger = StockLedger::create([
            'item_id' => $item->id,
            'quantity' => 10,
            'type' => 'IN',
            'source_type' => 'Purchase',
            'source_id' => 1,
            'unit' => 'kg',
            'transaction_date' => now(),
        ]);

        $this->assertInstanceOf(Item::class, $ledger->item);
        $this->assertEquals('Ledger Item', $ledger->item->name);
    }

    public function test_belongs_to_warehouse()
    {
        $item = Item::create(['name' => 'Ledger Item 2', 'type' => 'Feed', 'base_unit' => 'kg', 'conversion_rate' => 1.0]);
        $warehouse = Warehouse::create(['name' => 'WH 1', 'is_active' => 1]);
        $ledger = StockLedger::create([
            'item_id' => $item->id,
            'warehouse_id' => $warehouse->id,
            'quantity' => 10,
            'type' => 'IN',
            'source_type' => 'Purchase',
            'source_id' => 1,
            'unit' => 'kg',
            'transaction_date' => now(),
        ]);

        $this->assertInstanceOf(Warehouse::class, $ledger->warehouse);
        $this->assertEquals('WH 1', $ledger->warehouse->name);
    }
}
