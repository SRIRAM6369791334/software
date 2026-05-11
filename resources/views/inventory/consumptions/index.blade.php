@extends('layouts.app')
@section('title', 'Daily Usage (FCR)')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-950 text-glow">Daily Usage & Consumption</h1>
        <p class="text-sm text-slate-500 mt-1">Track feed and medicine usage to calculate Batch performance</p>
    </div>
    <a href="{{ route('inventory.consumptions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-600 to-sky-500 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-600/20">
        + Record Daily Usage
    </a>
</div>

{{-- Stats Summary --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-2xl border border-slate-200 shadow-sm">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Feed Used (MTD)</p>
        <h3 class="text-2xl font-black text-slate-950">{{ number_format($consumptions->sum('quantity'), 2) }} <span class="text-sm font-normal text-slate-400">kg</span></h3>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-2xl border border-slate-200 shadow-sm">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Active Batches Feeding</p>
        <h3 class="text-2xl font-black text-emerald-600">{{ $consumptions->unique('batch_id')->count() }}</h3>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-2xl border border-slate-200 shadow-sm">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Stock Health</p>
        <div class="flex items-center gap-2 mt-1">
            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-sm font-bold text-slate-700">All Optimal</span>
        </div>
    </div>
</div>

{{-- Consumption Table --}}
<div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-2xl border border-slate-200 shadow-md overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-emerald-50 border-b border-slate-200">
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Date</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Batch (Flock)</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Item / Feed</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Quantity</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest">Source Warehouse</th>
                    <th class="px-6 py-4 text-xs font-bold text-slate-400 uppercase tracking-widest text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($consumptions as $c)
                <tr class="hover:bg-gradient-to-r from-emerald-50/80 to-sky-50/80 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-bold text-slate-950">{{ $c->date->format('d M, Y') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-black text-emerald-700">{{ $c->batch->batch_code }}</span>
                            <span class="text-[10px] text-slate-400 font-bold uppercase">{{ $c->batch->breed }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-blue-50 text-blue-700 text-[10px] font-black uppercase rounded-lg">
                            {{ $c->item->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-black text-slate-950 text-base">{{ number_format($c->quantity, 2) }}</span>
                        <span class="text-[10px] font-bold text-slate-400 uppercase">{{ $c->unit }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-slate-500 font-medium">{{ $c->warehouse->name }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('inventory.consumptions.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Revert this usage and restore stock?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 font-bold transition-colors"></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <span class="text-4xl mb-4"></span>
                            <p class="text-slate-400 font-bold">No consumption recorded for today.</p>
                            <a href="{{ route('inventory.consumptions.create') }}" class="mt-4 text-emerald-600 font-black hover:underline">Start Recording -></a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($consumptions->hasPages())
    <div class="px-6 py-4 border-t border-slate-200 bg-slate-50">
        {{ $consumptions->links() }}
    </div>
    @endif
</div>
@endsection
