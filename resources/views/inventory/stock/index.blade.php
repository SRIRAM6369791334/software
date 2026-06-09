@extends('layouts.app')
@section('title', 'Stock Dashboard')

@section('content')

<div class="cm-page">

    {{-- Top Bar --}}
    <div class="cm-topbar">
        <div>
            <h1 class="cm-page-title">Stock Dashboard</h1>
            <p class="cm-page-sub">Real-time inventory overview and stock status alerts</p>
        </div>
        <a href="{{ route('inventory.stock.movements') }}" class="cm-btn-primary cm-btn-primary--blue">
            <span class="material-symbols-rounded text-[18px]">receipt_long</span>
            View Stock Ledgers
        </a>
    </div>

    {{-- Stats --}}
    <div class="cm-stats">
        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-teal">
                <span class="material-symbols-rounded">inventory</span>
            </div>
            <div>
                <div class="cm-stat-label">Tracked Items</div>
                <div class="cm-stat-value">{{ $stats['total_items'] }}</div>
            </div>
        </div>
        
        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-amber">
                <span class="material-symbols-rounded">warning</span>
            </div>
            <div>
                <div class="cm-stat-label">Low Stock</div>
                <div class="cm-stat-value text-amber-600">{{ $stats['low_stock_count'] }}</div>
            </div>
        </div>

        <div class="cm-stat-card">
            <div class="cm-stat-icon" style="background: rgba(220, 38, 38, 0.1); color: #dc2626;">
                <span class="material-symbols-rounded">error</span>
            </div>
            <div>
                <div class="cm-stat-label">Out of Stock</div>
                <div class="cm-stat-value text-red-600">{{ $stats['out_of_stock_count'] }}</div>
            </div>
        </div>

        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-blue">
                <span class="material-symbols-rounded">verified</span>
            </div>
            <div>
                <div class="cm-stat-label">Active Items</div>
                <div class="cm-stat-value">{{ $items->where('is_active', true)->count() }}</div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="cm-table-card">
        <div class="cm-table-toolbar">
            <form action="{{ route('inventory.stock.index') }}" method="GET" class="flex items-center gap-4 w-full">
                <div class="cm-search-wrap" style="flex: 1; max-width: 320px;">
                    <svg class="cm-search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search item..." class="cm-search-input">
                </div>
                
                <select name="type" onchange="this.form.submit()" class="cm-form-input" style="width: auto;">
                    <option value="">All Types</option>
                    <option value="Feed" {{ request('type') == 'Feed' ? 'selected' : '' }}>Feed</option>
                    <option value="Medicine" {{ request('type') == 'Medicine' ? 'selected' : '' }}>Medicine</option>
                    <option value="Chicks" {{ request('type') == 'Chicks' ? 'selected' : '' }}>Chicks</option>
                    <option value="Vaccine" {{ request('type') == 'Vaccine' ? 'selected' : '' }}>Vaccine</option>
                </select>
            </form>
        </div>

        <div class="cm-table-wrap">
            <table class="cm-table">
                <thead>
                    <tr>
                        <th>Item Details</th>
                        <th>Type / Category</th>
                        <th class="cm-th-right">Available Stock</th>
                        <th>Stock Health</th>
                        <th class="cm-th-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr class="cm-tr">
                        <td class="cm-td">
                            <div class="cm-identity">
                                <div class="cm-avatar cm-avatar--{{ strtolower(substr($item->name, 0, 1)) }}">
                                    {{ strtoupper(substr($item->name, 0, 2)) }}
                                </div>
                                <div>
                                    <span class="cm-cust-name">{{ $item->name }}</span>
                                    <div class="cm-gst-mono">{{ $item->code }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="cm-td">
                            <div class="cm-cust-name">{{ $item->type }}</div>
                            <div class="cm-cust-meta uppercase tracking-wider">{{ $item->category ?: 'General' }}</div>
                        </td>
                        <td class="cm-td cm-td-right text-right">
                            <div class="cm-balance {{ $item->current_stock <= 0 ? 'text-red-600' : '' }}">
                                {{ number_format($item->current_stock, 2) }}
                            </div>
                            <div class="cm-cust-meta">{{ $item->base_unit }}</div>
                        </td>
                        <td class="cm-td">
                            @php
                                $statusClass = match($item->stock_status) {
                                    'Healthy' => 'bg-emerald-100 text-emerald-700 border border-emerald-200',
                                    'Low Stock' => 'bg-amber-100 text-amber-700 border border-amber-200',
                                    'Out of Stock' => 'bg-red-100 text-red-700 border border-red-200',
                                    default => 'bg-zinc-100 text-zinc-700 border border-zinc-200'
                                };
                            @endphp
                            <div class="flex flex-col gap-1 items-start">
                                <span class="px-2 py-1 rounded text-[10px] font-bold uppercase tracking-widest {{ $statusClass }}">
                                    {{ $item->stock_status }}
                                </span>
                                @if($item->stock_status === 'Low Stock')
                                    <span class="text-[10px] text-amber-600 font-medium">Threshold: {{ number_format($item->low_stock_threshold) }}</span>
                                @endif
                            </div>
                        </td>
                        <td class="cm-td">
                            <div class="cm-actions">
                                <a href="{{ route('inventory.items.edit', $item->id) }}" 
                                    class="cm-action-btn cm-action-btn--edit" title="Edit Item">
                                    <span class="material-symbols-rounded text-[18px]">edit</span>
                                </a>
                                <a href="{{ route('purchases.entry') }}?item_id={{ $item->id }}" 
                                    class="cm-btn-primary text-xs py-1.5 px-3 ml-2">
                                    Refill
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="cm-empty">
                            <div class="cm-empty-icon">
                                <span class="material-symbols-rounded text-3xl">inventory_2</span>
                            </div>
                            <p class="cm-empty-title">No items found in inventory</p>
                            <p class="cm-empty-sub">
                                <a href="{{ route('inventory.items.create') }}" class="text-emerald-600 hover:underline">Add your first product to track stock</a>
                            </p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('styles')
@include('partials.cm-style')
<style>
/* Additional specific overrides if any */
.cm-td-right { text-align: right; }
</style>
@endpush

