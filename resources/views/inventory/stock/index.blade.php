@extends('layouts.app')

@section('title', 'Stock Dashboard')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Stock Dashboard</h1>
        <p class="text-sm text-gray-500 mt-0.5">Real-time inventory overview and stock status alerts</p>
    </div>
    <div class="mt-4 md:mt-0 flex gap-2">
        <a href="{{ route('inventory.stock.movements') }}" 
           class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 hover:bg-gray-800
                  text-white text-sm font-bold rounded-xl shadow-lg transition-all active:scale-95">
            📋 View Stock Ledgers
        </a>
    </div>
</div>

{{-- Summary Stats --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4 transition-all hover:shadow-md">
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl shadow-sm">📦</div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Tracked Items</p>
            <h3 class="text-2xl font-black text-gray-900">{{ $stats['total_items'] }}</h3>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4 transition-all hover:shadow-md">
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl shadow-sm">⚠️</div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Low Stock</p>
            <h3 class="text-2xl font-black text-amber-600">{{ $stats['low_stock_count'] }}</h3>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4 transition-all hover:shadow-md">
        <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center text-xl shadow-sm">🚫</div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Out of Stock</p>
            <h3 class="text-2xl font-black text-red-600">{{ $stats['out_of_stock_count'] }}</h3>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex items-start gap-4 transition-all hover:shadow-md">
        <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl shadow-sm">💰</div>
        <div>
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-0.5">Active Items</p>
            <h3 class="text-2xl font-black text-blue-600">{{ $items->where('is_active', true)->count() }}</h3>
        </div>
    </div>
</div>

{{-- Stock Inventory Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/20">
        <h3 class="text-xs font-black text-gray-400 uppercase tracking-[0.2em]">Current Inventory Levels</h3>
        
        <form action="{{ route('inventory.stock.index') }}" method="GET" class="flex items-center gap-4">
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-xs">🔍</span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search item..."
                       class="pl-9 pr-4 py-1.5 text-xs bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all w-48">
            </div>
            <select name="type" onchange="this.form.submit()" 
                    class="px-3 py-1.5 text-xs bg-white border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all font-bold text-gray-500">
                <option value="">All Types</option>
                <option value="Feed" {{ request('type') == 'Feed' ? 'selected' : '' }}>Feed</option>
                <option value="Medicine" {{ request('type') == 'Medicine' ? 'selected' : '' }}>Medicine</option>
                <option value="Chicks" {{ request('type') == 'Chicks' ? 'selected' : '' }}>Chicks</option>
                <option value="Vaccine" {{ request('type') == 'Vaccine' ? 'selected' : '' }}>Vaccine</option>
            </select>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-gray-50 bg-gray-50/30">
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Item Details</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Type / Category</th>
                    <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Available Stock</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Stock Health</th>
                    <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-400 uppercase tracking-widest">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($items as $item)
                    <tr class="hover:bg-gray-50/20 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900 group-hover:text-emerald-600 transition-colors">{{ $item->name }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">{{ $item->code }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-gray-700">{{ $item->type }}</span>
                                <span class="text-[10px] text-gray-400 font-medium uppercase tracking-tight">{{ $item->category ?: 'General' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex flex-col items-end">
                                <span class="text-lg font-black {{ $item->current_stock <= 0 ? 'text-red-500' : 'text-gray-900' }}">
                                    {{ number_format($item->current_stock, 2) }}
                                </span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $item->base_unit }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusClass = match($item->stock_status) {
                                    'Healthy' => 'bg-emerald-100 text-emerald-700',
                                    'Low Stock' => 'bg-amber-100 text-amber-700',
                                    'Out of Stock' => 'bg-red-100 text-red-700',
                                    default => 'bg-gray-100 text-gray-500'
                                };
                            @endphp
                            <div class="flex flex-col gap-1">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest w-fit {{ $statusClass }}">
                                    {{ $item->stock_status }}
                                </span>
                                @if($item->stock_status === 'Low Stock')
                                    <span class="text-[9px] text-amber-600 font-bold italic">Threshold: {{ number_format($item->low_stock_threshold) }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('inventory.items.show', $item->id) }}" 
                                   class="px-3 py-1 text-[10px] font-bold uppercase tracking-widest border border-gray-200 rounded-lg hover:bg-gray-50 transition-all active:scale-95">
                                    🔍 History
                                </a>
                                <a href="{{ route('purchases.entry') }}?item_id={{ $item->id }}" 
                                   class="px-3 py-1 text-[10px] font-bold uppercase tracking-widest bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 shadow-sm transition-all active:scale-95">
                                    🛒 Refill
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center opacity-40">
                                <span class="text-5xl mb-4">🏪</span>
                                <p class="text-sm font-bold uppercase tracking-widest">No items found in inventory</p>
                                <a href="{{ route('inventory.items.create') }}" class="text-emerald-600 text-xs mt-2 underline italic font-medium">Add your first product to track stock</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
