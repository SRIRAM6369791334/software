<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\WeeklyBill;
use App\Models\Item;
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
        $bills  = WeeklyBill::with(['customer', 'items'])
            ->search($search)
            ->latest()
            ->paginate(15);
        $customers = Customer::orderBy('name')->get();
        $items     = Item::active()->get();
        return view('billing.weekly.index', compact('bills', 'customers', 'search', 'items'));
    }

    public function bulk(): View
    {
        $customers = Customer::orderBy('name')->get();
        return view('billing.weekly.bulk', compact('customers'));
    }

    public function store(Request $request, \App\Services\InvoiceNumberService $invoiceService): RedirectResponse
    {
        $request->validate([
            'customer_id'  => 'required|exists:customers,id',
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after_or_equal:period_start',
            'status'       => 'required|in:Generated,Pending,Paid',
            'items'        => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.qty'  => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0.01',
        ]);

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($request, $invoiceService) {
                $itemsData = $request->input('items');
                
                $subtotal = 0;
                foreach ($itemsData as $item) {
                    $subtotal += $item['qty'] * $item['rate'];
                }

                $gstData = \App\Helpers\GSTCalculator::calculate($subtotal, 18);
                
                $bill = WeeklyBill::create([
                    'customer_id'    => $request->input('customer_id'),
                    'period_start'   => $request->input('period_start'),
                    'period_end'     => $request->input('period_end'),
                    'invoice_no'     => $invoiceService->generateUnique('INV-W', 'weekly_bills'),
                    'amount'         => $subtotal,
                    'gst_percentage' => 18,
                    'gst_amount'     => $gstData['total_gst'],
                    'net_amount'     => $gstData['net_amount'],
                    'status'         => $request->input('status'),
                    'payment_mode'   => 'Cash', // Default
                ]);

                foreach ($itemsData as $item) {
                    $base = $item['qty'] * $item['rate'];
                    $tax = round($base * 18 / 100, 2);

                    $billItem = $bill->items()->create([
                        'item_name'    => $item['name'],
                        'quantity_kg'  => $item['qty'],
                        'rate_per_kg'  => $item['rate'],
                        'tax_amount'   => $tax,
                        'total_amount' => $base + $tax,
                    ]);

                    // Auto-trigger stock movement for each item
                    app(\App\Services\StockService::class)->recordOut([
                        'item_name'      => $billItem->item_name,
                        'quantity'       => $billItem->quantity_kg,
                        'rate'           => $billItem->rate_per_kg,
                        'reference_type' => WeeklyBill::class,
                        'reference_id'   => $bill->id,
                        'date'           => $bill->period_end,
                        'created_by'     => auth()->id() ?? 1,
                    ]);
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Could not create bill: ' . $e->getMessage());
        }

        return back()->with('success', 'Weekly bill created successfully.');
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

                    $bill = WeeklyBill::create([
                        'invoice_no'   => $invoiceService->generateUnique('INV-W', 'weekly_bills'),
                        'customer_id'  => $cid,
                        'period_start' => $request->period_start,
                        'period_end'   => $request->period_end,
                        'amount'       => $request->amount,
                        'gst_percentage' => 18,
                        'gst_amount'   => $gstData['total_gst'],
                        'net_amount'   => $gstData['net_amount'],
                        'status'       => $request->status,
                    ]);

                    $bill->items()->create([
                        'item_name'    => 'Weekly Poultry Settlement',
                        'quantity_kg'  => 1,
                        'rate_per_kg'  => $request->amount,
                        'tax_amount'   => $gstData['total_gst'],
                        'total_amount' => $gstData['net_amount'],
                    ]);
                }
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Could not create bills: ' . $e->getMessage());
        }

        return back()->with('success', count($request->customer_ids) . ' bills generated.');
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
