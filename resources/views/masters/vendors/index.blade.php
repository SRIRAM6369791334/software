@extends('layouts.app')
@section('title', 'Vendor Master')

@section('content')
<div class="space-y-6">
    <x-page-header 
        title="Vendor Master" 
        subtitle="Directory of logistics and pharmaceutical suppliers"
    >
        <x-slot:actions>
            <x-button href="{{ route('masters.vendors.create') }}" variant="primary" icon="add">
                Register Vendor
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-stat-card title="Total Suppliers" value="{{ $vendors->total() }}" icon="local_shipping" color="teal" />
        <x-stat-card title="Route Reach" value="{{ $vendors->pluck('route')->filter()->unique()->count() }}" icon="route" color="blue" subtitle="Routes" />
    </div>

    <x-card padding="p-0">
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50 flex flex-wrap gap-4 items-center justify-between bg-zinc-50/50 dark:bg-zinc-800/50">
            <form action="{{ route('masters.vendors.index') }}" method="GET" class="flex flex-wrap gap-4 items-center w-full md:w-auto">
                <div class="w-full md:w-64">
                    <x-search name="search" value="{{ request('search') }}" placeholder="Search firm, contact..." />
                </div>
                
                <div class="w-full md:w-64">
                    <x-form.select 
                        name="route" 
                        :options="['' => 'All Routes'] + collect($routes)->mapWithKeys(fn($rt) => [$rt => $rt])->toArray()" 
                        :selected="request('route')" 
                        onchange="this.form.submit()" 
                    />
                </div>

                @if(request('search') || request('route'))
                    <x-button href="{{ route('masters.vendors.index') }}" variant="secondary" size="md">Clear</x-button>
                @endif
            </form>
        </div>

        <x-data-table :headers="['Firm & Location', 'Point of Contact', 'Route', 'GSTIN', 'Actions']">
            @forelse($vendors as $vendor)
                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <x-avatar name="{{ $vendor->firm_name }}" size="sm" />
                            <div>
                                <a href="{{ route('masters.vendors.show', $vendor) }}" class="font-medium text-zinc-900 dark:text-zinc-100 hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                    {{ $vendor->firm_name }}
                                </a>
                                <div class="text-xs text-zinc-500">{{ $vendor->location ?: 'No Location Specified' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $vendor->contact_person ?: 'No contact person' }}</div>
                        <div class="text-xs text-zinc-500">{{ $vendor->phone }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $vendor->route ?: 'General Sector' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-jetbrains text-zinc-600 dark:text-zinc-400">{{ $vendor->gst_number ?: 'UNREGISTERED' }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <x-button href="{{ route('masters.vendors.edit', $vendor) }}" variant="ghost" size="sm" icon="edit" title="Edit" />
                            <form action="{{ route('masters.vendors.destroy', $vendor) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ $vendor->firm_name }}?');">
                                @csrf @method('DELETE')
                                <x-button type="submit" variant="ghost" size="sm" icon="delete" class="text-rose-500 hover:text-rose-600" title="Delete" />
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <x-empty-state 
                            icon="inventory_2" 
                            title="No vendors found" 
                            subtitle="Start by registering your first supply partner." 
                        />
                    </td>
                </tr>
            @endforelse

            @if($vendors->hasPages())
                <x-slot:pagination>
                    {{ $vendors->withQueryString()->links() }}
                </x-slot:pagination>
            @endif
        </x-data-table>
    </x-card>
</div>
@endsection
