<?php

namespace Tests\Feature\Reports;

use App\Models\WeeklyBill;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WeeklySalesReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());
    }

    public function test_weekly_sales_report_loads()
    {
        $response = $this->get('/reports/sales/weekly');

        $response->assertStatus(200);
        $response->assertViewIs('reports.sales.weekly');
    }

    public function test_weekly_report_shows_correct_date_range()
    {
        $start = today()->subDays(7)->toDateString();
        $end = today()->toDateString();

        $response = $this->get("/reports/sales/weekly?start=$start&end=$end");

        $response->assertSee($start);
        $response->assertSee($end);
    }

    public function test_weekly_report_route_wise_filter()
    {
        $customerA = Customer::factory()->create(['route' => 'Route A']);
        $customerB = Customer::factory()->create(['route' => 'Route B']);

        WeeklyBill::factory()->create([
            'customer_id' => $customerA->id, 
            'period_start' => now()->startOfWeek()
        ]);
        WeeklyBill::factory()->create([
            'customer_id' => $customerB->id, 
            'period_start' => now()->startOfWeek()
        ]);

        $response = $this->get('/reports/sales/weekly');

        $response->assertViewHas('routeWise');
    }

    public function test_weekly_report_shows_total_sale()
    {
        $start = now()->startOfWeek()->toDateString();
        WeeklyBill::factory()->create([
            'amount' => 5000, 
            'gst_amount' => 0, 
            'net_amount' => 5000, 
            'period_start' => $start
        ]);
        WeeklyBill::factory()->create([
            'amount' => 3000, 
            'gst_amount' => 0, 
            'net_amount' => 3000, 
            'period_start' => $start
        ]);

        $response = $this->get('/reports/sales/weekly');

        $response->assertSee('8,000');
    }
}
