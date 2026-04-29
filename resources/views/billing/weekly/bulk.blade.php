@extends('layouts.app')
@section('title', 'Bulk Weekly Billing')

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('billing.weekly.index') }}" class="text-[10px] font-black text-primary-500 uppercase tracking-[0.2em] mb-2 flex items-center gap-2 hover:gap-3 transition-all">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to Billing
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Bulk Billing Generation</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Select multiple customers to generate weekly bills in one click</p>
        </div>
    </div>

    <form action="{{ route('billing.weekly.bulkStore') }}" method="POST">
        @csrf
        <div class="space-y-8">
            <x-card padding="false">
                <div class="p-8 lg:p-10">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-12 mb-12">
                        <div class="space-y-6">
                            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center text-[10px]">01</span>
                                Billing Period
                            </h3>
                            <div class="grid grid-cols-2 gap-6">
                                <x-input label="Start Date *" type="date" name="period_start" required />
                                <x-input label="End Date *" type="date" name="period_end" required />
                            </div>
                        </div>

                        <div class="space-y-6">
                            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center text-[10px]">02</span>
                                Default Values
                            </h3>
                            <div class="grid grid-cols-2 gap-6">
                                <x-input label="Flat Amount (₹) *" type="number" name="amount" step="0.01" required placeholder="0.00" />
                                <div class="space-y-2">
                                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-1">Initial Status</label>
                                    <select name="status" class="w-full bg-slate-50 border-slate-200 rounded-2xl py-3 px-5 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all">
                                        <option value="Generated">Generated</option>
                                        <option value="Pending">Pending</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-6">
                        <div class="flex justify-between items-end border-b border-slate-100 pb-4">
                            <h3 class="text-[11px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                <span class="w-6 h-6 rounded-lg bg-primary-100 text-primary-600 flex items-center justify-center text-[10px]">03</span>
                                Select Target Customers
                            </h3>
                            <button type="button" onclick="toggleAll(this)" class="text-[10px] font-black text-primary-500 hover:text-primary-600 uppercase tracking-wider">Select All</button>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 max-h-[400px] overflow-y-auto pr-4 custom-scroll">
                            @foreach($customers as $customer)
                            <label class="flex items-center gap-4 p-4 bg-slate-50 hover:bg-white border border-slate-100 hover:border-primary-200 rounded-[1.5rem] cursor-pointer transition-all group hover:shadow-xl hover:shadow-primary-500/5">
                                <input type="checkbox" name="customer_ids[]" value="{{ $customer->id }}" class="customer-checkbox w-5 h-5 text-primary-500 rounded-lg border-slate-300 focus:ring-primary-500/20 transition-all">
                                <div class="min-w-0">
                                    <p class="text-sm font-black text-slate-900 group-hover:text-primary-600 truncate transition-colors">{{ $customer->name }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $customer->route ?: 'Standard Route' }}</p>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="px-8 lg:px-10 py-8 bg-slate-50/50 border-t border-slate-100">
                    <div class="flex items-center justify-between gap-6">
                        <div class="hidden md:block">
                            <p class="text-sm text-slate-500 font-medium">Bulk processing will create multiple individual invoices.</p>
                        </div>
                        <x-button variant="primary" size="lg" type="submit" class="w-full md:w-auto min-w-[240px] shadow-2xl shadow-primary-500/20">
                            Run Bulk Generation ⚡
                        </x-button>
                    </div>
                </div>
            </x-card>
        </div>
    </form>
</div>

@push('scripts')
<script>
    function toggleAll(btn) {
        const checkboxes = document.querySelectorAll('.customer-checkbox');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
        btn.textContent = allChecked ? 'Select All' : 'Deselect All';
    }
</script>
@endpush
@endsection
