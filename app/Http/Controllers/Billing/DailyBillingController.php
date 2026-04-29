<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DailyBill;
use App\Services\ExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DailyBillingController extends Controller
{
    public function __construct(private ExportService $exporter) {}

    public function index(Request $request): View
    {
        $search    = $request->input('search');
        $bills     = DailyBill::with('customer')->search($search)->latest()->paginate(15);
        $customers = Customer::orderBy('name')->get();
        return view('billing.daily.index', compact('bills', 'customers', 'search'));
    }

    public function create(): View
    {
        $customers = Customer::orderBy('name')->get();
        return view('billing.daily.create', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id'       => 'required|exists:customers,id',
            'date'              => 'required|date|before_or_equal:today',
            'items_description' => 'nullable|string|max:255',
            'quantity_kg'       => 'nullable|numeric|min:0',
            'rate_per_kg'       => 'nullable|numeric|min:0',
            'amount'            => 'required|numeric|min:0.01',
            'status'            => 'required|in:Generated,Pending,Paid',
        ]);

        DailyBill::create($validated);
        return back()->with('success', 'Daily bill created.');
    }

    public function gst(): View
    {
        $bills = DailyBill::with('customer')->whereNotNull('items_description')->paginate(15);
        return view('billing.daily.gst', compact('bills'));
    }

    public function export(): StreamedResponse
    {
        $bills = DailyBill::with('customer')->latest()->get();
        $rows  = $bills->map(fn($b) => [
            $b->customer->name ?? '—', $b->date->format('Y-m-d'),
            $b->items_description, $b->quantity_kg, $b->rate_per_kg, $b->amount, $b->status,
        ]);
        return $this->exporter->streamCsv('daily-billing', ['Customer','Date','Items','Qty(kg)','Rate','Amount','Status'], $rows);
    }
}
