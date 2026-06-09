@extends('layouts.app')
@section('title', 'Weekly Dealer Billing')

@section('content')
<div class="animate-fade-in">
    <x-page-header title="Weekly Dealer Billing" subtitle="Create wholesale billing settlements, bulk route invoices, and manage ledger transactions">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('billing.weekly.export') }}" icon="download">
                Export
            </x-button>
            <x-button variant="primary" x-data x-on:click="$dispatch('open-modal', 'record-bill')" icon="add">
                Record Bill
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Performance Stats Header --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-stat-card 
            label="Generated Invoices" 
            value="{{ $bills->total() }}" 
            icon="receipt_long" 
            color="indigo" />
        <x-stat-card 
            label="Outstanding Dues" 
            value="Rs {{ number_format($bills->where('status', 'Pending')->sum(fn($b) => $b->net_amount ?? $b->amount), 0) }}" 
            icon="pending_actions" 
            color="amber" />
        <div class="rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-800 p-6 shadow-sm text-white flex items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-indigo-500/20">
            <div>
                <p class="font-outfit text-sm font-medium text-indigo-100">Total Revenue</p>
                <p class="font-jetbrains mt-2 text-3xl font-bold tracking-tight">Rs {{ number_format($bills->where('status', 'Paid')->sum(fn($b) => $b->net_amount ?? $b->amount), 0) }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                <span class="material-symbols-rounded text-2xl">account_balance_wallet</span>
            </div>
        </div>
    </div>

    {{-- Main List Section --}}
    <x-card>
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">Weekly Invoice Log</h2>
            <form method="GET" class="relative max-w-sm w-full sm:w-auto">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-400">
                    <span class="material-symbols-rounded text-xl">search</span>
                </div>
                <input type="text" name="search" value="{{ $search }}" class="bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 p-2.5 transition-colors font-outfit" placeholder="Search customer or invoice...">
            </form>
        </div>

        <x-data-table :headers="['Inv No', 'Customer', 'Period', 'Product Breakdown', 'Quantity', 'Net Amount', 'Status', 'Actions']">
            @forelse($bills as $bill)
                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <span class="font-jetbrains text-xs font-bold text-zinc-500">
                            #{{ $bill->invoice_no ?? $bill->invoice_number }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <x-avatar :name="$bill->customer->name ?? 'a'" size="sm" />
                            <div>
                                <p class="font-cabinet font-bold text-zinc-900 dark:text-zinc-100">{{ $bill->customer->name ?? '-' }}</p>
                                <p class="font-outfit text-xs text-zinc-500">{{ $bill->customer->route ?? 'General Route' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $bill->period_start->format('d M') }} - {{ $bill->period_end->format('d M') }}</p>
                        <p class="text-[10px] text-zinc-500 font-medium uppercase tracking-wider">{{ $bill->period_end->format('Y') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-wrap items-center gap-2 max-w-[200px]">
                            @if($bill->items_description)
                                @foreach(explode(',', $bill->items_description) as $item)
                                    @if(trim($item))
                                        <x-badge variant="zinc">{{ trim($item) }}</x-badge>
                                    @endif
                                @endforeach
                            @else
                                <span class="text-zinc-400 text-xs">—</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-jetbrains font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($bill->quantity_kg, 2) }}</span>
                        <span class="text-[10px] text-zinc-500 font-medium uppercase ml-0.5">kg</span>
                    </td>
                    <td class="px-6 py-4 font-jetbrains font-medium text-indigo-600 dark:text-indigo-400 text-center">
                        <div class="flex flex-col items-end">
                            <span class="font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 text-sm">Rs {{ number_format($bill->net_amount ?? $bill->amount, 0) }}</span>
                            <span class="text-[9px] text-indigo-600 font-bold uppercase tracking-tighter">Incl. GST</span>
                        </div>
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
                            <a href="{{ route('billing.weekly.show', $bill) }}" target="_blank" class="text-zinc-400 hover:text-indigo-600 transition-colors" title="Print Invoice">
                                <span class="material-symbols-rounded text-lg">print</span>
                            </a>
                            <a href="{{ route('billing.weekly.pdf', $bill) }}" class="text-zinc-400 hover:text-rose-600 transition-colors" title="Download PDF">
                                <span class="material-symbols-rounded text-lg">picture_as_pdf</span>
                            </a>
                            <a href="{{ route('billing.weekly.whatsapp', $bill) }}" target="_blank" class="text-emerald-500 hover:text-emerald-600 transition-colors" title="WhatsApp Message">
                                <span class="material-symbols-rounded text-lg">chat</span>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <x-slot:empty>
                    <x-empty-state 
                        icon="receipt_long" 
                        title="No Bills Found" 
                        description="Start generating invoices for your dealers." />
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

{{-- Record Bill Modal --}}
<x-modal name="record-bill" title="Record Dealer Bill" subtitle="Single Invoice or Bulk Generation" icon="receipt_long" iconColor="indigo" maxWidth="3xl">
    
    <div class="border-b border-zinc-200 dark:border-zinc-800 mb-6 flex gap-4">
        <button id="tab-single-btn" onclick="switchDealerTab('single')" class="px-4 py-2 text-sm font-bold border-b-2 border-indigo-600 text-indigo-600 focus:outline-none dark:border-indigo-400 dark:text-indigo-400 transition-colors">
            Single Invoice
        </button>
        <button id="tab-bulk-btn" onclick="switchDealerTab('bulk')" class="px-4 py-2 text-sm font-semibold text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white focus:outline-none transition-colors">
            Bulk Route Generation
        </button>
    </div>

    {{-- Single Invoice Form --}}
    <form id="form-single" action="{{ route('billing.weekly.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <x-form.select name="customer_id" label="Customer" required>
                <option value="">Select customer...</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->route }})</option>
                @endforeach
            </x-form.select>
            <x-form.input type="date" name="period_start" label="Period Start" required value="{{ old('period_start') }}" />
            <x-form.input type="date" name="period_end" label="Period End" required value="{{ old('period_end') }}" />
            <x-form.select name="status" label="Initial Status" required>
                <option value="Generated" {{ old('status') === 'Generated' ? 'selected' : '' }}>Generated</option>
                <option value="Pending" {{ old('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                <option value="Paid" {{ old('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
            </x-form.select>
        </div>

        <div class="mb-6">
            <div class="flex items-center justify-between mb-3">
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit">Billing Items & Birds</label>
                <x-button type="button" variant="outline" size="sm" icon="add" onclick="addWeeklyRow()">Add Item</x-button>
            </div>
            
            <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                <table class="w-full text-sm text-left text-zinc-600 dark:text-zinc-400 font-outfit" id="weekly-items-table">
                    <thead class="text-xs text-zinc-500 dark:text-zinc-400 uppercase bg-zinc-100/50 dark:bg-zinc-800 font-cabinet">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Item / Description</th>
                            <th class="px-4 py-3 font-semibold text-center w-24">Qty/kg</th>
                            <th class="px-4 py-3 font-semibold text-right w-32">Rate/kg</th>
                            <th class="px-4 py-3 font-semibold text-right w-32">Subtotal</th>
                            <th class="px-4 py-3 text-center w-12"></th>
                        </tr>
                    </thead>
                    <tbody id="weekly-items-body" class="divide-y divide-zinc-200 dark:divide-zinc-700">
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
                                <input type="number" name="items[0][qty]" step="0.01" required placeholder="0.0" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2 text-center row-qty" oninput="recalcWeekly()">
                            </td>
                            <td class="p-2">
                                <input type="number" name="items[0][rate]" step="0.01" required placeholder="0.0" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2 text-right row-rate" oninput="recalcWeekly()">
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
                    <input type="number" name="gst_percentage" id="gst-percentage" value="18" readonly class="bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-500 text-sm rounded-lg block w-20 p-2 text-center font-jetbrains font-bold cursor-not-allowed">
                    <span class="text-sm text-zinc-500 font-bold">% GST</span>
                </div>
                <p class="text-xs text-zinc-500 mt-2 font-medium">Calculated GST: <span id="display-tax" class="font-jetbrains text-zinc-900 dark:text-zinc-100 font-bold">₹0.00</span></p>
            </div>
            
            <div class="flex flex-col justify-end items-end">
                <span class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1 font-outfit">Grand Total</span>
                <span id="display-total" class="font-jetbrains text-3xl font-bold text-indigo-600 dark:text-indigo-400">₹0.00</span>
                <input type="hidden" name="amount" id="total-hidden">
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3">
            <x-button type="button" variant="outline" x-on:click="show = false">Cancel</x-button>
            <x-button type="submit" variant="primary" icon="receipt_long">Generate Invoice</x-button>
        </div>
    </form>

    {{-- Bulk Generation Form --}}
    <form id="form-bulk" action="{{ route('billing.weekly.bulkStore') }}" method="POST" class="hidden">
        @csrf
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-2">1. Select Dealers</label>
                <div class="h-64 overflow-y-auto pr-2 border border-zinc-200 dark:border-zinc-700 rounded-xl p-2 space-y-1 bg-zinc-50 dark:bg-zinc-800/50">
                    <div class="flex justify-between items-center px-2 py-1 mb-2">
                        <span class="text-xs text-zinc-500">Select customers for bulk billing</span>
                        <button type="button" onclick="toggleAllDealers(this)" class="text-[10px] font-bold text-indigo-600 hover:text-indigo-700 uppercase">Select All</button>
                    </div>
                    @foreach($customers as $c)
                        <label class="flex items-center gap-3 p-2 bg-white dark:bg-zinc-800 border border-zinc-100 dark:border-zinc-700 rounded-lg hover:border-indigo-200 transition-all cursor-pointer">
                            <input type="checkbox" name="customer_ids[]" value="{{ $c->id }}" class="bulk-dealer-cb w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-zinc-300">
                            <div class="flex flex-col">
                                <span class="text-xs font-bold text-zinc-700 dark:text-zinc-200">{{ $c->name }}</span>
                                <span class="text-[9px] font-bold text-zinc-400 uppercase">{{ $c->route }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="space-y-4">
                <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit">2. Global Billing Settings</label>
                
                <div class="grid grid-cols-2 gap-4">
                    <x-form.input type="date" name="period_start" label="From Date" required />
                    <x-form.input type="date" name="period_end" label="To Date" required />
                </div>

                <x-form.input type="number" name="amount" label="Standard Flat Amount (Rs)" required step="0.01" placeholder="0.00" class="font-bold text-indigo-600 dark:text-indigo-400" />

                <x-form.select name="status" label="Initial Status" required>
                    <option value="Generated">Generated</option>
                    <option value="Pending">Pending</option>
                </x-form.select>
            </div>
        </div>

        <div class="mt-6 flex justify-end gap-3 border-t border-zinc-200 dark:border-zinc-800 pt-6">
            <x-button type="button" variant="outline" x-on:click="show = false">Cancel</x-button>
            <x-button type="submit" variant="primary" icon="layers">Generate Bulk Bills</x-button>
        </div>
    </form>
</x-modal>
@endsection

@push('scripts')
<script>
let weeklyRowCount = 1;
const activeItems = @json($items);

function addWeeklyRow() {
    const body = document.getElementById('weekly-items-body');
    const newRow = document.createElement('tr');
    newRow.className = 'item-row border-t border-zinc-200 dark:border-zinc-700';
    
    let optionsHtml = activeItems.map(i => `
        <option value="${i.name}" ${i.name === 'Live Broiler Birds' ? 'selected' : ''}>
            ${i.name}
        </option>
    `).join('');

    newRow.innerHTML = `
        <td class="p-2">
            <select name="items[${weeklyRowCount}][name]" required class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2 transition-colors">
                ${optionsHtml}
            </select>
        </td>
        <td class="p-2">
            <input type="number" name="items[${weeklyRowCount}][qty]" step="0.01" required placeholder="0.00" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2 text-center row-qty" oninput="recalcWeekly()">
        </td>
        <td class="p-2">
            <input type="number" name="items[${weeklyRowCount}][rate]" step="0.01" required placeholder="0.00" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-emerald-500 focus:border-emerald-500 block w-full p-2 text-right row-rate" oninput="recalcWeekly()">
        </td>
        <td class="p-2 text-right font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 row-total">
            ₹0.00
        </td>
        <td class="p-2 text-center">
            <button type="button" onclick="this.closest('tr').remove(); recalcWeekly();" class="text-zinc-400 hover:text-rose-500 transition-colors p-1">
                <span class="material-symbols-rounded text-lg block">close</span>
            </button>
        </td>
    `;
    body.appendChild(newRow);
    weeklyRowCount++;
}

function recalcWeekly() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
        const rate = parseFloat(row.querySelector('.row-rate').value) || 0;
        const total = qty * rate;
        row.querySelector('.row-total').textContent = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2 });
        subtotal += total;
    });

    const gstP = parseFloat(document.getElementById('gst-percentage').value) || 0;
    const gstA = subtotal * gstP / 100;
    const final = subtotal + gstA;

    document.getElementById('display-tax').textContent = '₹' + gstA.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    document.getElementById('display-total').textContent = '₹' + final.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    document.getElementById('total-hidden').value = final.toFixed(2);
}

