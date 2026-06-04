<?php

namespace Tests\Feature\Inventory;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->user->assignRole($role);
    }

    public function test_can_view_items_index()
    {
        Item::create(['name' => 'Item 1', 'type' => 'Feed', 'base_unit' => 'kg', 'conversion_rate' => 1.0]);
        Item::create(['name' => 'Item 2', 'type' => 'Equipment', 'base_unit' => 'pcs', 'conversion_rate' => 1.0]);

        $response = $this->actingAs($this->user)->get(route('inventory.items.index'));

        $response->assertStatus(200);
        $response->assertViewIs('inventory.items.index');
        $response->assertViewHas('items');
    }

    public function test_can_filter_items_by_search_and_type()
    {
        Item::create(['name' => 'Broiler Feed', 'type' => 'Feed', 'base_unit' => 'kg', 'conversion_rate' => 1.0]);
        Item::create(['name' => 'Vaccine A', 'type' => 'Vaccine', 'base_unit' => 'ml', 'conversion_rate' => 1.0]);

        $response = $this->actingAs($this->user)->get(route('inventory.items.index', [
            'search' => 'Broiler',
            'type' => 'Feed'
        ]));

        $response->assertStatus(200);
        $response->assertSee('Broiler Feed');
        $response->assertDontSee('Vaccine A');
    }

    public function test_can_store_new_item()
    {
        $data = [
            'name' => 'Starter Feed',
            'code' => 'FD-001',
            'type' => 'Feed',
            'category' => 'Nutrition',
            'brand' => 'PoultryPro',
            'base_unit' => 'kg',
            'conversion_rate' => 1.0,
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->user)->post(route('inventory.items.store'), $data);

        $response->assertRedirect(route('inventory.items.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('items', ['code' => 'FD-001']);
    }

    public function test_validates_required_fields_on_store()
    {
        $response = $this->actingAs($this->user)->post(route('inventory.items.store'), []);

        $response->assertSessionHasErrors(['name', 'type', 'base_unit', 'conversion_rate']);
    }

    public function test_can_update_item()
    {
        $item = Item::create(['name' => 'Old Name', 'type' => 'Feed', 'base_unit' => 'kg', 'conversion_rate' => 1.0, 'code' => 'OLD-123']);

        $data = [
            'name' => 'New Name',
            'code' => $item->code,
            'type' => 'Equipment',
            'base_unit' => 'pcs',
            'conversion_rate' => 1.0,
            'is_active' => 1,
        ];

        $response = $this->actingAs($this->user)->put(route('inventory.items.update', $item), $data);

        $response->assertRedirect(route('inventory.items.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('items', ['id' => $item->id, 'name' => 'New Name']);
    }

    public function test_can_delete_item_without_stock_ledgers()
    {
        $item = Item::create(['name' => 'To Delete', 'type' => 'Equipment', 'base_unit' => 'pcs', 'conversion_rate' => 1.0]);

        $response = $this->actingAs($this->user)->delete(route('inventory.items.destroy', $item));

        $response->assertRedirect(route('inventory.items.index'));
        $this->assertDatabaseMissing('items', ['id' => $item->id]);
    }

    public function test_cannot_delete_item_with_stock_ledgers()
    {
        $item = Item::create(['name' => 'Has Stock', 'type' => 'Feed', 'base_unit' => 'kg', 'conversion_rate' => 1.0]);
        
        // Simulate a stock ledger entry
        $item->stockLedgers()->create([
            'quantity' => 10,
            'type' => 'IN',
            'source_type' => 'Purchase',
            'source_id' => 1,
            'unit' => 'kg',
            'transaction_date' => now()
        ]);

        $response = $this->actingAs($this->user)->delete(route('inventory.items.destroy', $item));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('items', ['id' => $item->id]);
    }
}
