@extends('layouts.app')
@section('title', 'Daily Usage (FCR)')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 text-glow">Daily Usage & Consumption</h1>
        <p class="text-sm text-gray-500 mt-1">Track feed and medicine usage to calculate Batch performance</p>
    </div>
    <a href="{{ route('inventory.consumptions.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-600/20">
        + Record Daily Usage
    </a>
</div>

{{-- Stats Summary --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Feed Used (MTD)</p>
        <h3 class="text-2xl font-black text-gray-900">{{ number_format($consumptions->sum('quantity'), 2) }} <span class="text-sm font-normal text-gray-400">kg</span></h3>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Active Batches Feeding</p>
        <h3 class="text-2xl font-black text-emerald-600">{{ $consumptions->unique('batch_id')->count() }}</h3>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Stock Health</p>
        <div class="flex items-center gap-2 mt-1">
            <span class="w-3 h-3 rounded-full bg-emerald-500 animate-pulse"></span>
            <span class="text-sm font-bold text-gray-700">All Optimal</span>
        </div>
    </div>
</div>

{{-- Consumption Table --}}
<div class="bg-white rounded-2xl border border-gray-200 shadow-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Date</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Batch (Flock)</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Item / Feed</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Quantity</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest">Source Warehouse</th>
                    <th class="px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-widest text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($consumptions as $c)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-bold text-gray-900">{{ $c->date->format('d M, Y') }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="font-black text-emerald-700">{{ $c->batch->batch_code }}</span>
                            <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $c->batch->breed }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 bg-blue-50 text-blue-700 text-[10px] font-black uppercase rounded-lg">
                            {{ $c->item->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-black text-gray-900 text-base">{{ number_format($c->quantity, 2) }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $c->unit }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-gray-500 font-medium">{{ $c->warehouse->name }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <form action="{{ route('inventory.consumptions.destroy', $c->id) }}" method="POST" onsubmit="return confirm('Revert this usage and restore stock?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-600 font-bold transition-colors">🗑️</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <span class="text-4xl mb-4">🍽️</span>
                            <p class="text-gray-400 font-bold">No consumption recorded for today.</p>
                            <a href="{{ route('inventory.consumptions.create') }}" class="mt-4 text-emerald-600 font-black hover:underline">Start Recording →</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($consumptions->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
        {{ $consumptions->links() }}
    </div>
    @endif
</div>
@endsection
