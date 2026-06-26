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
        
        $query = Vendor::search($search);
        
        if ($routeFilter) {
            $query->where('route', $routeFilter);
        }
        
        $vendors = $query->orderBy('firm_name')->paginate(15);
        $routes = \App\Models\Vendor::select('route')->distinct()->whereNotNull('route')->where('route', '!=', '')->pluck('route');
        
        $totalVendors  = \App\Models\Vendor::count();
        $activeRoutes  = \App\Models\Vendor::distinct('route')->count('route');
        $gstRegistered = \App\Models\Vendor::whereNotNull('gst_number')->count();
        $unregistered  = \App\Models\Vendor::whereNull('gst_number')->count();
        
        return view('masters.vendors.index', compact(
            'vendors', 'search', 'routeFilter', 'routes',
            'totalVendors', 'activeRoutes', 'gstRegistered', 'unregistered'
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
        return view('masters.vendors.show', compact('vendor'));
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
        return view('masters.vendors.purchase-history', compact('vendor', 'purchases'));
    }

    public function downloadHistoryPdf(Vendor $vendor)
    {
        $purchases = $vendor->purchases()->with('items')->latest()->get();
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('masters.vendors.history_pdf', compact('vendor', 'purchases'));
        return $pdf->download("vendor-history-{$vendor->firm_name}.pdf");
    }
}
