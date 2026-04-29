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
        $this->service->record($request->validated());
        return back()->with('success', 'Dealer payment recorded.');
    }

    public function ledger(Dealer $dealer): View
    {
        $payments = $dealer->payments()->latest('date')->paginate(20);
        return view('payments.dealers.ledger', compact('dealer', 'payments'));
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
