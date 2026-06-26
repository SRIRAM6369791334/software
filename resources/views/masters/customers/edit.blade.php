@extends('layouts.app')
@section('title', 'Edit Customer')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="mb-4">
        <a href="{{ route('masters.customers.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition-colors">
            <span class="material-symbols-rounded text-[20px]">arrow_back</span>
            Back to Customers
        </a>
    </div>

    <x-page-header 
        title="Edit Customer" 
        subtitle="Update details for {{ $customer->name }}"
    />

    <x-card>
        <form action="{{ route('masters.customers.update', $customer) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <x-form.input 
                    name="name" 
                    label="Full Name" 
                    icon="person" 
                    :value="$customer->name" 
                    required 
                />

                <x-form.input 
                    name="phone" 
                    label="Phone Number" 
                    icon="call" 
                    :value="$customer->phone" 
                    required 
                />

                <div class="md:col-span-2">
                    <x-form.textarea 
                        name="address" 
                        label="Store Address" 
                        :value="$customer->address" 
                        rows="2"
                        required
                    />
                </div>

                <x-form.input 
                    name="gst_number" 
                    label="GST Number" 
                    icon="badge" 
                    :value="$customer->gst_number" 
                />

                @if($routes->isEmpty())
                    <x-form.input 
                        name="route" 
                        label="Route / Area" 
                        icon="alt_route" 
                        :value="$customer->route" 
                        required
                    />
                @else
                    <x-form.select 
                        name="route_id" 
                        label="Route / Area" 
                        :options="$routes->pluck('route_name', 'id')->toArray()" 
                        :selected="$customer->route_id"
                        placeholder="Select Route"
                        required
                    />
                @endif

                <x-form.select 
                    name="type" 
                    label="Customer Type" 
                    :options="['Retail' => 'Retail', 'Wholesale' => 'Wholesale']" 
                    :selected="$customer->type"
                    required
                />

                <x-form.input 
                    type="number" 
                    name="balance" 
                    label="Current Balance (Rs)" 
                    icon="account_balance_wallet" 
                    :value="$customer->balance" 
                    step="0.01" 
                    required
                />
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                <x-button href="{{ route('masters.customers.index') }}" variant="ghost">Cancel</x-button>
                <x-button type="submit" variant="primary">Update Customer</x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection
