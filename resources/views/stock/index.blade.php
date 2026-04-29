@extends('layouts.app')
@section('title', 'Stock & Inventory')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Stock Inventory</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Real-time poultry stock tracking and logistics audit</p>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="secondary" size="md" href="{{ route('reports.index') }}">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></x-slot>
                Inventory Reports
            </x-button>
        </div>
    </div>

    {{-- Current Stock Summary --}}
    <x-card padding="false">
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex justify-between items-center">
            <div>
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Inventory Pulse</h2>
                <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Current live asset volume</p>
            </div>
            <x-badge variant="primary">LIVE UPDATE</x-badge>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-100 bg-slate-50/20">
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Inventory Asset</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">UOM</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Available Volume</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Log Updated</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($summaries as $summary)
                    <tr class="hover:bg-slate-50/30 transition-colors group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <p class="font-extrabold text-slate-900">{{ $summary->item_name }}</p>
                                @if($summary->current_stock < 10)
                                    <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-[8px] font-black uppercase bg-rose-50 text-rose-600 ring-1 ring-rose-100 animate-pulse">
                                        Critically Low
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-6 text-slate-500 font-bold uppercase tracking-widest text-[10px]">{{ $summary->unit }}</td>
                        <td class="px-8 py-6 text-right">
                            <p class="text-lg font-black {{ $summary->current_stock < 10 ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ number_format($summary->current_stock, 3) }}
                            </p>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                {{ $summary->last_updated->diffForHumans() }}
                            </p>
                        </td>
                    </tr>
                    @endforeach
                    @if($summaries->isEmpty())
                    <tr>
                        <td colspan="4" class="px-8 py-12 text-center text-slate-400 font-medium italic">No active stock levels recorded.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </x-card>

    {{-- Recent Movements --}}
    <x-card padding="false">
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div>
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Logistics Flow</h2>
                <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Audit trail of incoming/outgoing movements</p>
            </div>
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <input type="date" name="from" value="{{ $from }}" class="bg-white border-slate-200 rounded-xl py-2 px-4 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-primary-500/10 transition-all outline-none">
                <span class="text-slate-300 font-black text-[10px] uppercase">to</span>
                <input type="date" name="to" value="{{ $to }}" class="bg-white border-slate-200 rounded-xl py-2 px-4 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-primary-500/10 transition-all outline-none">
                <x-button variant="slate" size="sm" type="submit">Apply Filter</x-button>
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-100 bg-slate-50/20">
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Entry Date</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Flow Type</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Movement Item</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Adjustment</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Valuation</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($movements as $movement)
                    <tr class="hover:bg-slate-50/30 transition-colors">
                        <td class="px-8 py-6 text-slate-500 font-bold text-xs uppercase tracking-widest">{{ $movement->date->format('d M, Y') }}</td>
                        <td class="px-8 py-6">
                            @if($movement->type === 'purchase_in')
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 14l-7 7m0 0l-7-7m7 7V3" /></svg>
                                    Stock Incoming
                                </div>
                            @elseif($movement->type === 'sale_out')
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 10l7-7m0 0l7 7m-7-7v18" /></svg>
                                    Stock Outgoing
                                </div>
                            @else
                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider bg-slate-100 text-slate-600">
                                    Manual Adjustment
                                </div>
                            @endif
                        </td>
                        <td class="px-8 py-6">
                            <p class="font-extrabold text-slate-900">{{ $movement->item_name }}</p>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <p class="text-sm font-black {{ $movement->type === 'sale_out' ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $movement->type === 'sale_out' ? '-' : '+' }}{{ number_format($movement->quantity, 3) }} 
                                <span class="text-[9px] font-bold uppercase tracking-widest text-slate-400">{{ $movement->unit }}</span>
                            </p>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <p class="text-xs font-bold text-slate-600 font-mono italic">₹{{ number_format($movement->rate, 2) }}</p>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($movements->hasPages())
        <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/30">
            {{ $movements->appends(['from' => $from, 'to' => $to])->links() }}
        </div>
        @endif
    </x-card>
</div>
@endsection
