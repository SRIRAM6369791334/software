<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DailyBill;
use App\Services\ExportService;
use App\Services\InvoiceNumberService;
use App\Services\StockService;
use App\Helpers\GSTCalculator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DailyBillingController extends Controller
{
    public function __construct(private ExportService $exporter) {}

    public function index(Request $request): View
    {
        $search    = $request->input('search');
        $bills     = DailyBill::with(['customer', 'items'])->search($search)->latest()->paginate(15);
        $customers = Customer::orderBy('name')->get();
        return view('billing.daily.index', compact('bills', 'customers', 'search'));
    }

    public function store(Request $request, InvoiceNumberService $invoiceService): RedirectResponse
    {
        $request->validate([
            'customer_id'    => 'required|exists:customers,id',
            'date'           => 'required|date|before_or_equal:today',
            'status'         => 'required|in:Generated,Pending,Paid',
            'gst_percentage' => 'required|numeric|min:0|max:28',
            'items'          => 'required|array|min:1',
            'items.*.name'   => 'required|string|max:255',
            'items.*.qty'    => 'required|numeric|min:0.01',
            'items.*.rate'   => 'required|numeric|min:0.01',
            'items.*.unit'   => 'nullable|string|max:20',
        ]);

        try {
            DB::transaction(function () use ($request, $invoiceService) {
                $itemsData = $request->input('items');
                $gstPercent = $request->input('gst_percentage');
                
                $subtotal = 0;
                foreach ($itemsData as $item) {
                    $subtotal += $item['qty'] * $item['rate'];
                }

                $gstData = GSTCalculator::calculate($subtotal, $gstPercent);
                
                $bill = DailyBill::create([
                    'customer_id'    => $request->input('customer_id'),
                    'date'           => $request->input('date'),
                    'invoice_no'     => $invoiceService->generateUnique('INV-D', 'daily_bills'),
                    'amount'         => $subtotal,
                    'gst_percentage' => $gstPercent,
                    'gst_amount'     => $gstData['total_gst'],
                    'net_amount'     => $gstData['net_amount'],
                    'status'         => $request->input('status'),
                    'payment_mode'   => 'Cash', // Default
                ]);

                foreach ($itemsData as $item) {
                    $base = $item['qty'] * $item['rate'];
                    $tax = round($base * $gstPercent / 100, 2);
                    
                    $billItem = $bill->items()->create([
                        'item_name'    => $item['name'],
                        'quantity_kg'  => $item['qty'],
                        'rate_per_kg'  => $item['rate'],
                        'tax_amount'   => $tax,
                        'total_amount' => $base + $tax,
                        'unit'         => $item['unit'] ?? 'kg',
                    ]);

                    // Auto-trigger stock movement
                    app(StockService::class)->recordOut([
                        'item_name'      => $billItem->item_name,
                        'quantity'       => $billItem->quantity_kg,
                        'rate'           => $billItem->rate_per_kg,
                        'reference_type' => DailyBill::class,
                        'reference_id'   => $bill->id,
                        'date'           => $bill->date,
                        'created_by'     => auth()->id() ?? 1,
                    ]);
                }
            });
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
