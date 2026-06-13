<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\WeeklyBill;
use App\Models\Item;
use App\Services\ExportService;
use App\Services\WeeklyBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WeeklyBillingController extends Controller
{
    public function __construct(
        private ExportService $exporter,
        private WeeklyBillingService $billingService
    ) {}

    public function index(Request $request): View
    {
        $search    = $request->input('search');
        $bills     = WeeklyBill::with(['customer', 'items'])->search($search)->latest()->paginate(15);
        $customers = Customer::orderBy('name')->get();
        $items     = Item::active()->get();
        return view('billing.weekly.index', compact('bills', 'customers', 'search', 'items'));
    }

    public function bulk(): View
    {
        $customers = Customer::orderBy('name')->get();
        return view('billing.weekly.bulk', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id'  => 'required|exists:customers,id',
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after_or_equal:period_start',
            'status'       => 'required|in:Generated,Pending,Paid',
            'payment_mode' => 'required|in:Cash,Credit,UPI,NEFT,Cheque',
            'items'        => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.qty'  => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0.01',
        ]);

        try {
            $this->billingService->create($validated);
        } catch (\Exception $e) {
            return back()->with('error', 'Could not create bill: ' . $e->getMessage());
        }

        return back()->with('success', 'Weekly bill created successfully.');
    }

    public function bulkStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_ids'   => 'required|array',
            'customer_ids.*' => 'exists:customers,id',
            'period_start'   => 'required|date',
            'period_end'     => 'required|date|after_or_equal:period_start',
            'amount'         => 'required|numeric|min:0.01',
            'status'         => 'required|in:Generated,Pending,Paid',
            'payment_mode'   => 'required|in:Cash,Credit,UPI,NEFT,Cheque',
        ]);

        try {
            $count = $this->billingService->bulkCreate($validated['customer_ids'], $validated);
        } catch (\Exception $e) {
            return back()->with('error', 'Could not create bills: ' . $e->getMessage());
        }

        return back()->with('success', $count . ' bills generated.');
    }

    public function show(WeeklyBill $bill): View
    {
        $bill->load(['customer', 'items']);
        return view('billing.invoice', compact('bill'));
    }

    public function print(WeeklyBill $bill): View
    {
        $bill->load(['customer', 'items']);
        return view('billing.invoice', compact('bill'));
    }

    public function whatsapp(WeeklyBill $bill): RedirectResponse
    {
        $phone = preg_replace('/[^0-9]/', '', $bill->customer->phone ?? '');
        if (!$phone) return back()->with('error', 'Customer phone missing.');

        $text = urlencode("Hello {$bill->customer->name}, your poultry bill for period {$bill->period_start->format('d M')} to {$bill->period_end->format('d M')} is ₹" . number_format($bill->amount, 2) . ". Thank you!");

        return redirect()->away("https://wa.me/91{$phone}?text={$text}");
    }

    public function export(): StreamedResponse
    {
        $bills = WeeklyBill::with(['customer', 'items'])->latest()->get();
        $rows  = $bills->map(fn($b) => [
            $b->customer->name ?? '—',
            $b->period_start->format('Y-m-d'),
            $b->period_end->format('Y-m-d'),
            $b->items_description,
            $b->quantity_kg,
            $b->amount,
            $b->status,
        ]);
        return $this->exporter->streamCsv('weekly-billing', ['Customer','From','To','Items','Qty(kg)','Amount','Status'], $rows);
    }

    public function downloadPdf(WeeklyBill $bill)
    {
        $bill->load(['customer', 'items']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('billing.weekly.pdf', compact('bill'));
        return $pdf->download("invoice-{$bill->invoice_no}.pdf");
    }
}


