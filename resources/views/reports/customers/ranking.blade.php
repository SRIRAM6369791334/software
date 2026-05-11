@extends('layouts.app')
@section('title', 'Customer Ranking')

@section('content')
<div class="mb-6">
    <a href="{{ route('reports.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Analytics</a>
    <h1 class="text-2xl font-bold text-slate-950">Customer Rankings</h1>
    <p class="text-sm text-slate-500 mt-0.5">Top customers by outstanding balance and transaction volume</p>
</div>

<div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-slate-200 bg-gradient-to-r from-emerald-50/80 to-sky-50/80">
                    <th class="px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Rank</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Customer Name</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Type</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Route</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">Outstanding (Rs )</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Score</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($customers as $index => $customer)
                    @php $rank = (($customers->currentPage()-1) * $customers->perPage()) + $index + 1; @endphp
                    <tr class="hover:bg-gradient-to-r from-emerald-50/70 to-sky-50/70 transition-colors">
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full text-[10px] font-black {{ $rank <= 3 ? 'bg-amber-100 text-amber-700' : 'bg-sky-50 text-slate-500' }}">
                                {{ $rank }}
                            </span>
                        </td>
                        <td class="px-5 py-4 font-bold text-slate-950">{{ $customer->name }}</td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-tight {{ $customer->type == 'Wholesale' ? 'text-primary bg-indigo-50' : 'text-slate-600 bg-slate-50' }}">
                                {{ $customer->type }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-slate-500 text-xs">{{ $customer->route ?: 'General' }}</td>
                        <td class="px-5 py-4 text-right font-black text-emerald-900">Rs {{ number_format($customer->balance, 2) }}</td>
                        <td class="px-5 py-4 text-center">
                            @php $score = max(0, 100 - ($customer->balance / 1000)); @endphp
                            <div class="w-full bg-sky-50 h-1.5 rounded-full overflow-hidden min-w-[60px]">
                                <div class="h-full bg-emerald-500" style="width: {{ $score }}%"></div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-slate-400 italic">No customer data found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
    <div class="px-5 py-4 border-t border-slate-200">{{ $customers->links() }}</div>
    @endif
</div>
@endsection
