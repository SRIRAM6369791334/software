@extends('layouts.app')
@section('title', 'Record Customer Payment')

@section('content')
<div class="mb-6">
    <a href="{{ route('payments.customers.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Payments</a>
    <h1 class="text-2xl font-bold text-gray-900">Record Customer Payment</h1>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('payments.customers.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div class="space-y-1.5 flex flex-col">
                <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Select Customer <span class="text-red-500">*</span></label>
                <select name="customer_id" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    <option value="">Choose a customer...</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ $selected_customer_id == $customer->id ? 'selected' : '' }}>
                            {{ $customer->name }} (Balance: ₹{{ number_format($customer->balance, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5 flex flex-col">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Payment Date <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="space-y-1.5 flex flex-col">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Amount Received (₹) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" step="0.01" required class="w-full px-4 py-2 bg-emerald-50 border border-emerald-200 text-emerald-900 font-bold rounded-lg focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all" placeholder="0.00">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5 flex flex-col">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Payment Mode</label>
                    <select name="payment_mode" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                        <option value="Cash">Cash</option>
                        <option value="UPI">UPI</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="Cheque">Cheque</option>
                    </select>
                </div>
                <div class="space-y-1.5 flex flex-col">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Entry Type</label>
                    <select name="payment_type" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                        <option value="Regular">Regular Payment</option>
                        <option value="Adjustment">Balance Adjustment</option>
                        <option value="Opening">Opening Balance Payment</option>
                    </select>
                </div>
            </div>

            <div class="space-y-1.5 flex flex-col">
                <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Notes / Reference</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="Optional notes..."></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="reset" class="px-5 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800 transition-colors">Reset</button>
                <button type="submit" class="px-8 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-sm transition-all">
                    Save Payment 🧾
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
