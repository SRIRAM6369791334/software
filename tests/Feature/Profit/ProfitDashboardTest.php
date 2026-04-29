<?php

namespace Tests\Feature\Profit;

use App\Models\DailyBill;
use App\Models\Purchase;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfitDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());
    }

    public function test_profit_dashboard_loads()
    {
        $response = $this->get('/profit');

        $response->assertStatus(200);
        $response->assertViewHas('summary');
    }

    public function test_profit_formula_is_correct()
    {
        // Monthly summary uses whereMonth, so we need a date in the current month
        $today = now()->toDateString();
        
        DailyBill::factory()->create(['amount' => 10000, 'net_amount' => 10000, 'date' => $today]);
        
        // Explicitly set quantity and rate to avoid observer-driven random totals
        // quantity * rate = 4000
        Purchase::factory()->create([
            'quantity' => 40,
            'rate' => 100,
            'gst_percentage' => 0,
            'date' => $today
        ]);
        
        Expense::factory()->create(['amount' => 2000, 'date' => $today]);

        $response = $this->get('/profit');

        $summary = $response->original->getData()['summary'];
        // Profit = Sales (10000) - Purchases (4000) - Expenses (2000) = 4000
        $this->assertEquals(4000, $summary['profit']);
    }

    public function test_profit_dashboard_shows_daily_profit()
    {
        $today = now()->toDateString();
        DailyBill::factory()->create(['amount' => 500, 'net_amount' => 500, 'date' => $today]);
        
        $response = $this->get('/profit');
        $summary = $response->original->getData()['summary'];
        $this->assertEquals(500, $summary['revenue']);
    }

    public function test_profit_dashboard_shows_monthly_trend()
    {
        $today = now()->toDateString();
        DailyBill::factory()->create(['amount' => 1000, 'net_amount' => 1000, 'date' => $today]);
        
        $response = $this->get('/profit');
        $response->assertViewHas('monthlyData');
    }

    public function test_profit_is_zero_when_no_data()
    {
        $response = $this->get('/profit');
        $summary = $response->original->getData()['summary'];
        $this->assertEquals(0, $summary['profit']);
    }
}
