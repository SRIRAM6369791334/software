<?php

namespace App\Services;

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Dealer;
use App\Models\DailyBill;
use App\Models\DailyBillItem;
use App\Models\Item;
use App\Models\DayLoadEntry;
use App\Services\ExportService;
use App\Services\DailyBillingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
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
        $search = $request->input('search');

        $pendingBills = DailyBill::search($search)->where('status', 'Pending')->get();
        $outstandingDuesTotal = 0;
        foreach ($pendingBills as $bill) {
            if ($bill->dealer_id) {
                $entryIds = \App\Models\DayLoadEntry::where('daily_bill_id', $bill->id)->pluck('id')->toArray();
                $periodPaid = \App\Models\DealerPayment::where('dealer_id', $bill->dealer_id)
                    ->where(function($q) use ($bill, $entryIds) {
                        $q->whereDate('date', $bill->date->format('Y-m-d'))
                          ->orWhereIn('day_load_entry_id', $entryIds)
                          ->orWhere('invoice_id', $bill->id);
                    })->sum('amount');
            } else {
                $periodPaid = 0;
            }
            $outstandingDuesTotal += max(0, (float)$bill->net_amount - $periodPaid);
        }

        $paidRevenueTotal = DailyBill::search($search)
            ->where('status', 'Paid')
            ->selectRaw('SUM(COALESCE(net_amount, amount)) as total')
            ->value('total') ?? 0;

        $bills = DailyBill::with(['dealer', 'customer', 'items'])
            ->search($search)
            ->latest()
            ->paginate(15, ['*'], 'bills_page');

        $dealerDayLoads = DayLoadEntry::with(['dealer', 'batch', 'vendor'])
            ->where('status', '!=', 'Cancelled')
            ->latest()
            ->paginate(15, ['*'], 'dealer_dayload_page');

        $dealers = Dealer::orderBy('firm_name')->get();
        $items = Item::active()->get();

        return view('billing.daily.index', compact(
            'bills', 'dealerDayLoads', 'dealers', 'search', 'items',
            'outstandingDuesTotal', 'paidRevenueTotal'
        ));
    }

    /**
     * Show form to manually create a daily invoice for a customer.
     */
    public function create(): \Illuminate\View\View
    {
        $customers = \App\Models\Customer::orderBy('name')->get();
        $dealers = \App\Models\Dealer::orderBy('firm_name')->get();
        return view('billing.daily.create', compact('customers', 'dealers'));
    }

    /**
     * Get dealer's available stock for a specific date.
     */
    public function getDealerStock(Request $request): JsonResponse
    {
        $request->validate([
            'dealer_id' => 'required|exists:dealers,id',
            'date'      => 'required|date',
        ]);

        $dealerId = $request->input('dealer_id');
        $date = $request->input('date');

        // Calculate total stock received by dealer on this date
        $totalStock = \App\Models\DayLoadEntry::where('dealer_id', $dealerId)
            ->where('status', '!=', 'Cancelled')
            ->whereHas('batch', function($q) use ($date) {
                $q->whereDate('billing_date', $date);
            })->sum('bird_weight');

        // Calculate total stock already billed to customers on this date
        $billedStock = \App\Models\DailyBillItem::whereHas('dailyBill', function($q) use ($dealerId, $date) {
            $q->where('dealer_id', $dealerId)
              ->whereDate('date', $date)
              ->where('status', '!=', 'Cancelled')
              ->whereDoesntHave('dayLoadEntries');
        })->sum('quantity_kg');

        $availableStock = max(0, $totalStock - $billedStock);

        return response()->json([
            'success' => true,
            'total_stock' => $totalStock,
            'billed_stock' => $billedStock,
            'available_stock' => $availableStock
        ]);
    }

    /**
     * Preview daily billing calculation for a dealer.
     */
    public function calculatePreview(Request $request): JsonResponse
    {
        $request->validate([
            'dealer_id'       => 'required|exists:dealers,id',
            'date_from'       => 'nullable|date',
            'date_to'         => 'nullable|date|after_or_equal:date_from',
            'date'            => 'nullable|date',
            'discount_amount' => 'nullable|numeric|min:0',
        ]);

        try {
            $dateFrom = $request->input('date_from') ?: $request->input('date', today()->format('Y-m-d'));
            $dateTo   = $request->input('date_to')   ?: $dateFrom;
            $discountAmount = (float)$request->input('discount_amount', 0.0);

            $totals = $this->billingService->calculateDailyTotals(
                (int) $request->input('dealer_id'),
                $dateFrom,
                $dateTo,
                $discountAmount
            );

            return response()->json([
                'success'              => true,
                'date_from'            => $dateFrom,
                'date_to'              => $dateTo,
                'previous_outstanding' => $totals['previous_outstanding'],
                'total_purchases'      => $totals['total_purchases'],
                'total_payments'       => $totals['total_payments'],
                'net_invoice_amount'   => $totals['net_invoice_amount'],
                'balance_due'          => $totals['balance_due'],
                'purchases_count'      => $totals['purchases']->count(),
                'discount_amount'      => $discountAmount,
                'exists'               => $totals['existing_bills_count'] > 0,
                'existing_bills_count' => $totals['existing_bills_count'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Generate daily bill for a dealer.
     */
    public function generateDaily(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dealer_id'        => 'required|exists:dealers,id',
            'date_from'        => 'nullable|date',
            'date_to'          => 'nullable|date|after_or_equal:date_from',
            'date'             => 'nullable|date|before_or_equal:today',
            'discount_amount'  => 'nullable|numeric|min:0',
            'replace_existing' => 'nullable|boolean',
        ]);

        if (empty($validated['date_from'])) {
            $validated['date_from'] = $validated['date'] ?? today()->format('Y-m-d');
        }
        if (empty($validated['date_to'])) {
            $validated['date_to'] = $validated['date_from'];
        }
        $validated['date'] = $validated['date_to'];

        try {
            $this->billingService->generateDailyBill($validated);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Daily bill generated successfully.');
    }

    public function show(DailyBill $bill): View
    {
        $bill->load(['dealer', 'customer', 'items']);

        // Day-Load entries for this bill's date
        $dayLoadEntries = DayLoadEntry::with(['batch', 'vendor', 'dailyBill'])
            ->where('dealer_id', $bill->dealer_id)
            ->where('status', '!=', 'Cancelled')
            ->whereHas('batch', fn($q) => $q->whereDate('billing_date', $bill->date->format('Y-m-d')))
            ->orderBy('batch_id')
            ->get();

        $entryIds = $dayLoadEntries->pluck('id')->toArray();
        $allPayments = \App\Models\DealerPayment::where('dealer_id', $bill->dealer_id)
            ->where(function($q) use ($bill, $entryIds) {
                $q->whereDate('date', $bill->date->format('Y-m-d'))
                  ->orWhereIn('day_load_entry_id', $entryIds)
                  ->orWhere('invoice_id', $bill->id);
            })
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $dayLoadTotal = round($dayLoadEntries->sum(
            fn($e) => (float) $e->bird_weight * (float) $e->customer_rate
        ), 2);
        if ($dayLoadTotal == 0 && (float)$bill->amount > 0) {
            $dayLoadTotal = (float)$bill->amount;
        }

        $prevOutstanding = (float)($bill->previous_outstanding ?? 0);
        $discountAmount = (float)($bill->discount_amount ?? 0);
        $netInvoiceAmount = max(0, round($dayLoadTotal - $discountAmount, 2));
        $totalPayable = round($prevOutstanding + $netInvoiceAmount, 2);

        $totalPaid = round($allPayments->sum('amount'), 2);
        $remainingDue = max(0, round($totalPayable - $totalPaid, 2));

        return view('billing.invoice', array_merge(
            compact('bill', 'dayLoadEntries', 'allPayments', 'dayLoadTotal', 'totalPaid', 'remainingDue', 'prevOutstanding', 'totalPayable', 'netInvoiceAmount'),
            ['weekly' => $bill]
        ));
    }

    public function invoice(DailyBill $bill): View
    {
        return $this->show($bill);
    }

    public function whatsapp(DailyBill $daily): RedirectResponse
    {
        $phone = preg_replace('/[^0-9]/', '', $daily->dealer->phone ?? $daily->customer->phone ?? '');
        if (!$phone) return back()->with('error', 'Phone number missing.');

        $name = $daily->dealer->firm_name ?? $daily->customer->name ?? 'Valued Client';
        $text = urlencode("Hello {$name}, your daily poultry bill for {$daily->date->format('d M Y')} is ₹" . number_format($daily->net_amount, 2) . ". Thank you!");

        return redirect()->away("https://wa.me/91{$phone}?text={$text}");
    }

    public function export(): StreamedResponse
    {
        $bills = DailyBill::with(['dealer', 'customer', 'items'])->latest()->get();
        $rows  = $bills->map(fn($b) => [
            $b->dealer->firm_name ?? $b->customer->name ?? '—',
            $b->date->format('Y-m-d'),
            $b->items_description,
            $b->quantity_kg,
            $b->amount,
            $b->status,
        ]);
        return $this->exporter->streamCsv('daily-billing', ['Client', 'Date', 'Items', 'Qty(kg)', 'Amount', 'Status'], $rows);
    }

    public function downloadPdf(DailyBill $bill)
    {
        $bill->load(['dealer', 'customer', 'items']);

        $dayLoadEntries = DayLoadEntry::with(['batch', 'vendor', 'dailyBill'])
            ->where('dealer_id', $bill->dealer_id)
            ->where('status', '!=', 'Cancelled')
            ->whereHas('batch', fn($q) => $q->whereDate('billing_date', $bill->date->format('Y-m-d')))
            ->orderBy('batch_id')
            ->get();

        $entryIds = $dayLoadEntries->pluck('id')->toArray();
        $allPayments = \App\Models\DealerPayment::where('dealer_id', $bill->dealer_id)
            ->where(function($q) use ($bill, $entryIds) {
                $q->whereDate('date', $bill->date->format('Y-m-d'))
                  ->orWhereIn('day_load_entry_id', $entryIds)
                  ->orWhere('invoice_id', $bill->id);
            })
            ->orderBy('date')
            ->orderBy('id')
            ->get();

        $dayLoadTotal = round($dayLoadEntries->sum(
            fn($e) => (float) $e->bird_weight * (float) $e->customer_rate
        ), 2);
        $totalPaid = round($allPayments->sum('amount'), 2);
        $remainingDue = max(0, round((float) $bill->net_amount - $totalPaid, 2));

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('billing.daily.pdf', [
            'bill'           => $bill,
            'dayLoadEntries' => $dayLoadEntries,
            'allPayments'    => $allPayments,
            'dayLoadTotal'   => $dayLoadTotal,
            'totalPaid'      => $totalPaid,
            'remainingDue'   => $remainingDue,
        ]);
        return $pdf->download("invoice-{$bill->invoice_number}.pdf");
    }

    public function destroy(DailyBill $daily): RedirectResponse
    {
        try {
            $this->billingService->deleteDailyBill($daily);
        } catch (\Exception $e) {
            return back()->with('error', 'Could not delete daily bill: ' . $e->getMessage());
        }

        return redirect()->route('billing.daily.index')->with('success', 'Daily bill deleted successfully.');
    }

    public function gst(): View
    {
        $bills = DailyBill::with(['dealer', 'customer', 'items'])->paginate(15);
        return view('billing.daily.gst', compact('bills'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_id'    => 'nullable|exists:customers,id',
            'dealer_id'      => 'required|exists:dealers,id',
            'date'           => 'required|date|before_or_equal:today',
            'status'         => 'required|in:Pending,Paid,COD,Bank',
            'payment_mode'   => 'required|in:Cash,UPI,NEFT,Cheque(Bank Transfer),Pay later(EMI)',
            'gst_percentage' => 'nullable|numeric|min:0|max:28',
            'items'          => 'required|array|min:1',
            'items.*.name'   => 'required|string|max:255',
            'items.*.qty'    => 'required|numeric|min:0.01',
            'items.*.rate'   => 'required|numeric|min:0.01',
            'items.*.unit'   => 'nullable|string|max:20',
        ]);

        $dealerId = $validated['dealer_id'];
        $date = $validated['date'];
        
        $totalStock = \App\Models\DayLoadEntry::where('dealer_id', $dealerId)
            ->where('status', '!=', 'Cancelled')
            ->whereHas('batch', function($q) use ($date) {
                $q->whereDate('billing_date', $date);
            })->sum('bird_weight');

        $billedStock = \App\Models\DailyBillItem::whereHas('dailyBill', function($q) use ($dealerId, $date) {
            $q->where('dealer_id', $dealerId)
              ->whereDate('date', $date)
              ->where('status', '!=', 'Cancelled')
              ->whereDoesntHave('dayLoadEntries');
        })->sum('quantity_kg');

        $availableStock = max(0, $totalStock - $billedStock);
        $requestedQty = collect($validated['items'])->sum('qty');

        if ($requestedQty > $availableStock) {
            return back()->withErrors(['items' => "Insufficient stock for dealer. Available: {$availableStock} kg, Requested: {$requestedQty} kg."])->withInput();
        }

        try {
            $this->billingService->create($validated);
        } catch (\Exception $e) {
            return back()->with('error', 'Could not create bill: ' . $e->getMessage());
        }

        return back()->with('success', 'Daily bill created successfully.');
    }
}
