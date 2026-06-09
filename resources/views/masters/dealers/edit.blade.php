@extends('layouts.app')
@section('title', 'Edit Dealer')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('masters.dealers.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition-colors">
            <span class="material-symbols-rounded text-[20px]">arrow_back</span>
            Back to Directory
        </a>
    </div>

    <x-page-header 
        title="Edit Dealer" 
        subtitle="Modify existing dealer credentials for {{ $dealer->firm_name }}"
    />

    <x-card>
        <form action="{{ route('masters.dealers.update', $dealer) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <x-form.input 
                        name="firm_name" 
                        label="Firm Name" 
                        icon="store" 
                        :value="$dealer->firm_name" 
                        required 
                    />
                </div>

                <x-form.input 
                    name="contact_person" 
                    label="Contact Person" 
                    icon="person" 
                    :value="$dealer->contact_person" 
                />

                <x-form.input 
                    name="phone" 
                    label="Phone Number" 
                    icon="call" 
                    :value="$dealer->phone" 
                    required 
                />

                <x-form.input 
                    name="gst_number" 
                    label="GSTIN" 
                    icon="badge" 
                    :value="$dealer->gst_number" 
                    class="uppercase"
                />

                <x-form.input 
                    name="location" 
                    label="Location" 
                    icon="location_on" 
                    :value="$dealer->location" 
                />

                <x-form.input 
                    name="route" 
                    label="Route" 
                    icon="alt_route" 
                    :value="$dealer->route" 
                />

                <x-form.input 
                    type="number" 
                    name="pending_amount" 
                    label="Outstanding Amount (Rs)" 
                    icon="account_balance_wallet" 
                    :value="$dealer->pending_amount" 
                    step="0.01" 
                />
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <x-button href="{{ route('masters.dealers.index') }}" variant="ghost">Cancel</x-button>
                <x-button type="submit" variant="primary" icon="save">Update Dealer</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
