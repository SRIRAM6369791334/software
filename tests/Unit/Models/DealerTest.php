<?php

namespace Tests\Unit\Models;

use App\Models\Dealer;
use App\Models\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DealerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_has_fillable_attributes()
    {
        $dealer = new Dealer();
        $this->assertEquals([
            'firm_name', 'gst_number', 'location', 'contact_person', 'phone', 'route', 'route_id', 'pending_amount'
        ], $dealer->getFillable());
    }

    public function test_search_scope_filters_correctly()
    {
        Dealer::factory()->create(['firm_name' => 'Alpha Dealer', 'phone' => '9998887776']);
        Dealer::factory()->create(['firm_name' => 'Beta Sales', 'contact_person' => 'Jane Smith']);

        $this->assertEquals(1, Dealer::search('Alpha')->count());
        $this->assertEquals(1, Dealer::search('Jane')->count());
        $this->assertEquals(2, Dealer::search('')->count());
    }

    public function test_formatted_pending_accessor()
    {
        $dealer = Dealer::factory()->make(['pending_amount' => 2500]);
        $this->assertEquals('₹2,500', $dealer->formatted_pending);

        $dealerZero = Dealer::factory()->make(['pending_amount' => 0]);
        $this->assertEquals('—', $dealerZero->formatted_pending);
    }

    public function test_route_relation()
    {
        $route = Route::forceCreate(['route_name' => 'Dealer Route']);
        $dealer = Dealer::factory()->create(['route_id' => $route->id]);

        $this->assertInstanceOf(Route::class, $dealer->routeRelation);
        $this->assertEquals('Dealer Route', $dealer->routeRelation->route_name);
    }
}
