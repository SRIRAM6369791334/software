@extends('layouts.app')

@section('title', 'Batch Management')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Batch Management</h1>
        <p class="text-sm text-gray-500 mt-0.5">Track poultry flock lifecycle, placements, and performance</p>
    </div>
    <div class="mt-4 md:mt-0 flex gap-2">
        <a href="{{ route('inventory.batches.create') }}" 
           class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700
                  text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition-all active:scale-95">
            + Start New Batch
        </a>
    </div>
</div>

{{-- Summary Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm">🐣</div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Active Batches</p>
            <h3 class="text-2xl font-black text-gray-900">{{ $batches->where('status', 'Active')->count() }}</h3>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-sm">📊</div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Total Chicks</p>
            <h3 class="text-2xl font-black text-blue-600">{{ number_format($batches->where('status', 'Active')->sum('current_count')) }}</h3>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-sm">⏳</div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Avg Placement</p>
            <h3 class="text-2xl font-black text-amber-600">{{ number_format($batches->avg('avg_placement_weight'), 2) }}g</h3>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-gray-50 text-gray-400 flex items-center justify-center text-xl shadow-sm">📁</div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Closed Batches</p>
            <h3 class="text-2xl font-black text-gray-400">{{ $batches->where('status', 'Closed')->count() }}</h3>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6">
    <form action="{{ route('inventory.batches.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
        <div class="relative flex-1 min-w-[240px]">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by batch code or breed..."
                   class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
        </div>
        
        <div class="flex items-center gap-2">
            <label class="text-[10px] font-bold text-gray-400 uppercase">Status:</label>
            <select name="status" onchange="this.form.submit()" 
                    class="px-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all font-medium">
                <option value="">All Status</option>
                <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Closed" {{ request('status') == 'Closed' ? 'selected' : '' }}>Closed</option>
            </select>
        </div>

        @if(request()->anyFilled(['search', 'status']))
            <a href="{{ route('inventory.batches.index') }}" class="text-xs font-bold text-red-500 hover:bg-red-50 px-3 py-2 rounded-lg transition-colors">Clear Filters</a>
        @endif
    </form>
</div>

{{-- Batches Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-gray-50 bg-gray-50/30">
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Batch Details</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Flock Age</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Population</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($batches as $batch)
                    <tr class="hover:bg-gray-50/20 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 group-hover:text-emerald-600 transition-colors">{{ $batch->batch_code }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">{{ $batch->breed ?: 'Generic Breed' }}</span>
                                <span class="text-[10px] text-gray-500 mt-1">Placed: {{ $batch->placement_date->format('d M Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $days = $batch->status === 'Active' 
                                    ? now()->diffInDays($batch->placement_date) 
                                    : $batch->closed_at->diffInDays($batch->placement_date);
                            @endphp
                            <div class="flex flex-col">
                                <span class="text-lg font-black text-gray-700">{{ $days }} Days</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase">Old</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <div class="flex items-end gap-1">
                                    <span class="text-lg font-black text-gray-900">{{ number_format($batch->current_count) }}</span>
                                    <span class="text-xs text-gray-400 font-bold pb-1">/ {{ number_format($batch->initial_count) }}</span>
                                </div>
                                <div class="w-24 h-1.5 bg-gray-100 rounded-full mt-1 overflow-hidden">
                                    @php
                                        $percent = ($batch->initial_count > 0) ? ($batch->current_count / $batch->initial_count) * 100 : 0;
                                    @endphp
                                    <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $percent }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest
                                {{ $batch->status === 'Active' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $batch->status }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('inventory.batches.edit', $batch) }}" 
                                   class="p-2 rounded-lg hover:bg-blue-50 text-blue-500 transition-colors border border-transparent hover:border-blue-100" title="Edit Batch">
                                    ✏️
                                </a>
                                <form action="{{ route('inventory.batches.destroy', $batch) }}" method="POST"
                                      onsubmit="return confirm('Delete this batch record? All associated data will be affected.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg hover:bg-red-50 text-red-500 transition-colors border border-transparent hover:border-red-100" title="Delete">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center opacity-40">
                                <span class="text-5xl mb-4">🏠</span>
                                <p class="text-sm font-bold uppercase tracking-widest">No batches found in record</p>
                                <a href="{{ route('inventory.batches.create') }}" class="text-emerald-600 text-xs mt-2 underline">Start your first batch now</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($batches->hasPages())
    <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/30">
        {{ $batches->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
