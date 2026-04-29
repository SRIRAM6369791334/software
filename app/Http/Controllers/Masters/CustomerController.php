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
        return view('masters.customers.show', compact('customer'));
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
        $bills = $customer->weeklyBills()->latest()->paginate(15);
        return view('masters.customers.billing-history', compact('customer', 'bills'));
    }

    public function paymentHistory(Customer $customer): View
    {
        $payments = $customer->payments()->latest()->paginate(15);
        return view('masters.customers.payment-history', compact('customer', 'payments'));
    }
}
