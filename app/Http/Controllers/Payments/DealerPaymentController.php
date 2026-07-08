<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\StoreDealerPaymentRequest;
use App\Models\DayLoadEntry;
use App\Models\Dealer;
use App\Models\DealerPayment;
use App\Services\DealerPaymentService;
use App\Services\ExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DealerPaymentController extends Controller
{
    public function __construct(
        private DealerPaymentService $service,
        private ExportService        $exporter,
    ) {}

    public function index(Request $request): View
    {
        $search       = $request->input('search');
        $dealerFilter = $request->input('dealer_id');
        $dateFrom     = $request->input('date_from');
        $dateTo       = $request->input('date_to');
        $modeFilter   = $request->input('payment_mode');

        $payments = DealerPayment::with('dealer')
            ->search($search)
            ->when($dealerFilter, fn($q) => $q->where('dealer_id', $dealerFilter))
            ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->when($modeFilter, fn($q) => $q->where('payment_mode', $modeFilter))
            ->latest('date')
            ->paginate(15);

        $dealers = Dealer::orderBy('firm_name')->get();
        return view('payments.dealers', compact(
            'payments', 'dealers', 'search',
            'dealerFilter', 'dateFrom', 'dateTo', 'modeFilter'
        ));
    }

    public function create(Request $request): View
    {
        $selected_dealer_id = $request->input('dealer_id');
        $dealers = Dealer::orderBy('firm_name')->get();

        $pendingDayLoadCount = 0;
        if ($selected_dealer_id) {
            $pendingDayLoadCount = DayLoadEntry::where('dealer_id', $selected_dealer_id)
                ->whereIn('dealer_payment_status', ['Pending', 'Partial'])
                ->count();
        }

        return view('payments.dealers.create', compact('dealers', 'selected_dealer_id', 'pendingDayLoadCount'));
    }

    public function store(StoreDealerPaymentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        if ((float) $validated['cash_amount'] + (float) $validated['bank_amount'] <= 0) {
            return back()->with('error', 'Total payout amount must be greater than zero.');
        }
        
        // 1. Record payment (Service handles deduction)
        $payment = $this->service->record($validated);

        return back()->with('success', 'Dealer payment recorded and balance updated.');
    }

    public function ledger(Dealer $dealer): View
    {
        $dayLoads = $dealer->dayLoadEntries()
            ->with(['vendor', 'batch'])
            ->get();

        $payments = $dealer->payments()->with('dayLoadEntry.vendor')->get();

        $rows = [];

        foreach ($dayLoads as $e) {
            $rows[] = [
                'date' => $e->batch->billing_date,
                'type' => 'load',
                'desc' => 'Day-Load: ' . $e->no_of_boxes . ' boxes (' . ($e->vendor->firm_name ?? '-') . ')',
                'ref' => 'DL-' . $e->id,
                'debit' => round((float) $e->bird_weight, 2),
                'credit' => 0,
                'group_id' => null,
                'sub_items' => [],
            ];
        }

        // Separate individual vs grouped payments
        $individual = $payments->whereNull('payment_group_id');
        $grouped    = $payments->whereNotNull('payment_group_id')->groupBy('payment_group_id');

        foreach ($individual as $p) {
            $rows[] = [
                'date' => $p->date,
                'type' => 'payment',
                'desc' => 'Payment (' . $p->payment_mode . ')',
                'ref' => 'PAY-' . str_pad($p->id, 4, '0', STR_PAD_LEFT),
                'debit' => 0,
                'credit' => round((float) $p->amount, 2),
                'group_id' => null,
                'sub_items' => [],
            ];
        }

        foreach ($grouped as $groupId => $group) {
            $totalAmount = $group->sum('amount');
            $firstDate   = $group->first()->date;

            $subItems = $group->map(fn($p) => [
                'entry_label' => $p->dayLoadEntry
                    ? 'Entry #' . $p->day_load_entry_id . ' (' . ($p->dayLoadEntry->vendor->firm_name ?? '-') . ')'
                    : 'Unallocated / Advance',
                'amount' => round((float) $p->amount, 2),
            ]);

            $rows[] = [
                'date' => $firstDate,
                'type' => 'payment',
                'desc' => 'Lump-Sum Payment (' . $group->count() . ' allocations)',
                'ref' => 'GRP-' . substr($groupId, 0, 8),
                'debit' => 0,
                'credit' => round((float) $totalAmount, 2),
                'group_id' => $groupId,
                'sub_items' => $subItems,
            ];
        }

        usort($rows, fn($a, $b) => $a['date'] <=> $b['date']);

        $totalDebit = array_sum(array_column($rows, 'debit'));
        $totalCredit = array_sum(array_column($rows, 'credit'));

        $page = request()->input('page', 1);
        $perPage = 20;
        $offset = ($page - 1) * $perPage;
        $sliced = array_slice($rows, $offset, $perPage);

        $paginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            count($rows),
            $perPage,
            $page,
            ['path' => request()->url()]
        );

        return view('payments.dealers.ledger', compact(
            'dealer', 'paginated', 'totalDebit', 'totalCredit'
        ));
    }

    public function export(): StreamedResponse
    {
        $rows = $this->service->allForExport()->map(fn($p) => [
            $p->dealer->firm_name ?? '—', $p->date->format('Y-m-d'), $p->amount, $p->payment_mode, $p->notes,
        ]);
        return $this->exporter->streamCsv(
            'dealer-payments',
            ['Dealer','Date','Amount','Mode','Notes'],
            $rows
        );
    }
}
