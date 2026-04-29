<?php

namespace Tests\Feature\Reports;

use App\Models\DailyBill;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonthlySalesReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());
    }

    public function test_monthly_sales_report_page_loads_successfully()
    {
        $response = $this->get('/reports/sales/monthly');

        $response->assertStatus(200);
        $response->assertViewIs('reports.sales.monthly');
    }

    public function test_monthly_sales_report_shows_summary_cards()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;
        
        // Format as YYYY-MM-DD for SQLite compatibility
        $date1 = now()->startOfMonth()->toDateString();
        $date2 = now()->endOfMonth()->toDateString();
        $oldDate = now()->subMonth()->startOfMonth()->toDateString();

        DailyBill::factory()->create([
            'date' => $date1, 
            'amount' => 1000, 
            'gst_amount' => 0, 
            'net_amount' => 1000,
            'status' => 'Paid'
        ]);
        DailyBill::factory()->create([
            'date' => $date2, 
            'amount' => 2000, 
            'gst_amount' => 0, 
            'net_amount' => 2000,
            'status' => 'Paid'
        ]);
        DailyBill::factory()->create([
            'date' => $oldDate, 
            'amount' => 5000,
            'status' => 'Paid'
        ]);

        $response = $this->get("/reports/sales/monthly?month=$currentMonth&year=$currentYear");

        $response->assertSee('3,000');
        $response->assertDontSee('5,000');
    }

    public function test_monthly_sales_report_groups_by_customer()
    {
        $customerA = Customer::factory()->create(['name' => 'Customer A']);
        $customerB = Customer::factory()->create(['name' => 'Customer B']);
        $today = now()->toDateString();

        DailyBill::factory()->create([
            'customer_id' => $customerA->id, 
            'amount' => 1000, 
            'gst_amount' => 0, 
            'net_amount' => 1000, 
            'date' => $today,
            'status' => 'Paid'
        ]);
        DailyBill::factory()->create([
            'customer_id' => $customerA->id, 
            'amount' => 500, 
            'gst_amount' => 0, 
            'net_amount' => 500, 
            'date' => $today,
            'status' => 'Paid'
        ]);
        DailyBill::factory()->create([
            'customer_id' => $customerB->id, 
            'amount' => 2000, 
            'gst_amount' => 0, 
            'net_amount' => 2000, 
            'date' => $today,
            'status' => 'Paid'
        ]);

        $response = $this->get('/reports/sales/monthly');

        $response->assertSee('Customer A');
        $response->assertSee('Customer B');
        $response->assertSee('1,500'); // Sum for Customer A
        $response->assertSee('2,000'); // Sum for Customer B
    }
}
