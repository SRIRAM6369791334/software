@extends('layouts.app')
@section('title', 'Expense vs Income')

@section('content')
<div class="mb-6">
    <a href="{{ route('profit.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Overview</a>
    <h1 class="text-2xl font-bold text-gray-900">Expense vs Income Matrix</h1>
    <p class="text-sm text-gray-500 mt-0.5">Comparative study of business efficiency</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="space-y-6">
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-8 text-center">Income Breakdown (This Month)</h3>
            <div class="flex justify-center mb-10">
                <div class="relative w-48 h-48 rounded-full border-[16px] border-emerald-500 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Revenue</p>
                        <p class="text-xl font-black text-gray-900">₹{{ number_format($summary['revenue'], 0) }}</p>
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Scheduled Invoicing</span>
                    <span class="font-bold text-gray-900">65%</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Counter Sales</span>
                    <span class="font-bold text-gray-900">35%</span>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-white p-8 rounded-3xl border border-gray-100 shadow-sm">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-8 text-center">Expense Matrix (Current)</h3>
            <div class="flex justify-center mb-10">
                <div class="relative w-48 h-48 rounded-full border-[16px] border-rose-500 flex items-center justify-center">
                    <div class="text-center">
                        <p class="text-[10px] font-bold text-gray-400 uppercase">Outflow</p>
                        <p class="text-xl font-black text-gray-900">₹{{ number_format($summary['purchase'] + $summary['expenses'], 0) }}</p>
                    </div>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Procurement (Stock)</span>
                    <span class="font-bold text-gray-900">₹{{ number_format($summary['purchase'], 0) }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Operationals & EMIs</span>
                    <span class="font-bold text-gray-900">₹{{ number_format($summary['expenses'], 0) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-10 bg-emerald-50 border border-emerald-100 rounded-2xl p-6 text-center">
    <p class="text-emerald-800 text-sm">
        <strong>Efficiency Ratio:</strong> Your business is currently retaining 
        <span class="font-black text-lg">₹{{ number_format($summary['profit'] / ($summary['revenue'] ?: 1) * 100, 1) }}%</span> 
        of every Rupee generated after all procurement and operational expenses.
    </p>
</div>
@endsection