function switchDealerTab(mode) {
    const tabSingleBtn = document.getElementById('tab-single-btn');
    const tabBulkBtn = document.getElementById('tab-bulk-btn');
    const formSingle = document.getElementById('form-single');
    const formBulk = document.getElementById('form-bulk');

    const activeClasses = "px-4 py-2 text-sm font-bold border-b-2 border-indigo-600 text-indigo-600 focus:outline-none dark:border-indigo-400 dark:text-indigo-400 transition-colors";
    const inactiveClasses = "px-4 py-2 text-sm font-semibold text-zinc-500 hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white focus:outline-none transition-colors border-b-2 border-transparent";

    if (mode === 'single') {
        tabSingleBtn.className = activeClasses;
        tabBulkBtn.className = inactiveClasses;
        formSingle.classList.remove('hidden');
        formBulk.classList.add('hidden');
    } else {
        tabBulkBtn.className = activeClasses;
        tabSingleBtn.className = inactiveClasses;
        formBulk.classList.remove('hidden');
        formSingle.classList.add('hidden');
    }
}

function toggleAllDealers(btn) {
    const checkboxes = document.querySelectorAll('.bulk-dealer-cb');
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    checkboxes.forEach(cb => cb.checked = !allChecked);
    btn.textContent = allChecked ? 'Select All' : 'Deselect All';
}

// Auto-run on load
window.addEventListener('DOMContentLoaded', () => {
    recalcWeekly();
});
</script>
@endpush
