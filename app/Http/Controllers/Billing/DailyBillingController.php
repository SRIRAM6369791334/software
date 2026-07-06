<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DailyBill;
use App\Models\Item;
use App\Services\ExportService;
use App\Services\DailyBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DailyBillingController extends Controller
{
    public function __construct(
        private ExportService $exporter,
        private DailyBillingService $billingService
    ) {}

    public function index(Request $request): View
    {
        $search    = $request->input('search');
        $bills     = DailyBill::with(['customer', 'items'])->search($search)->latest()->paginate(15);
        $customers = Customer::orderBy('name')->get();
        $items     = Item::active()->get();

        $dealerDayLoads = \App\Models\DayLoadEntry::with(['dealer', 'batch'])
            ->where('status', '!=', 'Cancelled')
            ->latest()
            ->paginate(15, ['*'], 'dealer_dayload_page');

        $dealerDayLoadTotalBoxes = \App\Models\DayLoadEntry::where('status', '!=', 'Cancelled')->sum('no_of_boxes');
        $dealerDayLoadTotalBird  = \App\Models\DayLoadEntry::where('status', '!=', 'Cancelled')->sum('bird_weight');
        $dealerDayLoadTotalLoss  = \App\Models\DayLoadEntry::where('status', '!=', 'Cancelled')->sum('loss_weight');

        return view('billing.daily.index', compact(
            'bills', 'customers', 'search', 'items',
            'dealerDayLoads', 'dealerDayLoadTotalBoxes', 'dealerDayLoadTotalBird', 'dealerDayLoadTotalLoss'
        ));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('billing.daily.index');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'date'           => 'required|date|before_or_equal:today',
            'status'         => 'required|in:Generated,Pending,Paid',
            'payment_mode'   => 'required|in:Cash,UPI,NEFT,Cheque(Bank Transfer),Pay later(EMI)',
            'gst_percentage' => 'required|numeric|min:0|max:28',
            'items'          => 'required|array|min:1',
            'items.*.name'   => 'required|string|max:255',
            'items.*.qty'    => 'required|numeric|min:0.01',
            'items.*.rate'   => 'required|numeric|min:0.01',
            'items.*.unit'   => 'nullable|string|max:20',
            'emis'           => 'required_if:payment_mode,Pay later(EMI)|array',
            'emis.*.due_date'=> 'required_if:payment_mode,Pay later(EMI)|date',
            'emis.*.amount'  => 'required_if:payment_mode,Pay later(EMI)|numeric|min:0.01',
        ]);

        try {
            $this->billingService->create($validated);
        } catch (\Exception $e) {
            return back()->with('error', 'Could not create bill: ' . $e->getMessage());
        }

        return back()->with('success', 'Daily bill created successfully.');
    }

    public function gst(): View
    {
        $bills = DailyBill::with(['customer', 'items'])->paginate(15);
        return view('billing.daily.gst', compact('bills'));
    }

    public function export(): StreamedResponse
    {
        $bills = DailyBill::with(['customer', 'items'])->latest()->get();
        $rows  = $bills->map(fn($b) => [
            $b->customer->name ?? '—', 
            $b->date->format('Y-m-d'),
            $b->items->pluck('item_name')->implode(', '), 
            $b->items->sum('quantity_kg'), 
            $b->amount, 
            $b->status,
        ]);
        return $this->exporter->streamCsv('daily-billing', ['Customer','Date','Items','Total Qty','Base Amount','Status'], $rows);
    }

    public function invoice(DailyBill $bill): View
    {
        $bill->load(['customer', 'items']);
        return view('billing.daily.invoice', compact('bill'));
    }

    public function downloadPdf(DailyBill $bill)
    {
        $bill->load(['customer', 'items']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('billing.daily.pdf', compact('bill'));
        return $pdf->download("invoice-{$bill->invoice_no}.pdf");
    }
}
