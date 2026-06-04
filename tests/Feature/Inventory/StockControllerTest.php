<?php

namespace Tests\Feature\Inventory;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class StockControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->user->assignRole($role);
    }

    public function test_can_view_stock_index_with_calculated_stock()
    {
        $item = Item::create([
            'name' => 'Test Feed',
            'type' => 'Feed',
            'base_unit' => 'kg',
            'conversion_rate' => 1.0,
            'is_active' => 1
        ]);

        // Add 50 IN, 20 OUT = 30 current stock
        DB::table('stock_ledgers')->insert([
            ['item_id' => $item->id, 'quantity' => 50, 'type' => 'IN', 'source_type' => 'Purchase', 'source_id' => 1, 'unit' => 'kg', 'transaction_date' => now()],
            ['item_id' => $item->id, 'quantity' => 20, 'type' => 'OUT', 'source_type' => 'Consumption', 'source_id' => 1, 'unit' => 'kg', 'transaction_date' => now()],
        ]);

        $response = $this->actingAs($this->user)->get(route('inventory.stock.index'));

        $response->assertStatus(200);
        $response->assertViewIs('inventory.stock.index');
        $response->assertViewHas('items');
        
        $items = $response->viewData('items');
        $this->assertEquals(30, $items->first()->current_stock);
    }

    public function test_can_filter_stock_index()
    {
        $item = Item::create(['name' => 'Searchable Feed', 'type' => 'Feed', 'base_unit' => 'kg', 'conversion_rate' => 1.0]);
        Item::create(['name' => 'Hidden Vaccine', 'type' => 'Vaccine', 'base_unit' => 'ml', 'conversion_rate' => 1.0]);

        $response = $this->actingAs($this->user)->get(route('inventory.stock.index', ['search' => 'Searchable']));

        $response->assertStatus(200);
        $items = $response->viewData('items');
        $this->assertCount(1, $items);
        $this->assertEquals('Searchable Feed', $items->first()->name);
    }

    public function test_can_view_movements()
    {
        $item = Item::create(['name' => 'Movement Item', 'type' => 'Feed', 'base_unit' => 'kg', 'conversion_rate' => 1.0]);
        
        $item->stockLedgers()->create([
            'quantity' => 100,
            'type' => 'IN',
            'source_type' => 'Purchase',
            'source_id' => 1,
            'unit' => 'kg',
            'transaction_date' => now(),
            'remarks' => 'Test movement'
        ]);

        $response = $this->actingAs($this->user)->get(route('inventory.stock.movements'));

        $response->assertStatus(200);
        $response->assertViewIs('inventory.stock.movements');
        $response->assertViewHas('movements');
        $response->assertSee('Test movement');
    }
}
