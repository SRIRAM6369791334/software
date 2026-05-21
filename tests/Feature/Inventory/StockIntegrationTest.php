<?php

namespace Tests\Feature\Inventory;

use App\Models\User;
use App\Models\Item;
use App\Models\Batch;
use App\Models\Warehouse;
use App\Models\Purchase;
use App\Models\Consumption;
use App\Models\StockLedger;
use App\Models\StockTransaction;
use App\Models\StockItem;
use App\Services\StockService;
use App\Services\PurchaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;
    protected $item;
    protected $batch;
    protected $warehouse;
    protected $stockService;
    protected $purchaseService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createAdmin();
        $this->actingAs($this->user);

        // Create core master records
        $this->item = Item::create([
            'name' => 'Maize Feed',
            'code' => 'FEED-MZ',
            'type' => 'Feed',
            'category' => 'Feed',
            'base_unit' => 'kg',
            'conversion_rate' => 1.00,
            'is_active' => true
        ]);

        $this->batch = Batch::create([
            'batch_code' => 'BATCH-001',
            'placement_date' => today()->format('Y-m-d'),
            'initial_count' => 1000,
            'current_count' => 1000,
            'status' => 'Active'
        ]);

        $this->warehouse = Warehouse::create([
            'name' => 'Main Granary',
            'code' => 'WH-MAIN',
            'location' => 'Block A',
            'is_active' => true
        ]);

        $this->stockService = app(StockService::class);
        $this->purchaseService = app(PurchaseService::class);
    }

    /** @test */
    public function creating_a_purchase_correctly_updates_both_stock_ledger_and_legacy_transactions()
    {
        $vendor = \App\Models\Vendor::factory()->create([
            'firm_name' => 'Elite Supplier'
        ]);

        $purchaseData = [
            'vendor_id' => $vendor->id,
            'vendor_name' => $vendor->firm_name,
            'date' => today()->format('Y-m-d'),
            'total_amount' => 1000.00,
            'gst_percentage' => 0.00,
            'gst_amount' => 0.00,
            'payment_mode' => 'Cash',
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'batch_id' => $this->batch->id,
                    'warehouse_id' => $this->warehouse->id,
                    'qty' => 500.000,
                    'unit' => 'kg',
                    'rate' => 2.00,
                    'tax_amount' => 0.00,
                    'total_amount' => 1000.00
                ]
            ]
        ];

        // Create purchase using the service
        $purchase = $this->purchaseService->create($purchaseData);
        $purchaseItem = $purchase->items->first();

        // 1. Verify that StockLedger row was created
        $this->assertDatabaseHas('stock_ledgers', [
            'item_id' => $this->item->id,
            'batch_id' => $this->batch->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 500.000,
            'type' => 'IN',
            'source_type' => 'Purchase',
            'source_id' => $purchaseItem->id,
        ]);

        // 2. Verify that StockTransaction row was created with resolved item_name
        $this->assertDatabaseHas('stock_transactions', [
            'txn_type' => 'IN',
            'item_name' => 'Maize Feed',
            'quantity' => 500.000,
            'reference_type' => \App\Models\PurchaseItem::class,
            'reference_id' => $purchaseItem->id,
        ]);

        // 3. Verify StockItem aggregated summary contains the 500.00 kg
        $this->assertDatabaseHas('stock_items', [
            'item_name' => 'Maize Feed',
            'current_stock' => 500.000
        ]);

        // 4. Verify StockService returns the correct current stock using both string and integer ID
        $this->assertEquals(500.000, $this->stockService->getCurrentStock($this->item->id));
        $this->assertEquals(500.000, $this->stockService->getCurrentStock('Maize Feed'));
    }

    /** @test */
    public function consuming_items_correctly_reduces_stock_and_is_blocked_by_insufficient_stock()
    {
        // Add 300 kg stock first
        $this->stockService->recordIn([
            'item_id' => $this->item->id,
            'batch_id' => $this->batch->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 300.000,
            'unit' => 'kg',
            'source_type' => 'Manual',
            'source_id' => 999,
        ]);

        $this->assertEquals(300.000, $this->stockService->getCurrentStock($this->item->id));

        // Test POST consumption endpoint with insufficient stock
        $response = $this->post(route('inventory.consumptions.store'), [
            'date' => today()->format('Y-m-d'),
            'batch_id' => $this->batch->id,
            'item_id' => $this->item->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 400.00, // exceeds 300 kg limit
            'remarks' => 'Feed evening batch'
        ]);

        $response->assertSessionHas('error');
        $this->assertDatabaseCount('poultry_consumptions', 0); // table is poultry_consumptions from migration

        // Test POST consumption endpoint with sufficient stock
        $response = $this->post(route('inventory.consumptions.store'), [
            'date' => today()->format('Y-m-d'),
            'batch_id' => $this->batch->id,
            'item_id' => $this->item->id,
            'warehouse_id' => $this->warehouse->id,
            'quantity' => 100.00,
            'remarks' => 'Feed morning batch'
        ]);

        $response->assertRedirect(route('inventory.consumptions.index'));
        $this->assertDatabaseCount('poultry_consumptions', 1);

        // Verify remaining stock is 200 kg
        $this->assertEquals(200.000, $this->stockService->getCurrentStock($this->item->id));
        $this->assertEquals(200.000, $this->stockService->getCurrentStock('Maize Feed'));
    }

    /** @test */
    public function deleting_purchase_or_consumption_reverts_all_associated_stock_moves()
    {
        // 1. Setup a Purchase with 200 kg
        $vendor = \App\Models\Vendor::factory()->create([
            'firm_name' => 'Elite Supplier'
        ]);

        $purchase = $this->purchaseService->create([
            'vendor_id' => $vendor->id,
            'vendor_name' => $vendor->firm_name,
            'date' => today()->format('Y-m-d'),
            'total_amount' => 400.00,
            'gst_percentage' => 0.00,
            'gst_amount' => 0.00,
            'payment_mode' => 'Cash',
            'items' => [
                [
                    'item_id' => $this->item->id,
                    'batch_id' => $this->batch->id,
                    'warehouse_id' => $this->warehouse->id,
                    'qty' => 200.000,
                    'unit' => 'kg',
                    'rate' => 2.00,
                    'tax_amount' => 0.00,
                    'total_amount' => 400.00
                ]
            ]
        ]);

        $this->assertEquals(200.000, $this->stockService->getCurrentStock($this->item->id));

        // 2. Delete the purchase via the service
        $this->purchaseService->delete($purchase);

        // Verify stock is reverted back to 0
        $this->assertEquals(0.000, $this->stockService->getCurrentStock($this->item->id));
        $this->assertDatabaseCount('stock_ledgers', 0);
        $this->assertDatabaseCount('stock_transactions', 0);
        $this->assertEquals(0.000, StockItem::where('item_name', 'Maize Feed')->first()->current_stock);
    }
}
