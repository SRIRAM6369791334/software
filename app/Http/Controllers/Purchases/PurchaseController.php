<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchases\StorePurchaseRequest;
use App\Services\ExportService;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Vendor;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PurchaseController extends Controller
{
    public function __construct(
        private PurchaseService $service,
        private ExportService   $exporter,
    ) {}

    public function index(Request $request): View
    {
        $search    = $request->input('search');
        $purchases = $this->service->paginated($search, 15);
        $vendors   = Vendor::orderBy('firm_name')->get();
        return view('purchases.index', compact('purchases', 'search', 'vendors'));
    }

    public function create(Request $request): View
    {
        $vendor_name = $request->input('vendor_name');
        $vendors = Vendor::orderBy('firm_name')->get();
        return view('purchases.create', compact('vendor_name', 'vendors'));
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());
        return back()->with('success', 'Purchase recorded successfully.');
    }

    public function invoices(Request $request): View
    {
        $search    = $request->input('search');
        $purchases = $this->service->paginated($search, 15);
        return view('purchases.invoices', compact('purchases', 'search'));
    }

    public function show($id): View
    {
        $purchase = $this->service->find($id);
        return view('purchases.show', compact('purchase'));
    }

    public function edit($id): View
    {
        $purchase = $this->service->find($id);
        $vendors = Vendor::orderBy('firm_name')->get();
        return view('purchases.edit', compact('purchase', 'vendors'));
    }

    public function update(StorePurchaseRequest $request, $id): RedirectResponse
    {
        $purchase = $this->service->find($id);
        $this->service->update($purchase, $request->validated());
        return redirect()->route('purchases.invoices')->with('success', 'Purchase updated.');
    }

    public function destroy($id): RedirectResponse
    {
        $purchase = $this->service->find($id);
        $this->service->delete($purchase);
        return back()->with('success', 'Purchase deleted.');
    }

    public function print($id): View
    {
        $purchase = $this->service->find($id);
        return view('purchases.print', compact('purchase'));
    }

    public function export(): StreamedResponse
    {
        $rows = $this->service->allForExport()->map(fn($p) => [
            $p->date->format('Y-m-d'), $p->vendor_name, $p->item,
            $p->quantity . ' ' . $p->unit, $p->rate, $p->gst_amount, $p->total_amount, $p->payment_mode,
        ]);
        return $this->exporter->streamCsv('purchase-entries', ['Date','Vendor','Item','Qty','Rate','GST','Total','Mode'], $rows);
    }
}
