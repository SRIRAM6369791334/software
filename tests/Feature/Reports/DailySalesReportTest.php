<?php

namespace Tests\Feature\Reports;

use App\Models\DailyBill;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DailySalesReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());
    }

    public function test_daily_sales_report_page_loads_successfully()
    {
        $response = $this->get('/reports/sales/daily');

        $response->assertStatus(200);
        $response->assertViewIs('reports.sales.daily');
    }

    public function test_daily_sales_report_shows_summary_cards()
    {
        DailyBill::factory()->create([
            'amount' => 1000, 
            'gst_amount' => 100, 
            'net_amount' => 1100,
            'payment_mode' => 'cash', 
            'date' => today()
        ]);
        DailyBill::factory()->create([
            'amount' => 2000, 
            'gst_amount' => 200, 
            'net_amount' => 2200,
            'payment_mode' => 'credit', 
            'date' => today()
        ]);

        $response = $this->get('/reports/sales/daily?date=' . today()->toDateString());

        $response->assertSee('Total Sale');
        $response->assertSee('Total GST');
        $response->assertSee('Cash Sales');
        $response->assertSee('Credit Sales');
        $response->assertSee('3,300');
    }

    public function test_daily_sales_report_shows_data_table()
    {
        $customer = Customer::factory()->create(['name' => 'John Doe']);
        DailyBill::factory()->create(['customer_id' => $customer->id, 'date' => today()]);

        $response = $this->get('/reports/sales/daily?date=' . today()->toDateString());

        $response->assertSee('John Doe');
    }

    public function test_daily_sales_report_date_filter_works()
    {
        $today = today()->toDateString();
        $yesterday = today()->subDay()->toDateString();

        DailyBill::factory()->create([
            'date' => $today, 
            'amount' => 777, 
            'gst_amount' => 0, 
            'net_amount' => 777
        ]);
        DailyBill::factory()->create([
            'date' => $yesterday, 
            'amount' => 999, 
            'gst_amount' => 0, 
            'net_amount' => 999
        ]);

        $response = $this->get("/reports/sales/daily?date=$today");

        $response->assertSee('777');
        $response->assertDontSee('999');
    }

    public function test_daily_sales_report_empty_state()
    {
        $response = $this->get('/reports/sales/daily?date=2099-01-01');

        $response->assertStatus(200);
        $response->assertSee('No sales records found');
    }
}
