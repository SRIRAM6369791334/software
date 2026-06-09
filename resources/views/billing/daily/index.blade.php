@extends('layouts.app')
@section('title', 'Daily Customer Billing')

@section('content')
<div class="animate-fade-in">
    <x-page-header title="Daily Customer Billing" subtitle="Record counter sales, calculate GST automatically, and issue receipts">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('billing.daily.export') }}" icon="download">
                Export
            </x-button>
            <x-button variant="primary" x-data x-on:click="$dispatch('open-modal', 'add-sale')" icon="add">
                Record Sale
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

    {{-- Daily Sales Logs Table Card --}}
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

        <x-data-table :headers="['Invoice', 'Sale Date', 'Customer', 'Product Breakdown', 'Net Weight', 'Net Amount', 'Status', 'Action']">
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
                        <span class="text-[10px] text-zinc-500 font-medium uppercase ml-0.5">kg</span>
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
</div>

{{-- Add Sale Modal (Replaces inline form) --}}
<x-modal name="add-sale" title="Record New Retail Sale" subtitle="Log daily counter sales and print invoice" icon="point_of_sale" iconColor="indigo" maxWidth="3xl">
    <form action="{{ route('billing.daily.store') }}" method="POST" id="daily-sale-form">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <x-form.select name="customer_id" label="Customer" required>
                <option value="">Select customer...</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                @endforeach
            </x-form.select>
            <x-form.input type="date" name="date" label="Sale Date" required value="{{ old('date', date('Y-m-d')) }}" />
            <x-form.select name="status" label="Payment Status" required>
                <option value="Generated" {{ old('status') === 'Generated' ? 'selected' : '' }}>Generated</option>
                <option value="Pending" {{ old('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Paid" {{ old('status', 'Paid') === 'Paid' ? 'selected' : '' }}>Paid (Cash)</option>
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
                            <th class="px-4 py-3 font-semibold text-center w-24">Qty/kg</th>
                            <th class="px-4 py-3 font-semibold text-right w-32">Rate/kg</th>
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-zinc-50 dark:bg-zinc-800/50 p-4 rounded-xl border border-zinc-200 dark:border-zinc-700">
            <div>
                <label class="block text-xs font-bold text-zinc-500 uppercase tracking-wider mb-2 font-outfit">Tax Settings (GST)</label>
                <div class="flex items-center gap-3">
                    <input type="number" name="gst_percentage" id="gst-percentage" value="18" min="0" max="28" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-20 p-2 text-center font-jetbrains font-bold" oninput="recalcSales()">
                    <span class="text-sm text-zinc-500 font-bold">% GST</span>
                </div>
                <p class="text-xs text-zinc-500 mt-2 font-medium">Calculated GST: <span id="display-tax" class="font-jetbrains text-zinc-900 dark:text-zinc-100 font-bold">₹0.00</span></p>
            </div>
            
            <div class="flex flex-col justify-end items-end">
                <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1 font-outfit">Grand Total</span>
                <span id="display-total" class="font-jetbrains text-3xl font-bold text-emerald-600 dark:text-emerald-400">₹0.00</span>
                <input type="hidden" name="amount" id="total-hidden">
            </div>
        </div>

        <x-slot:footer>
            <x-button type="button" variant="outline" x-on:click="show = false">Cancel</x-button>
            <x-button type="submit" variant="primary" icon="receipt_long">Generate Invoice</x-button>
        </x-slot:footer>
    </form>
</x-modal>
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
