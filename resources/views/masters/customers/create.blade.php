@extends('layouts.app')
@section('title', 'Register Customer')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('masters.customers.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition-colors">
            <span class="material-symbols-rounded text-[20px]">arrow_back</span>
            Back to Customers
        </a>
    </div>

    <x-page-header 
        title="Register New Customer" 
        subtitle="Enter the details below to add a new customer to BizTrack."
    />

    <x-card>
        <form action="{{ route('masters.customers.store') }}" method="POST" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input 
                    name="name" 
                    label="Full Name" 
                    icon="person" 
                    placeholder="e.g. John Doe" 
                    required 
                />

                <x-form.input 
                    name="phone" 
                    label="Phone Number" 
                    icon="call" 
                    placeholder="e.g. 9876543210" 
                    required 
                />

                <div class="md:col-span-2">
                    <x-form.textarea 
                        name="address" 
                        label="Store Address" 
                        placeholder="Street, Area, City..." 
                        rows="2"
                    />
                </div>

                <x-form.input 
                    name="gst_number" 
                    label="GST Number (Optional)" 
                    icon="badge" 
                    placeholder="22AAAAA0000A1Z5" 
                />

                <x-form.select 
                    name="route_id" 
                    label="Route / Area" 
                    :options="$routes->pluck('route_name', 'id')->toArray()" 
                    placeholder="Select Route"
                />

                <x-form.select 
                    name="type" 
                    label="Customer Type" 
                    :options="['Retail' => 'Retail', 'Wholesale' => 'Wholesale']" 
                />

                <x-form.input 
                    type="number" 
                    name="balance" 
                    label="Opening Balance (Rs)" 
                    icon="account_balance_wallet" 
                    value="0.00" 
                    step="0.01" 
                />
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <x-button href="{{ route('masters.customers.index') }}" variant="ghost">Cancel</x-button>
                <x-button type="submit" variant="primary">Register Customer</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
