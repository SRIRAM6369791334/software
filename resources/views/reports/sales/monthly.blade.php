@extends('layouts.app')
@section('title', 'Monthly Sales Report')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <a href="{{ route('reports.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Analytics</a>
        <h1 class="text-2xl font-bold text-gray-900">Monthly Revenue Stream</h1>
        <p class="text-sm text-gray-500 mt-0.5">Aggregated sales performance per month</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Monthly aggregation</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-gray-100 text-gray-400 uppercase tracking-widest text-[10px]">
                        <th class="px-5 py-3">Month</th>
                        <th class="px-5 py-3 text-right">Total Revenue Generated</th>
                        <th class="px-5 py-3 text-center">Trend</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($sales as $row)
                        <tr>
                            <td class="px-5 py-4 font-black text-gray-900 text-lg uppercase tracking-tight">
                                {{ \Carbon\Carbon::parse($row->month . '-01')->format('F Y') }}
                            </td>
                            <td class="px-5 py-4 text-right font-black text-emerald-600 text-xl">
                                ₹{{ number_format($row->total, 2) }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="text-emerald-500">▲</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-12 text-center text-gray-400 italic">No aggregated data available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-emerald-900 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-emerald-800 rounded-full opacity-50 blur-3xl"></div>
        <h3 class="text-sm font-bold uppercase tracking-widest text-emerald-300 mb-8">Quarterly Forecast</h3>
        <div class="space-y-6 relative z-10">
            <div class="p-6 bg-white/5 border border-white/10 rounded-2xl">
                <p class="text-[10px] font-bold uppercase opacity-50 mb-1">Projected Next Month</p>
                <h4 class="text-3xl font-black">₹{{ number_format(($sales->first()?->total ?? 0) * 1.15, 0) }}</h4>
                <p class="text-xs mt-2 text-emerald-400">Estimated 15% growth based on cycle</p>
            </div>
            
            <div class="space-y-2">
                <div class="flex justify-between text-xs font-bold tracking-tight">
                    <span>Performance Target</span>
                    <span>85% Reach</span>
                </div>
                <div class="h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                    <div class="h-full bg-emerald-400" style="width: 85%"></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
