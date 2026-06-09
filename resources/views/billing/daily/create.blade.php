@extends('layouts.app')
@section('title', 'Create Daily Invoice')

@section('content')
<div class="mb-6 animate-fade-in max-w-3xl mx-auto">
    <a href="{{ route('billing.daily.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2">
        <span class="material-symbols-rounded text-sm">arrow_back</span>
        Back to Daily Billing
    </a>
    <h1 class="font-cabinet text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Create Daily Invoice</h1>
</div>

<div class="max-w-3xl mx-auto animate-fade-in">
    <x-card>
        <div class="p-6">
            <form action="{{ route('billing.daily.store') }}" method="POST">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <x-form.select name="customer_id" label="Customer" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->route }})</option>
                        @endforeach
                    </x-form.select>
                    <x-form.input type="date" name="date" label="Billing Date" value="{{ date('Y-m-d') }}" required />
                </div>

                <div class="mb-5">
                    <x-form.input type="text" name="items_description" label="Items Description" placeholder="e.g. Broiler Birds (Small Size)" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                    <x-form.input type="number" name="quantity_kg" id="qty" label="Quantity (kg)" step="0.01" placeholder="0.00" />
                    <x-form.input type="number" name="rate_per_kg" id="rate" label="Rate per kg (Rs)" step="0.01" placeholder="0.00" />
                    <x-form.input type="number" name="amount" id="total" label="Total Amount (Rs)" step="0.01" required class="text-emerald-600 dark:text-emerald-400 font-bold" />
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-2">Payment Status <span class="text-rose-500">*</span></label>
                    <div class="flex gap-4">
                        @foreach(['Generated', 'Pending', 'Paid'] as $st)
                        <label class="flex items-center gap-2 cursor-pointer group">
                            <input type="radio" name="status" value="{{ $st }}" {{ $st === 'Generated' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 bg-white dark:bg-zinc-900 border-zinc-300 dark:border-zinc-600">
                            <span class="text-sm font-outfit text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-zinc-100 transition-colors">{{ $st }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="pt-5 border-t border-zinc-200/50 dark:border-zinc-800/50 flex justify-end gap-3">
                    <x-button type="submit" variant="primary" icon="receipt_long">
                        Generate Invoice 
                    </x-button>
                </div>
            </form>
        </div>
    </x-card>
</div>

@push('scripts')
<script>
    const qty = document.getElementById('qty');
    const rate = document.getElementById('rate');
    const total = document.getElementById('total');

    function calculate() {
        const q = parseFloat(qty.value) || 0;
        const r = parseFloat(rate.value) || 0;
        if (q && r) {
            total.value = (q * r).toFixed(2);
        }
    }

    qty.addEventListener('input', calculate);
    rate.addEventListener('input', calculate);
</script>
@endpush
@endsection
