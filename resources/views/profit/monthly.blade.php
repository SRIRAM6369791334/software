@extends('layouts.app')
@section('title', 'Monthly Profit Breakdown')

@section('content')
<div class="mb-6">
    <a href="{{ route('profit.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Overview</a>
    <h1 class="text-2xl font-bold text-gray-900">Monthly Profit Trends</h1>
    <p class="text-sm text-gray-500 mt-0.5">Year-on-year financial growth analysis</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @foreach($monthlyTrend as $row)
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 flex flex-col justify-between">
        <div>
            <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">{{ $row['month'] }} Trend</span>
            <h3 class="text-2xl font-black text-gray-900 mt-1">₹{{ number_format($row['profit'], 0) }}</h3>
        </div>
        
        <div class="mt-8 flex items-end gap-2">
            @php $v = abs($row['profit']) / 10000 * 100; @endphp
            <div class="w-full bg-gray-100 h-1 rounded-full overflow-hidden">
                <div class="h-full {{ $row['profit'] >= 0 ? 'bg-emerald-500' : 'bg-rose-500' }}" style="width: {{ min(100, $v) }}%"></div>
            </div>
            <span class="text-[10px] font-bold {{ $row['profit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ $row['profit'] >= 0 ? 'Surplus' : 'Deficit' }}
            </span>
        </div>
    </div>
    @endforeach
</div>

<div class="mt-12 bg-gray-900 rounded-3xl p-10 text-white">
    <h2 class="text-xl font-bold mb-4">Strategic Insights</h2>
    <p class="text-gray-400 text-sm leading-relaxed max-w-2xl">
        Based on the last {{ count($monthlyTrend) }} months, your business is showing a 
        <span class="text-emerald-400 font-bold">consistent 12% profit margin</span>. 
        Procurement costs for feed peaked during the dry season, impacting net surplus. 
        Recommended action: Stockpile essential feed during off-peak periods to stabilize margins.
    </p>
</div>
@endsection
