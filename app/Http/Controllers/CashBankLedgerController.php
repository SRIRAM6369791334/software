<?php

namespace App\Http\Controllers;

use App\Models\CashBankLedger;
use App\Models\CustomerPayment;
use App\Models\DayLoadBatch;
use App\Models\DayLoadEntry;
use App\Models\DealerPayment;
use App\Models\Expense;
use App\Models\VendorPayment;
use App\Services\CashBankLedgerService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashBankLedgerController extends Controller
{
    public function __construct(
        private CashBankLedgerService $cashBankLedgerService,
    ) {}

    public function index(Request $request): View
    {
        $startDate = $request->input('start');
        $endDate   = $request->input('end');
        $status    = $request->input('status', 'all');

        // Ensure today's row exists before display
        $this->cashBankLedgerService->getOrCreateForDate(now());

        // Base filtered query (date range + optional approval status)
        $baseQuery = CashBankLedger::query()
            ->when($startDate, fn($q, $v) => $q->whereDate('ledger_date', '>=', $v))
            ->when($endDate, fn($q, $v) => $q->whereDate('ledger_date', '<=', $v))
            ->when($status === 'approved', fn($q) => $q->where('is_approved', true))
            ->when($status === 'not_approved', fn($q) => $q->where('is_approved', false));

        // Aggregate sums over the FULL filtered range (before pagination)
        $totalCashIncome  = (clone $baseQuery)->sum('cash_income');
        $totalBankIncome  = (clone $baseQuery)->sum('bank_income');
        $totalCashExpense = (clone $baseQuery)->sum('cash_expense');
        $totalBankExpense = (clone $baseQuery)->sum('bank_expense'); // BUG 4 FIX

        // Current total balance: date-range only (no status filter),
        // from the most recent ledger row in the range
        $balanceQuery = CashBankLedger::query()
            ->when($startDate, fn($q, $v) => $q->whereDate('ledger_date', '>=', $v))
            ->when($endDate, fn($q, $v) => $q->whereDate('ledger_date', '<=', $v))
            ->orderBy('ledger_date', 'desc');
        $latestRow            = (clone $balanceQuery)->first();
        $currentTotalBalance  = $latestRow
            ? ((float) $latestRow->closing_cash_balance + (float) $latestRow->closing_bank_balance)
            : 0;

        // Paginated ledger rows (respects all filters)
        $ledgers = (clone $baseQuery)
            ->orderBy('ledger_date', 'desc')
            ->paginate(20)
            ->withQueryString();

        return view('billing.cash-bank-ledger.index', compact(
            'ledgers', 'startDate', 'endDate', 'status',
            'totalCashIncome', 'totalBankIncome', 'totalCashExpense', 'totalBankExpense', 'currentTotalBalance'
        ));
    }

    // TODO: restrict to Admin role once role-based permissions are finalized
    public function approve(CashBankLedger $ledger): RedirectResponse
    {
        try {
            $this->cashBankLedgerService->approve($ledger, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', "Ledger for {$ledger->ledger_date->format('d M Y')} approved successfully.");
    }

    public function showDay(string $date): View
    {
        $ledger = CashBankLedger::whereDate('ledger_date', $date)->firstOrFail();

        $dayLoadBatch = DayLoadBatch::with(['entries.vendor', 'entries.dealer', 'entries.dealerPayments'])
            ->whereDate('billing_date', $date)
            ->first();

        $dealerPayments = DealerPayment::with('dealer')
            ->whereDate('date', $date)
            ->orderBy('created_at')
            ->get();

        $customerPayments = CustomerPayment::with('customer')
            ->whereDate('date', $date)
            ->orderBy('created_at')
            ->get();

        $expenses = Expense::whereDate('date', $date)
            ->orderBy('created_at')
            ->get();

        $vendorPayments = VendorPayment::with('vendor')
            ->whereDate('date', $date)
            ->orderBy('created_at')
            ->get();

        return view('billing.cash-bank-ledger.show', compact(
            'ledger', 'date', 'dayLoadBatch',
            'dealerPayments', 'customerPayments', 'expenses', 'vendorPayments'
        ));
    }
}
