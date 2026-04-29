@extends('layouts.app')
@section('title', 'Create Daily Invoice')

@section('content')
<div class="mb-6">
    <a href="{{ route('billing.daily.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Daily Billing</a>
    <h1 class="text-2xl font-bold text-gray-900">Create Daily Invoice</h1>
</div>

<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('billing.daily.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Customer <span class="text-red-500">*</span></label>
                        <select name="customer_id" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->route }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Billing Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Items Description</label>
                    <input type="text" name="items_description" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="e.g. Broiler Birds (Small Size)">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Quantity (kg)</label>
                        <input type="number" name="quantity_kg" id="qty" step="0.01" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="0.00">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Rate per kg (₹)</label>
                        <input type="number" name="rate_per_kg" id="rate" step="0.01" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="0.00">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Total Amount (₹) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" id="total" step="0.01" required class="w-full px-4 py-2 bg-emerald-50 border border-emerald-200 text-emerald-900 font-bold rounded-lg focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Payment Status</label>
                    <div class="flex gap-4">
                        @foreach(['Generated', 'Pending', 'Paid'] as $st)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="status" value="{{ $st }}" {{ $st === 'Generated' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                            <span class="text-sm font-medium text-gray-600 group-hover:text-gray-900">{{ $st }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                <button type="submit" class="px-8 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-md transition-all active:scale-95">
                    Generate Invoice 📤
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const qty = document.getElementById('qty');
    const rate = document.getElementById('rate');
    const total = document.getElementById('total');

    function calculate() {
        const q = parseFloat(qty.value) || 0;
        const r = parseFloat(rate.value) || 0;
        if (q && r) {
            total.value = (q * r).toFixed(2);
        }
    }

    qty.addEventListener('input', calculate);
    rate.addEventListener('input', calculate);
</script>
@endsection
