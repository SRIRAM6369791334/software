@extends('layouts.app')
@section('title', 'Daily Sales Report')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Daily Sales Report</h1>
        <p class="text-gray-500 font-medium italic">Audit of all daily retail bird sales & feed transactions</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="window.print()" class="px-6 py-3 bg-white border border-gray-200 text-gray-500 hover:text-gray-900 text-sm font-black rounded-xl transition-all shadow-sm active:scale-95 flex items-center gap-2 uppercase tracking-widest">
            🖨️ Print View
        </button>
        <a href="{{ route('reports.sales.export-pdf', ['date' => $date]) }}" 
           class="px-6 py-3 bg-emerald-600 text-white text-sm font-black rounded-xl hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-95 flex items-center gap-2 uppercase tracking-widest">
            📜 Export PDF
        </a>
    </div>
</div>

{{-- Filter Hub --}}
<div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 mb-10">
    <form action="{{ route('reports.sales.daily') }}" method="GET" class="flex flex-col md:flex-row items-end gap-6">
        <div class="space-y-2 flex-1">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Target Report Date</label>
            <div class="relative">
                <input type="date" name="date" value="{{ $date }}" 
                       class="w-full pl-5 pr-12 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black text-gray-900">
                <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xl opacity-30">📅</span>
            </div>
        </div>
        <button type="submit" class="w-full md:w-auto px-10 py-4 bg-gray-900 text-white font-black rounded-2xl hover:bg-gray-800 transition-all shadow-lg active:scale-95 uppercase tracking-widest text-sm">
            Generate Audit
        </button>
    </form>
</div>

{{-- Dynamic Insights Cards --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 group hover:border-emerald-200 transition-all border-l-8 border-l-blue-500">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Daily Revenue</h3>
        <p class="text-2xl font-black text-gray-900">₹{{ number_format($totalSale, 2) }}</p>
    </div>
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 group hover:border-emerald-200 transition-all border-l-8 border-l-emerald-500">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Sales Tax (GST)</h3>
        <p class="text-2xl font-black text-emerald-600">₹{{ number_format($dailyBills->sum('gst_amount'), 2) }}</p>
    </div>
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 group hover:border-emerald-200 transition-all border-l-8 border-l-indigo-500">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Cash Realized</h3>
        <p class="text-2xl font-black text-indigo-600">₹{{ number_format($cashSales, 2) }}</p>
    </div>
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 group hover:border-emerald-200 transition-all border-l-8 border-l-amber-500">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Credit Extended</h3>
        <p class="text-2xl font-black text-amber-600">₹{{ number_format($creditSales, 2) }}</p>
    </div>
</div>

{{-- Detailed Data Table --}}
<div class="bg-white rounded-[3rem] border border-gray-200 shadow-2xl overflow-hidden mb-12">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-gray-900 text-white font-black uppercase text-[10px] tracking-widest border-b border-gray-100">
                    <th class="px-8 py-6">Customer & Route</th>
                    <th class="px-8 py-6 text-right">Taxable Amt</th>
                    <th class="px-8 py-6 text-right">GST Component</th>
                    <th class="px-8 py-6 text-right">Net Receivable</th>
                    <th class="px-8 py-6 text-center">Payment Mode</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($dailyBills as $bill)
                    <tr class="hover:bg-emerald-50/30 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center font-black text-gray-500 group-hover:bg-emerald-100 group-hover:text-emerald-600 transition-all shadow-inner">
                                    {{ substr($bill->customer->name ?? '?', 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-black text-gray-900 tracking-tight">{{ $bill->customer->name ?? 'WALK-IN' }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $bill->customer->route ?? 'General' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right font-bold text-gray-600 italic">₹{{ number_format($bill->amount, 2) }}</td>
                        <td class="px-8 py-6 text-right font-bold text-emerald-600">₹{{ number_format($bill->gst_amount, 2) }}</td>
                        <td class="px-8 py-6 text-right">
                            <span class="text-lg font-black text-gray-900">₹{{ number_format($bill->net_amount, 2) }}</span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @php
                                $isCash = strtolower($bill->payment_mode) === 'cash';
                            @endphp
                            <span class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm
                                  {{ $isCash ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-amber-100 text-amber-700 border border-amber-200' }}">
                                {{ $isCash ? '💸 CASH' : '💳 CREDIT' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center opacity-30">
                                <span class="text-7xl mb-6">📉</span>
                                <h3 class="text-xl font-black text-gray-900 uppercase tracking-widest">No Sales Found</h3>
                                <p class="text-gray-500 font-medium">Select another date to view transaction audit</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
