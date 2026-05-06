<?php

namespace App\Http\Controllers\Masters;

use App\Http\Controllers\Controller;
use App\Http\Requests\Masters\StoreCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function __construct(private CustomerService $service) {}

    public function index(Request $request): View
    {
        $search    = $request->input('search');
        $customers = $this->service->search($search, 15);
        return view('masters.customers.index', compact('customers', 'search'));
    }

    public function create(): View
    {
        return view('masters.customers.create');
    }

    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());
        return back()->with('success', 'Customer added successfully.');
    }

    public function update(StoreCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->service->update($customer, $request->validated());
        return back()->with('success', 'Customer updated successfully.');
    }

    public function show(Customer $customer): View
    {
        $customer->loadCount(['weeklyBills', 'payments'])
                 ->loadSum('payments', 'amount');

        $latestBill = $customer->weeklyBills()->latest()->first();
        $latestPayment = $customer->payments()->latest()->first();

        return view('masters.customers.show', compact('customer', 'latestBill', 'latestPayment'));
    }

    public function edit(Customer $customer): View
    {
        return view('masters.customers.edit', compact('customer'));
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $this->service->delete($customer);
        return redirect()->route('masters.customers.index')->with('success', 'Customer deleted.');
    }

    public function billingHistory(Customer $customer): View
    {
        $totalBilled = $customer->weeklyBills()->sum('amount');
        $bills = $customer->weeklyBills()->latest()->paginate(15);
        return view('masters.customers.billing-history', compact('customer', 'bills', 'totalBilled'));
    }

    public function paymentHistory(Customer $customer): View
    {
        $totalPaid = $customer->payments()->sum('amount');
        $payments = $customer->payments()->latest()->paginate(15);
        return view('masters.customers.payment-history', compact('customer', 'payments', 'totalPaid'));
    }

    public function downloadLedgerPdf(Customer $customer)
    {
        $bills = $customer->weeklyBills()->get()->map(fn($b) => [
            'date' => $b->period_end,
            'desc' => "Invoice #{$b->invoice_no} ({$b->items_description})",
            'debit' => $b->net_amount,
            'credit' => 0,
            'type' => 'bill'
        ]);

        $dailyBills = $customer->dailyBills()->get()->map(fn($b) => [
            'date' => $b->date,
            'desc' => "Daily Invoice #{$b->invoice_no}",
            'debit' => $b->net_amount,
            'credit' => 0,
            'type' => 'bill'
        ]);

        $payments = $customer->payments()->get()->map(fn($p) => [
            'date' => $p->date,
            'desc' => "Payment Recv ({$p->payment_mode})",
            'debit' => 0,
            'credit' => $p->amount,
            'type' => 'payment'
        ]);

        $ledger = $bills->concat($dailyBills)->concat($payments)->sortBy('date');

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('masters.customers.ledger_pdf', [
            'customer' => $customer,
            'ledger' => $ledger
        ]);

        return $pdf->download("ledger-{$customer->name}.pdf");
    }
}
