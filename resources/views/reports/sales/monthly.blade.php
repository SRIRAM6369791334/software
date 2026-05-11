@extends('layouts.app')
<<<<<<< HEAD
@section('title', 'Monthly Financial Audit')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h1 class="text-3xl font-black text-slate-950 tracking-tight">Monthly Financial Audit</h1>
        <p class="text-slate-500 font-medium italic">High-level executive summary of monthly revenue streams</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="window.print()" class="px-6 py-3 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 text-slate-500 hover:text-slate-950 text-sm font-black rounded-xl transition-all shadow-sm active:scale-95 flex items-center gap-2 uppercase tracking-widest">
             Print View
        </button>
        <a href="{{ route('reports.sales.export-pdf', ['month' => $month, 'year' => $year]) }}" 
           class="px-6 py-3 bg-gradient-to-r from-emerald-600 to-sky-500 text-white text-sm font-black rounded-xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-600/20 active:scale-95 flex items-center gap-2 uppercase tracking-widest">
             Export PDF
=======
@section('title', 'Monthly Sales Report')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-950">Monthly Sales Report</h1>
        <p class="text-sm text-slate-500 mt-0.5">Performance summary for {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</p>
    </div>
    <div class="flex gap-2">
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 border border-slate-300 hover:bg-emerald-50 text-slate-700 text-sm font-semibold rounded-lg shadow-sm transition-colors">
             Print
        </button>
        <a href="{{ route('reports.sales.export-pdf', ['month' => $month, 'year' => $year]) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
             Export PDF
>>>>>>> 03781aa (feat: implement Logistics, Stock Analytics, and Sovereign RBAC modules with Elite Bento UI)
        </a>
    </div>
</div>

<<<<<<< HEAD
{{-- Filter Hub --}}
<div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-8 rounded-2xl border border-slate-200 shadow-md shadow-slate-200/60 mb-10">
    <form action="{{ route('reports.sales.monthly') }}" method="GET" class="flex flex-col md:flex-row items-end gap-6">
        <div class="space-y-2 flex-1">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Target Month</label>
            <select name="month" class="w-full px-5 py-4 bg-emerald-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black text-slate-950 appearance-none">
=======
{{-- Filter Form --}}
<div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-5 rounded-xl border border-slate-200 shadow-sm mb-6">
    <form action="{{ route('reports.sales.monthly') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Select Month</label>
            <select name="month" class="w-full px-4 py-2 bg-emerald-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all text-sm appearance-none">
>>>>>>> 03781aa (feat: implement Logistics, Stock Analytics, and Sovereign RBAC modules with Elite Bento UI)
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endforeach
            </select>
        </div>
<<<<<<< HEAD
        <div class="space-y-2 flex-1">
            <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Target Year</label>
            <select name="year" class="w-full px-5 py-4 bg-emerald-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black text-slate-950 appearance-none">
=======
        <div>
            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Select Year</label>
            <select name="year" class="w-full px-4 py-2 bg-emerald-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all text-sm appearance-none">
>>>>>>> 03781aa (feat: implement Logistics, Stock Analytics, and Sovereign RBAC modules with Elite Bento UI)
                @foreach(range(now()->year - 5, now()->year) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
<<<<<<< HEAD
        <button type="submit" class="w-full md:w-auto px-10 py-4 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 text-white font-black rounded-2xl hover:bg-emerald-50 transition-all shadow-lg active:scale-95 uppercase tracking-widest text-sm">
            Refresh Report
=======
        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
            Apply Filter
>>>>>>> 03781aa (feat: implement Logistics, Stock Analytics, and Sovereign RBAC modules with Elite Bento UI)
        </button>
    </form>
</div>

<<<<<<< HEAD
{{-- Strategic Insights --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-8 rounded-2xl border border-slate-200 shadow-md shadow-slate-200/60 group hover:border-emerald-200 transition-all border-l-8 border-l-blue-500">
        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Gross Monthly Sale</h3>
        <p class="text-3xl font-black text-slate-950">Rs {{ number_format($totalSale, 2) }}</p>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-8 rounded-2xl border border-slate-200 shadow-md shadow-slate-200/60 group hover:border-emerald-200 transition-all border-l-8 border-l-emerald-500">
        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Accrued GST</h3>
        <p class="text-3xl font-black text-emerald-600">Rs {{ number_format($bills->sum('gst_amount'), 2) }}</p>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-8 rounded-2xl border border-slate-200 shadow-md shadow-slate-200/60 group hover:border-emerald-200 transition-all border-l-8 border-l-indigo-500">
        <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Liquid Collections</h3>
        <p class="text-3xl font-black text-primary">Rs {{ number_format($bills->where('status', 'paid')->sum('net_amount'), 2) }}</p>
    </div>
</div>

{{-- Data Hub --}}
<div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-3xl border border-slate-200 shadow-lg overflow-hidden mb-12">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 text-white font-black uppercase text-[10px] tracking-widest border-b border-slate-200">
                    <th class="px-10 py-6">Customer Portfolio</th>
                    <th class="px-10 py-6 text-right">Aggregate Sales</th>
                    <th class="px-10 py-6 text-right">Tax Contribution</th>
                    <th class="px-10 py-6 text-right">Unrealized Due</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @php $grouped = $bills->groupBy('customer_id'); @endphp
                @forelse($grouped as $customerId => $customerBills)
                    <tr class="hover:bg-emerald-50/30 transition-all group">
                        <td class="px-10 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-sky-50 flex items-center justify-center font-black text-slate-500 group-hover:bg-emerald-100 group-hover:text-emerald-600 transition-all shadow-inner">
                                    {{ substr($customerBills->first()->customer->name ?? '?', 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-black text-slate-950 tracking-tight text-lg">{{ $customerBills->first()->customer->name ?? 'WALK-IN' }}</span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">{{ $customerBills->count() }} Transactions</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-6 text-right font-black text-slate-950 text-lg italic">Rs {{ number_format($customerBills->sum('net_amount'), 2) }}</td>
                        <td class="px-10 py-6 text-right font-bold text-emerald-600">Rs {{ number_format($customerBills->sum('gst_amount'), 2) }}</td>
                        <td class="px-10 py-6 text-right font-black text-red-600 text-lg">
                            Rs {{ number_format($customerBills->where('status', 'unpaid')->sum('net_amount'), 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-10 py-32 text-center">
                            <div class="flex flex-col items-center opacity-30">
                                <span class="text-8xl mb-6"></span>
                                <h3 class="text-2xl font-black text-slate-950 uppercase tracking-widest">No Data Available</h3>
                                <p class="text-slate-500 font-medium">Monthly revenue stream is empty for this period</p>
                            </div>
                        </td>
                    </tr>
=======
{{-- Summary Tiles --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-xl border border-slate-200 shadow-sm text-center">
        <span class="text-2xl"></span>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Total Monthly Sale</p>
        <p class="text-2xl font-extrabold text-slate-950 mt-0.5">Rs {{ number_format($totalSale, 0, '.', ',') }}</p>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-xl border border-slate-200 shadow-sm text-center">
        <span class="text-2xl"></span>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Total Monthly GST</p>
        <p class="text-2xl font-extrabold text-emerald-600 mt-0.5">Rs {{ number_format($bills->sum('gst_amount'), 0, '.', ',') }}</p>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-xl border border-slate-200 shadow-sm text-center">
        <span class="text-2xl"></span>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-2">Total Collections</p>
        <p class="text-2xl font-extrabold text-blue-600 mt-0.5">Rs {{ number_format($bills->where('status', 'paid')->sum('net_amount'), 0, '.', ',') }}</p>
    </div>
</div>

{{-- Data Table --}}
<div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-200 bg-slate-50">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Sales</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">Total GST</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">Outstanding</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @php
                    $grouped = $bills->groupBy('customer_id');
                @endphp
                @forelse($grouped as $customerId => $customerBills)
                <tr class="hover:bg-gradient-to-r from-emerald-50/80 to-sky-50/80 transition-colors">
                    <td class="px-6 py-4 font-medium text-slate-950">{{ $customerBills->first()->customer->name ?? '-' }}</td>
                    <td class="px-6 py-4 text-right font-mono font-bold text-slate-950">Rs {{ number_format($customerBills->sum('net_amount'), 0, '.', ',') }}</td>
                    <td class="px-6 py-4 text-right font-mono text-slate-400 text-xs">Rs {{ number_format($customerBills->sum('gst_amount'), 2) }}</td>
                    <td class="px-6 py-4 text-right font-mono font-bold text-rose-600">
                        Rs {{ number_format($customerBills->where('status', 'unpaid')->sum('net_amount'), 0, '.', ',') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-slate-400">
                        <div class="text-3xl mb-2"></div>
                        No sales records found for this month.
                    </td>
                </tr>
>>>>>>> 03781aa (feat: implement Logistics, Stock Analytics, and Sovereign RBAC modules with Elite Bento UI)
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

