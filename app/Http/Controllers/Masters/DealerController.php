<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Http\Requests\Masters\StoreDealerRequest;
use App\Models\Dealer;
use App\Services\DealerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DealerController extends Controller
{
    public function __construct(private DealerService $service) {}

    public function index(Request $request): View
    {
        $search  = $request->input('search');
        $dealers = $this->service->search($search, 15);
        return view('masters.dealers.index', compact('dealers', 'search'));
    }

    public function create(): View
    {
        return view('masters.dealers.create');
    }

    public function store(StoreDealerRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());
        return back()->with('success', 'Dealer added successfully.');
    }

    public function update(StoreDealerRequest $request, Dealer $dealer): RedirectResponse
    {
        $this->service->update($dealer, $request->validated());
        return back()->with('success', 'Dealer updated.');
    }

    public function show(Dealer $dealer): View
    {
        return view('masters.dealers.show', compact('dealer'));
    }

    public function edit(Dealer $dealer): View
    {
        return view('masters.dealers.edit', compact('dealer'));
    }

    public function destroy(Dealer $dealer): RedirectResponse
    {
        $this->service->delete($dealer);
        return redirect()->route('masters.dealers.index')->with('success', 'Dealer deleted.');
    }

    public function purchaseHistory(Dealer $dealer): View
    {
        $purchases = $dealer->purchases()->latest()->paginate(15);
        return view('masters.dealers.purchase-history', compact('dealer', 'purchases'));
    }

    public function outstandingReport(Dealer $dealer): View
    {
        // Simple logic for now, showing purchase vs payment summary
        return view('masters.dealers.outstanding-report', compact('dealer'));
    }

    public function downloadLedgerPdf(Dealer $dealer)
    {
        $purchases = $dealer->purchases()->get()->map(fn($p) => [
            'date' => $p->date,
            'desc' => "Purchase #{$p->id} ({$p->item})",
            'debit' => $p->total_amount,
            'credit' => 0,
        ]);

        $payments = $dealer->payments()->get()->map(fn($p) => [
            'date' => $p->date,
            'desc' => "Payment Sent ({$p->payment_mode})",
            'debit' => 0,
            'credit' => $p->amount,
        ]);

        $ledger = $purchases->concat($payments)->sortBy('date');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('masters.dealers.ledger_pdf', [
            'dealer' => $dealer,
            'ledger' => $ledger
        ]);

        return $pdf->download("ledger-{$dealer->firm_name}.pdf");
    }
}
