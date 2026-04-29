@extends('layouts.app')
@section('title', 'Purchase Invoice #' . $purchase->id)

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-4">
            <x-button variant="ghost" size="md" href="{{ route('purchases.invoices') }}" class="!p-2">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></x-slot>
            </x-button>
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Purchase Invoice</h1>
                <p class="text-sm text-slate-500 font-medium mt-1 uppercase tracking-widest italic">Reference: #PUR{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="ghost" size="md" href="{{ route('purchases.edit', $purchase->id) }}">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></x-slot>
                Edit Entry
            </x-button>
            <x-button variant="primary" size="md" href="{{ route('purchases.print', $purchase->id) }}">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 00-2 2h2m2 4h10a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h6z" /></x-slot>
                Print Invoice
            </x-button>
        </div>
    </div>

    <div class="max-w-4xl mx-auto">
        <x-card class="relative overflow-hidden p-0 border-none shadow-2xl">
            <!-- Branded Header -->
            <div class="bg-slate-900 p-10 flex justify-between items-start text-white relative">
                <div class="absolute inset-0 opacity-10 pointer-events-none">
                    <svg class="h-full w-full" preserveAspectRatio="none" viewBox="0 0 100 100"><path d="M0 100 L100 0 L100 100 Z" fill="currentColor" /></svg>
                </div>
                
                <div class="relative space-y-6">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-primary-500 rounded-2xl flex items-center justify-center rotate-3">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        </div>
                        <span class="text-xl font-black tracking-tighter uppercase">PoultryPro</span>
                    </div>
                    
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-2">Vendor Details</p>
                        <h2 class="text-2xl font-black text-white">{{ $purchase->vendor_name }}</h2>
                        <p class="text-sm text-slate-400 font-medium mt-1 italic">Verified Supplier Entity</p>
                    </div>
                </div>

                <div class="relative text-right space-y-6">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-2">Billing Timeline</p>
                        <p class="text-lg font-black text-white">{{ $purchase->date->format('d F, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] mb-2">Channel</p>
                        <x-badge variant="primary" class="!bg-primary-500/20 !text-primary-400 border-none px-4 py-1.5">{{ $purchase->payment_mode }}</x-badge>
                    </div>
                </div>
            </div>

            <div class="p-10 space-y-12">
                <!-- Transaction Ledger -->
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] border-b border-slate-100">
                                <th class="pb-6">Asset Description</th>
                                <th class="pb-6 text-right">Volume</th>
                                <th class="pb-6 text-right">Unit Price</th>
                                <th class="pb-6 text-right">Valuation</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <tr>
                                <td class="py-8">
                                    <div class="flex items-center gap-4">
                                        <div class="w-2 h-12 bg-emerald-500 rounded-full"></div>
                                        <div>
                                            <p class="text-lg font-black text-slate-900">{{ $purchase->item }}</p>
                                            <p class="text-xs text-slate-500 font-medium italic mt-0.5">Standard Inward Procurement</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-8 text-right">
                                    <span class="text-base font-black text-slate-900">{{ number_format($purchase->quantity, 2) }}</span>
                                    <span class="text-[10px] font-black text-slate-400 uppercase ml-1">{{ $purchase->unit }}</span>
                                </td>
                                <td class="py-8 text-right text-base font-bold text-slate-600">₹{{ number_format($purchase->rate, 2) }}</td>
                                <td class="py-8 text-right text-lg font-black text-slate-900 tracking-tight">₹{{ number_format($purchase->quantity * $purchase->rate, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Financial Synthesis -->
                <div class="flex flex-col items-end pt-8 border-t border-slate-100">
                    <div class="w-full md:w-80 space-y-4">
                        <div class="flex justify-between items-center px-4">
                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">Gross Subtotal</span>
                            <span class="text-base font-bold text-slate-900">₹{{ number_format($purchase->quantity * $purchase->rate, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center px-4 py-3 bg-slate-50 rounded-2xl">
                            <span class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em]">GST Levy ({{ $purchase->gst_percentage }}%)</span>
                            <span class="text-base font-black text-slate-900">₹{{ number_format($purchase->gst_amount, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center p-6 bg-primary-500 rounded-[2rem] shadow-xl shadow-primary-500/20 text-white">
                            <span class="text-[11px] font-black uppercase tracking-[0.3em]">Net Payable</span>
                            <span class="text-3xl font-black tracking-tighter">₹{{ number_format($purchase->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Verification Seal -->
                <div class="flex items-center gap-4 py-6 px-8 bg-slate-50 rounded-[2rem] border border-slate-100/50">
                    <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center text-white">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                    </div>
                    <div>
                        <p class="text-xs font-black text-slate-900 uppercase tracking-widest">Digitally Verified Entry</p>
                        <p class="text-[10px] text-slate-500 font-medium italic mt-0.5">This invoice has been reconciled with the central ledger system.</p>
                    </div>
                </div>
            </div>
        </x-card>
    </div>
</div>
@endsection
