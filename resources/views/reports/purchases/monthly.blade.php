@extends('layouts.app')
@section('title', 'Monthly Purchase Report')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Monthly Purchase Report</h1>
        <p class="text-sm text-gray-500 mt-0.5">Summary for {{ date('F Y', mktime(0, 0, 0, $month, 1, $year)) }}</p>
    </div>
    <div class="flex gap-2">
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition-colors">
            🖨️ Print
        </button>
        <a href="{{ route('reports.purchases.export-pdf', ['month' => $month, 'year' => $year]) }}" 
           class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
            📄 Export PDF
        </a>
    </div>
</div>

{{-- Filter Form --}}
<div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm mb-6">
    <form action="{{ route('reports.purchases.monthly') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Select Month</label>
            <select name="month" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all text-sm appearance-none">
                @foreach(range(1, 12) as $m)
                    <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Select Year</label>
            <select name="year" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all text-sm appearance-none">
                @foreach(range(now()->year - 5, now()->year) as $y)
                    <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
            Filter Results
        </button>
    </form>
</div>

{{-- Summary Tiles --}}
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm text-center">
        <span class="text-2xl">📦</span>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2">Total Monthly Purchase</p>
        <p class="text-2xl font-extrabold text-gray-900 mt-0.5">₹{{ number_format($purchases->sum('total_amount'), 0, '.', ',') }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm text-center">
        <span class="text-2xl">⚖️</span>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2">Total GST Paid</p>
        <p class="text-2xl font-extrabold text-emerald-600 mt-0.5">₹{{ number_format($purchases->sum('gst_amount'), 0, '.', ',') }}</p>
    </div>
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm text-center">
        <span class="text-2xl">👥</span>
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-2">Active Vendors</p>
        <p class="text-2xl font-extrabold text-blue-600 mt-0.5">{{ $purchases->unique('vendor_id')->count() }}</p>
    </div>
</div>

{{-- Data Table --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Vendor</th>
                    <th class="px-6 py-4 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Order Count</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Purchase</th>
                    <th class="px-6 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Total GST</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @php
                    $grouped = $purchases->groupBy('vendor_id');
                @endphp
                @forelse($grouped as $vendorId => $vendorPurchases)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-900">{{ $vendorPurchases->first()->vendor->name ?? $vendorPurchases->first()->vendor_name }}</td>
                    <td class="px-6 py-4 text-center text-gray-500 font-mono">{{ $vendorPurchases->count() }}</td>
                    <td class="px-6 py-4 text-right font-mono font-bold text-gray-900">₹{{ number_format($vendorPurchases->sum('total_amount'), 0, '.', ',') }}</td>
                    <td class="px-6 py-4 text-right font-mono text-gray-400 text-xs">₹{{ number_format($vendorPurchases->sum('gst_amount'), 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                        <div class="text-3xl mb-2">🚛</div>
                        No purchase records found for this month.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

