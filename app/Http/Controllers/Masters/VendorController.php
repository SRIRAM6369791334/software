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
        $vendors = $this->service->search($search, 15);
        return view('masters.vendors.index', compact('vendors', 'search'));
    }

    public function create(): View
    {
        return view('masters.vendors.create');
    }

    public function store(StoreVendorRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());
        return back()->with('success', 'Vendor added successfully.');
    }

    public function update(StoreVendorRequest $request, Vendor $vendor): RedirectResponse
    {
        $this->service->update($vendor, $request->validated());
        return back()->with('success', 'Vendor updated.');
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
        $purchases = $vendor->purchases()->latest()->paginate(15);
        return view('masters.vendors.purchase-history', compact('vendor', 'purchases'));
    }
}
