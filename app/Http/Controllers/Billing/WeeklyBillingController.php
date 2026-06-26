<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Models\Dealer;
use App\Models\WeeklyBill;
use App\Models\Item;
use App\Models\DealerPurchase;
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
    public function paySplit(Request $request, WeeklyBill $bill, string $part): RedirectResponse
    {
        $request->validate([
            'payment_mode' => 'required|in:Cash,UPI,NEFT,Cheque(Bank Transfer)',
            'notes'        => 'nullable|string|max:500',
        ]);

        try {
            $this->billingService->recordSplitPayment($bill->id, $part, [
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

    public function show(WeeklyBill $bill): View
    {
        $bill->load(['dealer', 'items']);
        return view('billing.invoice', compact('bill'));
    }

    public function print(WeeklyBill $bill): View
    {
        $bill->load(['dealer', 'items']);
        return view('billing.invoice', compact('bill'));
    }

    public function whatsapp(WeeklyBill $bill): RedirectResponse
    {
        $phone = preg_replace('/[^0-9]/', '', $bill->dealer->phone ?? '');
        if (!$phone) return back()->with('error', 'Dealer phone missing.');

        $text = urlencode("Hello {$bill->dealer->firm_name}, your poultry bill for period {$bill->period_start->format('d M')} to {$bill->period_end->format('d M')} is ₹" . number_format($bill->amount, 2) . ". Thank you!");

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

    public function downloadPdf(WeeklyBill $bill)
    {
        $bill->load(['dealer', 'items']);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('billing.weekly.pdf', compact('bill'));
        return $pdf->download("invoice-{$bill->invoice_no}.pdf");
    }
}
