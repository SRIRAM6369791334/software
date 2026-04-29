@extends('layouts.app')
@section('title', 'Bulk Weekly Billing')

@section('content')
<div class="space-y-8 max-w-6xl mx-auto">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <x-button variant="ghost" size="md" href="{{ route('billing.weekly.index') }}" class="mb-4 !p-2">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></x-slot>
            </x-button>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Bulk Operations</h1>
            <p class="text-sm text-slate-500 font-medium mt-1 uppercase tracking-widest italic">Industrial Scale Billing Engine</p>
        </div>
        <div class="flex items-center gap-3 bg-slate-100 p-2 rounded-2xl">
            <div class="px-4 py-2 bg-white rounded-xl shadow-sm">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Available Entities</p>
                <p class="text-lg font-black text-slate-900 leading-none">{{ count($customers) }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('billing.weekly.bulkStore') }}" method="POST">
        @csrf
        <div class="space-y-8">
            <x-card padding="false" class="overflow-hidden shadow-2xl border-slate-100">
                <div class="p-10 lg:p-14">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-16 mb-16">
                        <!-- Step 01 -->
                        <div class="space-y-8">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-slate-900 text-primary-500 flex items-center justify-center text-sm font-black shadow-lg rotate-3">01</div>
                                <div>
                                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Billing Horizon</h3>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Define the statement period</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-8">
                                <x-input label="Cycle Start Date *" type="date" name="period_start" required />
                                <x-input label="Cycle End Date *" type="date" name="period_end" required />
                            </div>
                        </div>

                        <!-- Step 02 -->
                        <div class="space-y-8">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-slate-900 text-primary-500 flex items-center justify-center text-sm font-black shadow-lg -rotate-3">02</div>
                                <div>
                                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Financial Blueprint</h3>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Default value propagation</p>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-8">
                                <x-input label="Flat Settlement (₹) *" type="number" name="amount" step="0.01" required placeholder="0.00" />
                                <div class="space-y-2">
                                    <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest px-1 italic">Initial State</label>
                                    <select name="status" class="w-full bg-slate-50 border-slate-200 rounded-[1.5rem] py-4 px-6 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all border appearance-none">
                                        <option value="Generated">Generated (Draft)</option>
                                        <option value="Pending">Pending (Active)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 03 -->
                    <div class="space-y-8">
                        <div class="flex justify-between items-center border-b border-slate-50 pb-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-slate-900 text-primary-500 flex items-center justify-center text-sm font-black shadow-lg">03</div>
                                <div>
                                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Entity Selection</h3>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Target clients for statement run</p>
                                </div>
                            </div>
                            <x-button variant="ghost" size="sm" type="button" onclick="toggleAll(this)" id="select-all-btn" class="rounded-xl border border-slate-100 font-black">
                                Select All Entities
                            </x-button>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 max-h-[500px] overflow-y-auto pr-4 custom-scroll">
                            @foreach($customers as $customer)
                            <label class="flex items-center gap-5 p-5 bg-slate-50/50 hover:bg-white border border-transparent hover:border-slate-100 rounded-[2rem] cursor-pointer transition-all group hover:shadow-2xl hover:shadow-slate-200/50 relative overflow-hidden">
                                <div class="absolute inset-0 bg-primary-500/0 group-has-[:checked]:bg-primary-500/5 transition-colors"></div>
                                <input type="checkbox" name="customer_ids[]" value="{{ $customer->id }}" class="customer-checkbox w-6 h-6 text-primary-500 rounded-lg border-slate-300 focus:ring-primary-500/20 transition-all relative z-10">
                                <div class="min-w-0 relative z-10">
                                    <p class="text-sm font-black text-slate-900 group-hover:text-primary-600 truncate transition-colors">{{ $customer->name }}</p>
                                    <div class="flex items-center gap-1.5 mt-1">
                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-300"></div>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $customer->route ?: 'Standard Logistics' }}</p>
                                    </div>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="px-10 lg:px-14 py-10 bg-slate-900 border-t border-slate-800">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-8">
                        <div>
                            <p class="text-sm text-slate-400 font-bold italic tracking-tight">The engine will process selected entities and generate unique ledger entries.</p>
                            <p class="text-[10px] text-primary-500 font-black uppercase tracking-[0.2em] mt-1">Double check period dates before proceeding.</p>
                        </div>
                        <x-button variant="primary" size="lg" type="submit" class="w-full md:w-auto min-w-[320px] py-5 rounded-[2rem] shadow-primary-500/20">
                            <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></x-slot>
                            Execute Statement Run
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
        btn.textContent = allChecked ? 'Select All Entities' : 'Deselect All Entities';
        btn.classList.toggle('bg-primary-500', !allChecked);
        btn.classList.toggle('text-white', !allChecked);
    }
</script>
@endpush
@endsection
