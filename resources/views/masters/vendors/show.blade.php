@extends('layouts.app')
@section('title', 'Vendor Details - ' . $vendor->firm_name)

@section('content')
<div class="space-y-6">
    <div class="mb-4">
        <a href="{{ route('masters.vendors.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition-colors">
            <span class="material-symbols-rounded text-[20px]">arrow_back</span>
            Back to Directory
        </a>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <x-avatar name="{{ $vendor->firm_name }}" size="lg" />
            <div>
                <h1 class="text-2xl font-bold font-cabinet text-zinc-900 dark:text-zinc-100 tracking-tight">{{ $vendor->firm_name }}</h1>
                <div class="flex items-center gap-2 mt-1">
                    <x-badge color="teal">Supplier Partner</x-badge>
                    <x-badge color="zinc">
                        <span class="material-symbols-rounded text-[14px] mr-1">alt_route</span>
                        {{ $vendor->route ?: 'General Sector' }}
                    </x-badge>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <x-button href="{{ route('masters.vendors.edit', $vendor) }}" variant="secondary" icon="edit">Edit Profile</x-button>
            <form action="{{ route('masters.vendors.destroy', $vendor) }}" method="POST" onsubmit="return confirm('Delete {{ $vendor->firm_name }}? This will keep their transaction history intact.')">
                @csrf @method('DELETE')
                <x-button type="submit" variant="danger" icon="delete">Delete</x-button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-1 space-y-6">
            <x-card title="Profile Credentials" icon="contact_page">
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-zinc-400">person</span>
                        <div>
                            <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Contact Person</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $vendor->contact_person ?: 'Not specified' }}</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-zinc-400">call</span>
                        <div>
                            <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Contact Phone</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $vendor->phone }}</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-zinc-400">location_on</span>
                        <div>
                            <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Firm Location</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $vendor->location ?: 'Not set' }}</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-zinc-400">badge</span>
                        <div>
                            <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">GSTIN / Registration</div>
                            <div class="font-mono text-sm text-zinc-900 dark:text-zinc-100">{{ $vendor->gst_number ?: 'Unregistered' }}</div>
                        </div>
                    </div>
                </div>
            </x-card>

            @if($vendor->notes)
                <x-card title="Vendor Notes" icon="description" class="border-l-4 border-l-teal-500">
                    <div class="text-sm text-zinc-600 dark:text-zinc-400 whitespace-pre-line leading-relaxed">
                        {{ $vendor->notes }}
                    </div>
                </x-card>
            @endif
        </div>

        <div class="lg:col-span-2">
            <x-card padding="p-0" class="overflow-hidden">
                <div class="flex flex-wrap border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
                    <a href="{{ route('masters.vendors.show', $vendor) }}" class="flex-1 text-center py-4 text-sm font-bold text-teal-600 border-b-2 border-teal-600 bg-white dark:bg-zinc-900 transition-colors">
                        Quick Look
                    </a>
                    <a href="{{ route('masters.vendors.purchase-history', $vendor) }}" class="flex-1 text-center py-4 text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        Full Purchase History
                    </a>
                </div>

                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">Recent Supply Activity</h4>
                        <x-button href="{{ route('purchases.create', ['vendor_name' => $vendor->firm_name]) }}" variant="primary" size="sm" icon="add">
                            Record Entry
                        </x-button>
                    </div>

                    <x-data-table :headers="['Date', 'Item Details', ['label' => 'Quantity', 'align' => 'right'], ['label' => 'Total Bill', 'align' => 'right']]">
                        @forelse($vendor->purchases()->with('items')->latest()->take(5)->get() as $purchase)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                                <td class="px-6 py-4 font-semibold text-sm text-zinc-700 dark:text-zinc-300">
                                    {{ $purchase->date->format('d M Y') }}
                                </td>
                                <td class="px-6 py-4 font-bold text-sm text-zinc-900 dark:text-zinc-100">
                                    @if($purchase->items->isNotEmpty())
                                        {{ $purchase->items->pluck('item_name')->join(', ') }}
                                    @else
                                        {{ $purchase->item }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-sm text-zinc-600 dark:text-zinc-400">
                                    @if($purchase->items->isNotEmpty())
                                        {{ number_format($purchase->items->sum('quantity'), 2) }} {{ $purchase->items->first()->unit }}
                                    @else
                                        {{ number_format($purchase->quantity, 2) }} {{ $purchase->unit }}
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-sm text-zinc-900 dark:text-zinc-100 font-jetbrains">
                                    Rs {{ number_format($purchase->total_amount, 0) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <x-empty-state 
                                        icon="inventory_2" 
                                        title="No supplies logged" 
                                        subtitle="No recent transaction entries found for this supplier." 
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </x-data-table>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
