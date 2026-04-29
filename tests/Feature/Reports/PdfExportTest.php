<?php

namespace Tests\Feature\Reports;

use App\Models\User;
use App\Models\DailyBill;
use App\Models\Purchase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfExportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());
    }

    public function test_sales_pdf_export_returns_pdf_response()
    {
        DailyBill::factory()->create();

        $response = $this->get('/reports/sales/export-pdf');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_purchases_pdf_export_returns_pdf_response()
    {
        Purchase::factory()->create();

        $response = $this->get('/reports/purchases/export-pdf');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_pdf_export_with_date_filter()
    {
        $response = $this->get('/reports/sales/export-pdf?date=' . today()->toDateString());

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_pdf_export_with_no_data_does_not_crash()
    {
        $response = $this->get('/reports/sales/export-pdf?date=2099-12-31');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/pdf');
    }
}
