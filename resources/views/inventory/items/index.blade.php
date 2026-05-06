@extends('layouts.app')

@section('title', 'Item Master')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Item Master</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage poultry resources and inventory definitions</p>
    </div>
    <div class="mt-4 md:mt-0 flex gap-2">
        <a href="{{ route('inventory.items.create') }}" 
           class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700
                  text-white text-sm font-bold rounded-xl shadow-lg shadow-emerald-600/20 transition-all active:scale-95">
            + Register New Item
        </a>
    </div>
</div>

{{-- Summary Stats - Matching Dashboard Style --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm">📦</div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Total Items</p>
            <h3 class="text-2xl font-black text-gray-900">{{ $items->total() }}</h3>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-sm">🐥</div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Chick Breeds</p>
            <h3 class="text-2xl font-black text-amber-600">{{ $items->where('type', 'Chick')->count() }}</h3>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xl shadow-sm">💊</div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Medications</p>
            <h3 class="text-2xl font-black text-purple-600">{{ $items->where('type', 'Medicine')->count() + $items->where('type', 'Vaccine')->count() }}</h3>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-sm">🚜</div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Equipments</p>
            <h3 class="text-2xl font-black text-blue-600">{{ $items->where('type', 'Equipment')->count() }}</h3>
        </div>
    </div>
</div>

{{-- Dynamic Filters --}}
<div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm mb-6">
    <form action="{{ route('inventory.items.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
        <div class="relative flex-1 min-w-[240px]">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by name, brand or category..."
                   class="w-full pl-10 pr-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
        </div>
        
        <div class="flex items-center gap-2">
            <label class="text-[10px] font-bold text-gray-400 uppercase">Filter Type:</label>
            <select name="type" onchange="this.form.submit()" 
                    class="px-4 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all font-medium">
                <option value="">All Categories</option>
                @foreach(['Feed', 'Chick', 'Medicine', 'Vaccine', 'Equipment', 'Other'] as $type)
                    <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                @endforeach
            </select>
        </div>

        @if(request()->anyFilled(['search', 'type']))
            <a href="{{ route('inventory.items.index') }}" class="text-xs font-bold text-red-500 hover:bg-red-50 px-3 py-2 rounded-lg transition-colors">Clear Filters</a>
        @endif
    </form>
</div>

{{-- Main Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-gray-50 bg-gray-50/30">
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Item Description</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Category & Type</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Unit Logics</th>
                    <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Current Stock</th>
                    <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50/20 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 group-hover:text-emerald-600 transition-colors">{{ $item->name }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">{{ $item->brand ?: 'Master Record' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="px-2 py-0.5 w-max rounded text-[10px] font-black uppercase tracking-tighter
                                    {{ $item->type === 'Feed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                    {{ $item->type === 'Chick' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $item->type === 'Medicine' ? 'bg-purple-100 text-purple-700' : '' }}
                                    {{ !in_array($item->type, ['Feed','Chick','Medicine']) ? 'bg-gray-100 text-gray-600' : '' }}">
                                    {{ $item->type }}
                                </span>
                                <span class="text-[10px] text-gray-400 font-medium px-0.5">{{ $item->category ?: 'General' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">Base</span>
                                    <span class="font-bold text-gray-700 uppercase">{{ $item->base_unit }}</span>
                                </div>
                                <div class="w-px h-6 bg-gray-100"></div>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">Conversion</span>
                                    <span class="text-xs text-gray-500 font-medium italic">1 Bag = {{ number_format($item->conversion_rate, 1) }} {{ $item->base_unit }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-col items-end">
                                <span class="text-xl font-black {{ $item->current_stock <= 50 ? 'text-red-500' : 'text-gray-900' }}">
                                    {{ number_format($item->current_stock, 0) }}
                                </span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $item->base_unit }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('inventory.items.edit', $item) }}" 
                                   class="p-2 rounded-lg hover:bg-emerald-50 text-emerald-600 transition-colors border border-transparent hover:border-emerald-100" title="Edit">
                                    ✏️
                                </a>
                                <form action="{{ route('inventory.items.destroy', $item) }}" method="POST"
                                      onsubmit="return confirm('Are you sure? This will remove the item from master records.')">
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
                                <span class="text-5xl mb-4">📂</span>
                                <p class="text-sm font-bold uppercase tracking-widest">No items found in record</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($items->hasPages())
    <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/30">
        {{ $items->withQueryString()->links() }}
    </div>
    @endif
</div>
@endsection
