@extends('layouts.app')
@section('title', 'Stock & Inventory')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Stock Inventory</h1>
        <p class="text-gray-500 font-medium">Real-time tracking of poultry stock and inventory movements</p>
    </div>
</div>

{{-- Stock Summaries --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    @foreach($summaries as $summary)
        <div class="bg-white p-6 rounded-[2.5rem] border {{ $summary->current_stock < 10 ? 'border-red-200 shadow-red-100' : 'border-gray-100' }} shadow-xl shadow-gray-200/50 group transition-all relative overflow-hidden">
            @if($summary->current_stock < 10)
                <div class="absolute top-0 right-0 px-4 py-1 bg-red-500 text-[8px] font-black text-white uppercase tracking-widest rounded-bl-xl">Low Stock</div>
            @endif
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 rounded-2xl {{ $summary->current_stock < 10 ? 'bg-red-50 text-red-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center text-xl font-black group-hover:scale-110 transition-transform">
                    {{ substr($summary->item_name, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-0.5">{{ $summary->unit }}</h3>
                    <p class="text-sm font-black text-gray-900 truncate max-w-[120px]">{{ $summary->item_name }}</p>
                </div>
            </div>
            <div class="flex items-end justify-between">
                <p class="text-3xl font-black {{ $summary->current_stock < 10 ? 'text-red-600' : 'text-gray-900' }} tracking-tighter">
                    {{ number_format($summary->current_stock, 1) }}
                </p>
                <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $summary->last_updated->diffForHumans() }}</span>
            </div>
        </div>
    @endforeach
</div>

{{-- Movement History --}}
<div class="bg-white rounded-[2.5rem] border border-gray-200 shadow-2xl overflow-hidden mb-12">
    <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gray-50/50">
        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Inventory Log & Movements</h3>
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-400 uppercase tracking-widest">From</span>
                <input type="date" name="from" value="{{ $from }}" class="pl-14 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none text-sm font-bold">
            </div>
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-gray-400 uppercase tracking-widest">To</span>
                <input type="date" name="to" value="{{ $to }}" class="pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500 outline-none text-sm font-bold">
            </div>
            <button type="submit" class="px-6 py-3 bg-gray-900 text-white text-xs font-black rounded-xl hover:bg-gray-800 transition-all uppercase tracking-widest">Apply Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-gray-50/50 text-gray-400 font-black uppercase text-[10px] tracking-widest border-b border-gray-100">
                    <th class="px-8 py-5">Timestamp</th>
                    <th class="px-8 py-5">Event Type</th>
                    <th class="px-8 py-5">Resource Item</th>
                    <th class="px-8 py-5 text-right">Volume Adjustment</th>
                    <th class="px-8 py-5 text-right">Unit Rate</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($movements as $movement)
                    <tr class="hover:bg-gray-50/30 transition-all">
                        <td class="px-8 py-5">
                            <span class="font-black text-gray-900 tracking-tighter">{{ $movement->date->format('M d, Y') }}</span>
                        </td>
                        <td class="px-8 py-5">
                            @php
                                $typeMap = [
                                    'purchase_in' => ['label' => 'INBOUND', 'class' => 'bg-blue-50 text-blue-600 border-blue-100', 'icon' => '📥'],
                                    'sale_out' => ['label' => 'OUTBOUND', 'class' => 'bg-emerald-50 text-emerald-600 border-emerald-100', 'icon' => '📤'],
                                    'adjustment' => ['label' => 'ADJUSTMENT', 'class' => 'bg-amber-50 text-amber-600 border-amber-100', 'icon' => '⚙️']
                                ];
                                $style = $typeMap[$movement->type] ?? $typeMap['adjustment'];
                            @endphp
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 {{ $style['class'] }} border text-[9px] font-black rounded-lg uppercase tracking-tighter">
                                <span>{{ $style['icon'] }}</span>
                                {{ $style['label'] }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <span class="font-bold text-gray-700 tracking-tight">{{ $movement->item_name }}</span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <span class="text-base font-black {{ $movement->type === 'sale_out' ? 'text-red-500' : 'text-emerald-500' }}">
                                {{ $movement->type === 'sale_out' ? '-' : '+' }}{{ number_format($movement->quantity, 2) }}
                                <span class="text-[10px] text-gray-400 ml-1">{{ strtoupper($movement->unit) }}</span>
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right font-black text-gray-900">
                            ₹{{ number_format($movement->rate, 2) }}
                        </td>
                    </tr>
                @endforeach
                @if($movements->isEmpty())
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="text-6xl mb-6">📉</div>
                                <h3 class="text-xl font-black text-gray-900">No Movements Recorded</h3>
                                <p class="text-gray-400 font-medium mt-1">Transaction history for the selected period is empty.</p>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
    @if($movements->hasPages())
        <div class="p-8 border-t border-gray-50 bg-gray-50/30">
            {{ $movements->appends(['from' => $from, 'to' => $to])->links() }}
        </div>
    @endif
</div>
@endsection
