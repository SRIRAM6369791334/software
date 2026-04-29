@extends('layouts.app')
@section('title', 'Generate New Invoice')

@section('content')
<div class="max-w-5xl mx-auto space-y-8">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('billing.daily.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-primary-500 uppercase tracking-widest hover:gap-3 transition-all mb-3">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                Back to Billing
            </a>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Create Daily Invoice</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Issue a new billing statement for daily sales</p>
        </div>
    </div>

    <form action="{{ route('billing.daily.store') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        @csrf
        
        <!-- Left: Form Inputs -->
        <div class="lg:col-span-2 space-y-8">
            <x-card title="Invoice Details" subtitle="General billing information">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-1">Customer <span class="text-red-500">*</span></label>
                        <select name="customer_id" required class="w-full bg-slate-50 border-slate-200 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 rounded-2xl py-3 px-5 text-sm font-medium transition-all outline-none appearance-none">
                            <option value="">Select Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->route }})</option>
                            @endforeach
                        </select>
                    </div>
                    <x-input label="Billing Date" name="date" type="date" value="{{ date('Y-m-d') }}" required />
                </div>

                <div class="mt-6">
                    <x-input label="Items Description" name="items_description" placeholder="e.g. Broiler Birds (Small Size)" 
                             :icon="'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />'" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <x-input label="Quantity (kg)" name="quantity_kg" id="qty" type="number" step="0.01" placeholder="0.00" 
                             :icon="'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />'" />
                    <x-input label="Rate per kg (₹)" name="rate_per_kg" id="rate" type="number" step="0.01" placeholder="0.00" 
                             :icon="'<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />'" />
                </div>
            </x-card>

            <x-card title="Payment Status" subtitle="Mark current state of invoice">
                <div class="flex flex-wrap gap-4">
                    @foreach(['Generated', 'Pending', 'Paid'] as $st)
                        <label class="flex-1 cursor-pointer group">
                            <input type="radio" name="status" value="{{ $st }}" {{ $st === 'Generated' ? 'checked' : '' }} class="sr-only peer">
                            <div class="p-4 rounded-2xl border-2 border-slate-100 bg-white text-center transition-all peer-checked:border-primary-500 peer-checked:bg-primary-50 group-hover:bg-slate-50">
                                <p class="text-sm font-bold text-slate-700 group-hover:text-slate-900 peer-checked:text-primary-700">{{ $st }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </x-card>
        </div>

        <!-- Right: Summary & Action -->
        <div class="space-y-6">
            <x-card title="Billing Summary" subtitle="Auto-calculated totals">
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-medium">Subtotal</span>
                        <span class="text-slate-900 font-bold" id="display-subtotal">₹0.00</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-slate-500 font-medium">Tax (0%)</span>
                        <span class="text-slate-900 font-bold">₹0.00</span>
                    </div>
                    <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                        <span class="text-base font-bold text-slate-900">Grand Total</span>
                        <span class="text-2xl font-black text-primary-500" id="display-total">₹0.00</span>
                    </div>
                </div>

                <input type="hidden" name="amount" id="total" required>

                <x-slot name="footer">
                    <x-button variant="primary" size="lg" class="w-full" type="submit">
                        <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></x-slot>
                        Generate Invoice
                    </x-button>
                </x-slot>
            </x-card>

            <div class="p-6 bg-slate-900 rounded-[2rem] text-white">
                <h4 class="text-sm font-bold mb-2">Quick Tip 💡</h4>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Ensure the Rate per kg matches the current market rate for bird sizes. Rates are calculated in real-time.
                </p>
            </div>
        </div>
    </form>
</div>

<script>
    const qtyInput = document.getElementById('qty');
    const rateInput = document.getElementById('rate');
    const totalHidden = document.getElementById('total');
    const displaySubtotal = document.getElementById('display-subtotal');
    const displayTotal = document.getElementById('display-total');

    function calculate() {
        const q = parseFloat(qtyInput.value) || 0;
        const r = parseFloat(rateInput.value) || 0;
        const total = (q * r).toFixed(2);
        
        const formatted = new Intl.NumberFormat('en-IN', {
            style: 'currency',
            currency: 'INR'
        }).format(total);

        totalHidden.value = total;
        displaySubtotal.innerText = formatted;
        displayTotal.innerText = formatted;
    }

    qtyInput.addEventListener('input', calculate);
    rateInput.addEventListener('input', calculate);
</script>
@endsection

