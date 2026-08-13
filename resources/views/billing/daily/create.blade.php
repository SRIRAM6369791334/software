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
                
                @if ($errors->any())
                    <div class="mb-4 p-4 rounded-xl bg-red-50 border border-red-200">
                        <ul class="list-disc pl-5 text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                    <x-form.select name="dealer_id" id="dealer_id" label="Dealer (Transfer From)" required>
                        <option value="">Select Dealer</option>
                        @foreach($dealers as $dealer)
                            <option value="{{ $dealer->id }}">{{ $dealer->firm_name }}</option>
                        @endforeach
                    </x-form.select>
                    
                    <x-form.select name="customer_id" label="Customer (Transfer To)" required>
                        <option value="">Select Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->route }})</option>
                        @endforeach
                    </x-form.select>
                    
                    <x-form.input type="date" name="date" id="billing_date" label="Billing Date" value="{{ date('Y-m-d') }}" required />
                </div>

                <!-- Stock Display -->
                <div id="stock_display" class="hidden mb-5 p-4 rounded-xl border flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center" id="stock_icon_bg">
                            <span class="material-symbols-rounded" id="stock_icon">inventory_2</span>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-500 font-medium">Dealer's Available Stock</p>
                            <h4 class="text-xl font-bold font-jetbrains" id="stock_amount">0.00 kg</h4>
                        </div>
                    </div>
                    <div class="text-right text-xs text-zinc-500 font-medium">
                        Total Received: <span id="total_received" class="font-bold">0.00 kg</span><br>
                        Already Billed: <span id="total_billed" class="font-bold">0.00 kg</span>
                    </div>
                </div>

                <div class="mb-5">
                    <x-form.input type="text" name="items[0][name]" label="Items Description" placeholder="e.g. Broiler Birds (Small Size)" required />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                    <x-form.input type="number" name="items[0][qty]" label="Quantity (kg)" step="0.01" placeholder="0.00" required />
                    <x-form.input type="number" name="items[0][rate]" label="Rate per kg (Rs)" step="0.01" placeholder="0.00" required />
                    <x-form.input type="number" name="amount" label="Total Amount (Rs)" step="0.01" required class="text-emerald-600 dark:text-emerald-400 font-bold" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-2">Payment Status <span class="text-rose-500">*</span></label>
                        <div class="flex gap-4">
                            @foreach(['Pending', 'Paid'] as $st)
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="radio" name="status" value="{{ $st }}" {{ $st === 'Pending' ? 'checked' : '' }} class="w-4 h-4 text-emerald-600 focus:ring-emerald-500 bg-white dark:bg-zinc-900 border-zinc-300 dark:border-zinc-600">
                                <span class="text-sm font-outfit text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-zinc-100 transition-colors">{{ $st }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <x-form.select name="payment_mode" label="Payment Mode" required>
                            <option value="Cash">Cash</option>
                            <option value="UPI">UPI</option>
                            <option value="NEFT">NEFT</option>
                            <option value="Cheque(Bank Transfer)">Cheque (Bank Transfer)</option>
                            <option value="Pay later(EMI)">Pay later(EMI)</option>
                        </x-form.select>
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
    const qty = document.querySelector('input[name="items[0][qty]"]');
    const rate = document.querySelector('input[name="items[0][rate]"]');
    const total = document.querySelector('input[name="amount"]');
    
    const dealerSelect = document.getElementById('dealer_id');
    const dateInput = document.getElementById('date');
    const stockDisplay = document.getElementById('stock_display');
    const stockAmount = document.getElementById('stock_amount');
    const totalReceived = document.getElementById('total_received');
    const totalBilled = document.getElementById('total_billed');
    const stockIcon = document.getElementById('stock_icon');
    const stockIconBg = document.getElementById('stock_icon_bg');
    
    let currentAvailableStock = 0;

    function calculate() {
        if (!qty || !rate || !total) return;
        let q = parseFloat(qty.value) || 0;
        const r = parseFloat(rate.value) || 0;
        
        const isStockFetched = !stockDisplay.classList.contains('hidden');

        // Auto-correct and show SweetAlert
        if (isStockFetched && qty.value !== '' && q > currentAvailableStock) {
            qty.value = currentAvailableStock > 0 ? currentAvailableStock : '';
            q = currentAvailableStock > 0 ? currentAvailableStock : 0;
            
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Limit Exceeded',
                    text: 'Max available stock is ' + currentAvailableStock.toFixed(2) + ' kg',
                    confirmButtonColor: '#10b981'
                });
            }
        }

        total.value = (q && r) ? (q * r).toFixed(2) : '';
        
        // Stock warning visualization
        if (isStockFetched) {
            if (currentAvailableStock === 0) {
                qty.classList.add('border-red-500', 'focus:ring-red-500');
                stockDisplay.classList.remove('border-emerald-200', 'bg-emerald-50/50');
                stockDisplay.classList.add('border-red-200', 'bg-red-50/50');
                stockIconBg.className = 'w-10 h-10 rounded-full flex items-center justify-center bg-red-100 text-red-600';
                stockIcon.textContent = 'error';
                qty.setCustomValidity('Out of stock');
            } else {
                qty.classList.remove('border-red-500', 'focus:ring-red-500');
                stockDisplay.classList.remove('border-red-200', 'bg-red-50/50');
                stockDisplay.classList.add('border-emerald-200', 'bg-emerald-50/50');
                stockIconBg.className = 'w-10 h-10 rounded-full flex items-center justify-center bg-emerald-100 text-emerald-600';
                stockIcon.textContent = 'inventory_2';
                qty.setCustomValidity('');
            }
        } else {
            qty.setCustomValidity('');
        }
    }

    async function fetchDealerStock() {
        const dealerId = dealerSelect.value;
        const date = dateInput.value;
        
        if (!dealerId || !date) {
            stockDisplay.classList.add('hidden');
            if (qty) {
                qty.removeAttribute('max');
                qty.setCustomValidity('');
            }
            return;
        }

        try {
            const response = await fetch(`{{ route('billing.daily.get-dealer-stock') }}?dealer_id=${dealerId}&date=${date}`);
            const data = await response.json();
            
            if (data.success) {
                currentAvailableStock = parseFloat(data.available_stock) || 0;
                stockAmount.textContent = `${currentAvailableStock.toFixed(2)} kg`;
                totalReceived.textContent = `${parseFloat(data.total_stock).toFixed(2)} kg`;
                totalBilled.textContent = `${parseFloat(data.billed_stock).toFixed(2)} kg`;
                
                if (qty) {
                    qty.setAttribute('max', currentAvailableStock);
                    qty.setAttribute('min', '0.01');
                }
                
                stockDisplay.classList.remove('hidden');
                calculate(); // Trigger validation styles
            }
        } catch (error) {
            console.error('Failed to fetch stock:', error);
        }
    }

    if (qty) qty.addEventListener('input', calculate);
    if (rate) rate.addEventListener('input', calculate);
    if (dealerSelect) dealerSelect.addEventListener('change', fetchDealerStock);
    if (dateInput) dateInput.addEventListener('change', fetchDealerStock);
    
    // Fetch stock on initial load if dealer is already selected
    if (dealerSelect && dealerSelect.value) {
        fetchDealerStock();
    }
</script>
@endpush
@endsection
