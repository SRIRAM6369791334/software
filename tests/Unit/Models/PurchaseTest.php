<?php

namespace Tests\Unit\Models;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PurchaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_backward_compatibility_accessors_use_first_item_when_attributes_missing()
    {
        $purchase = Purchase::create([
            'vendor_name' => 'Vendor A',
            'date' => now(),
            'payment_mode' => 'Cash',
            'gst_percentage' => 5,
            'gst_amount' => 5,
            'total_amount' => 105,
        ]);

        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'item_name' => 'First Item',
            'quantity' => 10,
            'unit' => 'kg',
            'rate' => 10,
            'total_amount' => 100,
            'tax_amount' => 0,
        ]);
        
        PurchaseItem::create([
            'purchase_id' => $purchase->id,
            'item_name' => 'Second Item',
            'quantity' => 5,
            'unit' => 'kg',
            'rate' => 20,
            'total_amount' => 100,
            'tax_amount' => 0,
        ]);

        $purchase->refresh();

        // Should sum quantity of all items
        $this->assertEquals(15, $purchase->quantity);
        // Should get unit, rate, and item from the first item
        $this->assertEquals('kg', $purchase->unit);
        $this->assertEquals(10, $purchase->rate);
        $this->assertEquals('First Item', $purchase->item);
    }

    public function test_search_scope_filters_by_vendor_or_item_name()
    {
        $purchase1 = Purchase::create([
            'vendor_name' => 'Alpha Vendor',
            'date' => now(),
            'payment_mode' => 'Cash',
            'gst_percentage' => 0,
            'gst_amount' => 0,
            'total_amount' => 100,
        ]);
        PurchaseItem::create([
            'purchase_id' => $purchase1->id,
            'item_name' => 'Widget A',
            'quantity' => 1,
            'rate' => 100,
            'total_amount' => 100,
            'tax_amount' => 0,
        ]);

        $purchase2 = Purchase::create([
            'vendor_name' => 'Beta Vendor',
            'date' => now(),
            'payment_mode' => 'Cash',
            'gst_percentage' => 0,
            'gst_amount' => 0,
            'total_amount' => 200,
        ]);
        PurchaseItem::create([
            'purchase_id' => $purchase2->id,
            'item_name' => 'Widget B',
            'quantity' => 2,
            'rate' => 100,
            'total_amount' => 200,
            'tax_amount' => 0,
        ]);

        // Search by Vendor Name
        $results = Purchase::search('Alpha')->get();
        $this->assertCount(1, $results);
        $this->assertEquals('Alpha Vendor', $results->first()->vendor_name);

        // Search by Item Name
        $results = Purchase::search('Widget B')->get();
        $this->assertCount(1, $results);
        $this->assertEquals('Beta Vendor', $results->first()->vendor_name);
    }
}
