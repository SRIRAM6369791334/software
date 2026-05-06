@extends('layouts.app')
@section('title', 'Monthly Financial Audit')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Monthly Financial Audit</h1>
        <p class="text-gray-500 font-medium italic">High-level executive summary of monthly revenue streams</p>
    </div>
    <div class="flex items-center gap-3">
        <button onclick="window.print()" class="px-6 py-3 bg-white border border-gray-200 text-gray-500 hover:text-gray-900 text-sm font-black rounded-xl transition-all shadow-sm active:scale-95 flex items-center gap-2 uppercase tracking-widest">
            🖨️ Print View
        </button>
        <a href="{{ route('reports.sales.export-pdf', ['month' => $month, 'year' => $year]) }}" 
           class="px-6 py-3 bg-emerald-600 text-white text-sm font-black rounded-xl hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-95 flex items-center gap-2 uppercase tracking-widest">
            📜 Export PDF
        </a>
    </div>
</div>

{{-- Filter Hub --}}
<div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 mb-10">
    <form action="{{ route('reports.sales.monthly') }}" method="GET" class="flex flex-col md:flex-row items-end gap-6">
        <div class="space-y-2 flex-1">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Target Month</label>
            <select name="month" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black text-gray-900 appearance-none">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endforeach
            </select>
        </div>
        <div class="space-y-2 flex-1">
            <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Target Year</label>
            <select name="year" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black text-gray-900 appearance-none">
                @foreach(range(now()->year - 5, now()->year) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="w-full md:w-auto px-10 py-4 bg-gray-900 text-white font-black rounded-2xl hover:bg-gray-800 transition-all shadow-lg active:scale-95 uppercase tracking-widest text-sm">
            Refresh Report
        </button>
    </form>
</div>

{{-- Strategic Insights --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-12">
    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 group hover:border-emerald-200 transition-all border-l-8 border-l-blue-500">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Gross Monthly Sale</h3>
        <p class="text-3xl font-black text-gray-900">₹{{ number_format($totalSale, 2) }}</p>
    </div>
    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 group hover:border-emerald-200 transition-all border-l-8 border-l-emerald-500">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Accrued GST</h3>
        <p class="text-3xl font-black text-emerald-600">₹{{ number_format($bills->sum('gst_amount'), 2) }}</p>
    </div>
    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 group hover:border-emerald-200 transition-all border-l-8 border-l-indigo-500">
        <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Liquid Collections</h3>
        <p class="text-3xl font-black text-indigo-600">₹{{ number_format($bills->where('status', 'paid')->sum('net_amount'), 2) }}</p>
    </div>
</div>

{{-- Data Hub --}}
<div class="bg-white rounded-[3rem] border border-gray-200 shadow-2xl overflow-hidden mb-12">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-gray-900 text-white font-black uppercase text-[10px] tracking-widest border-b border-gray-100">
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
                                <div class="w-12 h-12 rounded-2xl bg-gray-100 flex items-center justify-center font-black text-gray-500 group-hover:bg-emerald-100 group-hover:text-emerald-600 transition-all shadow-inner">
                                    {{ substr($customerBills->first()->customer->name ?? '?', 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-black text-gray-900 tracking-tight text-lg">{{ $customerBills->first()->customer->name ?? 'WALK-IN' }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $customerBills->count() }} Transactions</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-10 py-6 text-right font-black text-gray-900 text-lg italic">₹{{ number_format($customerBills->sum('net_amount'), 2) }}</td>
                        <td class="px-10 py-6 text-right font-bold text-emerald-600">₹{{ number_format($customerBills->sum('gst_amount'), 2) }}</td>
                        <td class="px-10 py-6 text-right font-black text-red-600 text-lg">
                            ₹{{ number_format($customerBills->where('status', 'unpaid')->sum('net_amount'), 2) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-10 py-32 text-center">
                            <div class="flex flex-col items-center opacity-30">
                                <span class="text-8xl mb-6">📊</span>
                                <h3 class="text-2xl font-black text-gray-900 uppercase tracking-widest">No Data Available</h3>
                                <p class="text-gray-500 font-medium">Monthly revenue stream is empty for this period</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
