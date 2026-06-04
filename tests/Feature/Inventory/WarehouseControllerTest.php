<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class WarehouseControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->user->assignRole($role);
    }

    public function test_can_view_warehouses_index()
    {
        Warehouse::create(['name' => 'W1', 'location' => 'L1', 'is_active' => 1]);
        Warehouse::create(['name' => 'W2', 'location' => 'L2', 'is_active' => 1]);

        $response = $this->actingAs($this->user)->get(route('inventory.warehouses.index'));

        $response->assertStatus(200);
        $response->assertViewIs('inventory.warehouses.index');
        $response->assertViewHas('warehouses');
    }

    public function test_can_store_warehouse()
    {
        $data = [
            'name' => 'Main Silo',
            'location' => 'North Wing',
            'is_active' => 1
        ];

        $response = $this->actingAs($this->user)->post(route('inventory.warehouses.store'), $data);

        $response->assertRedirect(route('inventory.warehouses.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('warehouses', ['name' => 'Main Silo']);
    }

    public function test_validates_required_fields_on_store()
    {
        $response = $this->actingAs($this->user)->post(route('inventory.warehouses.store'), []);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_can_update_warehouse()
    {
        $warehouse = Warehouse::create(['name' => 'Old Silo', 'location' => 'Old Location', 'is_active' => 1]);

        $data = [
            'name' => 'New Silo',
            'location' => 'South Wing',
            'is_active' => 1
        ];

        $response = $this->actingAs($this->user)->put(route('inventory.warehouses.update', $warehouse), $data);

        $response->assertRedirect(route('inventory.warehouses.index'));
        $this->assertDatabaseHas('warehouses', ['id' => $warehouse->id, 'name' => 'New Silo']);
    }

    public function test_can_delete_warehouse()
    {
        $warehouse = Warehouse::create(['name' => 'To Delete', 'location' => 'Delete Location', 'is_active' => 1]);

        $response = $this->actingAs($this->user)->delete(route('inventory.warehouses.destroy', $warehouse));

        $response->assertRedirect(route('inventory.warehouses.index'));
        $this->assertDatabaseMissing('warehouses', ['id' => $warehouse->id]);
    }
}
