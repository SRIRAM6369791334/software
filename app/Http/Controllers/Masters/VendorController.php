<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Http\Requests\Masters\StoreVendorRequest;
use App\Models\Vendor;
use App\Services\VendorService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorController extends Controller
{
    public function __construct(private VendorService $service) {}

    public function index(Request $request): View
    {
        $search  = $request->input('search');
        $routeFilter = $request->input('route');
        
        $vendorsQuery = Vendor::with(['purchases', 'dayLoadEntries', 'vendorPayments'])->search($search);
        if ($routeFilter) {
            $vendorsQuery->where('route', $routeFilter);
        }
        
        $vendorsCollection = $vendorsQuery->orderBy('firm_name')->get();

        // Calculate stats on all matching vendors
        $totalPayable = $vendorsCollection->sum(fn($v) => $v->outstanding_balance);
        $activeVendorsCount = $vendorsCollection->filter(fn($v) => $v->outstanding_balance > 0)->count();

        // Paginate the collection manually
        $page = (int) $request->input('page', 1);
        $perPage = 15;
        $sliced = $vendorsCollection->slice(($page - 1) * $perPage, $perPage)->values();

        $vendors = new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            $vendorsCollection->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $routes = Vendor::select('route')->distinct()->whereNotNull('route')->where('route', '!=', '')->pluck('route');
        
        $totalVendors  = Vendor::count();
        $activeRoutes  = Vendor::distinct('route')->count('route');
        $gstRegistered = Vendor::whereNotNull('gst_number')->count();
        $unregistered  = Vendor::whereNull('gst_number')->count();
        
        return view('masters.vendors.index', compact(
            'vendors', 'search', 'routeFilter', 'routes',
            'totalVendors', 'activeRoutes', 'gstRegistered', 'unregistered',
            'totalPayable', 'activeVendorsCount'
        ));
    }

    public function create(): View
    {
        return view('masters.vendors.create');
    }

    public function store(StoreVendorRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());
        return redirect()->route('masters.vendors.index')->with('success', 'Vendor added successfully.');
    }

    public function update(StoreVendorRequest $request, Vendor $vendor): RedirectResponse
    {
        $this->service->update($vendor, $request->validated());
        return redirect()->route('masters.vendors.index')->with('success', 'Vendor updated.');
    }

    public function show(Vendor $vendor): View
    {
        $totalPurchaseAmount = $vendor->purchases()->sum('total_amount');
        $totalPurchaseCount = $vendor->purchases()->count();
        $lastPurchaseDate = $vendor->purchases()->latest('date')->first()?->date;
        $recentPurchases = $vendor->purchases()->with('items')->latest()->take(5)->get();

        $totalBoxesLoaded = $vendor->dayLoadEntries()->sum('no_of_boxes');
        $totalBirdWeight = $vendor->dayLoadEntries()->sum('bird_weight');
        $totalFarmWeight = $vendor->dayLoadEntries()->sum('farm_weight');
        $totalLossWeight = $vendor->dayLoadEntries()->sum('loss_weight');
        $rateVarianceEntries = $vendor->dayLoadEntries()
            ->where('customer_rate', '>', 0)
            ->get(['customer_rate', 'billing_rate', 'paper_rate']);
        $totalDiff = 0;
        $rateVarCount = 0;
        foreach ($rateVarianceEntries as $rve) {
            $vRate = (float) $rve->billing_rate > 0 ? (float) $rve->billing_rate : (float) $rve->paper_rate;
            if ($vRate > 0) {
                $totalDiff += (float) $rve->customer_rate - $vRate;
                $rateVarCount++;
            }
        }
        $avgRateVariance = $rateVarCount > 0 ? round($totalDiff / $rateVarCount, 2) : 0;
        $loadCount = $vendor->dayLoadEntries()->count();

        // Calculate outstanding balance details
        $totalCreditPurchases = (float) $vendor->purchases()->where('payment_mode', 'Credit')->sum('total_amount');
        $dayLoadEntriesForLiab = $vendor->dayLoadEntries()->where('status', '!=', 'Cancelled')->get();
        $totalDayLoadLiabilities = (float) $dayLoadEntriesForLiab->sum(function ($entry) {
            return $entry->vendor_cost;
        });
        $totalPaymentsPaid = (float) $vendor->vendorPayments()->sum('amount');
        $outstandingBalance = round(($totalCreditPurchases + $totalDayLoadLiabilities) - $totalPaymentsPaid, 2);

        return view('masters.vendors.show', compact(
            'vendor',
            'totalPurchaseAmount', 'totalPurchaseCount', 'lastPurchaseDate', 'recentPurchases',
            'totalBoxesLoaded', 'totalBirdWeight', 'totalFarmWeight', 'totalLossWeight',
            'avgRateVariance', 'loadCount',
            'totalCreditPurchases', 'totalDayLoadLiabilities', 'totalPaymentsPaid', 'outstandingBalance'
        ));
    }

    public function edit(Vendor $vendor): View
    {
        return view('masters.vendors.edit', compact('vendor'));
    }

    public function destroy(Vendor $vendor): RedirectResponse
    {
        $this->service->delete($vendor);
        return redirect()->route('masters.vendors.index')->with('success', 'Vendor deleted.');
    }

    public function purchaseHistory(Vendor $vendor): View
    {
        $purchases = $vendor->purchases()->with('items')->latest()->paginate(15);

        $dayLoadEntries = $vendor->dayLoadEntries()
            ->with(['dealer', 'batch'])
            ->latest()
            ->paginate(15);

        $totalBoxes = $vendor->dayLoadEntries()->sum('no_of_boxes');
        $totalBirdWeight = $vendor->dayLoadEntries()->sum('bird_weight');
        $totalFarmWeight = $vendor->dayLoadEntries()->sum('farm_weight');
        $totalLossWeight = $vendor->dayLoadEntries()->sum('loss_weight');

        return view('masters.vendors.purchase-history', compact(
            'vendor', 'purchases', 'dayLoadEntries',
            'totalBoxes', 'totalBirdWeight', 'totalFarmWeight', 'totalLossWeight'
        ));
    }

    public function downloadHistoryPdf(Vendor $vendor)
    {
        $purchases = $vendor->purchases()->with('items')->latest()->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('masters.vendors.history_pdf', compact('vendor', 'purchases'));
        return $pdf->download("vendor-history-{$vendor->firm_name}.pdf");
    }
}
