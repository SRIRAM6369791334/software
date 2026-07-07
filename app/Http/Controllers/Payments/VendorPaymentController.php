<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\VendorPayment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VendorPaymentController extends Controller
{
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

        // Recalculate cash/bank ledger
        app(\App\Services\CashBankLedgerService::class)->recalculateForDate(now());
        
        return back()->with('success', 'Payment recorded successfully.');
    }

    public function destroy(Vendor $vendor, VendorPayment $payment): RedirectResponse
    {
        $payment->delete();
        
        return back()->with('success', 'Payment deleted successfully.');
    }
}
