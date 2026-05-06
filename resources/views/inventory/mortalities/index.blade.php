@extends('layouts.app')
@section('title', 'Mortality Tracking')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Mortality Tracking</h1>
        <p class="text-gray-500 font-medium">Monitor flock health and attrition across active batches</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('inventory.mortalities.create') }}" 
           class="px-6 py-4 bg-red-600 text-white text-sm font-black rounded-[1.5rem] hover:bg-red-700 transition-all shadow-xl shadow-red-600/20 active:scale-95">
            + Record Loss 📉
        </a>
    </div>
</div>

{{-- Operational Health Stats --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 flex items-center gap-6 group hover:border-red-200 transition-all">
        <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">📉</div>
        <div>
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Deaths</h3>
            <p class="text-2xl font-black text-gray-900">{{ number_format($mortalities->sum('count')) }}</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 flex items-center gap-6 group hover:border-emerald-200 transition-all">
        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🛡️</div>
        <div>
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Survival Pulse</h3>
            <p class="text-2xl font-black text-gray-900">Tracking Active</p>
        </div>
    </div>
    <div class="bg-gray-900 p-6 rounded-[2.5rem] shadow-xl shadow-gray-900/20 text-white flex items-center gap-6 col-span-2">
        <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-2xl">🕊️</div>
        <div>
            <h3 class="text-[10px] font-black text-emerald-200 uppercase tracking-widest mb-1">Real-time Flock Count</h3>
            <p class="text-2xl font-black text-white italic">"Ensuring every bird is accounted for in your ledger."</p>
        </div>
    </div>
</div>

{{-- Mortality Logs --}}
<div class="bg-white rounded-[2.5rem] border border-gray-200 shadow-2xl overflow-hidden mb-12">
    <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gray-50/50">
        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Historical Attrition Log</h3>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-gray-50/50 text-gray-400 font-black uppercase text-[10px] tracking-widest border-b border-gray-100">
                    <th class="px-8 py-5">Event Date</th>
                    <th class="px-8 py-5">Source Batch</th>
                    <th class="px-8 py-5 text-center">Loss Count</th>
                    <th class="px-8 py-5 text-center">Remaining</th>
                    <th class="px-8 py-5">Reason & Observation</th>
                    <th class="px-8 py-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($mortalities as $m)
                    <tr class="hover:bg-red-50/20 transition-all group">
                        <td class="px-8 py-5">
                            <span class="font-black text-gray-900 tracking-tighter">{{ $m->date->format('M d, Y') }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="font-black text-red-700 tracking-tight">{{ $m->batch->batch_code }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $m->batch->breed }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="px-4 py-2 bg-red-100 text-red-700 text-sm font-black rounded-xl border border-red-200">
                                -{{ number_format($m->count) }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="text-sm font-black text-gray-600">{{ number_format($m->batch->current_count) }} Birds</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800">{{ $m->reason ?: 'General Attrition' }}</span>
                                @if($m->remarks)
                                    <span class="text-[10px] text-gray-400 italic font-medium truncate max-w-[200px]">{{ $m->remarks }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <form action="{{ route('inventory.mortalities.destroy', $m->id) }}" method="POST" onsubmit="return confirm('Restore these bird counts and delete record?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 rounded-2xl text-gray-300 hover:text-red-600 hover:border-red-200 hover:shadow-lg transition-all active:scale-95">
                                    🗑️
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-8 py-24 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center text-4xl mb-6 shadow-inner">📋</div>
                                <h3 class="text-xl font-black text-gray-900 tracking-tight uppercase tracking-widest">No Losses Recorded</h3>
                                <p class="text-gray-400 font-medium mt-1">Excellent flock health status. No attrition logs found.</p>
                                <a href="{{ route('inventory.mortalities.create') }}" class="mt-8 px-8 py-4 bg-gray-900 text-white text-xs font-black rounded-2xl hover:bg-black transition-all uppercase tracking-widest">Record First Entry</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($mortalities->hasPages())
        <div class="p-8 border-t border-gray-50 bg-gray-50/30">
            {{ $mortalities->links() }}
        </div>
    @endif
</div>
@endsection
