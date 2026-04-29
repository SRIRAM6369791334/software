<?php

namespace Tests\Feature\Reports;

use App\Models\Purchase;
use App\Models\Vendor;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VendorAnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());
    }

    public function test_vendor_analytics_page_loads()
    {
        $response = $this->get('/reports/purchases/vendor-analytics');

        $response->assertStatus(200);
        $response->assertViewIs('reports.purchases.vendor-analytics');
    }

    public function test_vendor_analytics_shows_vendor_data()
    {
        $vendor = Vendor::factory()->create(['firm_name' => 'Poultry Supplier Ltd']);
        Purchase::factory()->create(['vendor_id' => $vendor->id]);

        $response = $this->get('/reports/purchases/vendor-analytics');

        $response->assertViewHas('vendorWise');
        $response->assertSee('Poultry Supplier Ltd');
    }

    public function test_vendor_analytics_sorted_by_total_desc()
    {
        $vendor1 = Vendor::factory()->create(['firm_name' => 'Low Vendor']);
        $vendor2 = Vendor::factory()->create(['firm_name' => 'High Vendor']);

        Purchase::factory()->create(['vendor_id' => $vendor1->id, 'quantity' => 10, 'rate' => 100, 'gst_percentage' => 0]); // 1000
        Purchase::factory()->create(['vendor_id' => $vendor2->id, 'quantity' => 50, 'rate' => 100, 'gst_percentage' => 0]); // 5000

        $response = $this->get('/reports/purchases/vendor-analytics');

        $vendorWise = $response->original->getData()['vendorWise'];
        // High Vendor should be first because order is orderByDesc('total')
        $this->assertEquals($vendor2->id, $vendorWise->first()->vendor_id);
    }

    public function test_vendor_analytics_chart_data_present()
    {
        $vendor = Vendor::factory()->create(['firm_name' => 'Chart Vendor']);
        Purchase::factory()->create(['vendor_id' => $vendor->id]);

        $response = $this->get('/reports/purchases/vendor-analytics');

        $response->assertSee('vendorChart');
        $response->assertSee('Chart Vendor');
    }
}
