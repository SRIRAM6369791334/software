<?php

namespace Tests\Unit\Models;

use App\Models\DayLoadBatch;
use App\Models\DayLoadEntry;
use App\Models\Dealer;
use App\Models\DealerPayment;
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

    public function test_dayload_outstanding_counts_non_cancelled_entries_only()
    {
        $dealer = Dealer::factory()->create(['pending_amount' => 0]);
        $batch = DayLoadBatch::factory()->create();

        DayLoadEntry::factory()->create([
            'batch_id' => $batch->id,
            'dealer_id' => $dealer->id,
            'box_weight' => 510,
            'empty_weight' => 10,
            'customer_rate' => 10,
            'status' => 'Active',
        ]);

        DayLoadEntry::factory()->create([
            'batch_id' => $batch->id,
            'dealer_id' => $dealer->id,
            'box_weight' => 310,
            'empty_weight' => 10,
            'customer_rate' => 10,
            'status' => 'Cancelled',
        ]);

        $this->assertEquals(5000, $dealer->dayload_outstanding);
        $this->assertEquals(5000, $dealer->displayed_outstanding);
    }

    public function test_dayload_outstanding_subtracts_payments()
    {
        $dealer = Dealer::factory()->create(['pending_amount' => 0]);
        $batch = DayLoadBatch::factory()->create();

        $entry = DayLoadEntry::factory()->create([
            'batch_id' => $batch->id,
            'dealer_id' => $dealer->id,
            'box_weight' => 1010,
            'empty_weight' => 10,
            'customer_rate' => 10,
            'status' => 'Active',
        ]);

        DealerPayment::factory()->create([
            'dealer_id' => $dealer->id,
            'day_load_entry_id' => $entry->id,
            'amount' => 3000,
        ]);

        $this->assertEquals(7000, $dealer->dayload_outstanding);
    }

    public function test_displayed_outstanding_merges_both_systems()
    {
        $dealer = Dealer::factory()->create(['pending_amount' => 15000]);
        $batch = DayLoadBatch::factory()->create();

        DayLoadEntry::factory()->create([
            'batch_id' => $batch->id,
            'dealer_id' => $dealer->id,
            'box_weight' => 510,
            'empty_weight' => 10,
            'customer_rate' => 10,
            'status' => 'Active',
        ]);

        $this->assertEquals(5000, $dealer->dayload_outstanding);
        $this->assertEquals(20000, $dealer->displayed_outstanding);
    }

    public function test_dayload_outstanding_includes_uninvoiced_entries()
    {
        $dealer = Dealer::factory()->create(['pending_amount' => 0]);

        $batch = DayLoadBatch::factory()->create(['status' => 'Open']);

        DayLoadEntry::factory()->create([
            'batch_id' => $batch->id,
            'dealer_id' => $dealer->id,
            'box_weight' => 810,
            'empty_weight' => 10,
            'customer_rate' => 10,
            'status' => 'Active',
        ]);

        $this->assertEquals(8000, $dealer->dayload_outstanding,
            'Un-invoiced entries must count toward outstanding (liability accrues at delivery).');
    }
}
