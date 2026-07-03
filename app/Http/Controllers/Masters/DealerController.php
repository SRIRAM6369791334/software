<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Http\Requests\Masters\StoreDealerRequest;
use App\Models\Dealer;
use App\Models\Route;
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
        $balanceFilter = $request->input('balance');
        
        $query = Dealer::with('routeRelation')->search($search);
        
        if ($balanceFilter === 'pending') {
            $query->where('pending_amount', '>', 0);
        } elseif ($balanceFilter === 'cleared') {
            $query->where('pending_amount', '<=', 0);
        }

        $dealers = $query->orderBy('firm_name')->paginate(15);

        $statsQuery = Dealer::query();
        if ($balanceFilter === 'pending') {
            $statsQuery->where('pending_amount', '>', 0);
        } elseif ($balanceFilter === 'cleared') {
            $statsQuery->where('pending_amount', '<=', 0);
        }
        $totalPending = (clone $statsQuery)->sum('pending_amount');
        $activeDealers = (clone $statsQuery)->where('pending_amount', '>', 0)->count();

        return view('masters.dealers.index', compact('dealers', 'search', 'balanceFilter', 'totalPending', 'activeDealers'));
    }

    public function create(): View
    {
        $routes = Route::orderBy('route_name')->get();
        return view('masters.dealers.create', compact('routes'));
    }

    public function store(StoreDealerRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());
        return redirect()->route('masters.dealers.index')->with('success', 'Dealer added successfully.');
    }

    public function update(StoreDealerRequest $request, Dealer $dealer): RedirectResponse
    {
        $this->service->update($dealer, $request->validated());
        return redirect()->route('masters.dealers.index')->with('success', 'Dealer updated.');
    }

    public function show(Dealer $dealer): View
    {
        $recentPurchases = $dealer->purchases()->with('items')->latest()->take(3)->get();
        $recentPayments = $dealer->payments()->latest()->take(3)->get();

        $totalBoxesReceived = $dealer->dayLoadEntries()->sum('no_of_boxes');
        $totalBirdWeight = $dealer->dayLoadEntries()->sum('bird_weight');
        $totalFarmWeight = $dealer->dayLoadEntries()->sum('farm_weight');
        $totalLossWeight = $dealer->dayLoadEntries()->sum('loss_weight');
        $lossRate = $totalBirdWeight > 0 ? round(($totalLossWeight / $totalBirdWeight) * 100, 2) : 0;
        $loadCount = $dealer->dayLoadEntries()->count();

        return view('masters.dealers.show', compact(
            'dealer', 'recentPurchases', 'recentPayments',
            'totalBoxesReceived', 'totalBirdWeight', 'totalFarmWeight',
            'totalLossWeight', 'lossRate', 'loadCount'
        ));
    }

    public function edit(Dealer $dealer): View
    {
        $routes = Route::orderBy('route_name')->get();
        return view('masters.dealers.edit', compact('dealer', 'routes'));
    }

    public function destroy(Dealer $dealer): RedirectResponse
    {
        $this->service->delete($dealer);
        return redirect()->route('masters.dealers.index')->with('success', 'Dealer deleted.');
    }

    public function purchaseHistory(Dealer $dealer): View
    {
        $purchases = $dealer->purchases()->with('items')->latest()->paginate(15);

        $dayLoadEntries = $dealer->dayLoadEntries()
            ->with(['vendor', 'batch'])
            ->latest()
            ->paginate(15);

        $totalBoxes = $dealer->dayLoadEntries()->sum('no_of_boxes');
        $totalBirdWeight = $dealer->dayLoadEntries()->sum('bird_weight');
        $totalLossWeight = $dealer->dayLoadEntries()->sum('loss_weight');

        return view('masters.dealers.purchase-history', compact(
            'dealer', 'purchases', 'dayLoadEntries',
            'totalBoxes', 'totalBirdWeight', 'totalLossWeight'
        ));
    }

    public function outstandingReport(Dealer $dealer): View
    {
        $today = now()->toDateString();

        $purchases = $dealer->purchases()->where('total_amount', '>', 0)->get();
        $payments = $dealer->payments()->get();

        $totalPurchased = $purchases->sum('total_amount');
        $totalPaid = $payments->sum('amount');
        $outstanding = $dealer->pending_amount;

        $buckets = ['0_30' => 0, '31_60' => 0, '60_plus' => 0];
        foreach ($purchases as $purchase) {
            $days = \Carbon\Carbon::parse($purchase->date)->diffInDays(now());
            $amount = (float) $purchase->total_amount;
            if ($days <= 30) {
                $buckets['0_30'] += $amount;
            } elseif ($days <= 60) {
                $buckets['31_60'] += $amount;
            } else {
                $buckets['60_plus'] += $amount;
            }
        }

        $avgPaymentDays = null;
        if ($payments->isNotEmpty()) {
            $totalDays = 0;
            $matchedPayments = 0;
            foreach ($payments as $payment) {
                $relatedPurchase = $purchases->where('date', '<=', $payment->date)->last();
                if ($relatedPurchase) {
                    $totalDays += \Carbon\Carbon::parse($relatedPurchase->date)->diffInDays($payment->date);
                    $matchedPayments++;
                }
            }
            $avgPaymentDays = $matchedPayments > 0 ? round($totalDays / $matchedPayments) : null;
        }

        return view('masters.dealers.outstanding-report', compact(
            'dealer', 'totalPurchased', 'totalPaid', 'outstanding',
            'buckets', 'avgPaymentDays', 'payments'
        ));
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
