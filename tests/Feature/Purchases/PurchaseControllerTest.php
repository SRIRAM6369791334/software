<?php

namespace Tests\Feature\Purchases;

use App\Models\Item;
use App\Models\Purchase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PurchaseControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $role = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $this->user->assignRole($role);
    }

    public function test_can_view_purchases_index()
    {
        $response = $this->actingAs($this->user)->get(route('purchases.entry'));

        $response->assertStatus(200);
        $response->assertViewIs('purchases.index');
        $response->assertViewHas('purchases');
    }

    public function test_can_store_purchase_with_stock_ledger_updates()
    {
        // Setup Item
        $item = Item::create([
            'name' => 'Broiler Starter',
            'type' => 'Feed',
            'base_unit' => 'kg',
            'conversion_rate' => 1.0,
            'is_active' => 1
        ]);

        $data = [
            'vendor_name' => 'Test Vendor',
            'payment_mode' => 'Cash',
            'date' => now()->format('Y-m-d'),
            'gst_percentage' => 5,
            'items' => [
                [
                    'item_id' => $item->id,
                    'qty' => 100,
                    'rate' => 50,
                    'unit' => 'kg'
                ]
            ]
        ];

        $response = $this->actingAs($this->user)->post(route('purchases.store'), $data);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Check Purchase was created
        $this->assertDatabaseHas('purchases', [
            'vendor_name' => 'Test Vendor',
            'gst_percentage' => 5,
        ]);
        
        $purchase = Purchase::where('vendor_name', 'Test Vendor')->first();
        
        // Calculate subtotal = 100 * 50 = 5000. GST = 5000 * 0.05 = 250. Total = 5250
        $this->assertEquals(5000, $purchase->items->first()->total_amount);
        $this->assertEquals(250, $purchase->gst_amount);
        $this->assertEquals(5250, $purchase->total_amount);

        // Check Stock Ledger was updated
        $this->assertDatabaseHas('stock_ledgers', [
            'item_id' => $item->id,
            'quantity' => 100,
            'type' => 'IN',
            'source_type' => 'Purchase',
        ]);
        
        // Check Expense was created
        $this->assertDatabaseHas('expenses', [
            'category' => 'Purchase',
            'amount' => 5250,
        ]);
    }

    public function test_can_update_purchase_and_adjust_stock_ledger()
    {
        // 1. Setup Item and initial purchase
        $item = Item::create([
            'name' => 'Broiler Starter',
            'type' => 'Feed',
            'base_unit' => 'kg',
            'conversion_rate' => 1.0,
            'is_active' => 1
        ]);

        $initialData = [
            'vendor_name' => 'Test Vendor',
            'payment_mode' => 'Cash',
            'date' => now()->format('Y-m-d'),
            'gst_percentage' => 0,
            'items' => [
                [
                    'item_id' => $item->id,
                    'qty' => 50,
                    'rate' => 10,
                    'unit' => 'kg'
                ]
            ]
        ];

        $this->actingAs($this->user)->post(route('purchases.store'), $initialData);
        $purchase = Purchase::where('vendor_name', 'Test Vendor')->first();
        
        // Ensure initial stock ledger exists
        $this->assertDatabaseHas('stock_ledgers', ['quantity' => 50, 'source_type' => 'Purchase']);

        // 2. Update the purchase
        $updateData = [
            'vendor_name' => 'Updated Vendor',
            'payment_mode' => 'Cash',
            'date' => now()->format('Y-m-d'),
            'gst_percentage' => 0,
            'items' => [
                [
                    'item_id' => $item->id,
                    'qty' => 75,
                    'rate' => 10,
                    'unit' => 'kg'
                ]
            ]
        ];

        $response = $this->actingAs($this->user)->put(route('purchases.update', $purchase->id), $updateData);
        
        $response->assertRedirect(route('purchases.invoices', ['date' => now()->format('Y-m-d')]));
        $response->assertSessionHas('success');
        
        // Check Purchase was updated
        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'vendor_name' => 'Updated Vendor',
            'total_amount' => 750,
        ]);

        // Check old ledger is removed/reverted, and new one is inserted
        $this->assertDatabaseMissing('stock_ledgers', ['quantity' => 50, 'source_type' => 'Purchase']);
        $this->assertDatabaseHas('stock_ledgers', ['quantity' => 75, 'source_type' => 'Purchase']);
    }

    public function test_can_delete_purchase_and_revert_stock()
    {
        $item = Item::create([
            'name' => 'Delete Test Feed',
            'type' => 'Feed',
            'base_unit' => 'kg',
            'conversion_rate' => 1.0,
            'is_active' => 1
        ]);

        $this->actingAs($this->user)->post(route('purchases.store'), [
            'vendor_name' => 'Vendor to Delete',
            'payment_mode' => 'Cash',
            'date' => now()->format('Y-m-d'),
            'gst_percentage' => 0,
            'items' => [
                ['item_id' => $item->id, 'qty' => 20, 'rate' => 5]
            ]
        ]);

        $purchase = Purchase::where('vendor_name', 'Vendor to Delete')->first();
        $this->assertDatabaseHas('stock_ledgers', ['quantity' => 20]);

        // Delete purchase
        $response = $this->actingAs($this->user)->delete(route('purchases.destroy', $purchase->id));
        $response->assertRedirect();
        
        // Verify deletion cascade
        $this->assertDatabaseMissing('purchases', ['id' => $purchase->id]);
        $this->assertDatabaseMissing('purchase_items', ['purchase_id' => $purchase->id]);
        $this->assertDatabaseMissing('stock_ledgers', ['quantity' => 20]);
    }
}
