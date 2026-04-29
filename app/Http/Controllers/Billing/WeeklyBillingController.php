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

    public function store(Request $request, \App\Services\InvoiceNumberService $invoiceService): RedirectResponse
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

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $invoiceService) {
                $validated['invoice_no'] = $invoiceService->generateUnique('INV-W', 'weekly_bills');

                // Apply GST calculation
                $gstData = \App\Helpers\GSTCalculator::calculate($validated['amount'], 18);
                $validated['gst_percentage'] = 18;
                $validated['gst_amount'] = $gstData['total_gst'];
                $validated['net_amount'] = $gstData['net_amount'];

                $bill = WeeklyBill::create($validated);

                // Auto-trigger stock movement
                app(\App\Services\StockService::class)->recordOut([
                    'item_name'      => $bill->items_description ?? 'Poultry',
                    'quantity'       => $bill->quantity_kg ?? 0,
                    'rate'           => $bill->amount / max(1, $bill->quantity_kg ?? 1),
                    'reference_type' => WeeklyBill::class,
                    'reference_id'   => $bill->id,
                    'date'           => $bill->period_end,
                    'created_by'     => auth()->id(),
                ]);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Could not create bill due to a concurrent conflict. Please try again.');
        }

        return back()->with('success', 'Weekly bill created.');
    }

    public function bulkStore(Request $request, \App\Services\InvoiceNumberService $invoiceService): RedirectResponse
    {
        $request->validate([
            'customer_ids'   => 'required|array',
            'customer_ids.*' => 'exists:customers,id',
            'period_start'   => 'required|date',
            'period_end'     => 'required|date|after_or_equal:period_start',
            'amount'         => 'required|numeric|min:0.01',
            'status'         => 'required|in:Generated,Pending',
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($request, $invoiceService) {
                foreach ($request->customer_ids as $cid) {
                    $gstData = \App\Helpers\GSTCalculator::calculate($request->amount, 18);

                    WeeklyBill::create([
                        'invoice_no'   => $invoiceService->generateUnique('INV-W', 'weekly_bills'),
                        'customer_id'  => $cid,
                        'period_start' => $request->period_start,
                        'period_end'   => $request->period_end,
                        'amount'       => $request->amount,
                        'gst_percentage' => 18,
                        'gst_amount'   => $gstData['total_gst'],
                        'net_amount'   => $gstData['net_amount'],
                        'status'       => $request->status,
                        'items_description' => 'Bulk generated',
                    ]);
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Could not create bills due to a concurrent conflict. Please try again.');
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
