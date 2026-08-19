<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Dealer;
use App\Models\Vendor;
use App\Models\DayLoadBatch;
use App\Models\DayLoadEntry;
use App\Models\DailyBill;
use App\Models\WeeklyBill;
use App\Models\DayLoadInvoice;
use App\Models\DealerPayment;
use App\Models\Emi;
use App\Models\Expense;
use App\Models\CashBankLedger;
use App\Services\ProfitService;
use App\Services\DayLoadBillingService;
use App\Services\CashBankLedgerService;

class AccountingBugFixesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Create basic masters
        $this->dealer = Dealer::create(['firm_name' => 'Test Dealer', 'contact_person' => 'A']);
        $this->vendor = Vendor::create(['firm_name' => 'Test Vendor', 'contact_person' => 'B']);
    }

    public function test_revenue_double_counting_is_fixed()
    {
        $batch = DayLoadBatch::create(['billing_date' => now(), 'vehicle_id' => 1, 'vendor_id' => $this->vendor->id, 'driver_id' => 1]);
        $entry = DayLoadEntry::create([
            'batch_id' => $batch->id,
            'dealer_id' => $this->dealer->id,
            'vendor_id' => $this->vendor->id,
            'bird_weight' => 100,
            'customer_rate' => 100,
            'amount' => 10000,
            'status' => 'Active'
        ]);

        // Dealer pays for the bill
        DealerPayment::create([
            'dealer_id' => $this->dealer->id,
            'amount' => 10000,
            'date' => now(),
            'payment_mode' => 'Cash'
        ]);

        $summary = app(ProfitService::class)->getSummary();
        // Accrual revenue should be exactly 10,000. Before the fix, it was 20,000 (10k billed + 10k paid).
        $this->assertEquals(10000, $summary['revenue']);
    }

    public function test_weight_loss_expense_does_not_corrupt_cash_ledger()
    {
        $batch = DayLoadBatch::create([
            'billing_date' => now(),
            'total_loss_weight' => 10,
            'total_paper_rate' => 100,
            'vehicle_id' => 1, 'vendor_id' => $this->vendor->id, 'driver_id' => 1,
            'is_weight_loss_approved' => false
        ]);

        app(DayLoadBillingService::class)->approveWeightLoss($batch);

        $batch->refresh();
        $this->assertTrue((bool)$batch->is_weight_loss_approved);

        // Expense should be created with Book Entry
        $expense = Expense::where('category', 'Weight Loss')->first();
        $this->assertNotNull($expense);
        $this->assertEquals('Book Entry', $expense->payment_method);

        // Cash Ledger should NOT contain this expense
        $ledgerService = app(CashBankLedgerService::class);
        $ledgerService->recalculateForDate(now()->format('Y-m-d'));
        $ledger = CashBankLedger::where('date', now()->format('Y-m-d'))->first();
        
        $this->assertEquals(0, $ledger->total_cash_expense);
    }

    public function test_partial_emi_is_included_in_expenses()
    {
        Emi::create([
            'emi_type' => 'Bank Loan',
            'entity_id' => 0,
            'amount' => 5000,
            'paid_amount' => 2000,
            'remaining_amount' => 3000,
            'due_date' => now(),
            'status' => 'Partial'
        ]);

        $summary = app(ProfitService::class)->getSummary();
        // Should include the 2000 paid_amount, not 0
        $this->assertEquals(2000, $summary['expenses']);
    }

    public function test_no_double_counting_between_dayload_and_daily_bill()
    {
        $batch = DayLoadBatch::create(['billing_date' => now(), 'vehicle_id' => 1, 'vendor_id' => $this->vendor->id, 'driver_id' => 1]);
        $entry = DayLoadEntry::create([
            'batch_id' => $batch->id,
            'dealer_id' => $this->dealer->id,
            'vendor_id' => $this->vendor->id,
            'bird_weight' => 100,
            'customer_rate' => 100,
            'amount' => 10000,
            'status' => 'Active'
        ]);

        // Wrap entry in a DailyBill
        $dailyBill = DailyBill::create([
            'dealer_id' => $this->dealer->id,
            'date' => now(),
            'invoice_no' => 'DB-1',
            'net_amount' => 10000
        ]);
        $entry->update(['daily_bill_id' => $dailyBill->id]);

        $summary = app(ProfitService::class)->getSummary();
        // Revenue should be exactly 10,000, not 20,000
        $this->assertEquals(10000, $summary['revenue']);
    }

    public function test_no_double_counting_between_dayload_and_dayload_invoice()
    {
        $batch = DayLoadBatch::create(['billing_date' => now(), 'vehicle_id' => 1, 'vendor_id' => $this->vendor->id, 'driver_id' => 1]);
        $entry = DayLoadEntry::create([
            'batch_id' => $batch->id,
            'dealer_id' => $this->dealer->id,
            'vendor_id' => $this->vendor->id,
            'bird_weight' => 100,
            'customer_rate' => 100,
            'amount' => 10000,
            'status' => 'Active'
        ]);

        // Finalize invoice via service
        app(DayLoadBillingService::class)->finalizeInvoice($batch);

        $summary = app(ProfitService::class)->getSummary();
        // Revenue should be exactly 10,000, not 20,000
        $this->assertEquals(10000, $summary['revenue']);
    }
}
