@extends('layouts.app')
@section('title', 'Statement #' . ($bill->invoice_number ?? $bill->id))

@section('content')
<div class="max-w-4xl mx-auto my-8 no-print flex justify-end gap-3 mb-6">
    <x-button variant="secondary" size="md" onclick="window.close()">
        <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></x-slot>
        Close Preview
    </x-button>
    <x-button variant="primary" size="md" onclick="window.print()">
        <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></x-slot>
        Print Statement
    </x-button>
</div>

<div class="max-w-4xl mx-auto bg-white p-12 shadow-2xl rounded-[3rem] border border-slate-100 relative overflow-hidden" id="invoice-print">
    <!-- Brand Watermark -->
    <div class="absolute -right-20 -top-20 w-96 h-96 bg-primary-500/5 rounded-full blur-3xl pointer-events-none"></div>
    
    <!-- Header -->
    <div class="flex justify-between items-start border-b border-slate-100 pb-10 mb-10 relative z-10">
        <div>
            <div class="flex items-center gap-3 mb-4">
                <div class="w-12 h-12 bg-slate-900 rounded-2xl flex items-center justify-center text-primary-500 shadow-xl rotate-3">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                </div>
                <h1 class="text-3xl font-black text-slate-900 tracking-tighter">Flockwise <span class="text-primary-500 italic">BizTrack</span></h1>
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Premium Poultry Operations Ledger</p>
        </div>
        <div class="text-right">
            <h2 class="text-4xl font-black text-slate-900 tracking-tighter uppercase opacity-10 mb-2">Statement</h2>
            <div class="space-y-1">
                <p class="text-sm font-black text-slate-900">Ref: <span class="text-primary-600">#INV-{{ str_pad($bill->id, 6, '0', STR_PAD_LEFT) }}</span></p>
                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">{{ date('d F, Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Client Info -->
    <div class="grid grid-cols-2 gap-16 mb-16 relative z-10">
        <div class="space-y-6">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Invoiced To</p>
                <h3 class="text-2xl font-black text-slate-900 mb-2">{{ $bill->customer->name ?? 'Internal Recipient' }}</h3>
                <div class="space-y-1 text-sm font-bold text-slate-500 leading-relaxed">
                    <p>{{ $bill->customer->address ?? 'Administrative Registry Record' }}</p>
                    <p class="text-slate-900">Phone: {{ $bill->customer->phone ?? 'N/A' }}</p>
                </div>
            </div>
            @if($bill->customer->gst_number)
                <div class="inline-block px-4 py-2 bg-slate-50 rounded-xl border border-slate-100">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">GSTIN Registry</p>
                    <p class="text-xs font-black text-slate-900 font-mono">{{ $bill->customer->gst_number }}</p>
                </div>
            @endif
        </div>
        <div class="text-right space-y-8">
            <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Billing Cycle</p>
                <div class="inline-flex flex-col items-end gap-2">
                    <span class="px-6 py-2 bg-slate-900 text-white rounded-2xl text-xs font-black tracking-widest uppercase italic shadow-lg shadow-slate-200">
                        {{ $bill->period_start->format('d M') }} — {{ $bill->period_end->format('d M, Y') }}
                    </span>
                    <x-badge variant="{{ $bill->status == 'Paid' ? 'success' : 'amber' }}" class="px-5 py-1.5 rounded-full text-[10px] italic">
                        Payment Status: {{ $bill->status }}
                    </x-badge>
                </div>
            </div>
        </div>
    </div>

    <!-- Table Section -->
    <div class="mb-16 border border-slate-100 rounded-[2.5rem] overflow-hidden shadow-sm relative z-10">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-slate-50/50 text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] border-b border-slate-100">
                    <th class="px-10 py-6">Description of Goods/Services</th>
                    <th class="px-10 py-6 text-right">Volume (Yield)</th>
                    <th class="px-10 py-6 text-right">Unit Rate</th>
                    <th class="px-10 py-6 text-right">Line Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <tr class="group">
                    <td class="px-10 py-8">
                        <p class="text-base font-black text-slate-900 mb-1.5">{{ $bill->items_description ?: 'Standard Poultry Yield Liquidation' }}</p>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest italic">Weekly Cycle Aggregate Supply</p>
                    </td>
                    <td class="px-10 py-8 text-right font-black text-slate-900">
                        {{ number_format($bill->quantity_kg, 2) }} <span class="text-[10px] text-slate-400">KG</span>
                    </td>
                    <td class="px-10 py-8 text-right font-bold text-slate-500">
                        ₹{{ number_format($bill->amount / max(1, $bill->quantity_kg), 2) }}
                    </td>
                    <td class="px-10 py-8 text-right font-black text-slate-900 text-lg tracking-tight">
                        ₹{{ number_format($bill->amount, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Totals -->
    <div class="flex justify-between items-end relative z-10">
        <div class="max-w-xs">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4 italic">Authorization & Notes</p>
            <div class="space-y-4">
                <p class="text-[11px] font-bold text-slate-500 italic leading-relaxed">
                    This statement is generated electronically via BizTrack Poultry ERP. Please verify the yields and rates within 24 hours of receipt.
                </p>
                <div class="h-px w-32 bg-slate-100"></div>
                <p class="text-[10px] font-black text-slate-900 uppercase tracking-widest">Digital Auth Code: <span class="font-mono text-primary-600">FW-{{ time() }}</span></p>
            </div>
        </div>
        <div class="w-80 space-y-4">
            <div class="flex justify-between items-center px-6 text-sm">
                <span class="text-slate-400 font-bold uppercase tracking-widest">Subtotal Value</span>
                <span class="text-slate-900 font-black">₹{{ number_format($bill->amount, 2) }}</span>
            </div>
            <div class="flex justify-between items-center px-6 text-sm">
                <span class="text-slate-400 font-bold uppercase tracking-widest">Tax Provision (0%)</span>
                <span class="text-slate-900 font-black">₹0.00</span>
            </div>
            <div class="bg-slate-900 rounded-[2rem] p-8 text-white shadow-2xl shadow-slate-200 rotate-1 group hover:rotate-0 transition-transform duration-500">
                <div class="flex justify-between items-center mb-1">
                    <span class="text-[10px] font-black uppercase tracking-[0.3em] opacity-50 italic">Statement Total</span>
                </div>
                <div class="flex justify-between items-end">
                    <span class="text-3xl font-black tracking-tighter italic">₹{{ number_format($bill->amount, 2) }}</span>
                    <span class="text-[10px] font-black uppercase tracking-widest mb-1.5">INR</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="mt-20 pt-10 border-t border-slate-50 text-center relative z-10">
        <p class="text-sm font-black text-slate-900 tracking-tight mb-2">Thank you for partnering with Flockwise BizTrack</p>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] italic">Precision Poultry Management Systems</p>
    </div>
</div>

<style>
@media print {
    .no-print, nav, aside, header { display: none !important; }
    body { background: white !important; margin: 0 !important; padding: 0 !important; }
    #invoice-print { 
        margin: 0 !important; 
        padding: 40px !important; 
        border: none !important; 
        box-shadow: none !important; 
        width: 100% !important; 
        max-width: none !important; 
        border-radius: 0 !important;
    }
    .shadow-2xl, .shadow-xl, .shadow-lg, .shadow-sm { box-shadow: none !important; }
}
</style>
@endsection
