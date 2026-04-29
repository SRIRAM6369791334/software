@extends('layouts.app')
@section('title', 'Profit & Loss Overview')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Profit & Loss Dashboard</h1>
        <p class="text-sm text-gray-500 mt-0.5">Real-time financial performance overview</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('profit.monthly') }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition-all">Monthly Breakdown</a>
        <a href="{{ route('profit.expense-vs-income') }}" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-bold hover:bg-gray-800 shadow-sm transition-all">Expense vs Income</a>
    </div>
</div>

{{-- Top Summary Cards --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Current Month Revenue</p>
        <h3 class="text-2xl font-black text-emerald-600">₹{{ number_format($summary['revenue'], 2) }}</h3>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Purchases</p>
        <h3 class="text-2xl font-black text-amber-600">₹{{ number_format($summary['purchase'], 2) }}</h3>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Operational Expenses</p>
        <h3 class="text-2xl font-black text-rose-600">₹{{ number_format($summary['expenses'], 2) }}</h3>
    </div>
    <div class="bg-emerald-900 p-6 rounded-2xl shadow-xl">
        <p class="text-[10px] font-bold text-emerald-300 uppercase tracking-widest mb-1">Net Profit (Estim.)</p>
        <h3 class="text-2xl font-black text-white">₹{{ number_format($summary['profit'], 2) }}</h3>
    </div>
</div>

{{-- Weekly Breakdown Table --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-8">
    <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
        <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Recent Weekly Performance</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-gray-100 bg-gray-50/20">
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Week</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Revenue</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Purchases</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Expenses</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Net Profit</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($weeklyData as $row)
                <tr class="hover:bg-gray-50/30">
                    <td class="px-5 py-4 font-bold text-gray-900">{{ $row['week'] }}</td>
                    <td class="px-5 py-4 text-right text-emerald-600 font-mono">₹{{ number_format($row['revenue'], 2) }}</td>
                    <td class="px-5 py-4 text-right text-amber-600 font-mono">₹{{ number_format($row['purchase'], 2) }}</td>
                    <td class="px-5 py-4 text-right text-rose-600 font-mono">₹{{ number_format($row['expenses'], 2) }}</td>
                    <td class="px-5 py-4 text-right font-black {{ $row['profit'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                        ₹{{ number_format($row['profit'], 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
