<?php

namespace Tests\Unit\Models;

use App\Models\Customer;
use App\Models\WeeklyBill;
use App\Models\WeeklyBillItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeeklyBillTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_belongs_to_a_customer()
    {
        $bill = WeeklyBill::factory()->create();
        $this->assertInstanceOf(Customer::class, $bill->customer);
    }

    public function test_it_has_many_items()
    {
        $bill = WeeklyBill::factory()->create();
        $this->assertGreaterThan(0, $bill->items->count());
        $this->assertInstanceOf(WeeklyBillItem::class, $bill->items->first());
    }

    public function test_search_scope_filters_by_customer_name()
    {
        $customer1 = Customer::factory()->create(['name' => 'Alice Adams']);
        $customer2 = Customer::factory()->create(['name' => 'Bob Brown']);

        WeeklyBill::factory()->create(['customer_id' => $customer1->id]);
        WeeklyBill::factory()->create(['customer_id' => $customer2->id]);

        $results = WeeklyBill::search('Alice')->get();

        $this->assertCount(1, $results);
        $this->assertEquals('Alice Adams', $results->first()->customer->name);
    }

    public function test_get_invoice_number_attribute()
    {
        $bill = WeeklyBill::factory()->create();
        $expected = 'INV-W-' . str_pad($bill->id, 4, '0', STR_PAD_LEFT);
        $this->assertEquals($expected, $bill->invoice_number);
    }

    public function test_backward_compatibility_accessors()
    {
        $bill = WeeklyBill::factory()->create();
        
        $itemNames = $bill->items->pluck('item_name')->implode(', ');
        $totalQuantity = $bill->items->sum('quantity_kg');
        
        $this->assertEquals($itemNames, $bill->items_description);
        $this->assertEquals($totalQuantity, $bill->quantity_kg);
    }
}
