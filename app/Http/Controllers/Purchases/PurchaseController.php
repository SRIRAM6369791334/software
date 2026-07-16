<?php

namespace App\Http\Controllers\Purchases;

use App\Http\Controllers\Controller;
use App\Http\Requests\Purchases\StorePurchaseRequest;
use App\Services\ExportService;
use App\Services\PurchaseService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Models\Vendor;
use App\Models\Item;
use App\Models\Batch;
use App\Models\Warehouse;
use App\Models\Customer;
use App\Models\DailyBill;
use App\Models\WeeklyBill;
use App\Models\Purchase;
use App\Models\DayLoadBatch;
use App\Models\DayLoadEntry;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
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
        $search      = $request->input('search');
        $date        = $request->input('date', today()->format('Y-m-d'));

        $purchases   = $this->service->paginated($search, 15);
        $vendors     = Vendor::orderBy('firm_name')->get();
        $items       = Item::active()->get();
        $batches     = Batch::where('status', 'Active')->get();
        $warehouses  = Warehouse::active()->get();
        
        $customers   = Customer::orderBy('name')->get();
        $dailyBills  = DailyBill::with(['customer', 'items'])->latest()->take(10)->get();
        $weeklyBills = WeeklyBill::with('dealer')->latest()->take(10)->get();
        
        $autoInvoiceNo = null;

        $dailyPurchases = Purchase::with(['vendor', 'items'])
            ->whereDate('date', $date)
            ->latest()
            ->paginate(15, ['*'], 'daily_purchases_page');

        $dailyTotalAmount = $dailyPurchases->sum('total_amount');
        $dailyTotalGST    = $dailyPurchases->sum('gst_amount');
        $dailyItemCount   = $dailyPurchases->sum(fn($p) => $p->items->sum('quantity'));
        $dailyVendorCount = $dailyPurchases->pluck('vendor_id')->unique()->count();

        $vendorFilter = $request->input('vendor_filter');
        $dateFrom     = $request->input('date_from');
        $dateTo       = $request->input('date_to');

        $vendorDayLoadsQuery = \App\Models\DayLoadEntry::with(['vendor', 'batch'])
            ->where('status', '!=', 'Cancelled');

        if ($vendorFilter) {
            $vendorDayLoadsQuery->where('vendor_id', $vendorFilter);
        }
        if ($dateFrom) {
            $vendorDayLoadsQuery->whereHas('batch', fn($q) => $q->whereDate('billing_date', '>=', $dateFrom));
        }
        if ($dateTo) {
            $vendorDayLoadsQuery->whereHas('batch', fn($q) => $q->whereDate('billing_date', '<=', $dateTo));
        }

        $vendorDayLoads          = (clone $vendorDayLoadsQuery)->latest()->paginate(15, ['*'], 'vendor_dayload_page');
        $vendorDayLoadTotalBoxes = (clone $vendorDayLoadsQuery)->sum('no_of_boxes');
        $vendorDayLoadTotalBird  = (clone $vendorDayLoadsQuery)->sum('bird_weight');
        $vendorDayLoadTotalFarm  = (clone $vendorDayLoadsQuery)->sum('farm_weight');
        
        return view('purchases.index', compact(
            'purchases', 'search', 'vendors', 'items', 'batches', 'warehouses',
            'customers', 'dailyBills', 'weeklyBills', 'autoInvoiceNo', 'date',
            'dailyPurchases', 'dailyTotalAmount', 'dailyTotalGST', 'dailyItemCount', 'dailyVendorCount',
            'vendorDayLoads', 'vendorDayLoadTotalBoxes', 'vendorDayLoadTotalBird', 'vendorDayLoadTotalFarm',
            'vendorFilter', 'dateFrom', 'dateTo'
        ));
    }

    public function create(Request $request): View
    {
        $vendor_name = $request->input('vendor_name');
        $vendors     = Vendor::orderBy('firm_name')->get();
        $items       = Item::active()->get();
        $batches     = Batch::where('status', 'Active')->get();
        $warehouses  = Warehouse::active()->get();
        
        $autoInvoiceNo = null; // Generated securely in the service if left blank

        return view('purchases.create', compact('vendor_name', 'vendors', 'items', 'batches', 'warehouses', 'autoInvoiceNo'));
    }

    public function store(StorePurchaseRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());
        return back()->with('success', 'Purchase recorded successfully.');
    }

    public function invoices(Request $request): View
    {
        $date   = $request->input('date');
        $search = $request->input('search');

        // Overall stats (including Day-Load entries as purchases)
        $activeDayLoadEntriesCount = \App\Models\DayLoadEntry::where('status', '!=', 'Cancelled')->count();
        $activeDayLoadExpenditure  = \App\Models\DayLoadEntry::where('status', '!=', 'Cancelled')
            ->get()
            ->sum(fn($e) => $e->vendor_cost);

        $totalPurchases    = Purchase::count() + $activeDayLoadEntriesCount;
        $totalExpenditure  = Purchase::sum('total_amount') + $activeDayLoadExpenditure;
        $totalTaxPaid      = Purchase::sum('gst_amount');
        $totalDayLoads     = DayLoadBatch::count();
        $totalBirdsLoaded  = \App\Models\DayLoadEntry::where('status', '!=', 'Cancelled')->sum('no_of_boxes');

        if ($date) {
            // Day view: show both purchases and day-load entries for this date
            $purchases = Purchase::with(['vendor', 'items'])
                ->whereDate('date', $date)
                ->when($search, fn ($q) => $q->search($search))
                ->latest()
                ->get();

            $dayLoadBatch = DayLoadBatch::with(['entries.vendor', 'entries.dealer'])
                ->whereDate('billing_date', $date)
                ->first();

            $dayLoadEntries = \App\Models\DayLoadEntry::where('status', '!=', 'Cancelled')
                ->whereHas('batch', fn ($q) => $q->whereDate('billing_date', $date))
                ->get();

            $dayLoadVendorCost = $dayLoadEntries->sum(fn($e) => $e->vendor_cost);

            $dayStats = [
                'purchase_count' => $purchases->count() + $dayLoadEntries->count(),
                'purchase_total' => $purchases->sum('total_amount') + $dayLoadVendorCost,
                'purchase_gst'   => $purchases->sum('gst_amount'),
                'dayload_count'  => $dayLoadBatch ? $dayLoadBatch->entries()->count() : 0,
                'dayload_boxes'  => $dayLoadBatch?->total_boxes ?? 0,
                'dayload_birds'  => $dayLoadBatch?->total_bird_weight ?? 0,
            ];

            return view('purchases.invoices-day', compact(
                'purchases', 'dayLoadBatch', 'date', 'search', 'dayStats',
                'totalPurchases', 'totalExpenditure', 'totalTaxPaid', 'totalDayLoads', 'totalBirdsLoaded'
            ) + ['vendors' => Vendor::orderBy('firm_name')->get()]);
        }

        // Date list view: combine dates from both purchases and day-load batches
        $purchaseDates = Purchase::select(
                'date',
                DB::raw('COUNT(*) as purchase_count'),
                DB::raw('SUM(total_amount) as total_amount'),
                DB::raw('SUM(gst_amount) as gst_amount'),
                DB::raw('0 as dayload_count'),
                DB::raw('0 as total_boxes'),
                DB::raw('0 as total_bird_weight')
            )
            ->groupBy('date');

        $dayloadDates = DayLoadBatch::select(
                DB::raw('billing_date as date'),
                DB::raw('(SELECT COUNT(*) FROM day_load_entries WHERE day_load_entries.batch_id = day_load_batches.id AND day_load_entries.status != "Cancelled") as purchase_count'),
                DB::raw('(SELECT COALESCE(SUM(bird_weight * COALESCE(NULLIF(billing_rate, 0), paper_rate)), 0) FROM day_load_entries WHERE day_load_entries.batch_id = day_load_batches.id AND day_load_entries.status != "Cancelled") as total_amount'),
                DB::raw('0 as gst_amount'),
                DB::raw('COUNT(*) as dayload_count'),
                DB::raw('SUM(total_boxes) as total_boxes'),
                DB::raw('SUM(total_bird_weight) as total_bird_weight')
            )
            ->groupBy('billing_date', 'id');

        $dateGroups = DB::table($purchaseDates->unionAll($dayloadDates))
            ->selectRaw('
                date,
                SUM(purchase_count) as purchase_count,
                SUM(total_amount) as total_amount,
                SUM(gst_amount) as gst_amount,
                SUM(dayload_count) as dayload_count,
                SUM(total_boxes) as total_boxes,
                SUM(total_bird_weight) as total_bird_weight
            ')
            ->groupBy('date')
            ->orderBy('date', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('purchases.invoices', compact(
            'dateGroups', 'search', 'totalPurchases', 'totalExpenditure', 'totalTaxPaid', 'totalDayLoads', 'totalBirdsLoaded'
        ));
    }

    public function show($id): View
    {
        $purchase = $this->service->find($id);
        return view('purchases.show', compact('purchase'));
    }

    public function edit($id): View
    {
        $purchase   = $this->service->find($id);
        $vendors    = Vendor::orderBy('firm_name')->get();
        $items      = Item::active()->get();
        $batches    = Batch::where('status', 'Active')->get();
        $warehouses = Warehouse::active()->get();

        $autoInvoiceNo = $purchase->invoice_no ?: 'INV-' . date('Y') . '-' . str_pad($purchase->id, 4, '0', STR_PAD_LEFT);

        return view('purchases.edit', compact('purchase', 'vendors', 'items', 'batches', 'warehouses', 'autoInvoiceNo'));
    }

    public function update(StorePurchaseRequest $request, $id): RedirectResponse
    {
        $purchase = $this->service->find($id);
        $this->service->update($purchase, $request->validated());
        $date = $purchase->date->format('Y-m-d');
        return redirect()->route('purchases.invoices', ['date' => $date])->with('success', 'Purchase updated.');
    }

    public function destroy($id): RedirectResponse
    {
        $purchase = $this->service->find($id);
        $date = $purchase->date->format('Y-m-d');
        $this->service->delete($purchase);
        return redirect()->route('purchases.invoices', ['date' => $date])->with('success', 'Purchase deleted.');
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

    public function invoicesExport(Request $request): StreamedResponse
    {
        $date = $request->input('date', today()->format('Y-m-d'));

        $purchases = Purchase::with(['vendor', 'items'])
            ->whereDate('date', $date)
            ->get();

        $dayLoadEntries = DayLoadEntry::with(['vendor', 'dealer'])
            ->whereHas('batch', fn ($q) => $q->whereDate('billing_date', $date))
            ->where('status', '!=', 'Cancelled')
            ->get();

        $rows = collect();

        foreach ($purchases as $p) {
            foreach ($p->items as $item) {
                $rows->push([
                    $date, 'Purchase', $p->vendor_name, $item->item_name,
                    $item->quantity . ' ' . ($item->unit ?? 'pcs'),
                    $item->rate, $p->total_amount, $p->payment_mode,
                ]);
            }
        }

        foreach ($dayLoadEntries as $e) {
            $rows->push([
                $date, 'Day-Load', $e->vendor->firm_name ?? '-', $e->dealer->firm_name ?? '-',
                $e->no_of_boxes . ' boxes', number_format((float) $e->bird_weight, 2) . ' kg',
                number_format((float) ($e->total_weight ?? 0), 2) . ' kg', $e->status,
            ]);
        }

        return $this->exporter->streamCsv('invoices-' . $date, [
            'Date', 'Type', 'Vendor', 'Detail', 'Qty', 'Rate/Weight', 'Total', 'Mode',
        ], $rows);
    }

    public function invoicesPrint(string $date): View
    {
        $dateObj = Carbon::parse($date);

        $purchases = Purchase::with(['vendor', 'items'])
            ->whereDate('date', $date)->get();

        $dayLoadBatch = DayLoadBatch::with(['entries.vendor', 'entries.dealer'])
            ->whereDate('billing_date', $date)->first();

        $dayLoadEntries = $dayLoadBatch ? $dayLoadBatch->entries->where('status', '!=', 'Cancelled') : collect();

        $dayLoadVendorCost = $dayLoadEntries->sum(fn($e) => $e->vendor_cost);

        $purchaseCount = $purchases->count() + $dayLoadEntries->count();
        $purchaseTotal = $purchases->sum('total_amount') + $dayLoadVendorCost;

        return view('purchases.invoices-print', compact(
            'dateObj', 'purchases', 'dayLoadBatch', 'dayLoadEntries', 'purchaseCount', 'purchaseTotal'
        ));
    }

    public function invoicesPdf(string $date)
    {
        $dateObj = Carbon::parse($date);

        $purchases = Purchase::with(['vendor', 'items'])
            ->whereDate('date', $date)->get();

        $dayLoadBatch = DayLoadBatch::with(['entries.vendor', 'entries.dealer'])
            ->whereDate('billing_date', $date)->first();

        $dayLoadEntries = $dayLoadBatch ? $dayLoadBatch->entries->where('status', '!=', 'Cancelled') : collect();

        $dayLoadVendorCost = $dayLoadEntries->sum(fn($e) => $e->vendor_cost);

        $purchaseCount = $purchases->count() + $dayLoadEntries->count();
        $purchaseTotal = $purchases->sum('total_amount') + $dayLoadVendorCost;

        $pdf = Pdf::loadView('purchases.invoices-pdf', compact(
            'dateObj', 'purchases', 'dayLoadBatch', 'dayLoadEntries', 'purchaseCount', 'purchaseTotal'
        ));
        return $pdf->download("invoices-{$date}.pdf");
    }
}
