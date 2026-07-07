<?php

namespace App\Http\Controllers;

use App\Models\CashBankLedger;
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
        $startDate = $request->input('start', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end', now()->format('Y-m-d'));

        // Ensure today's row exists before display
        $this->cashBankLedgerService->getOrCreateForDate(now());

        $ledgers = CashBankLedger::whereDate('ledger_date', '>=', $startDate)
            ->whereDate('ledger_date', '<=', $endDate)
            ->orderBy('ledger_date', 'desc')
            ->get();

        return view('billing.cash-bank-ledger.index', compact('ledgers', 'startDate', 'endDate'));
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
}
