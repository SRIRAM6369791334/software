<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Customer;
use App\Models\Dealer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->seed(\Database\Seeders\RolesAndPermissionsSeeder::class);
        $this->user = User::factory()->create(['is_active' => true]);
        $this->user->assignRole('admin');
    }

    public function test_index_displays_report_summary(): void
    {
        Customer::create([
            'name' => 'Test Customer',
            'phone' => '1234567890',
            'balance' => 500,
        ]);
        Dealer::create([
            'firm_name' => 'Test Dealer',
            'phone' => '0987654321',
            'pending_amount' => 1000,
        ]);

        $response = $this->actingAs($this->user)->get(route('reports.index'));

        $response->assertStatus(200);
        $response->assertViewIs('reports.index');
        $response->assertViewHasAll(['summary', 'topCustomers', 'topDealers']);
        
        $summary = $response->viewData('summary');
        $this->assertEquals(1, $summary['total_customers']);
        $this->assertEquals(1, $summary['total_dealers']);
        $this->assertEquals(500, $summary['pending_receivables']);
        $this->assertEquals(1000, $summary['pending_payables']);
    }

    public function test_sales_daily_report_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.sales.daily', ['date' => now()->toDateString()]));
        
        $response->assertStatus(200);
        $response->assertViewIs('reports.sales.daily');
        $response->assertViewHasAll(['dailyBills', 'totalSale', 'totalGST', 'cashSales', 'creditSales']);
    }

    public function test_sales_weekly_report_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.sales.weekly'));
        
        $response->assertStatus(200);
        $response->assertViewIs('reports.sales.weekly');
        $response->assertViewHasAll(['bills', 'totalSale', 'routeWise']);
    }

    public function test_sales_monthly_report_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.sales.monthly'));
        
        $response->assertStatus(200);
        $response->assertViewIs('reports.sales.monthly');
        $response->assertViewHasAll(['bills', 'totalSale']);
    }

    public function test_purchases_daily_report_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.purchases.daily'));
        
        $response->assertStatus(200);
        $response->assertViewIs('reports.purchases.daily');
        $response->assertViewHasAll(['purchases', 'totalAmount']);
    }
    
    public function test_purchases_weekly_report_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.purchases.weekly'));
        
        $response->assertStatus(200);
        $response->assertViewIs('reports.purchases.weekly');
        $response->assertViewHasAll(['purchases', 'totalAmount', 'vendorWise']);
    }
    
    public function test_purchases_monthly_report_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.purchases.monthly'));
        
        $response->assertStatus(200);
        $response->assertViewIs('reports.purchases.monthly');
        $response->assertViewHasAll(['purchases', 'totalAmount', 'vendorWise', 'itemWise']);
    }

    public function test_vendor_analytics_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.purchases.vendor-analytics'));
        
        $response->assertStatus(200);
        $response->assertViewIs('reports.purchases.vendor-analytics');
        $response->assertViewHas('vendorWise');
    }

    public function test_customer_ranking_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.customers.ranking'));
        
        $response->assertStatus(200);
        $response->assertViewIs('reports.customers.ranking');
        $response->assertViewHas('customers');
    }

    public function test_purchase_analytics_renders(): void
    {
        $response = $this->actingAs($this->user)->get(route('reports.purchases.analytics'));
        
        $response->assertStatus(200);
        $response->assertViewIs('reports.purchases.analytics');
        $response->assertViewHas('analytics');
    }
}
