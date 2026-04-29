@extends('layouts.app')
@section('title', 'Dealer Ledger - ' . $dealer->firm_name)

@section('content')
<div class="space-y-8 no-print">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 border-b border-slate-100 pb-8">
        <div class="flex items-center gap-4">
            <x-button variant="ghost" size="md" href="{{ route('payments.dealers.index') }}" class="!p-2">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></x-slot>
            </x-button>
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">{{ $dealer->firm_name }}</h1>
                <p class="text-sm text-slate-500 font-medium mt-1 uppercase tracking-widest italic">Account Ledger & Statement</p>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <div class="bg-rose-50 px-6 py-4 rounded-[2rem] border border-rose-100 shadow-sm text-right">
                <p class="text-[10px] text-rose-600 font-black uppercase tracking-[0.2em] mb-1">Current Payables</p>
                <p class="text-2xl font-black text-rose-700 leading-none">₹{{ number_format($dealer->pending_amount, 2) }}</p>
            </div>
            <x-button variant="secondary" size="md" onclick="window.print()">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></x-slot>
                Print Statement
            </x-button>
        </div>
    </div>

    <!-- Dealer Meta Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-card class="relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z" /></svg>
            </div>
            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-3">Primary Connection</p>
            <p class="text-base font-black text-slate-900 leading-tight">{{ $dealer->contact_person ?: 'System Entity' }}</p>
            <p class="text-sm text-slate-500 font-bold mt-1 tracking-wide">{{ $dealer->phone }}</p>
        </x-card>

        <x-card class="relative overflow-hidden group">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
            </div>
            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mb-3">Logistics Anchor</p>
            <p class="text-base font-black text-slate-900 leading-tight">{{ $dealer->location ?: 'Universal' }}</p>
            <p class="text-sm text-slate-500 font-bold mt-1 tracking-wide italic">Route Path: {{ $dealer->route ?: 'Direct' }}</p>
        </x-card>

        <x-card class="relative overflow-hidden group border-primary-100 bg-primary-50/10">
            <div class="absolute top-0 right-0 p-4 opacity-5 group-hover:opacity-10 transition-opacity text-primary-500">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd" /></svg>
            </div>
            <p class="text-[10px] text-primary-600 font-black uppercase tracking-widest mb-3 px-2 py-0.5 bg-white rounded-full inline-block">Taxation Details</p>
            <p class="text-base font-black text-slate-900 leading-tight mt-2">{{ $dealer->gst_number ?: 'NON-GST ENTITY' }}</p>
            <p class="text-xs text-slate-400 font-bold mt-1 uppercase tracking-tighter">Internal UID: #DLR-{{ str_pad($dealer->id, 4, '0', STR_PAD_LEFT) }}</p>
        </x-card>
    </div>

    <!-- Ledger Table -->
    <x-card padding="false">
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Transaction Narrative</h2>
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Live Record</span>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-100 bg-slate-50/20">
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Post Date</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Transaction Event</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center">Reference</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right text-rose-400">Debit (+)</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right text-emerald-400">Credit (-)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 font-medium">
                    @forelse($payments as $p)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6 text-slate-500 font-bold text-xs font-mono">
                                {{ $p->date->format('d M Y') }}
                            </td>
                            <td class="px-8 py-6">
                                <p class="text-slate-900 font-extrabold uppercase tracking-tight text-xs">Payment Settlement</p>
                                @if($p->notes)
                                    <p class="text-[10px] text-slate-400 mt-1 italic font-medium">Ref: {{ $p->notes }}</p>
                                @endif
                            </td>
                            <td class="px-8 py-6 text-center font-mono text-[10px] font-bold text-slate-300">
                                PAY-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}
                            </td>
                            <td class="px-8 py-6 text-right font-mono text-slate-200">
                                —
                            </td>
                            <td class="px-8 py-6 text-right font-mono font-black text-emerald-600 text-base">
                                ₹{{ number_format($p->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-16 text-center">
                                <div class="flex flex-col items-center justify-center opacity-20">
                                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    <p class="text-sm font-black uppercase tracking-[0.3em] text-slate-900">Zero Ledger Footprint</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/30">
                {{ $payments->links() }}
            </div>
        @endif
    </x-card>
</div>

<!-- Print Only View -->
<div class="hidden print:block font-sans p-8">
    <div class="flex justify-between items-start mb-12 border-b-4 border-slate-900 pb-8">
        <div>
            <h1 class="text-4xl font-black uppercase tracking-tighter text-slate-900">Account Statement</h1>
            <p class="text-xl font-bold text-slate-500 mt-2">{{ $dealer->firm_name }}</p>
            <p class="text-sm text-slate-400 mt-1 uppercase tracking-widest font-bold">Generated on {{ date('d M Y, h:i A') }}</p>
        </div>
        <div class="text-right">
            <p class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 mb-1">Total Outstanding</p>
            <p class="text-4xl font-black text-slate-900">₹{{ number_format($dealer->pending_amount, 2) }}</p>
        </div>
    </div>
    
    <div class="grid grid-cols-2 gap-12 mb-12 border-b-2 border-slate-100 pb-8">
        <div>
            <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Dealer Information</h3>
            <p class="text-sm font-bold text-slate-700">Contact: {{ $dealer->contact_person ?: '—' }}</p>
            <p class="text-sm font-bold text-slate-700">Phone: {{ $dealer->phone }}</p>
            <p class="text-sm font-bold text-slate-700">GST: {{ $dealer->gst_number ?: 'Non-GST' }}</p>
        </div>
        <div class="text-right">
            <h3 class="text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2">Internal Reference</h3>
            <p class="text-sm font-bold text-slate-700">Entity Code: #DLR-{{ str_pad($dealer->id, 4, '0', STR_PAD_LEFT) }}</p>
            <p class="text-sm font-bold text-slate-700">Route: {{ $dealer->route ?: 'N/A' }}</p>
        </div>
    </div>

    <table class="w-full text-left border-collapse">
        <thead>
            <tr class="border-b-2 border-slate-900">
                <th class="py-4 text-[10px] font-black uppercase tracking-widest text-slate-900">Date</th>
                <th class="py-4 text-[10px] font-black uppercase tracking-widest text-slate-900">Description</th>
                <th class="py-4 text-[10px] font-black uppercase tracking-widest text-slate-900 text-right">Debit (+)</th>
                <th class="py-4 text-[10px] font-black uppercase tracking-widest text-slate-900 text-right text-emerald-600">Credit (-)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payments as $p)
                <tr class="border-b border-slate-100">
                    <td class="py-4 text-xs font-bold text-slate-600">{{ $p->date->format('d/m/Y') }}</td>
                    <td class="py-4">
                        <p class="text-xs font-black text-slate-900 uppercase">Payment Settlement</p>
                        @if($p->notes)<p class="text-[9px] text-slate-400 font-medium italic mt-0.5">{{ $p->notes }}</p>@endif
                    </td>
                    <td class="py-4 text-right text-xs font-mono text-slate-300">—</td>
                    <td class="py-4 text-right text-xs font-mono font-black text-emerald-600">₹{{ number_format($p->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="mt-20 flex justify-between items-end border-t-2 border-slate-100 pt-8">
        <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-300">End of Statement</div>
        <div class="text-center w-64 border-t border-slate-900 pt-2">
            <p class="text-[10px] font-black uppercase tracking-widest text-slate-900">Authorized Signature</p>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: white !important; font-family: 'Inter', sans-serif !important; }
    .no-print { display: none !important; }
    .print\:block { display: block !important; }
    @page { margin: 2cm; }
}
</style>
@endsection
