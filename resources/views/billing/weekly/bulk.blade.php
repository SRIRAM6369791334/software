@extends('layouts.app')
@section('title', 'Bulk Weekly Billing')

@section('content')
<div class="mb-6">
    <a href="{{ route('billing.weekly.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Weekly Billing</a>
    <h1 class="text-2xl font-bold text-gray-900">Bulk Billing Generation</h1>
    <p class="text-sm text-gray-500 mt-0.5">Select multiple customers to generate weekly bills in one click</p>
</div>

<div class="max-w-4xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('billing.weekly.bulkStore') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2">1. Billing Period</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">Start Date</label>
                            <input type="date" name="period_start" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">End Date</label>
                            <input type="date" name="period_end" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2">2. Default Values</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">Flat Amount (₹)</label>
                            <input type="number" name="amount" step="0.01" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="0.00">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">Initial Status</label>
                            <select name="status" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                                <option value="Generated">Generated</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">3. Select Customers</h3>
                    <button type="button" onclick="toggleAll(this)" class="text-[10px] font-bold text-emerald-600 hover:text-emerald-700 uppercase">Select All</button>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 max-h-64 overflow-y-auto p-1">
                    @foreach($customers as $customer)
                    <label class="flex items-center gap-3 p-3 bg-gray-50 hover:bg-emerald-50 border border-gray-200 hover:border-emerald-200 rounded-lg cursor-pointer transition-all group">
                        <input type="checkbox" name="customer_ids[]" value="{{ $customer->id }}" class="customer-checkbox w-4 h-4 text-emerald-600 rounded border-gray-300 focus:ring-emerald-500 transition-all">
                        <div>
                            <p class="text-sm font-bold text-gray-900 group-hover:text-emerald-900">{{ $customer->name }}</p>
                            <p class="text-[10px] text-gray-500">{{ $customer->route ?: 'No Route' }}</p>
                        </div>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 flex justify-end gap-3">
                <button type="submit" class="w-full sm:w-auto px-10 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition-all transform hover:-translate-y-0.5 active:translate-y-0">
                    Run Bulk Generation ⚡
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleAll(btn) {
        const checkboxes = document.querySelectorAll('.customer-checkbox');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
        btn.textContent = allChecked ? 'Select All' : 'Deselect All';
    }
</script>
@endsection
