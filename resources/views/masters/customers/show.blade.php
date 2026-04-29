@extends('layouts.app')
@section('title', 'Customer Details')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <a href="{{ route('masters.customers.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Customers</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $customer->name }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $customer->type }} Customer | {{ $customer->route ?? 'No route assigned' }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('masters.customers.edit', $customer) }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition-all">Edit Details</a>
        <form action="{{ route('masters.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Delete this customer? This action is permanent.')">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 border border-red-100 rounded-lg text-sm font-bold hover:bg-red-100 transition-all">Delete</button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Info Card --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden p-6 space-y-4">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">Profile Information</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Contact Phone</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $customer->phone }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Addresses</p>
                    <p class="text-sm text-gray-700 break-words">{{ $customer->address ?: 'Not provided' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">GST Number</p>
                    <p class="text-sm font-mono text-gray-900">{{ $customer->gst_number ?: 'Unregistered' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-emerald-600 rounded-xl shadow-lg p-6 text-white text-center">
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-80 mb-1">Total Outstanding</p>
            <h2 class="text-3xl font-black">₹{{ number_format($customer->balance, 2) }}</h2>
            <div class="mt-4 pt-4 border-t border-white/10 flex justify-center gap-4">
                <a href="{{ route('payments.customers.create', ['customer_id' => $customer->id]) }}" class="px-4 py-1.5 bg-white text-emerald-700 text-xs font-bold rounded-lg shadow-sm hover:shadow-md transition-all">Record Payment</a>
            </div>
        </div>
    </div>

    {{-- Activity Tabs --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex border-b border-gray-100">
                <a href="{{ route('masters.customers.show', $customer) }}" class="px-6 py-4 text-sm font-bold text-emerald-600 border-b-2 border-emerald-600">Quick Overview</a>
                <a href="{{ route('masters.customers.billing-history', $customer) }}" class="px-6 py-4 text-sm font-semibold text-gray-500 hover:text-gray-900">Billing History</a>
                <a href="{{ route('masters.customers.payment-history', $customer) }}" class="px-6 py-4 text-sm font-semibold text-gray-500 hover:text-gray-900">Payment History</a>
            </div>
            
            <div class="p-6">
                <h4 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-tight">Recent Activity Insights</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Last Bill Date</p>
                        <p class="text-sm font-bold text-gray-900">{{ $customer->weeklyBills()->latest()->first()?->period_end->format('d M Y') ?? 'No bills yet' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <p class="text-[10px] font-bold text-gray-400 uppercase mb-1">Last Payment</p>
                        <p class="text-sm font-bold text-gray-900">₹{{ number_format($customer->payments()->latest()->first()?->amount ?? 0, 0) }} ({{ $customer->payments()->latest()->first()?->date->format('d M') ?? 'N/A' }})</p>
                    </div>
                </div>
                <div class="mt-8 text-center bg-gray-50/50 py-12 rounded-xl border-2 border-dashed border-gray-200">
                    <p class="text-gray-400 text-sm italic">Detailed analytics coming soon...</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
