@extends('layouts.app')
@section('title', 'Stock & Inventory')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Stock Inventory</h1>
        <p class="text-sm text-gray-500 mt-0.5">Track available poultry stock and recent movements</p>
    </div>
</div>

{{-- Current Stock Summary --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Current Available Stock</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-gray-100 bg-gray-50/20">
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Item Name</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Unit</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Current Qty</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Last Updated</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($summaries as $summary)
                <tr class="hover:bg-gray-50/30">
                    <td class="px-5 py-4 font-bold text-gray-900">
                        {{ $summary->item_name }}
                        @if($summary->current_stock < 10)
                            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                Low Stock
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-4 text-gray-500">{{ $summary->unit }}</td>
                    <td class="px-5 py-4 text-right font-black {{ $summary->current_stock < 10 ? 'text-rose-600' : 'text-emerald-600' }}">
                        {{ number_format($summary->current_stock, 3) }}
                    </td>
                    <td class="px-5 py-4 text-right text-gray-500 text-xs">
                        {{ $summary->last_updated->diffForHumans() }}
                    </td>
                </tr>
                @endforeach
                @if($summaries->isEmpty())
                <tr>
                    <td colspan="4" class="px-5 py-4 text-center text-gray-500">No stock data available.</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

{{-- Recent Movements --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Recent Movements</h3>
        <form method="GET" class="flex gap-2">
            <input type="date" name="from" value="{{ $from }}" class="text-sm border-gray-200 rounded-md">
            <input type="date" name="to" value="{{ $to }}" class="text-sm border-gray-200 rounded-md">
            <button type="submit" class="px-3 py-1 bg-gray-900 text-white rounded-md text-sm">Filter</button>
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-gray-100 bg-gray-50/20">
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Type</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Item</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Quantity</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Rate</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($movements as $movement)
                <tr class="hover:bg-gray-50/30">
                    <td class="px-5 py-4 text-gray-600">{{ $movement->date->format('Y-m-d') }}</td>
                    <td class="px-5 py-4">
                        @if($movement->type === 'purchase_in')
                            <span class="px-2 py-1 bg-blue-50 text-blue-700 text-xs rounded font-medium border border-blue-100">Purchase In</span>
                        @elseif($movement->type === 'sale_out')
                            <span class="px-2 py-1 bg-emerald-50 text-emerald-700 text-xs rounded font-medium border border-emerald-100">Sale Out</span>
                        @else
                            <span class="px-2 py-1 bg-gray-50 text-gray-700 text-xs rounded font-medium border border-gray-200">Adjustment</span>
                        @endif
                    </td>
                    <td class="px-5 py-4 font-medium text-gray-900">{{ $movement->item_name }}</td>
                    <td class="px-5 py-4 text-right font-mono {{ $movement->type === 'sale_out' ? 'text-rose-600' : 'text-emerald-600' }}">
                        {{ $movement->type === 'sale_out' ? '-' : '+' }}{{ number_format($movement->quantity, 3) }} {{ $movement->unit }}
                    </td>
                    <td class="px-5 py-4 text-right text-gray-600">₹{{ number_format($movement->rate, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">
        {{ $movements->appends(['from' => $from, 'to' => $to])->links() }}
    </div>
</div>
@endsection
