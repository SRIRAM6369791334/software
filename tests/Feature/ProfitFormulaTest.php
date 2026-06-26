<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Dealer;
use App\Models\Vendor;
use App\Models\Route;
use App\Models\DailyBill;
use App\Models\WeeklyBill;
use App\Models\CustomerPayment;
use App\Models\DealerPayment;
use App\Models\Purchase;
use App\Models\VendorPayment;
use App\Models\Expense;
use App\Models\Emi;
use App\Services\ProfitService;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * INFLOW  = Rs 70,00,000  (DailyBill 20L + WeeklyBill 15L + CustPay 20L + DealerPay 15L)
 * OUTFLOW = Rs 60,00,000  (Purchase 40L + VendorPay 10L + Expense 7L + EMI 3L)
 * PROFIT  = Rs 10,00,000
 */
class ProfitFormulaTest extends TestCase
{
    use RefreshDatabase;

    private Customer $customer;
    private Dealer $dealer;
    private Vendor $vendor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());
        $this->seedData();
    }

    private function seedData(): void
    {
        $route = Route::create(['route_name' => 'Test Route']);

        $this->customer = Customer::factory()->create([
            'phone' => '9000000001', 'name' => 'Test Customer Profit',
            'type' => 'Retail', 'route_id' => $route->id,
        ]);
        $this->dealer = Dealer::factory()->create([
            'phone' => '9000000002', 'firm_name' => 'Test Dealer Profit', 'route' => 'Test Route',
        ]);
        $this->vendor = Vendor::factory()->create([
            'phone' => '9000000003', 'firm_name' => 'Test Vendor Profit',
        ]);

        // INFLOW Rs 70,00,000
        // DailyBill 4 x Rs 5L = Rs 20,00,000
        for ($i = 0; $i < 4; $i++) {
            DailyBill::factory()->create([
                'customer_id' => $this->customer->id,
                'date' => now()->format('Y-m-d'),
                'amount' => 500000,
                'net_amount' => 500000,
                'gst_amount' => 0,
                'gst_percentage' => 0,
                'payment_mode' => 'Cash',
                'status' => 'Paid',
            ]);
        }
        // WeeklyBill 3 x Rs 5L = Rs 15,00,000
        for ($i = 0; $i < 3; $i++) {
            WeeklyBill::factory()->create([
                'dealer_id' => $this->dealer->id,
                'period_start' => now()->startOfWeek()->format('Y-m-d'),
                'period_end' => now()->format('Y-m-d'),
                'amount' => 500000,
                'net_amount' => 500000,
                'gst_amount' => 0,
                'gst_percentage' => 0,
                'payment_mode' => 'Cash',
                'status' => 'Paid',
            ]);
        }
        // CustomerPayment 4 x Rs 5L = Rs 20,00,000
        for ($i = 0; $i < 4; $i++) {
            CustomerPayment::create([
                'customer_id'  => $this->customer->id,
                'amount'       => 500000,
                'date'         => now()->format('Y-m-d'),
                'payment_mode' => 'Cash',
                'payment_type' => 'Full',
                'notes'        => 'Test',
            ]);
        }
        // DealerPayment 3 x Rs 5L = Rs 15,00,000 (Dealer pays US = INFLOW)
        for ($i = 0; $i < 3; $i++) {
            DealerPayment::create([
                'dealer_id'    => $this->dealer->id,
                'amount'       => 500000,
                'date'         => now()->format('Y-m-d'),
                'payment_mode' => 'Cash',
                'notes'        => 'Test',
            ]);
        }

        // OUTFLOW Rs 60,00,000
        // Purchase 8 x Rs 5L = Rs 40,00,000
        for ($i = 0; $i < 8; $i++) {
            Purchase::factory()->create([
                'vendor_id' => $this->vendor->id,
                'vendor_name' => $this->vendor->firm_name,
                'date' => now()->format('Y-m-d'),
                'quantity' => 500000,
                'rate' => 1,
                'gst_percentage' => 0,
                'gst_amount' => 0,
                'total_amount' => 500000,
                'payment_mode' => 'Cash',
            ]);
        }
        // VendorPayment 2 x Rs 5L = Rs 10,00,000 (We pay Vendor = OUTFLOW)
        for ($i = 0; $i < 2; $i++) {
            VendorPayment::create([
                'vendor_id'    => $this->vendor->id,
                'amount'       => 500000,
                'date'         => now()->format('Y-m-d'),
                'payment_mode' => 'Cash',
                'notes'        => 'Test',
            ]);
        }
        // Expense 7 x Rs 1L = Rs 7,00,000
        foreach (['Rent', 'Electricity', 'Labour', 'Transport', 'Feed', 'Medicine', 'Misc'] as $cat) {
            Expense::create([
                'category' => $cat,
                'description' => 'Test',
                'amount' => 100000,
                'date' => now()->format('Y-m-d'),
            ]);
        }
        // EMI 3 x Rs 1L = Rs 3,00,000
        for ($i = 0; $i < 3; $i++) {
            Emi::create([
                'loan_name' => 'EMI ' . $i,
                'bank_name' => 'Test Bank',
                'amount' => 100000,
                'due_date' => now()->format('Y-m-d'),
                'status' => 'Paid',
                'emi_type' => 'Customer',
                'entity_id' => $this->customer->id,
            ]);
        }
    }

    public function test_profit_summary_net_profit_is_10_lakhs(): void
    {
        $summary = app(ProfitService::class)->getSummary();

        // INFLOW = 20L+15L+20L+15L = 70L
        $this->assertEquals(7000000.0, $summary['revenue'],  'INFLOW must be Rs 70,00,000');
        // OUTFLOW purchase = 40L+10L = 50L
        $this->assertEquals(5000000.0, $summary['purchase'], 'OUTFLOW purchase must be Rs 50,00,000');
        // OUTFLOW expenses = 7L+3L = 10L
        $this->assertEquals(1000000.0, $summary['expenses'], 'OUTFLOW expenses must be Rs 10,00,000');
        // NET PROFIT = 70L-50L-10L = 10L
        $this->assertEquals(1000000.0, $summary['profit'],   'NET PROFIT must be Rs 10,00,000');
    }

    public function test_dealer_payment_is_inflow_not_outflow(): void
    {
        $summary = app(ProfitService::class)->getSummary();
        $this->assertEquals(7000000.0, $summary['revenue'],
            'DealerPayment (15L) must be INFLOW making total 70L');
        $this->assertNotEquals(5500000.0, $summary['revenue'],
            'Revenue must NOT be 55L - that would mean DealerPayment wrongly in outflow');
    }

    public function test_report_index_revenue_includes_all_sources(): void
    {
        $summary = app(ReportService::class)
            ->getIndexSummary(sprintf('%02d', now()->month), (string)now()->year);

        $this->assertEquals(7000000.0, $summary['total_revenue_month'],
            'Reports overview must include DailyBill+WeeklyBill+CustPayment+DealerPayment = 70L');
    }

    public function test_monthly_sales_includes_dealer_weekly_bills(): void
    {
        $data = app(ReportService::class)->getMonthlySales(now()->month, now()->year);
        $this->assertEquals(3500000.0, $data['totalSale'],
            'Monthly sales must include DailyBill(20L) + WeeklyBill(15L) = 35L');
    }

    public function test_profit_page_loads_with_correct_numbers(): void
    {
        $response = $this->get('/profit/');
        $response->assertStatus(200);
        $summary = $response->viewData('summary');
        $this->assertEquals(7000000.0, $summary['revenue']);
        $this->assertEquals(1000000.0, $summary['profit']);
    }

    public function test_reports_index_loads(): void
    {
        $this->get('/reports/')->assertStatus(200);
    }
}
