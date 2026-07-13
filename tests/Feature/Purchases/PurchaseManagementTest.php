<?php

namespace Tests\Feature\Purchases;

use App\Models\Batch;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Vendor;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->actingAs($this->createAdmin());
    }

    public function test_purchase_entry_dashboard_loads_successfully()
    {
        $response = $this->get('/purchases/entry');
        $response->assertStatus(200);
        $response->assertSee('Purchase');
    }

    public function test_purchase_create_form_loads_successfully()
    {
        $response = $this->get('/purchases/create');
        $response->assertStatus(200);
    }

    public function test_purchase_invoices_list_loads_successfully()
    {
        $purchase = Purchase::factory()->create();

        $response = $this->get('/purchases/invoices?date=' . $purchase->date->format('Y-m-d'));
        $response->assertStatus(200);
        $response->assertSee($purchase->vendor_name);
    }

    public function test_purchase_show_page_loads_successfully()
    {
        $purchase = Purchase::factory()->create();

        $response = $this->get("/purchases/{$purchase->id}");
        $response->assertStatus(200);
        $response->assertSee($purchase->vendor_name);
    }

    public function test_purchase_edit_form_loads_successfully()
    {
        $purchase = Purchase::factory()->create();

        $response = $this->get("/purchases/{$purchase->id}/edit");
        $response->assertStatus(200);
        $response->assertSee($purchase->vendor_name);
    }

    public function test_purchase_print_view_loads_successfully()
    {
        $purchase = Purchase::factory()->create();

        $response = $this->get("/purchases/{$purchase->id}/print");
        $response->assertStatus(200);
    }

    public function test_purchase_can_be_stored()
    {
        $vendor = Vendor::factory()->create();
        $item = Item::create([
            'name' => 'Maize Feed',
            'code' => 'FEED-MZ',
            'type' => 'Feed',
            'category' => 'Feed',
            'base_unit' => 'kg',
            'conversion_rate' => 1.00,
            'is_active' => true
        ]);
        $warehouse = Warehouse::create([
            'name' => 'Main Granary',
            'code' => 'WH-MAIN',
            'location' => 'Block A',
            'is_active' => true
        ]);

        $data = [
            'vendor_id' => $vendor->id,
            'vendor_name' => $vendor->firm_name,
            'payment_mode' => 'UPI',
            'date' => today()->format('Y-m-d'),
            'gst_percentage' => 12,
            'items' => [
                [
                    'item_id' => $item->id,
                    'warehouse_id' => $warehouse->id,
                    'qty' => 50,
                    'rate' => 100,
                    'unit' => 'kg'
                ]
            ]
        ];

        $response = $this->post('/purchases', $data);
        $response->assertStatus(302); // Redirect back
        $this->assertDatabaseHas('purchases', [
            'vendor_name' => $vendor->firm_name,
            'payment_mode' => 'UPI'
        ]);
    }

    public function test_purchase_can_be_updated()
    {
        $purchase = Purchase::factory()->create();
        $vendor = Vendor::factory()->create();
        $item = Item::create([
            'name' => 'Soya Feed',
            'code' => 'FEED-SY',
            'type' => 'Feed',
            'category' => 'Feed',
            'base_unit' => 'pcs',
            'conversion_rate' => 1.00,
            'is_active' => true
        ]);
        $warehouse = Warehouse::create([
            'name' => 'Secondary WH',
            'code' => 'WH-SEC',
            'location' => 'Block B',
            'is_active' => true
        ]);

        $data = [
            'vendor_id' => $vendor->id,
            'vendor_name' => 'Updated Vendor Name',
            'payment_mode' => 'Cash',
            'date' => today()->format('Y-m-d'),
            'gst_percentage' => 18,
            'items' => [
                [
                    'item_id' => $item->id,
                    'warehouse_id' => $warehouse->id,
                    'qty' => 10,
                    'rate' => 200,
                    'unit' => 'pcs'
                ]
            ]
        ];

        $response = $this->put("/purchases/{$purchase->id}", $data);
        $response->assertRedirect('/purchases/invoices?date=' . today()->format('Y-m-d'));
        $this->assertDatabaseHas('purchases', [
            'id' => $purchase->id,
            'vendor_name' => 'Updated Vendor Name',
            'payment_mode' => 'Cash'
        ]);
    }

    public function test_purchase_can_be_deleted()
    {
        $purchase = Purchase::factory()->create();

        $response = $this->delete("/purchases/{$purchase->id}");
        $response->assertStatus(302);
        $this->assertDatabaseMissing('purchases', ['id' => $purchase->id]);
    }

    public function test_purchase_can_be_stored_on_emi_with_schedules()
    {
        $vendor = Vendor::factory()->create();
        $item = Item::create([
            'name' => 'Maize Feed',
            'code' => 'FEED-MZ',
            'type' => 'Feed',
            'category' => 'Feed',
            'base_unit' => 'kg',
            'conversion_rate' => 1.00,
            'is_active' => true
        ]);
        $warehouse = Warehouse::create([
            'name' => 'Main Granary',
            'code' => 'WH-MAIN',
            'location' => 'Block A',
            'is_active' => true
        ]);

        $data = [
            'vendor_id' => $vendor->id,
            'vendor_name' => $vendor->firm_name,
            'payment_mode' => 'Pay later(EMI)',
            'date' => today()->format('Y-m-d'),
            'gst_percentage' => 12,
            'items' => [
                [
                    'item_id' => $item->id,
                    'warehouse_id' => $warehouse->id,
                    'qty' => 50,
                    'rate' => 100,
                    'unit' => 'kg'
                ]
            ],
            'emis' => [
                ['due_date' => today()->addDays(30)->format('Y-m-d'), 'amount' => 2800.00],
                ['due_date' => today()->addDays(60)->format('Y-m-d'), 'amount' => 2800.00],
            ]
        ];

        $response = $this->post('/purchases', $data);
        $response->assertStatus(302);
        
        $this->assertDatabaseHas('purchases', [
            'vendor_name' => $vendor->firm_name,
            'payment_mode' => 'Pay later(EMI)'
        ]);

        // Verify that the expense was automatically registered!
        $this->assertDatabaseHas('expenses', [
            'category' => 'Purchase',
            'amount' => 5600.00, // 50 * 100 = 5000 + 12% GST = 5600
        ]);

        // Verify that the EMIs were created in the database!
        $this->assertDatabaseHas('emis', [
            'entity_id' => $vendor->id,
            'emi_type' => 'Vendor',
            'amount' => 2800.00,
            'status' => 'Upcoming'
        ]);
    }

    public function test_purchase_can_be_stored_with_null_warehouse_id()
    {
        $vendor = Vendor::factory()->create();
        $item = Item::create([
            'name' => 'Maize Feed Optional',
            'code' => 'FEED-MZ-OPT',
            'type' => 'Feed',
            'category' => 'Feed',
            'base_unit' => 'kg',
            'conversion_rate' => 1.00,
            'is_active' => true
        ]);

        $data = [
            'vendor_id' => $vendor->id,
            'vendor_name' => $vendor->firm_name,
            'payment_mode' => 'UPI',
            'date' => today()->format('Y-m-d'),
            'gst_percentage' => 12,
            'items' => [
                [
                    'item_id' => $item->id,
                    'warehouse_id' => null,
                    'qty' => 50,
                    'rate' => 100,
                    'unit' => 'kg'
                ]
            ]
        ];

        $response = $this->post('/purchases', $data);
        $response->assertStatus(302); // Redirect back
        $this->assertDatabaseHas('purchases', [
            'vendor_name' => $vendor->firm_name,
            'payment_mode' => 'UPI'
        ]);
    }
}
