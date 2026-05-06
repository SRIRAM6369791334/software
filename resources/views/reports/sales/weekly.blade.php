@extends('layouts.app')
@section('title', 'Weekly Performance Audit')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Weekly Performance Audit</h1>
        <p class="text-gray-500 font-medium italic">Consolidated report of weekly sales & receivables</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="window.print()" class="px-6 py-3 bg-white border border-gray-200 text-gray-500 hover:text-gray-900 text-sm font-black rounded-xl transition-all shadow-sm active:scale-95 flex items-center gap-2 uppercase tracking-widest">
            🖨️ Print View
        </button>
        <a href="{{ route('reports.sales.export-pdf', ['start' => $startDate, 'end' => $endDate]) }}" 
           class="px-6 py-3 bg-emerald-600 text-white text-sm font-black rounded-xl hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-95 flex items-center gap-2 uppercase tracking-widest">
            📜 Export PDF
        </a>
    </div>
</div>

{{-- Filter Hub --}}
<div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 mb-10">
    <form action="{{ route('reports.sales.weekly') }}" method="GET" class="flex flex-col md:flex-row items-end gap-6">
        <div class="space-y-2 flex-1">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Period Start</label>
            <input type="date" name="start" value="{{ $startDate }}" 
                   class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black text-gray-900">
        </div>
        <div class="space-y-2 flex-1">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Period End</label>
            <input type="date" name="end" value="{{ $endDate }}" 
                   class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black text-gray-900">
        </div>
        <button type="submit" class="w-full md:w-auto px-10 py-4 bg-gray-900 text-white font-black rounded-2xl hover:bg-gray-800 transition-all shadow-lg active:scale-95 uppercase tracking-widest text-sm">
            Refresh Audit
        </button>
    </form>
</div>

{{-- Strategic Insights --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 group hover:border-emerald-200 transition-all border-l-8 border-l-blue-500">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Weekly Revenue</h3>
        <p class="text-2xl font-black text-gray-900">₹{{ number_format($totalSale, 2) }}</p>
    </div>
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 group hover:border-emerald-200 transition-all border-l-8 border-l-emerald-500">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Weekly Tax (GST)</h3>
        <p class="text-2xl font-black text-emerald-600">₹{{ number_format($bills->sum('gst_amount'), 2) }}</p>
    </div>
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 group hover:border-emerald-200 transition-all border-l-8 border-l-indigo-500">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Active Accounts</h3>
        <p class="text-2xl font-black text-indigo-600">{{ $bills->unique('customer_id')->count() }}</p>
    </div>
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 group hover:border-emerald-200 transition-all border-l-8 border-l-amber-500">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Avg. Ticket Size</h3>
        <p class="text-2xl font-black text-amber-600">₹{{ number_format($bills->avg('net_amount') ?: 0, 0) }}</p>
    </div>
</div>

{{-- Data Hub --}}
<div class="bg-white rounded-[3rem] border border-gray-200 shadow-2xl overflow-hidden mb-12">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-gray-900 text-white font-black uppercase text-[10px] tracking-widest border-b border-gray-100">
                    <th class="px-8 py-6">Customer & Identification</th>
                    <th class="px-8 py-6 text-center">Audit Period</th>
                    <th class="px-8 py-6 text-right">Taxable</th>
                    <th class="px-8 py-6 text-right">GST</th>
                    <th class="px-8 py-6 text-right">Total Net</th>
                    <th class="px-8 py-6 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bills as $bill)
                    <tr class="hover:bg-emerald-50/30 transition-all group">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center font-black text-gray-500 group-hover:bg-emerald-100 group-hover:text-emerald-600 transition-all shadow-inner">
                                    {{ substr($bill->customer->name ?? '?', 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-black text-gray-900 tracking-tight">{{ $bill->customer->name ?? 'WALK-IN' }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">REF #W-{{ $bill->id }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-center font-bold text-gray-500 italic">
                            {{ $bill->period_start->format('d M') }} - {{ $bill->period_end->format('d M Y') }}
                        </td>
                        <td class="px-8 py-6 text-right font-bold text-gray-600 italic">₹{{ number_format($bill->amount ?? ($bill->net_amount - $bill->gst_amount), 2) }}</td>
                        <td class="px-8 py-6 text-right font-bold text-emerald-600">₹{{ number_format($bill->gst_amount, 2) }}</td>
                        <td class="px-8 py-6 text-right">
                            <span class="text-lg font-black text-gray-900">₹{{ number_format($bill->net_amount, 2) }}</span>
                        </td>
                        <td class="px-8 py-6 text-center">
                            @php
                                $isPaid = strtolower($bill->status) === 'paid';
                            @endphp
                            <span class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest shadow-sm
                                  {{ $isPaid ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-red-100 text-red-700 border border-red-200' }}">
                                {{ $isPaid ? '✓ PAID' : '⚠ PENDING' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center opacity-30">
                                <span class="text-7xl mb-6">🗓️</span>
                                <h3 class="text-xl font-black text-gray-900 uppercase tracking-widest">No Weekly Records</h3>
                                <p class="text-gray-500 font-medium">Select a broader range to view data</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
