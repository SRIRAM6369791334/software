<?php

namespace Tests\Unit\Models;

use App\Models\Customer;
use App\Models\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_fillable_attributes()
    {
        $customer = new Customer();
        $this->assertEquals([
            'name', 'phone', 'address', 'gst_number', 'route', 'route_id', 'type', 'balance'
        ], $customer->getFillable());
    }

    public function test_search_scope_filters_correctly()
    {
        Customer::factory()->create(['name' => 'John Doe', 'phone' => '1234567890']);
        Customer::factory()->create(['name' => 'Jane Smith', 'phone' => '0987654321']);

        $this->assertEquals(1, Customer::search('John')->count());
        $this->assertEquals(1, Customer::search('0987')->count());
        $this->assertEquals(2, Customer::search('')->count());
    }

    public function test_with_balance_scope()
    {
        Customer::factory()->create(['balance' => 100]);
        Customer::factory()->create(['balance' => 0]);
        Customer::factory()->create(['balance' => 50.5]);

        $this->assertEquals(2, Customer::withBalance()->count());
    }

    public function test_formatted_balance_accessor()
    {
        $customer = Customer::factory()->make(['balance' => 1500.50]);
        $this->assertEquals('₹1,501', $customer->formatted_balance);

        $customerZero = Customer::factory()->make(['balance' => 0]);
        $this->assertEquals('—', $customerZero->formatted_balance);
    }

    public function test_route_relation()
    {
        $route = Route::forceCreate(['route_name' => 'Test Route']);
        $customer = Customer::factory()->create(['route_id' => $route->id]);

        $this->assertInstanceOf(Route::class, $customer->routeRelation);
        $this->assertEquals('Test Route', $customer->routeRelation->route_name);
    }
}
