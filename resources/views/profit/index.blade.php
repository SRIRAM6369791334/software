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

<div class="mb-4 flex flex-col md:flex-row justify-between items-start md:items-center gap-4 bg-gray-50/50 p-4 rounded-2xl border border-gray-100">
    <form method="GET" class="flex items-center gap-2">
        <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 text-xs bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all font-bold">
        <span class="text-gray-400 font-black">→</span>
        <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 text-xs bg-white border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all font-bold">
        <button type="submit" class="ml-2 px-4 py-2 bg-gray-900 text-white rounded-xl text-xs font-black uppercase tracking-widest hover:bg-gray-800 transition-all active:scale-95 shadow-lg">Filter</button>
    </form>
    <div class="flex items-center gap-2">
        <a href="{{ route('profit.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
           class="px-5 py-2.5 bg-white border border-gray-200 rounded-xl text-xs font-black text-gray-500 hover:text-gray-900 hover:border-gray-300 hover:shadow-md transition-all flex items-center gap-2 uppercase tracking-widest">
            📊 Export CSV
        </a>
        <a href="{{ route('profit.export-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" 
           class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl text-xs font-black hover:bg-emerald-700 shadow-lg shadow-emerald-600/20 transition-all active:scale-95 flex items-center gap-2 uppercase tracking-widest">
            📜 Download PDF
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Billed</p>
        <h3 class="text-xl font-black text-blue-600">₹{{ number_format($breakdown['total_billed'], 2) }}</h3>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Collected</p>
        <h3 class="text-xl font-black text-emerald-600">₹{{ number_format($breakdown['total_collected'], 2) }}</h3>
    </div>
    <div class="bg-amber-50 p-6 rounded-2xl border border-amber-100 shadow-sm">
        <p class="text-[10px] font-bold text-amber-600 uppercase tracking-widest mb-1">Billed Profit</p>
        <h3 class="text-xl font-black text-amber-700">₹{{ number_format($breakdown['billed_profit'], 2) }}</h3>
    </div>
    <div class="bg-emerald-50 p-6 rounded-2xl border border-emerald-100 shadow-sm">
        <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1">Collected Profit</p>
        <h3 class="text-xl font-black text-emerald-700">₹{{ number_format($breakdown['collected_profit'], 2) }}</h3>
    </div>
    <div class="{{ $breakdown['pending_collection'] > 0 ? 'bg-rose-50 border-rose-100' : 'bg-gray-50 border-gray-100' }} p-6 rounded-2xl border shadow-sm">
        <p class="text-[10px] font-bold {{ $breakdown['pending_collection'] > 0 ? 'text-rose-600' : 'text-gray-500' }} uppercase tracking-widest mb-1">Pending Collection</p>
        <h3 class="text-xl font-black {{ $breakdown['pending_collection'] > 0 ? 'text-rose-700' : 'text-gray-700' }}">₹{{ number_format($breakdown['pending_collection'], 2) }}</h3>
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
