@extends('layouts.app')
@section('title', 'Purchase Entry')

@section('content')
<div class="space-y-6">

    <x-page-header title="Purchase Entry & Refills" subtitle="Record incoming inventory supply and map it to specific batches & locations">
        <x-button href="{{ route('purchases.invoices') }}" variant="secondary" icon="receipt_long">
            Invoice Archive
        </x-button>
        <x-button href="{{ route('purchases.export') }}" variant="secondary" icon="download">
            Export
        </x-button>
    </x-page-header>

    @can('create purchases')
    {{-- Inline Form Block --}}
    <x-card class="transition-all duration-300" x-data="{ showForm: false }" x-bind:class="showForm ? 'ring-4 ring-emerald-50 dark:ring-emerald-900/30 border-emerald-100 dark:border-emerald-800' : 'hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)]'">
        <div class="flex justify-between items-center cursor-pointer" @click="showForm = !showForm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-[14px] bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-500/20">
                    <span class="material-symbols-rounded text-[22px]">add_circle</span>
                </div>
                <div>
                    <h2 class="text-[1.1rem] font-extrabold text-zinc-800 dark:text-zinc-100 tracking-tight">Record Purchase</h2>
                    <p class="text-[0.75rem] font-semibold text-zinc-400 dark:text-zinc-500 mt-0.5 tracking-wide uppercase">Click to expand and fill details</p>
                </div>
            </div>
            <button type="button" class="flex items-center justify-center h-10 px-4 gap-2 rounded-xl text-sm transition-all duration-300 font-bold" :class="showForm ? 'bg-zinc-800 dark:bg-zinc-100 text-white dark:text-zinc-900' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700'">
                <span class="material-symbols-rounded" x-text="showForm ? 'expand_less' : 'add'"></span>
                <span x-text="showForm ? 'Close Panel' : 'New Entry'"></span>
            </button>
        </div>

        @if ($errors->any())
            <div class="mt-4 mb-4 mx-6 bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-xl relative">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li class="text-sm font-medium">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        <div x-show="showForm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4" class="pt-8 mt-6 border-t border-zinc-100 dark:border-zinc-800">
            <form action="{{ route('purchases.store') }}" method="POST" id="purchase-form" x-data="{ paymentMode: 'Cash' }">
                @csrf
                
                {{-- 1. Header Information Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase mb-1">1. Vendor / Partner <span class="text-rose-500">*</span></label>
                        <x-form.select name="vendor_name" required onchange="updateVendorOutstanding()">
                            <option value="">Select supply partner...</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->firm_name }}" data-outstanding="{{ $vendor->outstanding_balance }}" {{ old('vendor_name') === $vendor->firm_name ? 'selected' : '' }}>
                                    {{ $vendor->firm_name }} @if($vendor->outstanding_balance > 0) (Outstanding: ₹{{ number_format($vendor->outstanding_balance, 0) }}) @endif
                                </option>
                            @endforeach
                        </x-form.select>
                        
                        <div id="vendor-outstanding-info" class="mt-2 text-xs font-bold text-amber-600 dark:text-amber-400 hidden items-center gap-1.5 bg-amber-500/10 border border-amber-500/20 px-2.5 py-1.5 rounded-lg">
                            <span class="material-symbols-rounded text-[16px]">account_balance_wallet</span>
                            <span>Current Dues:</span>
                            <span id="vendor-outstanding-amount" class="font-mono text-amber-700 dark:text-amber-300 font-extrabold">₹0.00</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase mb-1">2. Invoice Number / Bill ID <span class="text-xs text-zinc-400 font-normal">(Auto)</span></label>
                        <x-form.input type="text" name="invoice_no" value="{{ old('invoice_no', $autoInvoiceNo) }}" readonly class="bg-zinc-100 dark:bg-zinc-800 text-zinc-500 cursor-not-allowed font-mono" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase mb-1">3. Billing Date <span class="text-rose-500">*</span></label>
                        <x-form.input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}" />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase mb-1">4. Payment Mode <span class="text-rose-500">*</span></label>
                        <x-form.select name="payment_mode" required x-model="paymentMode">
                            <option value="Cash" {{ old('payment_mode', 'Cash') === 'Cash' ? 'selected' : '' }}>Cash</option>
                            <option value="Pay later(EMI)" {{ old('payment_mode') === 'Pay later(EMI)' ? 'selected' : '' }}>Pay later(EMI)</option>
                            <option value="UPI" {{ old('payment_mode') === 'UPI' ? 'selected' : '' }}>UPI</option>
                            <option value="NEFT" {{ old('payment_mode') === 'NEFT' ? 'selected' : '' }}>NEFT</option>
                            <option value="Cheque(Bank Transfer)" {{ old('payment_mode') === 'Cheque(Bank Transfer)' ? 'selected' : '' }}>Cheque(Bank Transfer)</option>
                        </x-form.select>
                    </div>
                </div>

                <x-emi-schedule-generator totalAmountId="display-total" />

                {{-- 2. Dynamic Refill Rows Table --}}
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-2 text-sm font-bold text-zinc-700 dark:text-zinc-300">
                            <span class="material-symbols-rounded text-zinc-500">list_alt</span>
                            <span>Procured Products</span>
                        </div>
                        <x-button type="button" variant="secondary" onclick="addRow()" size="sm" icon="add">
                            Add Item Row
                        </x-button>
                    </div>

                    <div class="border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden">
                        <table class="w-full text-left text-sm" id="items-table">
                            <thead class="bg-zinc-50 dark:bg-zinc-800/50 text-zinc-500 dark:text-zinc-400 text-xs uppercase font-bold">
                                <tr>
                                    <th class="p-3 w-[30%]">Product / Item <span class="text-rose-500">*</span></th>
                                    <th class="p-3 w-[15%]">Qty <span class="text-rose-500">*</span></th>
                                    <th class="p-3 w-[15%]">Unit</th>
                                    <th class="p-3 w-[15%]">Rate (₹) <span class="text-rose-500">*</span></th>
                                    <th class="p-3 w-[15%] text-right">Total Amount</th>
                                    <th class="p-3 w-[10%]"></th>
                                </tr>
                            </thead>
                            <tbody id="items-body" class="divide-y divide-zinc-100 dark:divide-zinc-800">
                                <tr class="item-row hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors">
                                    <td class="p-3">
                                        <input list="items-list" name="items[0][name]" required onchange="updateUnit(this)" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm bg-white dark:bg-zinc-900 item-selector focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-shadow" placeholder="Select or type product...">
                                        <datalist id="items-list">
                                            @foreach($items as $item)
                                                <option value="{{ $item->name }}" data-unit="{{ $item->base_unit }}"></option>
                                            @endforeach
                                        </datalist>
                                    </td>
                                    <td class="p-3">
                                        <x-form.input type="number" name="items[0][qty]" step="0.01" required placeholder="0.00" class="row-qty" oninput="recalculate()" />
                                    </td>
                                    <td class="p-3">
                                        <x-form.input type="text" name="items[0][unit]" value="kg" class="row-unit" />
                                    </td>
                                    <td class="p-3">
                                        <x-form.input type="number" name="items[0][rate]" step="0.01" required placeholder="0.00" class="row-rate" oninput="recalculate()" />
                                    </td>
                                    <td class="p-3 text-right font-bold text-zinc-800 dark:text-zinc-200 text-base">
                                        <span class="row-total">₹0.00</span>
                                    </td>
                                    <td class="p-3 text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- 3. Billing Summaries Block --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-end">
                    <div class="bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5">
                        <label class="block text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-4">Tax Configuration</label>
                        <div class="flex items-center gap-4">
                            <div class="w-24">
                                <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase mb-1">GST %</label>
                                <x-form.input type="number" name="gst_percentage" id="gst-percentage" value="18" step="0.1" class="font-bold text-center" oninput="recalculate()" />
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-zinc-500 dark:text-zinc-400 uppercase mb-1">Computed GST Value</label>
                                <x-form.input type="text" name="display_tax" id="display-tax" readonly value="₹0.00" class="bg-zinc-100 dark:bg-zinc-800 font-mono" tabindex="-1" />
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-xl p-6 shadow-xl shadow-emerald-500/20 text-white flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div>
                            <span class="text-emerald-100 text-xs font-bold uppercase tracking-wider block mb-1">Final Grand Net Total</span>
                            <span id="display-total" class="text-3xl font-black font-mono tracking-tight">₹0.00</span>
                        </div>
                        <button type="submit" class="w-full sm:w-auto bg-white text-emerald-700 hover:bg-emerald-50 px-6 py-3 rounded-lg font-bold flex items-center justify-center gap-2 transition-transform active:scale-95 shadow-md">
                            <span class="material-symbols-rounded">check_circle</span>
                            Save Purchase
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-card>
    @endcan
        
    {{-- 4. Recent Purchase Logs Directory --}}
    <x-card>
        <div class="flex flex-col sm:flex-row gap-4 mb-6 justify-between items-center">
            <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Recent Purchase Logs</h2>
            <form method="GET" class="flex w-full sm:w-80">
                <x-search name="search" value="{{ $search }}" placeholder="Search vendor or product name..." class="w-full" />
            </form>
        </div>

        <x-data-table>
            <x-slot name="header">
                <tr>
                    <th class="w-[15%]">Date</th>
                    <th class="w-[25%]">Vendor & Invoice ID</th>
                    <th class="w-[30%]">Refilled Products</th>
                    <th class="w-[15%] text-right">Net Amount</th>
                    <th class="w-[10%] text-center">Payment Mode</th>
                    <th class="w-[5%] text-center">Action</th>
                </tr>
            </x-slot>
            @forelse($purchases as $p)
                <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                    <td class="px-4 py-3 text-sm text-zinc-500 dark:text-zinc-400 font-medium">
                        {{ $p->date->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-3">
                            <x-avatar name="{{ $p->vendor_name }}" class="bg-indigo-100 text-indigo-700" />
                            <div>
                                <span class="block font-bold text-zinc-900 dark:text-zinc-100">{{ $p->vendor_name }}</span>
                                <span class="block text-xs text-zinc-500 dark:text-zinc-400 font-mono mt-0.5">Bill ID: {{ $p->invoice_no ?: 'N/A' }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($p->items as $item)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-[11px] font-medium text-zinc-700 dark:text-zinc-300">
                                    {{ $item->item_name }} <b class="ml-1">({{ number_format($item->quantity) }} {{ $item->unit }})</b>
                                </span>
                            @endforeach
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right font-black text-zinc-900 dark:text-zinc-100 text-base">
                        <x-currency :amount="$p->total_amount" />
                    </td>
                    <td class="px-4 py-3 text-center">
                        @php
                            $paymentColor = match(strtolower($p->payment_mode)) {
                                'cash' => 'emerald',
                                'upi' => 'indigo',
                                'neft' => 'sky',
                                'credit' => 'rose',
                                default => 'slate',
                            };
                        @endphp
                        <x-badge :color="$paymentColor">{{ $p->payment_mode }}</x-badge>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <div class="flex items-center justify-center gap-1.5">
                            <x-button variant="ghost" size="sm" href="{{ route('purchases.show', $p->id) }}" icon="visibility" class="text-sky-600" />
                            @can('edit purchases')
                                <x-button variant="ghost" size="sm" href="{{ route('purchases.edit', $p->id) }}" icon="edit" class="text-amber-600" />
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-4 py-12">
                        <x-empty-state icon="receipt" title="No purchases matched" description="Adjust your filter query or record a new purchase above" />
                    </td>
                </tr>
            @endforelse
        </x-data-table>
        
        @if($purchases->hasPages())
            <div class="mt-4">
                {{ $purchases->withQueryString()->links() }}
            </div>
        @endif
    </x-card>

</div>
@endsection

@push('scripts')
<script>

let rowCount = 1;

const ITEM_OPTIONS = `@foreach($items as $item)<option value="{{ $item->name }}" data-unit="{{ $item->base_unit }}"></option>@endforeach`;

function addRow() {
    const body = document.getElementById('items-body');
    const newRow = document.createElement('tr');
    newRow.className = 'item-row hover:bg-zinc-50/50 dark:hover:bg-zinc-800/20 transition-colors border-t border-zinc-100 dark:border-zinc-800';
    newRow.innerHTML = `
        <td class="p-3">
            <input list="items-list" name="items[${rowCount}][name]" required onchange="updateUnit(this)" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm bg-white dark:bg-zinc-900 item-selector focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-shadow" placeholder="Select or type product...">
        </td>
        <td class="p-3">
            <input type="number" name="items[${rowCount}][qty]" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm bg-white dark:bg-zinc-900 row-qty focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-shadow" oninput="recalculate()">
        </td>
        <td class="p-3">
            <input type="text" name="items[${rowCount}][unit]" value="kg" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm bg-white dark:bg-zinc-900 row-unit focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-shadow">
        </td>
        <td class="p-3">
            <input type="number" name="items[${rowCount}][rate]" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 border border-zinc-200 dark:border-zinc-700 rounded-lg text-sm bg-white dark:bg-zinc-900 row-rate focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition-shadow" oninput="recalculate()">
        </td>
        <td class="p-3 text-right font-bold text-zinc-800 dark:text-zinc-200 text-base">
            <span class="row-total">₹0.00</span>
        </td>
        <td class="p-3 text-center">
            <button type="button" onclick="this.closest('tr').remove(); recalculate();" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 transition-colors" title="Remove row">
                <span class="material-symbols-rounded text-[18px]">delete</span>
            </button>
        </td>
    `;
    body.appendChild(newRow);
    rowCount++;
}

function updateUnit(input) {
    const list = document.getElementById('items-list');
    if (!list) return;
    const options = list.options;
    for (let i = 0; i < options.length; i++) {
        if (options[i].value === input.value) {
            const unit = options[i].getAttribute('data-unit');
            const row = input.closest('tr');
            if (unit && row) {
                row.querySelector('.row-unit').value = unit;
            }
            return;
        }
    }
}

function recalculate() {
    let subtotal = 0;
    const rows = document.querySelectorAll('.item-row');
    
    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
        const rate = parseFloat(row.querySelector('.row-rate').value) || 0;
        const total = qty * rate;
        
        row.querySelector('.row-total').textContent = '₹' + total.toFixed(2);
        subtotal += total;
    });
    
    const gstInput = document.getElementById('gst_percentage') || document.getElementById('gst-percentage');
    const gstPercentage = parseFloat(gstInput ? gstInput.value : 0) || 0;
    const gstAmt = subtotal * gstPercentage / 100;
    const finalTotal = subtotal + gstAmt;
    
    const displayTax = document.getElementById('display_tax') || document.getElementById('display-tax');
    if (displayTax) {
        displayTax.value = '₹' + gstAmt.toFixed(2);
    }
    
    const displayTotal = document.getElementById('display-total');
    if (displayTotal) {
        displayTotal.textContent = '₹' + finalTotal.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    }
}


function updateVendorOutstanding() {
    const vendorSelect = document.querySelector('select[name="vendor_name"]');
    const outstandingInfo = document.getElementById('vendor-outstanding-info');
    const outstandingAmount = document.getElementById('vendor-outstanding-amount');
    
    if (!vendorSelect || !outstandingInfo || !outstandingAmount) return;
    
    const selectedOption = vendorSelect.options[vendorSelect.selectedIndex];
    if (!selectedOption || !selectedOption.value) {
        outstandingInfo.classList.add('hidden');
        outstandingInfo.classList.remove('flex');
        return;
    }
    
    const outstanding = parseFloat(selectedOption.getAttribute('data-outstanding')) || 0;
    
    if (outstanding > 0) {
        outstandingAmount.textContent = '₹' + outstanding.toLocaleString('en-IN', { minimumFractionDigits: 2 });
        outstandingInfo.classList.remove('hidden');
        outstandingInfo.classList.add('flex');
    } else {
        outstandingInfo.classList.add('hidden');
        outstandingInfo.classList.remove('flex');
    }
}

window.addEventListener('DOMContentLoaded', () => {
    const selector = document.querySelector('.item-selector');
    if (selector && selector.value) updateUnit(selector);
    
    updateVendorOutstanding();
});
</script>
@endpush

