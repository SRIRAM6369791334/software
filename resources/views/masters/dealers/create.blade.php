@extends('layouts.app')
@section('title', 'Add Dealer')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('masters.dealers.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition-colors">
            <span class="material-symbols-rounded text-[20px]">arrow_back</span>
            Back to Directory
        </a>
    </div>

    <x-page-header 
        title="Add New Dealer" 
        subtitle="Expand your procurement network with a new supplier profile"
    />

    <x-card>
        <form action="{{ route('masters.dealers.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <x-form.input 
                        name="firm_name" 
                        label="Firm Identity" 
                        icon="store" 
                        placeholder="e.g. Superior Feed Mills" 
                        required 
                    />
                </div>

                <x-form.input 
                    name="contact_person" 
                    label="Contact Person" 
                    icon="person" 
                    placeholder="e.g. Sales Manager" 
                />

                <x-form.input 
                    name="phone" 
                    label="Phone Number" 
                    icon="call" 
                    placeholder="+91 00000 00000" 
                    required 
                />

                <x-form.input 
                    name="gst_number" 
                    label="GSTIN" 
                    icon="badge" 
                    placeholder="Optional GSTIN" 
                    class="uppercase"
                />

                <x-form.input 
                    name="location" 
                    label="Location / City" 
                    icon="location_on" 
                    placeholder="e.g. Industrial Estate" 
                />

                <x-form.input 
                    name="route" 
                    label="Route" 
                    icon="alt_route" 
                    placeholder="Supply route..." 
                />

                <x-form.input 
                    type="number" 
                    name="pending_amount" 
                    label="Opening Outstanding (Rs)" 
                    icon="account_balance_wallet" 
                    value="0.00" 
                    step="0.01" 
                />
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <x-button type="reset" variant="ghost">Reset</x-button>
                <x-button type="submit" variant="primary" icon="check_circle">Register Dealer</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
