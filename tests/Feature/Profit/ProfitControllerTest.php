<?php

namespace Tests\Feature\Profit;

use App\Models\DailyBill;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfitControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());
    }

    public function test_monthly_displays_trend()
    {
        $response = $this->get(route('profit.monthly'));

        $response->assertStatus(200);
        $response->assertViewHas('monthlyTrend');
    }

    public function test_expense_vs_income_displays_view()
    {
        $response = $this->get(route('profit.expense-vs-income'));

        $response->assertStatus(200);
        $response->assertViewHas('summary');
        $response->assertViewHas('weeklyData');
    }

    public function test_batch_displays_view()
    {
        $response = $this->get(route('profit.batch'));

        $response->assertStatus(200);
    }

    public function test_order_wise_displays_view()
    {
        $response = $this->get(route('profit.order-wise'));

        $response->assertStatus(200);
    }

    public function test_comparison_displays_view()
    {
        $response = $this->get(route('profit.comparison'));

        $response->assertStatus(200);
        $response->assertViewHas('summary');
    }

    public function test_export_downloads_csv()
    {
        DailyBill::factory()->create(['amount' => 1000]);

        $response = $this->get(route('profit.export'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->assertHeader('Content-Disposition', 'attachment; filename=profit-report.csv');
    }

    public function test_export_pdf_downloads_pdf()
    {
        DailyBill::factory()->create(['amount' => 1000]);

        $response = $this->get(route('profit.export-pdf'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
