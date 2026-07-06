@extends('layouts.app')
@section('title', 'Customer Billing')

@section('content')
<div class="animate-fade-in">
    <x-page-header title="Customer Billing" subtitle="Record counter sales, calculate GST automatically, and issue receipts">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('billing.daily.export') }}" icon="download">
                Export
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Insights Header Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-stat-card 
            label="Today's Sales" 
            value="Rs {{ number_format($bills->where('date', date('Y-m-d'))->sum('net_amount'), 0) }}" 
            icon="bar_chart" 
            color="emerald" />
        <x-stat-card 
            label="Avg Ticket Size" 
            value="Rs {{ number_format($bills->avg('net_amount') ?: 0, 0) }}" 
            icon="show_chart" 
            color="blue" />
        <div class="rounded-2xl bg-gradient-to-br from-emerald-500 to-emerald-600 dark:from-emerald-600 dark:to-emerald-800 p-6 shadow-sm text-white flex items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-emerald-500/20">
            <div>
                <p class="font-outfit text-sm font-medium text-emerald-100">Total Counter Cash</p>
                <p class="font-jetbrains mt-2 text-3xl font-bold tracking-tight">Rs {{ number_format($bills->sum('net_amount'), 0) }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                <span class="material-symbols-rounded text-2xl">bolt</span>
            </div>
        </div>
    </div>

    @can('create bills')
    {{-- Inline Expandable Form --}}
    <x-card class="transition-all duration-300 mb-8" x-data="{ showForm: false }" x-bind:class="showForm ? 'ring-4 ring-emerald-50 dark:ring-emerald-900/30 border-emerald-100 dark:border-emerald-800' : 'hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)]'">
        <div class="flex justify-between items-center cursor-pointer" @click="showForm = !showForm">
            <div class="flex items-center gap-4">
                <div class="flex h-12 w-12 items-center justify-center rounded-[14px] bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-lg shadow-emerald-500/20">
                    <span class="material-symbols-rounded text-[22px]">add_circle</span>
                </div>
                <div>
                    <h2 class="text-[1.1rem] font-extrabold text-zinc-800 dark:text-zinc-100 tracking-tight">Record Retail Sale</h2>
                    <p class="text-[0.75rem] font-semibold text-zinc-400 dark:text-zinc-500 mt-0.5 tracking-wide uppercase">Click to expand and fill details</p>
                </div>
            </div>
            <button type="button" class="flex items-center justify-center h-10 px-4 gap-2 rounded-xl text-sm transition-all duration-300 font-bold" :class="showForm ? 'bg-zinc-800 dark:bg-zinc-100 text-white dark:text-zinc-900' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700'">
                <span class="material-symbols-rounded" x-text="showForm ? 'expand_less' : 'add'"></span>
                <span x-text="showForm ? 'Close Panel' : 'New Entry'"></span>
            </button>
        </div>

        <div x-show="showForm" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-4" class="pt-8 mt-6 border-t border-zinc-100 dark:border-zinc-800">
            <form action="{{ route('billing.daily.store') }}" method="POST" id="daily-sale-form" x-data="{ paymentMode: 'Cash' }">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                    <x-form.select name="customer_id" label="Customer" required>
                        <option value="">Select customer...</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </x-form.select>
                    <x-form.input type="date" name="date" label="Sale Date" required value="{{ old('date', date('Y-m-d')) }}" />
                    
                    <x-form.select name="payment_mode" label="Payment Mode" required x-model="paymentMode">
                        <option value="Cash" {{ old('payment_mode', 'Cash') === 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Pay later(EMI)" {{ old('payment_mode') === 'Pay later(EMI)' ? 'selected' : '' }}>Pay later(EMI)</option>
                        <option value="UPI" {{ old('payment_mode') === 'UPI' ? 'selected' : '' }}>UPI</option>
                        <option value="NEFT" {{ old('payment_mode') === 'NEFT' ? 'selected' : '' }}>NEFT</option>
                        <option value="Cheque(Bank Transfer)" {{ old('payment_mode') === 'Cheque(Bank Transfer)' ? 'selected' : '' }}>Cheque(Bank Transfer)</option>
                    </x-form.select>
                    
                    <x-form.select name="status" label="Invoice Status" required>
                        <option value="Paid" {{ old('status', 'Paid') === 'Paid' ? 'selected' : '' }}>Paid</option>
                        <option value="Pending" {{ old('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Generated" {{ old('status') === 'Generated' ? 'selected' : '' }}>Generated</option>
                    </x-form.select>
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit">Sale Items & Birds</label>
                        <x-button type="button" variant="outline" size="sm" icon="add" onclick="addSaleRow()">Add Item</x-button>
                    </div>
                    
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                        <table class="w-full text-sm text-left text-zinc-600 dark:text-zinc-400 font-outfit" id="sale-items-table">
                            <thead class="text-xs text-zinc-500 dark:text-zinc-400 uppercase bg-zinc-100/50 dark:bg-zinc-800 font-cabinet">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Item / Description</th>
                                    <th class="px-4 py-3 font-semibold text-center w-24">Qty</th>
                                    <th class="px-4 py-3 font-semibold text-right w-32">Rate</th>
                                    <th class="px-4 py-3 font-semibold text-right w-32">Subtotal</th>
                                    <th class="px-4 py-3 text-center w-12"></th>
                                </tr>
                            </thead>
                            <tbody id="sale-items-body" class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                <tr class="item-row">
                                    <td class="p-2">
                                        <select name="items[0][name]" required class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2 transition-colors">
                                            @foreach($items as $item)
                                                <option value="{{ $item->name }}" {{ $item->name === 'Live Broiler Birds' ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-2">
                                        <input type="number" name="items[0][qty]" step="0.01" required placeholder="0.0" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2 text-center row-qty" oninput="recalcSales()">
                                    </td>
                                    <td class="p-2">
                                        <input type="number" name="items[0][rate]" step="0.01" required placeholder="0.0" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2 text-right row-rate" oninput="recalcSales()">
                                    </td>
                                    <td class="p-2 text-right font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 row-total">
                                        ₹0.00
                                    </td>
                                    <td class="p-2 text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <x-emi-schedule-generator totalAmountId="display-total" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-end">
                    <div class="bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5">
                        <label class="block text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-4 font-outfit">Tax Settings (GST)</label>
                        <div class="flex items-center gap-4">
                            <div class="w-24">
                                <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">GST %</label>
                                <input type="number" name="gst_percentage" id="gst-percentage" value="18" min="0" max="28" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2 text-center font-jetbrains font-bold" oninput="recalcSales()">
                            </div>
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Calculated GST</label>
                                <div id="display-tax" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-2 font-jetbrains font-bold text-zinc-900 dark:text-zinc-100">₹0.00</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-xl p-6 shadow-xl shadow-emerald-500/20 text-white flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div class="flex flex-col">
                            <span class="text-emerald-100 text-xs font-bold uppercase tracking-wider block mb-1 font-outfit">Grand Total</span>
                            <span id="display-total" class="text-3xl font-black font-jetbrains tracking-tight">₹0.00</span>
                            <input type="hidden" name="amount" id="total-hidden">
                        </div>
                        <button type="submit" class="w-full sm:w-auto bg-white text-emerald-700 hover:bg-emerald-50 px-6 py-3 rounded-lg font-bold flex items-center justify-center gap-2 transition-transform active:scale-95 shadow-md">
                            <span class="material-symbols-rounded">receipt_long</span>
                            Generate Invoice
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </x-card>
    @endcan
    <x-card>
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">Recent Counter Sales</h2>
            <form method="GET" class="relative max-w-sm w-full sm:w-auto">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-400">
                    <span class="material-symbols-rounded text-xl">search</span>
                </div>
                <input type="text" name="search" value="{{ $search }}" class="bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 p-2.5 transition-colors font-outfit" placeholder="Search customer, item or route...">
            </form>
        </div>

        <x-data-table :headers="['Invoice', 'Sale Date', 'Customer', 'Product Breakdown', 'Total Qty', 'Net Amount', 'Status', 'Action']">
            @forelse($bills as $bill)
                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <p class="font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 text-sm">{{ $bill->invoice_number }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $bill->date->format('d M') }}</p>
                        <p class="text-[10px] text-zinc-500 font-medium uppercase tracking-wider">{{ $bill->date->format('l, Y') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <x-avatar :name="$bill->customer->name ?? '?'" size="sm" />
                            <div>
                                <p class="font-cabinet font-bold text-zinc-900 dark:text-zinc-100">{{ $bill->customer->name ?? '-' }}</p>
                                <p class="font-outfit text-xs text-zinc-500">{{ $bill->customer->phone ?? 'NO PHONE' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $firstItem = $bill->items->first();
                            $othersCount = $bill->items->count() - 1;
                        @endphp
                        <div class="flex items-center gap-2">
                            <x-badge variant="zinc">{{ $firstItem?->item_name }}</x-badge>
                            @if($othersCount > 0)
                                <span class="px-2 py-0.5 bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 font-bold text-[9px] rounded-md tracking-wider">+{{ $othersCount }} OTHERS</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-jetbrains font-medium text-zinc-900 dark:text-zinc-100">{{ number_format($bill->items->sum('quantity_kg'), 1) }}</span>
                    </td>
                    <td class="px-6 py-4 font-jetbrains font-medium text-emerald-600 dark:text-emerald-400 text-center">
                        <x-currency :amount="$bill->net_amount" />
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $statusMap = [
                                'Generated' => 'info',
                                'Pending'   => 'warning',
                                'Paid'      => 'success',
                            ];
                            $st = $statusMap[$bill->status] ?? 'warning';
                        @endphp
                        <x-badge :variant="$st">{{ strtoupper($bill->status) }}</x-badge>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex items-center justify-center gap-2">
                            <a href="{{ route('billing.daily.invoice', $bill->id) }}" target="_blank" class="text-zinc-400 hover:text-emerald-600 transition-colors" title="Print Invoice">
                                <span class="material-symbols-rounded text-lg">print</span>
                            </a>
                            <a href="{{ route('billing.daily.pdf', $bill->id) }}" class="text-zinc-400 hover:text-blue-600 transition-colors" title="Download PDF">
                                <span class="material-symbols-rounded text-lg">picture_as_pdf</span>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <x-slot:empty>
                    <x-empty-state 
                        icon="receipt_long" 
                        title="No Sales Recorded" 
                        description="Ready to record your first daily sale?" />
                </x-slot:empty>
            @endforelse

            @if($bills->hasPages())
                <x-slot:pagination>
                    {{ $bills->withQueryString()->links() }}
                </x-slot:pagination>
            @endif
        </x-data-table>
    </x-card>

    {{-- Dealer Deliveries via Day-Load --}}
    <x-card>
        <div class="flex flex-col sm:flex-row gap-4 mb-6 justify-between items-center">
            <div>
                <h2 class="text-lg font-bold text-zinc-900 dark:text-zinc-100">Dealer Bird Deliveries</h2>
                <p class="text-xs text-zinc-500 mt-1">Day-load birds delivered to dealers</p>
            </div>
            <div class="flex gap-3 text-xs font-bold">
                <span class="px-3 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 border border-blue-200 dark:border-blue-800">
                    <span class="material-symbols-rounded text-[14px] align-text-bottom">inventory_2</span>
                    {{ number_format($dealerDayLoadTotalBoxes) }} Boxes
                </span>
                <span class="px-3 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                    <span class="material-symbols-rounded text-[14px] align-text-bottom">scale</span>
                    {{ number_format($dealerDayLoadTotalBird, 1) }} kg Bird
                </span>
                <span class="px-3 py-1.5 rounded-lg bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                    <span class="material-symbols-rounded text-[14px] align-text-bottom">trending_down</span>
                    {{ number_format($dealerDayLoadTotalLoss, 1) }} kg Loss
                </span>
            </div>
        </div>

        <x-data-table :headers="['Date', 'Dealer', ['label' => 'Boxes', 'align' => 'right'], ['label' => 'Bird Weight', 'align' => 'right'], ['label' => 'Farm Weight', 'align' => 'right'], ['label' => 'Loss', 'align' => 'right']]">
            @forelse($dealerDayLoads as $entry)
                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50">
                    <td class="px-4 py-3 text-sm font-medium text-zinc-500 dark:text-zinc-400">
                        {{ $entry->batch->billing_date->format('d M Y') }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center gap-2">
                            <x-avatar name="{{ $entry->dealer->firm_name ?? '-' }}" size="sm" />
                            <span class="font-bold text-zinc-900 dark:text-zinc-100 text-sm">{{ $entry->dealer->firm_name ?? '-' }}</span>
                        </div>
                    </td>
                    <td class="px-4 py-3 text-right font-jetbrains text-sm">{{ $entry->no_of_boxes }}</td>
                    <td class="px-4 py-3 text-right font-jetbrains text-sm">{{ number_format($entry->bird_weight, 1) }} kg</td>
                    <td class="px-4 py-3 text-right font-jetbrains text-sm">{{ number_format($entry->farm_weight ?? 0, 1) }} kg</td>
                    <td class="px-4 py-3 text-right font-jetbrains text-sm">
                        @if(($entry->loss_weight ?? 0) > 0)
                            <span class="text-rose-600 dark:text-rose-400">{{ number_format($entry->loss_weight, 1) }} kg</span>
                        @else
                            <span class="text-emerald-600 dark:text-emerald-400">0 kg</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-8 text-zinc-500">No dealer day-load entries found.</td></tr>
            @endforelse
            @if($dealerDayLoads->hasPages())
                <x-slot:pagination>
                    {{ $dealerDayLoads->links() }}
                </x-slot:pagination>
            @endif
        </x-data-table>
    </x-card>
</div>

@endsection

@push('scripts')
<script>
let saleRowCount = 1;
const activeItems = @json($items);

function addSaleRow() {
    const body = document.getElementById('sale-items-body');
    const newRow = document.createElement('tr');
    newRow.className = 'item-row';
    
    let optionsHtml = activeItems.map(i => `
        <option value="${i.name}" ${i.name === 'Live Broiler Birds' ? 'selected' : ''}>
            ${i.name}
        </option>
    `).join('');

    newRow.innerHTML = `
        <td class="p-2">
            <select name="items[${saleRowCount}][name]" required class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2 transition-colors">
                ${optionsHtml}
            </select>
        </td>
        <td class="p-2">
            <input type="number" name="items[${saleRowCount}][qty]" step="0.01" required placeholder="0.0" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2 text-center row-qty" oninput="recalcSales()">
        </td>
        <td class="p-2">
            <input type="number" name="items[${saleRowCount}][rate]" step="0.01" required placeholder="0.0" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2 text-right row-rate" oninput="recalcSales()">
        </td>
        <td class="p-2 text-right font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 row-total">
            ₹0.00
        </td>
        <td class="p-2 text-center">
            <button type="button" onclick="this.closest('tr').remove(); recalcSales();" class="text-zinc-400 hover:text-rose-500 transition-colors p-1">
                <span class="material-symbols-rounded text-lg block">close</span>
            </button>
        </td>
    `;
    body.appendChild(newRow);
    saleRowCount++;
}

function recalcSales() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
        const rate = parseFloat(row.querySelector('.row-rate').value) || 0;
        const total = qty * rate;
        row.querySelector('.row-total').textContent = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        subtotal += total;
    });

    const gstP = parseFloat(document.getElementById('gst-percentage').value) || 0;
    const gstA = subtotal * gstP / 100;
    const final = subtotal + gstA;

    document.getElementById('display-tax').textContent = '₹' + gstA.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('display-total').textContent = '₹' + final.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    document.getElementById('total-hidden').value = final.toFixed(2);
}

// Auto-run on load
window.addEventListener('DOMContentLoaded', () => {
    recalcSales();
});
</script>
@endpush
