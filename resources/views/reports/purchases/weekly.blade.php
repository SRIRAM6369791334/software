@extends('layouts.app')
@section('title', 'Weekly Purchase Report')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-950">Weekly Purchase Report</h1>
        <p class="text-sm text-slate-500 mt-0.5">Summary for {{ \Carbon\Carbon::parse($startDate)->format('d M') }} - {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}</p>
    </div>
    <div class="flex gap-2">
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 hover:bg-emerald-50 text-slate-700 text-sm font-semibold rounded-lg shadow-sm transition-colors">
             Print
        </button>
        <a href="{{ route('reports.purchases.export-pdf', ['start' => $startDate, 'end' => $endDate]) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
             Export PDF
        </a>
    </div>
</div>

{{-- Filter Form --}}
<div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-5 rounded-xl border border-slate-200 shadow-sm mb-6">
    <form action="{{ route('reports.purchases.weekly') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Start Date</label>
            <input type="date" name="start" value="{{ $startDate }}" 
                   class="w-full px-4 py-2 bg-emerald-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all text-sm">
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">End Date</label>
            <input type="date" name="end" value="{{ $endDate }}" 
                   class="w-full px-4 py-2 bg-emerald-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all text-sm">
        </div>
        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
            Filter Period
        </button>
    </form>
</div>

{{-- Summary Tiles --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-5 rounded-xl border border-slate-200 shadow-sm">
        <span class="text-xl"></span>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Total Purchase</p>
        <p class="text-xl font-bold text-slate-950 mt-0.5">Rs {{ number_format($purchases->sum('total_amount'), 0, '.', ',') }}</p>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-5 rounded-xl border border-slate-200 shadow-sm">
        <span class="text-xl">⚖
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Total GST</p>
        <p class="text-xl font-bold text-emerald-600 mt-0.5">Rs {{ number_format($purchases->sum('gst_amount'), 0, '.', ',') }}</p>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-5 rounded-xl border border-slate-200 shadow-sm">
        <span class="text-xl"></span>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Total Qty</p>
        <p class="text-xl font-bold text-blue-600 mt-0.5">{{ $purchases->sum('quantity') }}</p>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-5 rounded-xl border border-slate-200 shadow-sm">
        <span class="text-xl"></span>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Vendors Paid</p>
        <p class="text-xl font-bold text-amber-600 mt-0.5">{{ $purchases->unique('vendor_id')->count() }}</p>
    </div>
</div>

{{-- Data Table --}}
<div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Vendor</th>
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Item Details</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Amount</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">GST</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($purchases as $purchase)
                <tr class="hover:bg-gradient-to-r from-emerald-50/80 to-sky-50/80 transition-colors">
                    <td class="px-6 py-4 font-medium text-slate-950">{{ $purchase->vendor->name ?? $purchase->vendor_name }}</td>
                    <td class="px-6 py-4 text-slate-500 font-semibold uppercase tracking-tight text-[11px]">{{ $purchase->item }}</td>
                    <td class="px-6 py-4 text-right font-mono font-bold text-slate-950">Rs {{ number_format($purchase->total_amount, 0, '.', ',') }}</td>
                    <td class="px-6 py-4 text-right font-mono text-slate-400 text-xs">Rs {{ number_format($purchase->gst_amount, 2) }}</td>
                    <td class="px-6 py-4 text-center text-slate-500 text-xs italic">{{ $purchase->date->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                        <div class="text-3xl mb-2"></div>
                        No purchase records found for this period.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

