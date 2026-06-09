@extends('layouts.app')
@section('title', 'Edit Vendor')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('masters.vendors.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition-colors">
            <span class="material-symbols-rounded text-[20px]">arrow_back</span>
            Back to Directory
        </a>
    </div>

    <x-page-header 
        title="Edit Vendor Profile" 
        subtitle="Update profile details and credentials for {{ $vendor->firm_name }}"
    />

    <x-card>
        <form action="{{ route('masters.vendors.update', $vendor) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <x-form.input 
                        name="firm_name" 
                        label="Firm Name" 
                        icon="store" 
                        :value="$vendor->firm_name" 
                        required 
                    />
                </div>

                <x-form.input 
                    name="contact_person" 
                    label="Contact Person" 
                    icon="person" 
                    :value="$vendor->contact_person" 
                />

                <x-form.input 
                    name="phone" 
                    label="Phone" 
                    icon="call" 
                    :value="$vendor->phone" 
                    required 
                />

                <x-form.input 
                    name="gst_number" 
                    label="GSTIN" 
                    icon="badge" 
                    :value="$vendor->gst_number" 
                    class="uppercase"
                />

                <x-form.input 
                    name="location" 
                    label="Location / City" 
                    icon="location_on" 
                    :value="$vendor->location" 
                />

                <div class="md:col-span-2">
                    <x-form.input 
                        name="route" 
                        label="Route" 
                        icon="alt_route" 
                        :value="$vendor->route" 
                    />
                </div>

                <div class="md:col-span-2">
                    <x-form.textarea 
                        name="notes" 
                        label="Strategic Notes" 
                        :value="$vendor->notes" 
                        rows="3"
                    />
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <x-button href="{{ route('masters.vendors.index') }}" variant="ghost">Cancel</x-button>
                <x-button type="submit" variant="primary" icon="save">Save Changes</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
