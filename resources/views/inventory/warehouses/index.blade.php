@extends('layouts.app')

@section('title', 'Warehouse Master')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-slate-950">Warehouse Master</h1>
        <p class="text-sm text-slate-500 mt-0.5">Define storage locations, Godowns, and Sheds</p>
    </div>
    <div class="mt-4 md:mt-0 flex gap-2">
        <a href="{{ route('inventory.warehouses.create') }}" 
           class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700
                  text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition-all active:scale-95">
            + Add Warehouse
        </a>
    </div>
</div>

{{-- Summary Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-5 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm"><span class="material-symbols-rounded">warehouse</span></div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Locations</p>
            <h3 class="text-2xl font-black text-slate-950">{{ $warehouses->total() }}</h3>
        </div>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-5 rounded-2xl border border-slate-200 shadow-sm flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-sm"></div>
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Active Units</p>
            <h3 class="text-2xl font-black text-blue-600">{{ $warehouses->where('is_active', true)->count() }}</h3>
        </div>
    </div>
</div>

{{-- Filter --}}
<div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-4 rounded-xl border border-slate-200 shadow-sm mb-6 max-w-sm">
    <form action="{{ route('inventory.warehouses.index') }}" method="GET" class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></span>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name or location..."
               class="w-full pl-10 pr-4 py-2 text-sm bg-emerald-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
    </form>
</div>

{{-- Warehouses Table --}}
<div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-slate-100 bg-gradient-to-r from-emerald-50/70 to-sky-50/70">
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Warehouse Name</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Address / Location</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-center text-[10px] font-bold text-slate-400 uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($warehouses as $warehouse)
                    <tr class="hover:bg-slate-50/20 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="font-bold text-slate-950 group-hover:text-emerald-600 transition-colors">{{ $warehouse->name }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-slate-500 font-medium">{{ $warehouse->location ?: '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest
                                {{ $warehouse->is_active ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $warehouse->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('inventory.warehouses.edit', $warehouse) }}" 
                                   class="p-2 rounded-lg hover:bg-blue-50 text-blue-500 transition-colors border border-transparent hover:border-blue-100" title="Edit">
                                    <span class="material-symbols-rounded text-lg">edit</span>
                                </a>
                                <form action="{{ route('inventory.warehouses.destroy', $warehouse) }}" method="POST"
                                      onsubmit="return confirm('Remove this warehouse location? Ensure it has no stock first.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg hover:bg-red-50 text-red-500 transition-colors border border-transparent hover:border-red-100" title="Delete">
                                        
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center opacity-40">
                                <span class="text-5xl mb-4"></span>
                                <p class="text-sm font-bold uppercase tracking-widest">No storage locations found</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($warehouses->hasPages())
    <div class="px-6 py-4 border-t border-slate-100 bg-gradient-to-r from-emerald-50/70 to-sky-50/70">
        {{ $warehouses->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
