<?php

namespace Tests\Unit\Models;

use App\Models\Customer;
use App\Models\DailyBill;
use App\Models\DailyBillItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailyBillTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_a_customer()
    {
        $bill = DailyBill::factory()->create();
        $this->assertInstanceOf(Customer::class, $bill->customer);
    }

    public function test_it_has_many_items()
    {
        $bill = DailyBill::factory()->create();
        $this->assertGreaterThan(0, $bill->items->count());
        $this->assertInstanceOf(DailyBillItem::class, $bill->items->first());
    }

    public function test_search_scope_filters_by_customer_name()
    {
        $customer1 = Customer::factory()->create(['name' => 'John Doe']);
        $customer2 = Customer::factory()->create(['name' => 'Jane Smith']);

        DailyBill::factory()->create(['customer_id' => $customer1->id]);
        DailyBill::factory()->create(['customer_id' => $customer2->id]);

        $results = DailyBill::search('John')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('John Doe', $results->first()->customer->name);
    }

    public function test_search_scope_filters_by_item_name()
    {
        $bill1 = DailyBill::factory()->create();
        $bill1->items()->create([
            'item_name' => 'Special Poultry Feed',
            'quantity_kg' => 10,
            'rate_per_kg' => 50,
            'tax_amount' => 0,
            'total_amount' => 500,
        ]);

        $bill2 = DailyBill::factory()->create();
        $bill2->items()->create([
            'item_name' => 'Regular Feed',
            'quantity_kg' => 10,
            'rate_per_kg' => 50,
            'tax_amount' => 0,
            'total_amount' => 500,
        ]);

        $results = DailyBill::search('Special Poultry')->get();

        // The search might also match the default item created by factory. We specifically check that bill1 is in the results.
        $this->assertTrue($results->contains($bill1));
        $this->assertFalse($results->contains($bill2));
    }

    public function test_get_invoice_number_attribute()
    {
        $bill = DailyBill::factory()->create();
        $expected = 'INV-D-' . str_pad($bill->id, 4, '0', STR_PAD_LEFT);
        $this->assertEquals($expected, $bill->invoice_number);
    }

    public function test_backward_compatibility_accessors()
    {
        $bill = DailyBill::factory()->create();
        
        $item = $bill->items()->first();
        
        $this->assertEquals($item->item_name, $bill->items_description);
        $this->assertEquals($bill->items()->sum('quantity_kg'), $bill->quantity_kg);
        $this->assertEquals($item->rate_per_kg, $bill->rate_per_kg);
    }
}
