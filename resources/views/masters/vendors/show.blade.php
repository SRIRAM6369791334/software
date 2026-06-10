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
            @can('edit vendors')
                <x-button href="{{ route('masters.vendors.edit', $vendor) }}" variant="secondary" icon="edit">Edit Profile</x-button>
            @endcan
            @can('delete vendors')
                <form action="{{ route('masters.vendors.destroy', $vendor) }}" method="POST" onsubmit="return confirm('Delete {{ $vendor->firm_name }}? This will keep their transaction history intact.')">
                    @csrf @method('DELETE')
                    <x-button type="submit" variant="danger" icon="delete">Delete</x-button>
                </form>
            @endcan
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-3xl p-6 bg-teal-500/40 dark:bg-teal-900/40 backdrop-blur-2xl text-teal-900 dark:text-teal-100 shadow-[0_8px_32px_rgba(20,184,166,0.15)] border border-teal-300/50 dark:border-teal-700/50 relative overflow-hidden transition-all duration-300 hover:shadow-[0_8px_32px_rgba(20,184,166,0.25)] hover:-translate-y-1">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/20 dark:bg-teal-400/10 rounded-full blur-2xl"></div>
                <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-teal-400/20 dark:bg-teal-600/20 rounded-full blur-2xl"></div>
                <div class="relative z-10 text-center">
                    <div class="text-xs font-bold uppercase tracking-widest text-teal-800/80 dark:text-teal-200 mb-2">Total Business Volume</div>
                    <div class="text-3xl font-extrabold tracking-tight font-jetbrains mb-6 text-teal-950 dark:text-white drop-shadow-sm">
                        Rs {{ number_format($vendor->purchases()->sum('total_amount'), 2) }}
                    </div>
                    <div class="flex flex-col gap-3">
                        @can('create purchases')
                            <x-button href="{{ route('purchases.create', ['vendor_name' => $vendor->firm_name]) }}" variant="secondary" icon="add_shopping_cart" class="w-full justify-center !text-teal-700 !bg-white/80 hover:!bg-white !border-white backdrop-blur-md shadow-sm">
                                New Purchase Entry
                            </x-button>
                        @endcan
                        <x-button href="{{ route('masters.vendors.purchase-history', $vendor) }}" variant="secondary" icon="history" class="w-full justify-center !bg-teal-600/20 !text-teal-900 dark:!text-teal-100 !border-teal-400/30 hover:!bg-teal-600/30 backdrop-blur-md">
                            View Full History
                        </x-button>
                    </div>
                </div>
            </div>
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
            <div id="cm-tabs-container" class="bg-white/30 dark:bg-zinc-900/40 backdrop-blur-2xl border border-white/60 dark:border-zinc-800/80 rounded-[2rem] overflow-hidden shadow-[0_8px_32px_rgba(31,38,135,0.07)] z-10 relative">
                <div class="flex flex-wrap p-2 m-4 bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md rounded-2xl border border-white/50 dark:border-zinc-700/50 gap-2">
                    <a href="{{ route('masters.vendors.show', $vendor) }}" class="flex-1 text-center py-3 text-sm font-bold text-teal-700 dark:text-teal-400 bg-white/70 dark:bg-zinc-800/80 shadow-sm rounded-xl transition-all duration-300">
                        Quick Look
                    </a>
                    @can('view vendor purchases')
                    <a href="{{ route('masters.vendors.purchase-history', $vendor) }}" class="flex-1 text-center py-3 text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-white/50 dark:hover:bg-zinc-800/50 rounded-xl transition-all duration-300">
                        Full Purchase History
                    </a>
                    @endcan
                </div>

                <div class="p-6">
                    <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider mb-6">Recent Activity Insights</h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="p-4 rounded-2xl border border-white/60 dark:border-zinc-700 shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)] bg-white/40 dark:bg-zinc-900/40 backdrop-blur-xl flex items-center gap-4 transition-all duration-300 hover:bg-white/60">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                <span class="material-symbols-rounded text-xl">shopping_cart</span>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Total Purchases</div>
                                <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ $vendor->purchases()->count() }}
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl border border-white/60 dark:border-zinc-700 shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)] bg-white/40 dark:bg-zinc-900/40 backdrop-blur-xl flex items-center gap-4 transition-all duration-300 hover:bg-white/60">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <span class="material-symbols-rounded text-xl">payments</span>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Total Volume</div>
                                <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100 font-jetbrains">
                                    Rs {{ number_format($vendor->purchases()->sum('total_amount'), 0) }}
                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl border border-white/60 dark:border-zinc-700 shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)] bg-white/40 dark:bg-zinc-900/40 backdrop-blur-xl flex items-center gap-4 transition-all duration-300 hover:bg-white/60">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                                <span class="material-symbols-rounded text-xl">calendar_today</span>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Last Purchase</div>
                                <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                                    {{ $vendor->purchases()->latest('date')->first()?->date->format('d M y') ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">Recent Supply Activity</h4>
                            @can('create purchases')
                                <x-button href="{{ route('purchases.create', ['vendor_name' => $vendor->firm_name]) }}" variant="primary" size="sm" icon="add">
                                    Record Entry
                                </x-button>
                            @endcan
                        </div>

                    <x-data-table :headers="['Date', 'Item Details', ['label' => 'Quantity', 'align' => 'right'], ['label' => 'Total Bill', 'align' => 'right']]">
                        @forelse($vendor->purchases()->with('items')->latest()->take(5)->get() as $purchase)
                            <tr class="hover:bg-white/80 dark:hover:bg-zinc-800/50 transition-all duration-300">
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
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
