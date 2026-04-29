<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\WeeklyBill;
use App\Services\ExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WeeklyBillingController extends Controller
{
    public function __construct(private ExportService $exporter) {}

    public function index(Request $request): View
    {
        $search = $request->input('search');
        $bills  = WeeklyBill::with('customer')
            ->search($search)
            ->latest()
            ->paginate(15);
        $customers = Customer::orderBy('name')->get();
        return view('billing.weekly.index', compact('bills', 'customers', 'search'));
    }

    public function bulk(): View
    {
        $customers = Customer::orderBy('name')->get();
        return view('billing.weekly.bulk', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id'      => 'required|exists:customers,id',
            'period_start'     => 'required|date',
            'period_end'       => 'required|date|after_or_equal:period_start',
            'items_description'=> 'nullable|string|max:255',
            'quantity_kg'      => 'nullable|numeric|min:0',
            'amount'           => 'required|numeric|min:0.01',
            'status'           => 'required|in:Generated,Pending,Paid',
        ]);

        WeeklyBill::create($validated);
        return back()->with('success', 'Weekly bill created.');
    }

    public function bulkStore(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_ids'   => 'required|array',
            'customer_ids.*' => 'exists:customers,id',
            'period_start'   => 'required|date',
            'period_end'     => 'required|date|after_or_equal:period_start',
            'amount'         => 'required|numeric|min:0.01',
            'status'         => 'required|in:Generated,Pending',
        ]);

        foreach ($request->customer_ids as $cid) {
            WeeklyBill::create([
                'customer_id'  => $cid,
                'period_start' => $request->period_start,
                'period_end'   => $request->period_end,
                'amount'       => $request->amount,
                'status'       => $request->status,
                'items_description' => 'Bulk generated',
            ]);
        }

        return back()->with('success', count($request->customer_ids) . ' bills generated.');
    }

    public function show(WeeklyBill $bill): View
    {
        return view('billing.invoice', compact('bill'));
    }

    public function print(WeeklyBill $bill): View
    {
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
        $bills = WeeklyBill::with('customer')->latest()->get();
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
}
