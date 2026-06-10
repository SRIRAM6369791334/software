@extends('layouts.app')
@section('title', 'Vendor Master')

@section('content')
<div class="space-y-6">
    <x-page-header 
        title="Vendor Master" 
        subtitle="Directory of logistics and pharmaceutical suppliers"
    >
        <x-slot:actions>
            @can('create vendors')
                <x-button href="{{ route('masters.vendors.create') }}" variant="primary" icon="add">
                    Register Vendor
                </x-button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-stat-card title="Total Suppliers" value="{{ \App\Models\Vendor::count() }}" icon="local_shipping" color="teal" />
        <x-stat-card title="Active Routes" value="{{ \App\Models\Vendor::distinct('route')->count('route') }}" icon="route" color="blue" />
        <x-stat-card title="GST Registered" value="{{ \App\Models\Vendor::whereNotNull('gst_number')->count() }}" icon="verified" color="emerald" />
        <x-stat-card title="Unregistered" value="{{ \App\Models\Vendor::whereNull('gst_number')->count() }}" icon="warning" color="amber" />
    </div>

    <x-card padding="p-0">
        <div class="p-5 flex flex-wrap gap-4 items-center justify-between relative z-10 border-b border-white/40">
            <form action="{{ route('masters.vendors.index') }}" method="GET" class="flex flex-wrap gap-4 items-center w-full lg:w-auto">
                <div class="w-full sm:w-64">
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
                <tr class="hover:bg-white/80 dark:hover:bg-zinc-800/50 transition-all duration-300 group">
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
                        @if($vendor->gst_number)
                            <x-badge variant="success" class="font-jetbrains">{{ $vendor->gst_number }}</x-badge>
                        @else
                            <x-badge variant="secondary">UNREGISTERED</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <x-button href="{{ route('masters.vendors.history-pdf', $vendor) }}" variant="ghost" size="sm" icon="picture_as_pdf" title="Download History" class="text-rose-600 hover:text-rose-700 dark:text-rose-500 dark:hover:text-rose-400" />
                            @can('edit vendors')
                                <x-button href="{{ route('masters.vendors.edit', $vendor) }}" variant="ghost" size="sm" icon="edit" title="Edit" />
                            @endcan
                            @can('delete vendors')
                                <form action="{{ route('masters.vendors.destroy', $vendor) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ $vendor->firm_name }}?');">
                                    @csrf @method('DELETE')
                                    <x-button type="submit" variant="ghost" size="sm" icon="delete" class="text-rose-500 hover:text-rose-600" title="Delete" />
                                </form>
                            @endcan
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

            <x-slot:pagination>
                {{ $vendors->withQueryString()->links() }}
            </x-slot:pagination>
        </x-data-table>
    </x-card>
</div>
@endsection
