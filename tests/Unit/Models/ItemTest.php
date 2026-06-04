<?php

namespace Tests\Unit\Models;

use App\Models\Item;
use App\Models\StockLedger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_scope()
    {
        Item::create(['name' => 'Active Item', 'type' => 'Feed', 'base_unit' => 'kg', 'conversion_rate' => 1.0, 'is_active' => 1]);
        Item::create(['name' => 'Inactive Item', 'type' => 'Feed', 'base_unit' => 'kg', 'conversion_rate' => 1.0, 'is_active' => 0]);

        $activeItems = Item::active()->get();

        $this->assertCount(1, $activeItems);
        $this->assertEquals('Active Item', $activeItems->first()->name);
    }

    public function test_current_stock_accessor_calculates_in_minus_out()
    {
        $item = Item::create(['name' => 'Test Item', 'type' => 'Feed', 'base_unit' => 'kg', 'conversion_rate' => 1.0, 'is_active' => 1]);

        $item->stockLedgers()->create([
            'quantity' => 100,
            'type' => 'IN',
            'source_type' => 'Purchase',
            'source_id' => 1,
            'unit' => 'kg',
            'transaction_date' => now(),
        ]);

        $item->stockLedgers()->create([
            'quantity' => 30,
            'type' => 'OUT',
            'source_type' => 'Consumption',
            'source_id' => 1,
            'unit' => 'kg',
            'transaction_date' => now(),
        ]);
        
        $item->stockLedgers()->create([
            'quantity' => 20,
            'type' => 'IN',
            'source_type' => 'Adjustment',
            'source_id' => 1,
            'unit' => 'kg',
            'transaction_date' => now(),
        ]);

        // 100 - 30 + 20 = 90
        $this->assertEquals(90, $item->current_stock);
    }
}
