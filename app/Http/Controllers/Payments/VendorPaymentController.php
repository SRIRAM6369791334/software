<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\DayLoadEntry;
use App\Models\Vendor;
use App\Models\VendorPayment;
use App\Services\VendorPaymentService;
use App\Services\ExportService;
use App\Services\CashBankLedgerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VendorPaymentController extends Controller
{
    public function __construct(
        private VendorPaymentService $service,
        private ExportService        $exporter,
    ) {}

    public function index(Request $request): View
    {
        $search       = $request->input('search');
        $vendorFilter = $request->input('vendor_id');
        $dateFrom     = $request->input('date_from');
        $dateTo       = $request->input('date_to');
        $modeFilter   = $request->input('payment_mode');

        $paymentsQuery = VendorPayment::query()
            ->when($search, function ($q) use ($search) {
                $q->where(function ($nested) use ($search) {
                    $nested->whereHas('vendor', fn($v) => $v->where('firm_name', 'like', "%{$search}%"))
                           ->orWhere('reference_number', 'like', "%{$search}%");
                });
            })
            ->when($vendorFilter, fn($q) => $q->where('vendor_id', $vendorFilter))
            ->when($dateFrom, fn($q) => $q->whereDate('date', '>=', $dateFrom))
            ->when($dateTo, fn($q) => $q->whereDate('date', '<=', $dateTo))
            ->when($modeFilter, fn($q) => $q->where('payment_mode', $modeFilter));

        $totalPaidOut = (clone $paymentsQuery)->sum('amount');

        $payments = $paymentsQuery->with('vendor')->latest('date')->paginate(15);
        $vendors = Vendor::orderBy('firm_name')->get();

        return view('payments.vendors', compact(
            'payments', 'vendors', 'search',
            'vendorFilter', 'dateFrom', 'dateTo', 'modeFilter', 'totalPaidOut'
        ));
    }

    public function create(Request $request): View
    {
        $selected_vendor_id = $request->input('vendor_id');
        $query = Vendor::orderBy('firm_name');
        if ($selected_vendor_id) {
            $query->where('id', $selected_vendor_id);
        }
        $vendors = $query->get();

        $pendingDayLoadCount = 0;
        $dayLoadEntries = collect();
        $pendingEmis = collect();

        $unallocatedPayments = 0;
        $dayLoadEntriesTotal = 0;
        $pendingEmisTotal = 0;

        if ($selected_vendor_id) {
            $unallocatedPayments = (float) VendorPayment::where('vendor_id', $selected_vendor_id)
                ->whereNull('day_load_entry_id')
                ->sum('amount');

            $pendingDayLoadCount = DayLoadEntry::where('vendor_id', $selected_vendor_id)
                ->whereIn('vendor_payment_status', ['Pending', 'Partial'])
                ->where('status', '!=', 'Cancelled')
                ->count();

            $dayLoadEntries = DayLoadEntry::where('vendor_id', $selected_vendor_id)
                ->whereIn('vendor_payment_status', ['Pending', 'Partial'])
                ->where('status', '!=', 'Cancelled')
                ->with(['dealer', 'batch'])
                ->get()
                ->sortBy(function($e) {
                    return $e->batch ? $e->batch->billing_date->timestamp : $e->created_at->timestamp;
                });

            $dayLoadEntriesTotal = $dayLoadEntries->sum(function($e) {
                return (float)$e->vendor_cost - (float)$e->vendor_paid;
            });

            // Fetch pending EMIs for this vendor
            $pendingEmis = \App\Models\Emi::where('emi_type', 'Vendor')
                ->where('entity_id', $selected_vendor_id)
                ->where('status', '!=', 'Paid')
                ->orderBy('due_date')
                ->get();

            $pendingEmisTotal = $pendingEmis->sum('remaining_amount');
        }

        return view('payments.vendors.create', compact(
            'vendors', 'selected_vendor_id', 'pendingDayLoadCount', 'dayLoadEntries',
            'unallocatedPayments', 'dayLoadEntriesTotal', 'pendingEmis', 'pendingEmisTotal'
        ));
    }

    public function storeGeneralPayment(Request $request): RedirectResponse
    {
        $paymentMode = $request->input('payment_mode');
        if (!$paymentMode || $paymentMode === 'Bank Transfer') {
            $paymentMode = ((float) ($request->input('bank_amount') ?? 0) > 0)
                ? ($request->input('bank_transfer_type') ?: 'Bank Transfer')
                : 'Cash';
            $request->merge(['payment_mode' => $paymentMode]);
        }

        $validated = $request->validate([
            'vendor_id'          => 'required|exists:vendors,id',
            'date'               => 'required|date',
            'cash_amount'        => 'required|numeric|min:0',
            'bank_amount'        => 'required|numeric|min:0',
            'payment_mode'       => 'required|in:Cash,Bank Transfer,UPI,NEFT,RTGS,IMPS,Cheque,Other',
            'bank_transfer_type' => 'nullable|required_if:bank_amount,>0|in:UPI,Bank Transfer,NEFT,RTGS,IMPS,Cheque,Other',
            'notes'              => 'nullable|string',
            'selected_entry_ids' => 'nullable|array',
            'selected_entry_ids.*' => 'exists:day_load_entries,id',
            'selected_emi_ids'   => 'nullable|array',
            'selected_emi_ids.*' => 'exists:emis,id',
        ]);

        $cashAmount = (float) $validated['cash_amount'];
        $bankAmount = (float) $validated['bank_amount'];
        $amount = round($cashAmount + $bankAmount, 2);

        if ($cashAmount + $bankAmount <= 0) {
            return back()->with('error', 'Total payment amount must be greater than zero.');
        }

        if ($amount > 0) {
            $this->service->record($validated);
        }

        return redirect()->route('payments.vendors.index')->with('success', 'Vendor payment recorded and balance updated.');
    }

    public function ledger(Vendor $vendor): View
    {
        $purchases = $vendor->purchases()->orderBy('date', 'desc')->get();
        $payments = $vendor->vendorPayments()->orderBy('date', 'desc')->get();
        $dayLoads = $vendor->dayLoadEntries()->with('batch')->get();
        
        $transactions = collect();
        
        foreach ($purchases as $p) {
            $transactions->push((object)[
                'type' => 'Purchase',
                'date' => $p->date,
                'reference' => 'Invoice: ' . $p->id,
                'amount' => $p->total_amount,
                'mode' => $p->payment_mode,
                'is_credit' => $p->payment_mode === 'Credit',
            ]);
        }

        foreach ($dayLoads as $e) {
            $transactions->push((object)[
                'type' => 'Day-Load',
                'date' => $e->batch->billing_date,
                'reference' => 'DL-' . $e->id . ' (' . $e->no_of_boxes . ' boxes)',
                'amount' => round((float) $e->bird_weight, 2),
                'mode' => 'Load',
                'is_credit' => true,
            ]);
        }
        
        foreach ($payments as $p) {
            $transactions->push((object)[
                'type' => 'Payment',
                'date' => $p->date,
                'reference' => 'Paid via ' . $p->payment_mode,
                'amount' => $p->amount,
                'mode' => $p->payment_mode,
                'is_credit' => false,
                'id' => $p->id
            ]);
        }
        
        $transactions = $transactions->sortByDesc('date');
        
        return view('masters.vendors.ledger', compact('vendor', 'transactions'));
    }

    public function store(Request $request, Vendor $vendor): RedirectResponse
    {
        $paymentMode = $request->input('payment_mode');
        if (!$paymentMode || $paymentMode === 'Bank Transfer') {
            $paymentMode = ((float) ($request->input('bank_amount') ?? 0) > 0)
                ? ($request->input('bank_transfer_type') ?: 'Bank Transfer')
                : 'Cash';
            $request->merge(['payment_mode' => $paymentMode]);
        }

        $validated = $request->validate([
            'date'               => 'required|date',
            'cash_amount'        => 'required|numeric|min:0',
            'bank_amount'        => 'required|numeric|min:0',
            'payment_mode'       => 'required|in:Cash,Bank Transfer,UPI,NEFT,RTGS,IMPS,Cheque,Other',
            'bank_transfer_type' => 'nullable|required_if:bank_amount,>0|in:UPI,Bank Transfer,NEFT,RTGS,IMPS,Cheque,Other',
            'notes'              => 'nullable|string'
        ]);
        
        $cashAmount = (float) $validated['cash_amount'];
        $bankAmount = (float) $validated['bank_amount'];
        $amount = round($cashAmount + $bankAmount, 2);
        
        if ($cashAmount + $bankAmount <= 0) {
            return back()->with('error', 'Total payment amount must be greater than zero.');
        }

        if ($amount > $vendor->outstanding_balance) {
            return back()->withErrors(['amount' => "The payment amount cannot exceed the vendor's outstanding balance of Rs " . number_format($vendor->outstanding_balance, 2) . "."])->withInput();
        }

        $validated['vendor_id'] = $vendor->id;
        $validated['amount'] = $amount;
        
        $this->service->record($validated);
        
        return back()->with('success', 'Payment recorded successfully.');
    }

    public function destroy(Vendor $vendor, VendorPayment $payment): RedirectResponse
    {
        $paymentDate = $payment->date;
        $payment->delete();
        app(CashBankLedgerService::class)->recalculateForDate(\Carbon\Carbon::parse($paymentDate));
        
        return back()->with('success', 'Payment deleted successfully.');
    }

    public function export(): StreamedResponse
    {
        $rows = $this->service->allForExport()->map(fn($p) => [
            $p->vendor->firm_name ?? '—', $p->date->format('Y-m-d'), $p->amount, $p->payment_mode, $p->notes,
        ]);
        return $this->exporter->streamCsv(
            'vendor-payments',
            ['Vendor','Date','Amount','Mode','Notes'],
            $rows
        );
    }
}
