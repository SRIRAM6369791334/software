<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\WeeklyBill;
use App\Models\WeeklyBillItem;
use App\Models\Item;
use App\Models\DealerPurchase;
use App\Models\DayLoadEntry;
use App\Models\DayLoadBatch;
use App\Services\ExportService;
use App\Services\WeeklyBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
        $search = $request->input('search');
        
        $bills = WeeklyBill::with(['dealer', 'items'])
            ->search($search)
            ->latest()
            ->paginate(15, ['*'], 'bills_page');

        $purchases = DealerPurchase::with(['dealer', 'items'])
            ->search($search)
            ->latest()
            ->paginate(15, ['*'], 'purchases_page');

        $dealers = Dealer::orderBy('firm_name')->get();
        $items = Item::active()->get();

        return view('billing.weekly.index', compact('bills', 'purchases', 'dealers', 'search', 'items'));
    }

    public function bulk(): View
    {
        $dealers = Dealer::orderBy('firm_name')->get();
        return view('billing.weekly.bulk', compact('dealers'));
    }

    /**
     * Store a daily/individual dealer purchase.
     */
    public function storePurchase(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dealer_id'    => 'required|exists:dealers,id',
            'date'         => 'required|date|before_or_equal:today',
            'items'        => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.qty'  => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0.01',
        ]);

        try {
            $this->billingService->createPurchase($validated);
        } catch (\Exception $e) {
            return back()->with('error', 'Could not record purchase: ' . $e->getMessage());
        }

        return back()->with('success', 'Dealer daily purchase recorded successfully.');
    }

    /**
     * Preview weekly billing calculation for a dealer.
     */
    public function calculatePreview(Request $request): JsonResponse
    {
        $request->validate([
            'dealer_id'    => 'required|exists:dealers,id',
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after_or_equal:period_start',
        ]);

        try {
            $totals = $this->billingService->calculateWeeklyTotals(
                $request->input('dealer_id'),
                $request->input('period_start'),
                $request->input('period_end')
            );
            
            // Format for preview response
            $purchasesCount = $totals['purchases']->count();
            
            return response()->json([
                'success' => true,
                'previous_outstanding' => $totals['previous_outstanding'],
                'total_purchases' => $totals['total_purchases'],
                'total_payments' => $totals['total_payments'],
                'net_invoice_amount' => $totals['net_invoice_amount'],
                'purchases_count' => $purchasesCount,
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Generate weekly bill from compiled daily purchases/payments.
     */
    public function generateWeekly(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dealer_id'    => 'required|exists:dealers,id',
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after_or_equal:period_start',
        ]);

        try {
            $this->billingService->generateWeeklyBill($validated);
        } catch (\Exception $e) {
            return back()->with('error', 'Could not generate weekly bill: ' . $e->getMessage());
        }

        return back()->with('success', 'Weekly bill generated successfully.');
    }

    /**
     * Pay split part (Monday or Friday).
     */
    public function paySplit(Request $request, WeeklyBill $weekly, string $part): RedirectResponse
    {
        $request->validate([
            'payment_mode' => 'required|in:Cash,UPI,NEFT,Cheque(Bank Transfer)',
            'notes'        => 'nullable|string|max:500',
        ]);

        try {
            $this->billingService->recordSplitPayment($weekly->id, $part, [
                'payment_mode' => $request->input('payment_mode'),
                'notes'        => $request->input('notes'),
                'date'         => now()->format('Y-m-d'),
            ]);
        } catch (\Exception $e) {
            return back()->with('error', 'Could not record split payment: ' . $e->getMessage());
        }

        return back()->with('success', 'Split payment recorded successfully.');
    }

    /**
     * Legacy Manual Store method.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dealer_id'    => 'required|exists:dealers,id',
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after_or_equal:period_start',
            'status'       => 'required|in:Generated,Pending,Paid',
            'payment_mode' => 'required|in:Cash,UPI,NEFT,Cheque(Bank Transfer),Pay later(EMI)',
            'items'        => 'required|array|min:1',
            'items.*.name' => 'required|string|max:255',
            'items.*.qty'  => 'required|numeric|min:0.01',
            'items.*.rate' => 'required|numeric|min:0.01',
            'items.*.unit' => 'nullable|string|max:20',
            'emis'           => 'required_if:payment_mode,Pay later(EMI)|array',
            'emis.*.due_date'=> 'required_if:payment_mode,Pay later(EMI)|date',
            'emis.*.amount'  => 'required_if:payment_mode,Pay later(EMI)|numeric|min:0.01',
        ]);

        try {
            $this->billingService->create($validated);
        } catch (\Exception $e) {
            return back()->with('error', 'Could not create bill: ' . $e->getMessage());
        }

        return back()->with('success', 'Weekly bill created successfully.');
    }

    /**
     * Legacy Bulk Store method.
     */
    public function bulkStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dealer_ids'     => 'required|array',
            'dealer_ids.*'   => 'exists:dealers,id',
            'period_start'   => 'required|date',
            'period_end'     => 'required|date|after_or_equal:period_start',
            'amount'         => 'required|numeric|min:0.01',
            'status'         => 'required|in:Generated,Pending,Paid',
            'payment_mode'   => 'required|in:Cash,UPI,NEFT,Cheque(Bank Transfer),Pay later(EMI)',
        ]);

        try {
            $count = $this->billingService->bulkCreate($validated['dealer_ids'], $validated);
        } catch (\Exception $e) {
            return back()->with('error', 'Could not create bills: ' . $e->getMessage());
        }

        return back()->with('success', $count . ' bills generated.');
    }

    public function show(WeeklyBill $weekly): View
    {
        $weekly->load(['dealer', 'items']);
        return view('billing.invoice', ['bill' => $weekly]);
    }

    public function print(WeeklyBill $weekly): View
    {
        $weekly->load(['dealer', 'items']);
        return view('billing.invoice', ['bill' => $weekly]);
    }

    public function whatsapp(WeeklyBill $weekly): RedirectResponse
    {
        $phone = preg_replace('/[^0-9]/', '', $weekly->dealer->phone ?? '');
        if (!$phone) return back()->with('error', 'Dealer phone missing.');

        $text = urlencode("Hello {$weekly->dealer->firm_name}, your poultry bill for period {$weekly->period_start->format('d M')} to {$weekly->period_end->format('d M')} is ₹" . number_format($weekly->amount, 2) . ". Thank you!");

        return redirect()->away("https://wa.me/91{$phone}?text={$text}");
    }

    public function export(): StreamedResponse
    {
        $bills = WeeklyBill::with(['dealer', 'items'])->latest()->get();
        $rows  = $bills->map(fn($b) => [
            $b->dealer->firm_name ?? '—',
            $b->period_start->format('Y-m-d'),
            $b->period_end->format('Y-m-d'),
            $b->items_description,
            $b->quantity_kg,
            $b->amount,
            $b->status,
        ]);
        return $this->exporter->streamCsv('weekly-billing', ['Dealer','From','To','Items','Qty(kg)','Amount','Status'], $rows);
    }

    public function downloadPdf(WeeklyBill $weekly)
    {
        $weekly->load(['dealer', 'items']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('billing.weekly.pdf', ['bill' => $weekly]);
        return $pdf->download("invoice-{$weekly->invoice_no}.pdf");
    }

    public function dealerInvoice(Request $request): View
    {
        $dealerId = $request->input('dealer_id');
        $periodStart = $request->input('period_start', now()->startOfWeek()->format('Y-m-d'));
        $periodEnd = $request->input('period_end', now()->endOfWeek()->subDay()->format('Y-m-d'));
        $preset = $request->input('preset', '');

        $dealers = Dealer::orderBy('firm_name')->get();
        $entries = collect();
        $dealer = null;
        $currentBillTotal = 0;
        $previousBalance = 0;
        $grandTotal = 0;

        if ($dealerId) {
            $dealer = Dealer::find($dealerId);

            $entries = DayLoadEntry::with(['batch', 'vendor'])
                ->where('dealer_id', $dealerId)
                ->where('status', 'Active')
                ->whereHas('batch', function ($q) use ($periodStart, $periodEnd) {
                    $q->whereDate('billing_date', '>=', $periodStart)
                      ->whereDate('billing_date', '<=', $periodEnd);
                })
                ->orderBy('batch_id')
                ->get()
                ->map(function ($entry) {
                    $rate = (float) $entry->billing_rate;
                    $kg = (float) $entry->bird_weight;
                    $total = round($kg * $rate, 2);
                    return [
                        'id'         => $entry->id,
                        'date'       => $entry->batch->billing_date->format('d-m-Y'),
                        'vendor'     => $entry->vendor->firm_name ?? '-',
                        'kg'         => $kg,
                        'rate'       => $rate,
                        'total'      => $total,
                        'bird_weight'=> $kg,
                    ];
                });

            $currentBillTotal = $entries->sum('total');
            $previousBalance = (float) $dealer->pending_amount;
            $grandTotal = $currentBillTotal + $previousBalance;
        }

        return view('billing.weekly.dealer-invoice', compact(
            'dealers', 'dealer', 'entries', 'periodStart', 'periodEnd', 'preset',
            'currentBillTotal', 'previousBalance', 'grandTotal'
        ));
    }

    public function generateInvoice(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dealer_id'    => 'required|exists:dealers,id',
            'period_start' => 'required|date',
            'period_end'   => 'required|date|after_or_equal:period_start',
        ]);

        $dealer = Dealer::findOrFail($validated['dealer_id']);

        $entries = DayLoadEntry::with(['batch', 'vendor'])
            ->where('dealer_id', $validated['dealer_id'])
            ->where('status', 'Active')
            ->whereHas('batch', function ($q) use ($validated) {
                $q->whereDate('billing_date', '>=', $validated['period_start'])
                  ->whereDate('billing_date', '<=', $validated['period_end']);
            })
            ->get();

        if ($entries->isEmpty()) {
            return back()->with('error', 'No day-load entries found for this dealer in the selected period.');
        }

        // One line item per entry
        $currentBillTotal = 0;
        $lineItems = [];

        foreach ($entries as $entry) {
            $kg = (float) $entry->bird_weight;
            $rate = (float) $entry->billing_rate;
            $total = round($kg * $rate, 2);

            $lineItems[] = [
                'item_name'    => 'Day-Load (' . $entry->batch->billing_date->format('d M') . ')',
                'vendor_name'  => $entry->vendor->firm_name ?? '-',
                'quantity_kg'  => $kg,
                'rate_per_kg'  => $rate,
                'total_amount' => $total,
            ];
            $currentBillTotal += $total;
        }

        $previousBalance = (float) $dealer->pending_amount;
        $grandTotal = $currentBillTotal + $previousBalance;

        // Create WeeklyBill
        $bill = WeeklyBill::create([
            'dealer_id'           => $validated['dealer_id'],
            'period_start'        => $validated['period_start'],
            'period_end'          => $validated['period_end'],
            'invoice_no'          => 'INV-DL-' . str_pad(WeeklyBill::max('id') + 1, 4, '0', STR_PAD_LEFT),
            'amount'              => $currentBillTotal,
            'gst_percentage'      => 0,
            'gst_amount'          => 0,
            'net_amount'          => $grandTotal,
            'status'              => 'Pending',
            'payment_mode'        => 'Pending',
            'previous_outstanding'=> $previousBalance,
            'payments_during_week'=> 0,
            'monday_payment_amount'=> 0,
            'monday_payment_status'=> 'Unpaid',
            'friday_payment_amount'=> 0,
            'friday_payment_status'=> 'Unpaid',
        ]);

        // Create line items
        foreach ($lineItems as $item) {
            WeeklyBillItem::create(array_merge($item, [
                'weekly_bill_id' => $bill->id,
                'tax_amount'     => 0,
            ]));
        }

        // Update dealer pending amount
        $dealer->update(['pending_amount' => $grandTotal]);

        return redirect()->route('billing.weekly.show', $bill->id)
            ->with('success', 'Invoice generated successfully.');
    }
}
