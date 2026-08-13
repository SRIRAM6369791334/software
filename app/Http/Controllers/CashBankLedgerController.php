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

        $rawDealerPayments = DealerPayment::with('dealer')
            ->whereDate('date', $date)
            ->orderBy('created_at')
            ->get();

        $dealerPayments = $rawDealerPayments->groupBy('dealer_id')->map(function ($group) {
            $first = $group->first();
            $cashSum = (float) $group->sum('cash_amount');
            $bankSum = (float) $group->sum('bank_amount');
            $modes = $group->pluck('payment_mode')->unique()->filter()->values();
            $modeStr = $modes->count() > 1 ? 'Split' : ($modes->first() ?? 'Cash');
            $refs = $group->pluck('reference_number')->filter()->unique()->implode(', ');

            return (object) [
                'dealer'           => $first->dealer,
                'cash_amount'      => $cashSum,
                'bank_amount'      => $bankSum,
                'payment_mode'     => $modeStr,
                'reference_number' => $refs ?: null,
                'count'            => $group->count(),
            ];
        })->values();

        $customerPayments = CustomerPayment::with('customer')
            ->whereDate('date', $date)
            ->orderBy('created_at')
            ->get();

        $expenses = Expense::whereDate('date', $date)
            ->orderBy('created_at')
            ->get();

        $rawVendorPayments = VendorPayment::with('vendor')
            ->whereDate('date', $date)
            ->orderBy('created_at')
            ->get();

        $vendorPayments = $rawVendorPayments->groupBy('vendor_id')->map(function ($group) {
            $first = $group->first();
            $cashSum = (float) $group->sum('cash_amount');
            $bankSum = (float) $group->sum('bank_amount');
            $modes = $group->pluck('payment_mode')->unique()->filter()->values();
            $modeStr = $modes->count() > 1 ? 'Split' : ($modes->first() ?? 'Cash');
            $refs = $group->pluck('notes')->filter()->unique()->implode(', ');

            return (object) [
                'vendor'           => $first->vendor,
                'cash_amount'      => $cashSum,
                'bank_amount'      => $bankSum,
                'payment_mode'     => $modeStr,
                'reference_number' => $refs ?: null,
                'notes'            => $refs ?: null,
                'count'            => $group->count(),
            ];
        })->values();

        $dealerAdjustments = [];
        $dailyBills = \App\Models\DailyBill::with(['items', 'dealer'])
            ->whereDate('date', $date)
            ->whereNotNull('dealer_id')
            ->whereNotNull('customer_id')
            ->where('status', '!=', 'Cancelled')
            ->get();
            
        foreach ($dailyBills as $bill) {
            $dayLoadEntry = \App\Models\DayLoadEntry::where('dealer_id', $bill->dealer_id)
                ->whereHas('batch', function($q) use ($date) {
                    $q->whereDate('billing_date', $date);
                })->first();
            
            $dealerRate = $dayLoadEntry ? (float) $dayLoadEntry->customer_rate : 0;
            if ($dealerRate > 0) {
                $qtySold = $bill->items->sum('quantity_kg');
                $adj = $qtySold * $dealerRate;
                
                if (!isset($dealerAdjustments[$bill->dealer_id])) {
                    $dealerAdjustments[$bill->dealer_id] = [
                        'dealer_name' => $bill->dealer->firm_name ?? 'Dealer',
                        'invoices' => [],
                        'total_theoretical' => 0,
                    ];
                }
                
                $dealerAdjustments[$bill->dealer_id]['invoices'][] = (object)[
                    'qty' => $qtySold,
                    'rate' => $dealerRate,
                    'amount' => $adj,
                    'invoice' => $bill->invoice_number,
                    'customer_name' => $bill->customer->name ?? 'Customer',
                    'customer_amount' => (float) $bill->net_amount,
                ];
                $dealerAdjustments[$bill->dealer_id]['total_theoretical'] += $adj;
            }
        }

        $dealerAdjustment = 0;
        $adjustmentDetails = [];
        
        foreach ($dealerAdjustments as $dealerId => $data) {
            $dealerCashPaid = (float) DealerPayment::whereDate('date', $date)
                ->where('dealer_id', $dealerId)
                ->sum('cash_amount');
            $dealerBankPaid = (float) DealerPayment::whereDate('date', $date)
                ->where('dealer_id', $dealerId)
                ->sum('bank_amount');
            $totalDealerPaid = $dealerCashPaid + $dealerBankPaid;
                
            $allowedAdjustment = min($data['total_theoretical'], $totalDealerPaid);
            
            if ($allowedAdjustment > 0) {
                $dealerAdjustment += $allowedAdjustment;
                
                if ($allowedAdjustment < $data['total_theoretical']) {
                    $totalQty = 0;
                    $dealerRate = 0;
                    $totalCustomerAmount = 0;
                    foreach ($data['invoices'] as $inv) {
                        $totalQty += $inv->qty;
                        $dealerRate = $inv->rate;
                        $totalCustomerAmount += $inv->customer_amount;
                    }
                     $adjustmentDetails[] = (object)[
                        'dealer' => $data['dealer_name'],
                        'invoice' => 'Multiple (Capped)',
                        'qty' => $totalQty,
                        'rate' => $dealerRate,
                        'amount' => $allowedAdjustment,
                        'customer_name' => 'Multiple Customers',
                        'customer_amount' => $totalCustomerAmount,
                     ];
                } else {
                     foreach ($data['invoices'] as $inv) {
                         $adjustmentDetails[] = (object)[
                             'dealer' => $data['dealer_name'],
                             'invoice' => $inv->invoice,
                             'qty' => $inv->qty,
                             'rate' => $inv->rate,
                             'amount' => $inv->amount,
                             'customer_name' => $inv->customer_name ?? 'Customer',
                             'customer_amount' => $inv->customer_amount ?? 0,
                         ];
                     }
                }
            }
        }

        return view('billing.cash-bank-ledger.show', compact(
            'ledger', 'date', 'dayLoadBatch',
            'dealerPayments', 'customerPayments', 'expenses', 'vendorPayments',
            'dealerAdjustment', 'adjustmentDetails'
        ));
    }
}
