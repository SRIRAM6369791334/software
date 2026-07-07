<?php

namespace Tests\Feature;

use App\Models\CashBankLedger;
use App\Models\DayLoadBatch;
use App\Models\DayLoadEntry;
use App\Models\DayLoadInvoice;
use App\Models\Dealer;
use App\Models\DealerPayment;
use App\Models\Expense;
use App\Models\PaymentAdjustmentLog;
use App\Models\Vendor;
use App\Services\CashBankLedgerService;
use App\Services\DayLoadBillingService;
use App\Services\DayLoadPaymentService;
use App\Services\InvoiceNumberService;
use App\Services\ProfitService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashBankLedgerTest extends TestCase
{
    use RefreshDatabase;

    private DayLoadBillingService $billingService;
    private DayLoadPaymentService $paymentService;
    private CashBankLedgerService $ledgerService;
    private ProfitService $profitService;
    private Vendor $vendor;
    private Dealer $dealer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAs($this->createAdmin());

        $this->billingService = app(DayLoadBillingService::class);
        $this->paymentService = app(DayLoadPaymentService::class);
        $this->ledgerService = app(CashBankLedgerService::class);
        $this->profitService = app(ProfitService::class);

        $this->vendor = Vendor::factory()->create();
        $this->dealer = Dealer::factory()->create();
    }

    // ── helpers ─────────────────────────────────────────────────────

    private function createEntry(array $overrides = []): DayLoadEntry
    {
        return $this->billingService->createEntry(array_merge([
            'billing_date'  => today()->format('Y-m-d'),
            'vendor_id'     => $this->vendor->id,
            'dealer_id'     => $this->dealer->id,
            'paper_rate'    => 100,
            'billing_rate'  => 150,
            'customer_rate' => 200,
            'no_of_boxes'   => 5,
            'box_weight'    => 20,
            'empty_weight'  => 2,
            'farm_weight'   => 85,
        ], $overrides));
    }

    // ═══════════════════════════════════════════════════════════════════
    //  1. Entry amount recalculates on bird_weight / customer_rate change
    // ═══════════════════════════════════════════════════════════════════

    public function test_entry_amount_is_computed_on_save(): void
    {
        $entry = $this->createEntry([
            'box_weight'    => 20,
            'empty_weight'  => 2,
            'customer_rate' => 200,
        ]);

        // bird_weight = 20 - 2 = 18
        // amount = 18 * 200 = 3600
        $entry->refresh();

        $this->assertEquals(18.00, (float) $entry->bird_weight);
        $this->assertEquals(3600.00, (float) $entry->amount);
    }

    public function test_entry_amount_updates_when_customer_rate_changes(): void
    {
        $entry = $this->createEntry([
            'box_weight'    => 20,
            'empty_weight'  => 2,
            'customer_rate' => 200,
        ]);

        $entry->update(['customer_rate' => 250]);
        $entry->refresh();

        // bird_weight = 18, new amount = 18 * 250 = 4500
        $this->assertEquals(4500.00, (float) $entry->amount);
    }

    public function test_entry_amount_updates_when_box_weight_changes(): void
    {
        $entry = $this->createEntry([
            'box_weight'    => 20,
            'empty_weight'  => 2,
            'customer_rate' => 200,
        ]);

        $entry->update(['box_weight' => 25]);
        $entry->refresh();

        // bird_weight = 25 - 2 = 23, amount = 23 * 200 = 4600
        $this->assertEquals(23.00, (float) $entry->bird_weight);
        $this->assertEquals(4600.00, (float) $entry->amount);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  2. Invoice total_amount sums entries
    // ═══════════════════════════════════════════════════════════════════

    public function test_invoice_total_amount_sums_entry_amounts(): void
    {
        // Create a batch with two entries
        $entry1 = $this->createEntry([
            'box_weight'    => 20,
            'empty_weight'  => 2,
            'customer_rate' => 200,
        ]);
        $batch = $entry1->batch;

        $entry2 = DayLoadEntry::factory()->create([
            'batch_id'      => $batch->id,
            'vendor_id'     => $this->vendor->id,
            'dealer_id'     => $this->dealer->id,
            'box_weight'    => 30,
            'empty_weight'  => 3,
            'customer_rate' => 150,
        ]);
        $entry2->refresh(); // triggers saving hook -> amount

        $invoice = $this->billingService->finalizeInvoice($batch);

        // entry1: bird 18 * 200 = 3600
        // entry2: bird 27 * 150 = 4050
        // total = 3600 + 4050 = 7650
        $this->assertEquals(7650.00, (float) $invoice->fresh()->total_amount);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  3. Invoice payment_status transitions Pending → Partial → Paid
    // ═══════════════════════════════════════════════════════════════════

    public function test_invoice_payment_status_transitions(): void
    {
        $entry = $this->createEntry([
            'box_weight'    => 20,
            'empty_weight'  => 2,
            'customer_rate' => 200,
        ]);
        // amount = 3600

        $batch = $entry->batch;
        $invoice = $this->billingService->finalizeInvoice($batch);
        $this->assertEquals(3600.00, (float) $invoice->fresh()->total_amount);
        $this->assertEquals('Pending', $invoice->fresh()->payment_status);

        // Partial: pay 1500
        $this->paymentService->recordDealerPayment($entry, [
            'date'        => today()->format('Y-m-d'),
            'payment_mode'=> 'Cash',
            'cash_amount' => 1500,
            'bank_amount' => 0,
        ]);
        $this->assertEquals('Partial', $invoice->fresh()->payment_status);
        $this->assertEquals(1500.00, (float) $invoice->fresh()->amount_paid);

        // Paid: pay remaining 2100
        $this->paymentService->recordDealerPayment($entry, [
            'date'        => today()->format('Y-m-d'),
            'payment_mode'=> 'Cash',
            'cash_amount' => 2100,
            'bank_amount' => 0,
        ]);
        $this->assertEquals('Paid', $invoice->fresh()->payment_status);
        $this->assertEquals(3600.00, (float) $invoice->fresh()->amount_paid);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  4. Validation: bank_amount > 0 requires bank_transfer_type
    // ═══════════════════════════════════════════════════════════════════

    public function test_bank_transfer_type_required_when_bank_amount_given(): void
    {
        $entry = $this->createEntry();

        $response = $this->post(
            route('billing.day-load.dealer-payment', $entry->id),
            [
                'date'        => today()->format('Y-m-d'),
                'payment_mode'=> 'UPI',
                'cash_amount' => 0,
                'bank_amount' => 5000,
                'bank_transfer_type' => '',
            ]
        );

        $response->assertSessionHasErrors('bank_transfer_type');
    }

    public function test_bank_transfer_type_not_required_when_bank_amount_zero(): void
    {
        $entry = $this->createEntry();

        $response = $this->post(
            route('billing.day-load.dealer-payment', $entry->id),
            [
                'date'        => today()->format('Y-m-d'),
                'payment_mode'=> 'Cash',
                'cash_amount' => 5000,
                'bank_amount' => 0,
                'bank_transfer_type' => '',
            ]
        );

        $response->assertSessionDoesntHaveErrors();
    }

    public function test_bank_transfer_type_accepted_values(): void
    {
        $entry = $this->createEntry();

        $response = $this->post(
            route('billing.day-load.dealer-payment', $entry->id),
            [
                'date'        => today()->format('Y-m-d'),
                'payment_mode'=> 'UPI',
                'cash_amount' => 0,
                'bank_amount' => 5000,
                'bank_transfer_type' => 'InvalidType',
            ]
        );

        $response->assertSessionHasErrors('bank_transfer_type');
    }

    // ═══════════════════════════════════════════════════════════════════
    //  5. Legacy amount column = cash_amount + bank_amount
    // ═══════════════════════════════════════════════════════════════════

    public function test_legacy_amount_equals_cash_plus_bank(): void
    {
        $entry = $this->createEntry();

        $payment = $this->paymentService->recordDealerPayment($entry, [
            'date'        => today()->format('Y-m-d'),
            'payment_mode'=> 'Cash',
            'cash_amount' => 1200.50,
            'bank_amount' => 800.25,
        ]);

        $this->assertEquals(2000.75, (float) $payment->fresh()->amount);
        $this->assertEquals(1200.50, (float) $payment->fresh()->cash_amount);
        $this->assertEquals(800.25, (float) $payment->fresh()->bank_amount);
    }

    public function test_legacy_amount_with_only_cash(): void
    {
        $entry = $this->createEntry();

        $payment = $this->paymentService->recordDealerPayment($entry, [
            'date'        => today()->format('Y-m-d'),
            'payment_mode'=> 'Cash',
            'cash_amount' => 3000,
            'bank_amount' => 0,
        ]);

        $this->assertEquals(3000.00, (float) $payment->fresh()->amount);
    }

    public function test_legacy_amount_with_only_bank(): void
    {
        $entry = $this->createEntry();

        $payment = $this->paymentService->recordDealerPayment($entry, [
            'date'        => today()->format('Y-m-d'),
            'payment_mode'=> 'UPI',
            'cash_amount' => 0,
            'bank_amount' => 5000,
            'bank_transfer_type' => 'UPI',
        ]);

        $this->assertEquals(5000.00, (float) $payment->fresh()->amount);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  6. recalculateForDate() closing balance formulas
    // ═══════════════════════════════════════════════════════════════════

    public function test_recalculate_closing_balances(): void
    {
        $date = Carbon::today();

        // Seed: opening_cash = 10000, opening_bank = 50000
        $this->ledgerService->getOrCreateForDate($date);
        CashBankLedger::whereDate('ledger_date', $date)->update([
            'opening_cash_balance' => 10000,
            'opening_bank_balance' => 50000,
        ]);

        // Record dealer payments: cash_income = 3000, bank_income = 7000
        $entry = $this->createEntry();
        DealerPayment::create([
            'dealer_id'  => $this->dealer->id,
            'day_load_entry_id' => $entry->id,
            'date'       => $date,
            'amount'     => 3000,
            'payment_mode' => 'Cash',
            'cash_amount' => 3000,
            'bank_amount' => 0,
        ]);
        DealerPayment::create([
            'dealer_id'  => $this->dealer->id,
            'day_load_entry_id' => $entry->id,
            'date'       => $date,
            'amount'     => 7000,
            'payment_mode' => 'UPI',
            'cash_amount' => 0,
            'bank_amount' => 7000,
            'bank_transfer_type' => 'UPI',
        ]);

        // Record cash expense
        Expense::create([
            'date'           => $date,
            'category'       => 'Fuel',
            'description'    => 'Test',
            'amount'         => 2000,
            'payment_method' => 'Cash',
        ]);

        $ledger = $this->ledgerService->recalculateForDate($date);

        // closing_cash = 10000 + 3000 - 2000 = 11000
        $this->assertEquals(11000.00, (float) $ledger->closing_cash_balance);
        // closing_bank = 50000 + 7000 = 57000
        $this->assertEquals(57000.00, (float) $ledger->closing_bank_balance);
        // Total = 11000 + 57000 = 68000
        $this->assertEquals(68000.00, (float) $ledger->closing_cash_balance + (float) $ledger->closing_bank_balance);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  7. approve() sweeps cash → bank, next day opening_cash = 0
    // ═══════════════════════════════════════════════════════════════════

    public function test_approve_sweeps_and_next_day_opens_at_zero(): void
    {
        $day1 = Carbon::today();
        $day2 = Carbon::today()->addDay();

        // Day 1: opening 0, income 10000
        $entry = $this->createEntry(['billing_date' => $day1->format('Y-m-d')]);
        DealerPayment::create([
            'dealer_id'  => $this->dealer->id,
            'day_load_entry_id' => $entry->id,
            'date'       => $day1,
            'amount'     => 10000,
            'payment_mode' => 'Cash',
            'cash_amount' => 10000,
            'bank_amount' => 0,
        ]);
        $ledger1 = $this->ledgerService->recalculateForDate($day1);

        // closing_cash = 10000, closing_bank = 0
        $this->assertEquals(10000.00, (float) $ledger1->closing_cash_balance);

        // Approve Day 1
        $ledger1 = $this->ledgerService->approve($ledger1, auth()->id());

        $this->assertTrue($ledger1->is_approved);
        $this->assertEquals(10000.00, (float) $ledger1->approved_amount);
        $this->assertEquals(0.00, (float) $ledger1->closing_cash_balance);
        $this->assertEquals(10000.00, (float) $ledger1->closing_bank_balance);

        // Day 2: opening_cash should be 0 (swept), opening_bank = 10000
        $ledger2 = $this->ledgerService->getOrCreateForDate($day2);
        $this->assertEquals(0.00, (float) $ledger2->opening_cash_balance);
        $this->assertEquals(10000.00, (float) $ledger2->opening_bank_balance);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  8. Already-approved ledger rejects second approve()
    // ═══════════════════════════════════════════════════════════════════

    public function test_already_approved_ledger_throws(): void
    {
        $date = Carbon::today();

        $entry = $this->createEntry();
        DealerPayment::create([
            'dealer_id'  => $this->dealer->id,
            'day_load_entry_id' => $entry->id,
            'date'       => $date,
            'amount'     => 5000,
            'payment_mode' => 'Cash',
            'cash_amount' => 5000,
            'bank_amount' => 0,
        ]);

        $ledger = $this->ledgerService->recalculateForDate($date);

        // First approval succeeds
        $this->ledgerService->approve($ledger, auth()->id());

        // Second approval must throw
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('already approved');

        $this->ledgerService->approve($ledger->fresh(), auth()->id());
    }

    // ═══════════════════════════════════════════════════════════════════
    //  9. Unapproved day's closing_cash carries forward
    // ═══════════════════════════════════════════════════════════════════

    public function test_unapproved_day_carries_closing_cash_forward(): void
    {
        $day1 = Carbon::today();
        $day2 = Carbon::today()->addDay();

        $entry = $this->createEntry(['billing_date' => $day1->format('Y-m-d')]);
        DealerPayment::create([
            'dealer_id'  => $this->dealer->id,
            'day_load_entry_id' => $entry->id,
            'date'       => $day1,
            'amount'     => 7500,
            'payment_mode' => 'Cash',
            'cash_amount' => 7500,
            'bank_amount' => 0,
        ]);
        $ledger1 = $this->ledgerService->recalculateForDate($day1);
        $this->assertEquals(7500.00, (float) $ledger1->closing_cash_balance);

        // NOT approved — closing carries forward
        $ledger2 = $this->ledgerService->getOrCreateForDate($day2);
        $this->assertEquals(7500.00, (float) $ledger2->opening_cash_balance);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  10. First-ever ledger row opens with 0/0
    // ═══════════════════════════════════════════════════════════════════

    public function test_first_ever_ledger_opens_at_zero(): void
    {
        $date = Carbon::today();
        $ledger = $this->ledgerService->getOrCreateForDate($date);

        $this->assertEquals(0.00, (float) $ledger->opening_cash_balance);
        $this->assertEquals(0.00, (float) $ledger->opening_bank_balance);
        $this->assertEquals(0.00, (float) $ledger->closing_cash_balance);
        $this->assertEquals(0.00, (float) $ledger->closing_bank_balance);
        $this->assertFalse($ledger->is_approved);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  11. Editing dealer_payment triggers PaymentAdjustmentLog
    // ═══════════════════════════════════════════════════════════════════

    public function test_update_dealer_payment_logs_audit_trail(): void
    {
        $entry = $this->createEntry();
        $payment = $this->paymentService->recordDealerPayment($entry, [
            'date'        => today()->format('Y-m-d'),
            'payment_mode'=> 'Cash',
            'cash_amount' => 2000,
            'bank_amount' => 0,
        ]);

        $this->assertDatabaseMissing('payment_adjustment_logs', [
            'payment_id' => $payment->id,
        ]);

        // Update the payment
        $this->paymentService->updateDealerPayment($payment, [
            'cash_amount' => 2500,
            'bank_amount' => 0,
            'payment_mode' => 'Cash',
        ], 'Corrected amount from 2000 to 2500');

        $this->assertDatabaseHas('payment_adjustment_logs', [
            'payment_id' => $payment->id,
            'action_type' => 'Edit',
            'reason' => 'Corrected amount from 2000 to 2500',
        ]);

        $log = PaymentAdjustmentLog::where('payment_id', $payment->id)->first();
        $this->assertNotNull($log);
        $this->assertEquals('Edit', $log->action_type);
        $this->assertIsArray($log->old_values);
        $this->assertIsArray($log->new_values);
        $this->assertEquals(2000, (float) $log->old_values['amount']);
        $this->assertEquals(2500, (float) $log->new_values['amount']);
    }

    // ═══════════════════════════════════════════════════════════════════
    //  12. Double-count check: DayLoadInvoice added, linked DealerPayment excluded
    // ═══════════════════════════════════════════════════════════════════

    public function test_summary_includes_invoice_total_not_invoice_plus_payment(): void
    {
        $entry = $this->createEntry([
            'box_weight'    => 50,
            'empty_weight'  => 0,
            'customer_rate' => 200,
        ]);
        // bird_weight = 50, amount = 10000

        $batch = $entry->batch;
        $invoice = $this->billingService->finalizeInvoice($batch);
        $this->assertEquals(10000.00, (float) $invoice->fresh()->total_amount);

        // Record a linked payment of 3000
        $this->paymentService->recordDealerPayment($entry, [
            'date'        => today()->format('Y-m-d'),
            'payment_mode'=> 'Cash',
            'cash_amount' => 3000,
            'bank_amount' => 0,
        ]);

        $summary = $this->profitService->getSummary();

        // revenue should be exactly 10000 (the invoice total), NOT 13000
        $this->assertEquals(10000.00, (float) $summary['revenue'],
            'Summary revenue must count DayLoadInvoice.total_amount only, not plus the linked DealerPayment.'
        );
    }

    public function test_profit_breakdown_counts_billed_and_collected_separately(): void
    {
        $entry = $this->createEntry([
            'box_weight'    => 50,
            'empty_weight'  => 0,
            'customer_rate' => 200,
        ]);
        // amount = 10000

        $batch = $entry->batch;
        $invoice = $this->billingService->finalizeInvoice($batch);
        $this->assertEquals(10000.00, (float) $invoice->fresh()->total_amount);

        // Record a linked payment of 3000
        $this->paymentService->recordDealerPayment($entry, [
            'date'        => today()->format('Y-m-d'),
            'payment_mode'=> 'Cash',
            'cash_amount' => 3000,
            'bank_amount' => 0,
        ]);

        $start = today()->startOfMonth()->format('Y-m-d');
        $end   = today()->endOfMonth()->format('Y-m-d');
        $bd    = $this->profitService->getProfitBreakdown($start, $end);

        // totalBilled must include the 10000 invoice
        $this->assertEquals(10000.00, (float) $bd['total_billed'],
            'totalBilled must include DayLoadInvoice.total_amount.'
        );
        // totalCollected must include the 3000 payment
        $this->assertEquals(3000.00, (float) $bd['total_collected'],
            'totalCollected must include the linked DealerPayment (separate metric).'
        );
        // pending = billed - collected = 10000 - 3000 = 7000
        $this->assertEquals(7000.00, (float) $bd['pending_collection'],
            'pendingCollection must be invoice total minus collected amount.'
        );
    }

    public function test_old_style_dealer_payment_without_invoice_still_counts_in_summary(): void
    {
        // Create an old-style DealerPayment (no invoice_id) — should still be counted in dPayments
        $dealerPayment = DealerPayment::factory()->create([
            'dealer_id'  => $this->dealer->id,
            'date'       => today()->format('Y-m-d'),
            'amount'     => 5000,
            'payment_mode' => 'Cash',
            'invoice_id' => null,
        ]);

        $summary = $this->profitService->getSummary();

        $this->assertGreaterThanOrEqual(5000, (float) $summary['revenue'],
            'Old-style DealerPayment (invoice_id IS NULL) must still be counted in revenue.'
        );
    }
}
