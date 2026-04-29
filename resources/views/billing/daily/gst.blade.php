@extends('layouts.app')
@section('title', 'GST Billing Overview')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <x-button variant="ghost" size="md" href="{{ route('billing.daily.index') }}" class="!p-2">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></x-slot>
            </x-button>
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Tax Compliance</h1>
                <p class="text-sm text-slate-500 font-medium mt-1 uppercase tracking-widest italic">GST Reporting & Audit Logs</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="secondary" size="md">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2a4 4 0 10-8 0v2h8zm0 0V9a2 2 0 00-2-2H5a2 2 0 00-2 2v8h6z" /></x-slot>
                GSTR-1 Helper
            </x-button>
            <x-button variant="primary" size="md">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></x-slot>
                Download Excel
            </x-button>
        </div>
    </div>

    <!-- Tax Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $totalAmount = $bills->sum('amount');
            $taxableValue = $totalAmount / 1.05;
            $gstAmount = $totalAmount - $taxableValue;
        @endphp
        <x-card class="bg-slate-900 border-none relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-white/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Aggregate Tax Liability</p>
            <h3 class="text-3xl font-black text-white tracking-tighter">₹{{ number_format($gstAmount, 2) }}</h3>
            <p class="text-[10px] font-bold text-primary-400 mt-2">Based on 5% Inclusive Rate</p>
        </x-card>
        <x-card class="relative overflow-hidden group border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Net Taxable Revenue</p>
            <h3 class="text-3xl font-black text-slate-900 tracking-tighter">₹{{ number_format($taxableValue, 2) }}</h3>
        </x-card>
        <x-card class="relative overflow-hidden group border-slate-100 shadow-sm">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Total Gross Billing</p>
            <h3 class="text-3xl font-black text-slate-900 tracking-tighter">₹{{ number_format($totalAmount, 2) }}</h3>
        </x-card>
    </div>

    <!-- Table Section -->
    <x-card padding="false" class="overflow-hidden border-slate-100 shadow-xl">
        <div class="px-8 py-6 border-b border-slate-50 bg-slate-50/30 flex justify-between items-center">
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Taxable Invoice Ledger</h3>
            <x-badge variant="primary" class="rounded-full px-4 italic font-black">5% Poultry Slab</x-badge>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="text-left border-b border-slate-50 bg-slate-50/20">
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Reporting Date</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Customer Entity / GSTIN</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Description</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Taxable (95%)</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Output GST (5%)</th>
                        <th class="px-8 py-4 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Invoice Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($bills as $bill)
                        @php
                            $taxable = $bill->amount / 1.05;
                            $tax = $bill->amount - $taxable;
                        @endphp
                        <tr class="hover:bg-slate-50/50 transition-all group">
                            <td class="px-8 py-5">
                                <span class="font-black text-slate-900">{{ $bill->date->format('d M, Y') }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <div class="space-y-1">
                                    <p class="font-black text-slate-800 leading-none">{{ $bill->customer->name }}</p>
                                    <p class="text-[10px] text-primary-600 font-black tracking-widest mt-1.5">{{ $bill->customer->gst_number ?: 'UNREGISTERED' }}</p>
                                </div>
                            </td>
                            <td class="px-8 py-5">
                                <span class="text-xs font-bold text-slate-500 italic">{{ $bill->items_description ?: 'Farm Asset Liquidation' }}</span>
                            </td>
                            <td class="px-8 py-5 text-right font-black text-slate-600">
                                ₹{{ number_format($taxable, 2) }}
                            </td>
                            <td class="px-8 py-5 text-right">
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-xs font-black rounded-lg">
                                    ₹{{ number_format($tax, 2) }}
                                </span>
                            </td>
                            <td class="px-8 py-5 text-right font-black text-slate-900 text-base">
                                ₹{{ number_format($bill->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-24 text-center">
                                <div class="flex flex-col items-center justify-center opacity-30">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    <p class="text-xl font-black text-slate-900 tracking-tight uppercase">No Taxable Events</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-6 border-t border-slate-50 bg-slate-50/10">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center italic">
                Verified Calculation: All values are derived using the 5% Inclusive GST formula as per poultry industry standards.
            </p>
        </div>
    </x-card>
</div>
@endsection
