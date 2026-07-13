<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
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

        $payments = $this->service->paginated($search, $vendorFilter, $dateFrom, $dateTo, $modeFilter, 15);
        $vendors = Vendor::orderBy('firm_name')->get();

        return view('payments.vendors', compact(
            'payments', 'vendors', 'search',
            'vendorFilter', 'dateFrom', 'dateTo', 'modeFilter'
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
        if ($selected_vendor_id) {
            $pendingDayLoadCount = \App\Models\DayLoadEntry::where('vendor_id', $selected_vendor_id)
                ->whereIn('vendor_payment_status', ['Pending', 'Partial'])
                ->count();
        }

        return view('payments.vendors.create', compact('vendors', 'selected_vendor_id', 'pendingDayLoadCount'));
    }

    public function storeGeneralPayment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'vendor_id'          => 'required|exists:vendors,id',
            'date'               => 'required|date',
            'cash_amount'        => 'required|numeric|min:0',
            'bank_amount'        => 'required|numeric|min:0',
            'payment_mode'       => 'required|in:Cash,UPI,NEFT,Cheque',
            'bank_transfer_type' => 'nullable|required_if:bank_amount,>0|in:UPI,Bank Transfer,NEFT,RTGS,IMPS,Cheque,Other',
            'notes'              => 'nullable|string'
        ]);

        if ((float) $validated['cash_amount'] + (float) $validated['bank_amount'] <= 0) {
            return back()->with('error', 'Total payment amount must be greater than zero.');
        }

        $this->service->record($validated);

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
        $validated = $request->validate([
            'date'               => 'required|date',
            'cash_amount'        => 'required|numeric|min:0',
            'bank_amount'        => 'required|numeric|min:0',
            'payment_mode'       => 'required|in:Cash,UPI,NEFT,Cheque',
            'bank_transfer_type' => 'nullable|required_if:bank_amount,>0|in:UPI,Bank Transfer,NEFT,RTGS,IMPS,Cheque,Other',
            'notes'              => 'nullable|string'
        ]);
        
        $cashAmount = (float) $validated['cash_amount'];
        $bankAmount = (float) $validated['bank_amount'];
        
        if ($cashAmount + $bankAmount <= 0) {
            return back()->with('error', 'Total payment amount must be greater than zero.');
        }

        $validated['amount'] = round($cashAmount + $bankAmount, 2);
        
        $vendor->vendorPayments()->create($validated);

        // Recalculate cash/bank ledger for the payment date
        app(CashBankLedgerService::class)->recalculateForDate(\Carbon\Carbon::parse($validated['date']));
        
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
