<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payments\StoreDealerPaymentRequest;
use App\Models\Dealer;
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
        $search   = $request->input('search');
        $payments = $this->service->paginated($search, 15);
        $dealers  = Dealer::orderBy('firm_name')->get();
        return view('payments.dealers', compact('payments', 'dealers', 'search'));
    }

    public function create(Request $request): View
    {
        $selected_dealer_id = $request->input('dealer_id');
        $dealers = Dealer::orderBy('firm_name')->get();
        return view('payments.dealers.create', compact('dealers', 'selected_dealer_id'));
    }

    public function store(StoreDealerPaymentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        
        // 1. Record payment (Service handles deduction)
        $payment = $this->service->record($validated);

        return back()->with('success', 'Dealer payment recorded and balance updated.');
    }

    public function ledger(Dealer $dealer): View
    {
        $dayLoads = $dealer->dayLoadEntries()
            ->with(['vendor', 'batch'])
            ->get();

        $payments = $dealer->payments()->get();

        $rows = [];

        foreach ($dayLoads as $e) {
            $rows[] = [
                'date' => $e->batch->billing_date,
                'type' => 'load',
                'desc' => 'Day-Load: ' . $e->no_of_boxes . ' boxes (' . ($e->vendor->firm_name ?? '-') . ')',
                'ref' => 'DL-' . $e->id,
                'debit' => round((float) $e->bird_weight, 2),
                'credit' => 0,
            ];
        }

        foreach ($payments as $p) {
            $rows[] = [
                'date' => $p->date,
                'type' => 'payment',
                'desc' => 'Payment (' . $p->payment_mode . ')',
                'ref' => 'PAY-' . str_pad($p->id, 4, '0', STR_PAD_LEFT),
                'debit' => 0,
                'credit' => round((float) $p->amount, 2),
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
