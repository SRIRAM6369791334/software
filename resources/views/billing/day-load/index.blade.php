@extends('layouts.app')
@section('title', 'Daily Load Billing')

@section('content')
<div class="animate-fade-in">
    <x-page-header title="Daily Load Billing" subtitle="Track vendor loads, dealer rates, box weights, and paper-rate variance">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('billing.day-load.export', ['date' => $date]) }}" icon="download">
                Export CSV
            </x-button>
            <x-button variant="outline" href="{{ route('billing.day-load.invoice', $date) }}" icon="print" target="_blank">
                Print Invoice
            </x-button>
            <x-button variant="outline" href="{{ route('billing.day-load.pdf', $date) }}" icon="picture_as_pdf">
                Download PDF
            </x-button>
            <x-button variant="outline" href="{{ route('billing.day-load.vendor-rates', ['vendor_id' => '', 'date' => $date]) }}" icon="price_change">
                Set Vendor Rates
            </x-button>
            <x-button variant="outline" href="{{ route('billing.weekly.index') }}" icon="receipt_long">
                Weekly Billing
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Collapsible Stats Panel --}}
    <x-card class="mb-6 transition-all duration-300 hover:shadow-md" x-data="{ showStats: false }">
        <div class="flex justify-between items-center cursor-pointer select-none" @click="showStats = !showStats">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 text-white shadow-md shadow-emerald-500/10">
                    <span class="material-symbols-rounded text-lg">analytics</span>
                </div>
                <div>
                    <h2 class="text-sm font-extrabold text-zinc-800 dark:text-zinc-100 tracking-tight">Billing & Financial Summary</h2>
                    <p class="text-[10px] font-bold text-zinc-400 dark:text-zinc-500 mt-0.5 tracking-wide uppercase">Click to view day totals and margins</p>
                </div>
            </div>
            <button type="button" class="flex items-center justify-center h-8 px-3 gap-1.5 rounded-lg text-xs transition-all duration-300 font-bold bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700">
                <span class="material-symbols-rounded text-sm" x-text="showStats ? 'expand_less' : 'expand_more'"></span>
                <span x-text="showStats ? 'Hide Summary' : 'Show Summary'"></span>
            </button>
        </div>

        <div x-show="showStats" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="pt-6 mt-4 border-t border-zinc-100 dark:border-zinc-800/80">
            {{-- Combined Metrics Row --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <x-stat-card label="Billing Date" value="{{ \Carbon\Carbon::parse($date)->format('d M Y') }}" subtitle="{{ \Carbon\Carbon::parse($date)->format('l') }}" icon="calendar_today" color="blue" />
                <x-stat-card label="Total Boxes" value="{{ number_format((float) ($batch?->total_boxes ?? 0), 0) }}" icon="inventory_2" color="amber" />
                <x-stat-card label="Bird Weight" value="{{ number_format((float) ($batch?->total_bird_weight ?? 0), 2) }} kg" icon="scale" color="indigo" />
                <x-stat-card label="Gross Margin" value="Rs {{ number_format($grossMargin, 0) }}" icon="trending_up" color="{{ $grossMargin >= 0 ? 'emerald' : 'rose' }}" />
            </div>

            {{-- Dealer Panel (Full Width) --}}
            <div class="rounded-2xl border border-zinc-200/60 dark:border-zinc-800/60 bg-white/40 dark:bg-zinc-900/20 p-4 mb-6">
                <div class="flex items-center gap-2 mb-3 px-1">
                    <span class="material-symbols-rounded text-teal-500 text-[16px]">storefront</span>
                    <h3 class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Dealer Summary (Receivables)</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <x-stat-card label="Dealer Income" value="Rs {{ number_format($totalDealerIncome, 0) }}" icon="payments" color="teal" />
                    <x-stat-card label="Dealer Collected" value="Rs {{ number_format($totalDealerCollected, 0) }}" subtitle="{{ $collectionPct }}% Collected" icon="account_balance" color="emerald" />
                    <x-stat-card label="Dealer Due" value="Rs {{ number_format($totalDealerDue, 0) }}" icon="pending" color="{{ $totalDealerDue > 0 ? 'amber' : 'emerald' }}" />
                </div>
            </div>

            {{-- Vendor Panel (Full Width) --}}
            <div class="rounded-2xl border border-zinc-200/60 dark:border-zinc-800/60 bg-white/40 dark:bg-zinc-900/20 p-4">
                <div class="flex items-center gap-2 mb-3 px-1">
                    <span class="material-symbols-rounded text-rose-500 text-[16px]">local_shipping</span>
                    <h3 class="text-[10px] font-bold uppercase tracking-wider text-zinc-500">Vendor Summary (Payables)</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <x-stat-card label="Vendor Cost" value="Rs {{ number_format($totalVendorCost, 0) }}" icon="shopping_cart" color="rose" />
                    <x-stat-card label="Vendor Paid" value="Rs {{ number_format($totalVendorPaid, 0) }}" icon="payments" color="violet" />
                    <x-stat-card label="Vendor Due" value="Rs {{ number_format($totalVendorDue, 0) }}" icon="pending_actions" color="{{ $totalVendorDue > 0 ? 'amber' : 'emerald' }}" />
                </div>
            </div>
        </div>
    </x-card>

    @can('create bills')
    <x-card class="mb-8">
        <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
            <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">New Load Entry</h2>
        </div>

        <form action="{{ route('billing.day-load.store') }}" method="POST" x-data="{ paperRate: 0, billingRate: 0, customerRate: 0, get activeVendorRate() { return this.billingRate > 0 ? this.billingRate : this.paperRate; } }">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                <x-form.select name="vendor_id" label="Vendor / Company Name" required>
                    <option value="">Select vendor...</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->id }}">{{ $vendor->firm_name }}{{ $vendor->is_shop ? ' (Shop)' : '' }}</option>
                    @endforeach
                </x-form.select>

                <x-form.select name="dealer_id" label="Dealer" required>
                    <option value="">Select dealer...</option>
                    @foreach($dealers as $dealer)
                        <option value="{{ $dealer->id }}">{{ $dealer->firm_name }}</option>
                    @endforeach
                </x-form.select>

                <x-form.input type="date" name="billing_date" label="Date" required value="{{ $date }}" />
                <x-form.input type="text" label="Day" value="{{ \Carbon\Carbon::parse($date)->format('l') }}" readonly />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5">
                <x-form.input type="number" step="0.01" name="paper_rate" label="Paper Rate" required x-model.number="paperRate" />
                <x-form.input type="number" step="0.01" name="billing_rate" label="Vendor Rate (Final)" x-model.number="billingRate" />
                <x-form.input type="number" step="0.01" name="customer_rate" label="Customer Rate" required x-model.number="customerRate" />
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 p-4">
                    <p class="text-xs font-bold uppercase text-zinc-500">Customer vs Vendor</p>
                    <p class="mt-2 font-jetbrains text-2xl font-black" :class="(customerRate - activeVendorRate) >= 0 ? 'text-emerald-600' : 'text-rose-600'">
                        <span x-text="(customerRate - activeVendorRate) >= 0 ? '+' : '-'"></span>Rs <span x-text="Math.abs(customerRate - activeVendorRate).toFixed(2)"></span>
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <x-form.input type="number" name="no_of_boxes" label="Boxes" required min="1" />
                <x-form.input type="number" step="0.01" name="box_weight" label="Box Weight" required />
                <x-form.input type="number" step="0.01" name="empty_weight" label="Empty Weight" required />
                <x-form.input type="number" step="0.01" name="farm_weight" label="Farm Weight" />
                <x-form.input name="remarks" label="Remarks" placeholder="Optional" />
            </div>

            <div class="flex justify-end">
                <x-button type="submit" variant="primary" icon="save">Save Load Entry</x-button>
            </div>
        </form>
    </x-card>
    @endcan

    <x-card>
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">Load Entries</h2>
                @can('create bills')
                    @if($entries->count() > 0)
                        <x-button
                            variant="primary"
                            size="sm"
                            icon="scale"
                            x-on:click="$dispatch('open-modal', 'set-farm-weight-modal')"
                        >
                            Set Farm Weight
                        </x-button>
                        <x-button
                            variant="outline"
                            size="sm"
                            icon="edit_note"
                            x-on:click="$dispatch('open-modal', 'adjust-all-modal')"
                        >
                            Adjust All
                        </x-button>
                        <x-button
                            variant="secondary"
                            size="sm"
                            icon="payments"
                            x-on:click="$dispatch('open-modal', 'lump-sum-payment-modal')"
                        >
                            Lump Payment
                        </x-button>
                    @endif
                @endcan
            </div>
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="date" name="date" value="{{ $date }}" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search vendor or dealer" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <x-button type="submit" variant="outline" icon="filter_alt">Filter</x-button>
            </form>
        </div>

        <x-data-table :headers="['Date', 'Vendor', 'Dealer', 'Rates', 'Margin', 'Boxes', 'Weights', 'Amount', 'Dealer Payment', 'Vendor Payment', 'Status', 'Actions']">
            @forelse($entries as $entry)
                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $entry->batch->billing_date->format('d M Y') }}</p>
                        <p class="text-xs text-zinc-500">{{ $entry->batch->billing_date->format('l') }}</p>
                    </td>
                    <td class="px-6 py-4 font-bold text-zinc-900 dark:text-zinc-100">{{ $entry->vendor->firm_name ?? '-' }}</td>
                    <td class="px-6 py-4">{{ $entry->dealer->firm_name ?? '-' }}</td>
                    <td class="px-6 py-4 text-xs">
                        <div>Paper: <span class="font-jetbrains">Rs {{ number_format((float) $entry->paper_rate, 2) }}</span></div>
                        <div>Vendor: <span class="font-jetbrains">@if((float) $entry->billing_rate > 0)Rs {{ number_format((float) $entry->billing_rate, 2) }}@else<span class="text-zinc-400">—</span>@endif</span></div>
                        <div>Customer: <span class="font-jetbrains">Rs {{ number_format((float) $entry->customer_rate, 2) }}</span></div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $diff = $entry->rate_difference;
                        @endphp
                        <span class="font-jetbrains font-bold {{ $diff >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $diff >= 0 ? '+' : '-' }}Rs {{ number_format(abs($diff), 2) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center font-jetbrains font-bold">{{ $entry->no_of_boxes }}</td>
                    <td class="px-6 py-4 text-xs">
                        <div>Box: {{ number_format((float) $entry->box_weight, 2) }}</div>
                        <div>Empty: {{ number_format((float) $entry->empty_weight, 2) }}</div>
                        <div>Bird: {{ number_format((float) $entry->bird_weight, 2) }}</div>
                        <div>Loss: {{ $entry->loss_weight === null ? '-' : number_format((float) $entry->loss_weight, 2) }}</div>
                        <div>Total: {{ $entry->total_weight === null ? '-' : number_format((float) $entry->total_weight, 2) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-jetbrains font-bold text-zinc-900 dark:text-zinc-100">Rs {{ number_format((float) $entry->amount, 0) }}</span>
                    </td>
                    <td class="px-6 py-4 text-xs">
                        @php
                            $dStatus = $entry->dealer_payment_status;
                            $dColor = match($dStatus) { 'Paid' => 'success', 'Partial' => 'warning', 'Overpaid' => 'info', default => 'zinc' };
                        @endphp
                        <div class="flex flex-col items-center gap-1">
                            <x-badge :variant="$dColor">{{ $dStatus }}</x-badge>
                            <span class="font-jetbrains text-[11px] {{ (float) $entry->dealer_collected > 0 ? 'text-emerald-600' : 'text-zinc-400' }}">
                                Rs {{ number_format((float) $entry->dealer_collected, 0) }} / Rs {{ number_format($entry->dealer_income, 0) }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-xs">
                        @php
                            $vStatus = $entry->vendor_payment_status;
                            $vColor = match($vStatus) { 'Paid' => 'success', 'Partial' => 'warning', 'Overpaid' => 'info', default => 'zinc' };
                        @endphp
                        <div class="flex flex-col items-center gap-1">
                            <x-badge :variant="$vColor">{{ $vStatus }}</x-badge>
                            <span class="font-jetbrains text-[11px] {{ (float) $entry->vendor_paid > 0 ? 'text-violet-600' : 'text-zinc-400' }}">
                                Rs {{ number_format((float) $entry->vendor_paid, 0) }} / Rs {{ number_format($entry->vendor_cost, 0) }}
                            </span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <x-badge variant="success">{{ $entry->status }}</x-badge>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($entry->status === 'Active')
                            <div class="flex items-center justify-center gap-2">
                                <button
                                    type="button"
                                    x-on:click="
                                        $dispatch('open-modal', 'edit-entry-modal');
                                        $nextTick(() => {
                                            editEntryId = {{ $entry->id }};
                                            editFormAction = '{{ route('billing.day-load.update', $entry->id) }}';
                                            editVendorId = {{ $entry->vendor_id }};
                                            editDealerId = {{ $entry->dealer_id }};
                                            editPaperRate = {{ $entry->paper_rate }};
                                            editBillingRate = {{ $entry->billing_rate }};
                                            editCustomerRate = {{ $entry->customer_rate }};
                                            editNoOfBoxes = {{ $entry->no_of_boxes }};
                                            editBoxWeight = {{ $entry->box_weight }};
                                            editEmptyWeight = {{ $entry->empty_weight }};
                                            editFarmWeight = '{{ $entry->farm_weight ?? '' }}';
                                            editRemarks = '{{ $entry->remarks ?? '' }}';
                                        });
                                    "
                                    class="inline-flex items-center gap-1 text-xs font-medium text-amber-600 hover:text-amber-800 dark:text-amber-400 dark:hover:text-amber-300 transition-colors"
                                >
                                    <span class="material-symbols-rounded text-sm">edit</span>
                                </button>
                                @if($entry->bird_weight > 0)
                                <button
                                    type="button"
                                    x-on:click="
                                        $dispatch('open-modal', 'transfer-boxes-modal');
                                        $nextTick(() => {
                                            transferSourceId = {{ $entry->id }};
                                            transferSourceBoxes = {{ $entry->no_of_boxes }};
                                            transferSourceWeight = {{ $entry->bird_weight }};
                                            transferSourceVendor = '{{ addslashes($entry->vendor->firm_name ?? '-') }}';
                                            transferSourceDealer = '{{ addslashes($entry->dealer->firm_name ?? '-') }}';
                                            transferBatchId = {{ $entry->batch_id }};
                                            transferDate = '{{ $entry->batch->billing_date->format('d M Y') }}';
                                            transferMaxWeight = {{ $entry->bird_weight }};
                                            transferWeight = {{ $entry->bird_weight }};
                                            transferFormAction = '{{ route('billing.day-load.transfer', $entry->id) }}';
                                        });
                                    "
                                    class="inline-flex items-center gap-1 text-xs font-medium text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors"
                                >
                                    <span class="material-symbols-rounded text-sm">swap_horiz</span>
                                </button>
                                @endif
                                <button
                                    type="button"
                                    x-on:click="
                                        $dispatch('open-modal', 'dealer-payment-modal');
                                        $nextTick(() => {
                                            dpEntryId = {{ $entry->id }};
                                            dpFormAction = '{{ route('billing.day-load.dealer-payment', $entry->id) }}';
                                            dpEntryVendor = '{{ addslashes($entry->vendor->firm_name ?? '-') }}';
                                            dpEntryDealer = '{{ addslashes($entry->dealer->firm_name ?? '-') }}';
                                            dpEntryIncome = {{ $entry->dealer_income }};
                                            dpEntryCollected = {{ (float) $entry->dealer_collected }};
                                            dpCashAmount = {{ round($entry->dealer_income - (float) $entry->dealer_collected, 2) }};
                                            dpBankAmount = 0;
                                            dpBankTransferType = '';
                                        });
                                    "
                                    class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-300 transition-colors"
                                    title="Record Dealer Payment"
                                >
                                    <span class="material-symbols-rounded text-sm">payments</span>
                                </button>
                                <button
                                    type="button"
                                    x-on:click="
                                        $dispatch('open-modal', 'vendor-payment-modal');
                                        $nextTick(() => {
                                            vpEntryId = {{ $entry->id }};
                                            vpFormAction = '{{ route('billing.day-load.vendor-payment', $entry->id) }}';
                                            vpEntryVendor = '{{ addslashes($entry->vendor->firm_name ?? '-') }}';
                                            vpEntryDealer = '{{ addslashes($entry->dealer->firm_name ?? '-') }}';
                                            vpEntryCost = {{ $entry->vendor_cost }};
                                            vpEntryPaid = {{ (float) $entry->vendor_paid }};
                                            vpCashAmount = {{ round($entry->vendor_cost - (float) $entry->vendor_paid, 2) }};
                                            vpBankAmount = 0;
                                            vpBankTransferType = '';
                                        });
                                    "
                                    class="inline-flex items-center gap-1 text-xs font-medium text-violet-600 hover:text-violet-800 dark:text-violet-400 dark:hover:text-violet-300 transition-colors"
                                    title="Record Vendor Payment"
                                >
                                    <span class="material-symbols-rounded text-sm">account_balance_wallet</span>
                                </button>
                            </div>
                        @endif
                    </td>
                </tr>
            @empty
                <x-slot:empty>
                    <x-empty-state icon="inventory_2" title="No load entries found" description="Record the first vendor-to-dealer load for this date." />
                </x-slot:empty>
            @endforelse

            @if($entries->hasPages())
                <x-slot:pagination>
                    {{ $entries->withQueryString()->links() }}
                </x-slot:pagination>
            @endif
        </x-data-table>
    </x-card>

    <div x-data="dayLoadBillingData()">
        <x-modal name="edit-entry-modal" title="Edit Entry" subtitle="Adjust rates, weights, or box count" icon="edit" maxWidth="2xl">
            <form id="edit-entry-form" :action="editFormAction" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                {{-- Primary Details --}}
                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200/50 dark:border-zinc-700/50">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-3 flex items-center gap-1.5">
                        <span class="material-symbols-rounded text-xs text-zinc-400">badge</span>
                        Primary Details
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Vendor</label>
                            <select name="vendor_id" required x-model="editVendorId" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                                <option value="">Select vendor...</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->firm_name }}{{ $vendor->is_shop ? ' (Shop)' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Dealer</label>
                            <select name="dealer_id" required x-model="editDealerId" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                                <option value="">Select dealer...</option>
                                @foreach($dealers as $dealer)
                                    <option value="{{ $dealer->id }}">{{ $dealer->firm_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Boxes</label>
                            <input type="number" name="no_of_boxes" min="1" x-model.number="editNoOfBoxes" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                    </div>
                </div>

                {{-- Pricing Rates --}}
                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200/50 dark:border-zinc-700/50">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-3 flex items-center gap-1.5">
                        <span class="material-symbols-rounded text-xs text-zinc-400">payments</span>
                        Pricing Rates (Rs)
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Paper Rate</label>
                            <input type="number" step="0.01" name="paper_rate" min="0" x-model.number="editPaperRate" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Billing Rate</label>
                            <input type="number" step="0.01" name="billing_rate" min="0" x-model.number="editBillingRate" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Customer Rate</label>
                            <input type="number" step="0.01" name="customer_rate" min="0" x-model.number="editCustomerRate" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                    </div>
                </div>

                {{-- Weights --}}
                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200/50 dark:border-zinc-700/50">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-3 flex items-center gap-1.5">
                        <span class="material-symbols-rounded text-xs text-zinc-400">scale</span>
                        Load Weights (Kg)
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Box Weight</label>
                            <input type="number" step="0.01" name="box_weight" min="0" x-model.number="editBoxWeight" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Empty Weight</label>
                            <input type="number" step="0.01" name="empty_weight" min="0" x-model.number="editEmptyWeight" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Farm Weight</label>
                            <input type="number" step="0.01" name="farm_weight" min="0" x-model="editFarmWeight" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                    </div>
                </div>

                {{-- Remarks & Audit --}}
                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200/50 dark:border-zinc-700/50">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-3 flex items-center gap-1.5">
                        <span class="material-symbols-rounded text-xs text-zinc-400">history</span>
                        Audit & Remarks
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Remarks</label>
                            <input type="text" name="remarks" x-model="editRemarks" placeholder="Optional notes" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Reason for Edit</label>
                            <input type="text" name="reason" required placeholder="Why are you editing this entry?" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                    </div>
                </div>

                <x-slot:footer>
                    <x-button type="button" variant="outline" x-on:click="$dispatch('close-modal', 'edit-entry-modal')">Cancel</x-button>
                    <x-button type="submit" form="edit-entry-form" variant="primary" icon="save">Save Changes</x-button>
                </x-slot:footer>
            </form>
        </x-modal>

        <x-modal name="transfer-boxes-modal" title="Transfer Weight" subtitle="Move weight of birds from one dealer/vendor to another" icon="swap_horiz" maxWidth="lg">
            <form id="transfer-form" :action="transferFormAction" method="POST">
                @csrf

                {{-- Source Entry Info Card --}}
                <div class="mb-5 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-700/50 shadow-inner">
                    <p class="text-[10px] font-extrabold uppercase tracking-wider text-zinc-400 mb-3 flex items-center gap-1.5">
                        <span class="material-symbols-rounded text-xs">info</span>
                        Source Entry Details
                    </p>
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="p-2.5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-150/80 dark:border-zinc-800">
                            <span class="text-zinc-400 font-medium block mb-0.5">Vendor</span>
                            <p class="font-extrabold text-zinc-800 dark:text-zinc-200 truncate" x-text="transferSourceVendor || '—'"></p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-150/80 dark:border-zinc-800">
                            <span class="text-zinc-400 font-medium block mb-0.5">Dealer</span>
                            <p class="font-extrabold text-zinc-800 dark:text-zinc-200 truncate" x-text="transferSourceDealer || '—'"></p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-150/80 dark:border-zinc-800">
                            <span class="text-zinc-400 font-medium block mb-0.5">Available Weight</span>
                            <p class="font-jetbrains font-extrabold text-base text-zinc-850 dark:text-zinc-150" x-text="parseFloat(transferSourceWeight).toFixed(2) + ' kg'"></p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-150/80 dark:border-zinc-800">
                            <span class="text-zinc-400 font-medium block mb-0.5">Date</span>
                            <p class="font-extrabold text-zinc-800 dark:text-zinc-200" x-text="transferDate || '—'"></p>
                        </div>
                    </div>
                </div>

                {{-- Input Fields --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Weight to Transfer (kg)</label>
                        <input
                            type="number"
                            name="transfer_weight"
                            min="0.01"
                            step="0.01"
                            :max="transferMaxWeight"
                            x-model.number="transferWeight"
                            required
                            class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                        >
                        <p class="mt-1.5 text-[11px] text-zinc-500">
                            Remaining: <span class="font-bold text-zinc-800 dark:text-zinc-200" x-text="parseFloat(transferSourceWeight - transferWeight).toFixed(2)"></span> kg
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Target Dealer</label>
                        <select name="target_dealer_id" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                            <option value="">Select dealer...</option>
                            @foreach($dealers as $dealer)
                                <option value="{{ $dealer->id }}">{{ $dealer->firm_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Target Vendor</label>
                        <select name="target_vendor_id" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                            <option value="">Select vendor...</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->firm_name }}{{ $vendor->is_shop ? ' (Shop)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Reason</label>
                        <input
                            type="text"
                            name="reason"
                            required
                            placeholder="e.g. Reassign weight to correct dealer"
                            class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                        >
                    </div>
                </div>

                <x-slot:footer>
                    <x-button type="button" variant="outline" x-on:click="$dispatch('close-modal', 'transfer-boxes-modal')">Cancel</x-button>
                    <x-button type="submit" form="transfer-form" variant="primary" icon="swap_horiz">Transfer Weight</x-button>
                </x-slot:footer>
            </form>
        </x-modal>

        <x-modal name="set-farm-weight-modal" title="Set Farm Weight" subtitle="Enter total farm weight — it will be distributed proportionally by bird weight" icon="scale" maxWidth="4xl">
            <form id="set-farm-weight-form" action="{{ route('billing.day-load.set-farm-weight') }}" method="POST"
                  x-data="{
                      totalFarmWeight: '',
                      totalBirdWeight: {{ (float) ($batch?->total_bird_weight ?? 0) }},
                      entries: [
                          @foreach($entries->where('status', 'Active') as $entry)
                              { id: {{ $entry->id }}, vendor: '{{ addslashes($entry->vendor->firm_name ?? '-') }}', dealer: '{{ addslashes($entry->dealer->firm_name ?? '-') }}', boxes: {{ $entry->no_of_boxes }}, birdWeight: {{ (float) $entry->bird_weight }}, proportion: {{ ($batch->total_bird_weight ?? 0) > 0 ? ((float) $entry->bird_weight / (float) $batch->total_bird_weight) : 0 }} },
                          @endforeach
                      ],
                      get distributedTotal() {
                          if (!this.totalFarmWeight || this.totalFarmWeight === '') return 0;
                          let sum = 0;
                          this.entries.forEach(e => { sum += parseFloat((this.totalFarmWeight * e.proportion).toFixed(2)); });
                          return sum;
                      },
                      get totalLoss() {
                          return (this.totalBirdWeight - this.distributedTotal).toFixed(2);
                      }
                  }"
            >
                @csrf
                <input type="hidden" name="batch_id" value="{{ $batch?->id }}">

                {{-- Summary Metrics & Main Input --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                    <div class="rounded-2xl border border-zinc-200/50 dark:border-zinc-700/50 bg-zinc-50 dark:bg-zinc-800/40 p-4 shadow-sm">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Total Bird Weight</p>
                        <p class="font-jetbrains text-2xl font-black text-indigo-600 dark:text-indigo-400" x-text="totalBirdWeight.toFixed(2) + ' kg'"></p>
                    </div>
                    <div class="rounded-2xl border border-zinc-200/50 dark:border-zinc-700/50 bg-zinc-50 dark:bg-zinc-800/40 p-4 shadow-sm">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Total Loss</p>
                        <p class="font-jetbrains text-2xl font-black" :class="parseFloat(totalLoss) >= 0 ? 'text-rose-600' : 'text-emerald-600'" x-text="totalFarmWeight ? totalLoss + ' kg' : '—'"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Enter Total Farm Weight (Kg)</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="total_farm_weight"
                            x-model="totalFarmWeight"
                            placeholder="0.00"
                            required
                            class="w-full rounded-xl border-2 border-emerald-300 dark:border-emerald-600 bg-white dark:bg-zinc-900 px-4 py-2.5 text-lg font-jetbrains font-bold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all text-emerald-600"
                        >
                    </div>
                </div>

                {{-- Proportion Distribution Table --}}
                <div class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800 max-h-[40vh] overflow-y-auto mb-4 scrollbar-thin scrollbar-thumb-zinc-200 dark:scrollbar-thumb-zinc-800 scrollbar-track-transparent">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 z-10 bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-750">
                            <tr class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">
                                <th class="px-4 py-3 text-left">Vendor</th>
                                <th class="px-4 py-3 text-left">Dealer</th>
                                <th class="px-4 py-3 text-center">Boxes</th>
                                <th class="px-4 py-3 text-center">Bird Wt (Kg)</th>
                                <th class="px-4 py-3 text-center bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-600">Farm Wt (Kg)</th>
                                <th class="px-4 py-3 text-center text-rose-600">Loss (Kg)</th>
                                <th class="px-4 py-3 text-center">Total (Kg)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80 bg-white dark:bg-zinc-900">
                            <template x-for="(entry, index) in entries" :key="entry.id">
                                <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition-colors">
                                    <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100 text-xs" x-text="entry.vendor"></td>
                                    <td class="px-4 py-3 text-zinc-550 dark:text-zinc-450 text-xs" x-text="entry.dealer"></td>
                                    <td class="px-4 py-3 text-center font-jetbrains font-bold text-xs text-zinc-500" x-text="entry.boxes"></td>
                                    <td class="px-4 py-3 text-center font-jetbrains text-xs font-semibold text-zinc-700 dark:text-zinc-300" x-text="entry.birdWeight.toFixed(2)"></td>
                                    <td class="px-4 py-3 text-center font-jetbrains text-xs font-bold text-emerald-600 bg-emerald-50/30 dark:bg-emerald-950/10"
                                        x-text="totalFarmWeight ? (totalFarmWeight * entry.proportion).toFixed(2) : '—'"></td>
                                    <td class="px-4 py-3 text-center font-jetbrains text-xs font-bold text-rose-600"
                                        x-text="totalFarmWeight ? (entry.birdWeight - (totalFarmWeight * entry.proportion)).toFixed(2) : '—'"></td>
                                    <td class="px-4 py-3 text-center font-jetbrains text-xs font-bold text-zinc-800 dark:text-zinc-200"
                                        x-text="totalFarmWeight ? (entry.birdWeight - (totalFarmWeight * entry.proportion)).toFixed(2) : '—'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Reason / Audit --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Reason for setting farm weight</label>
                    <input
                        type="text"
                        name="reason"
                        required
                        placeholder="Why are you setting farm weight?"
                        class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                    >
                </div>

                <x-slot:footer>
                    <x-button type="button" variant="ghost" x-on:click="$dispatch('close-modal', 'set-farm-weight-modal')">Cancel</x-button>
                    <x-button type="submit" form="set-farm-weight-form" variant="primary" icon="save">Distribute & Save</x-button>
                </x-slot:footer>
            </form>
        </x-modal>

        <x-modal name="adjust-all-modal" title="Adjust All Entries" subtitle="Edit farm weight and remarks for all entries at once" icon="edit_note" maxWidth="4xl">
            <form id="adjust-all-form" action="{{ route('billing.day-load.bulk-update') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                {{-- Bulk Edit Table --}}
                <div class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800 max-h-[55vh] overflow-y-auto scrollbar-thin scrollbar-thumb-zinc-200 dark:scrollbar-thumb-zinc-800 scrollbar-track-transparent">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 z-10 bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-750">
                            <tr class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">
                                <th class="px-4 py-3 text-left">Vendor</th>
                                <th class="px-4 py-3 text-left">Dealer</th>
                                <th class="px-4 py-3 text-center">Boxes</th>
                                <th class="px-4 py-3 text-center">Bird Wt (Kg)</th>
                                <th class="px-4 py-3 text-center min-w-[120px] bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-600">Farm Weight (Kg)</th>
                                <th class="px-4 py-3 text-center text-rose-600">Loss (Kg)</th>
                                <th class="px-4 py-3 text-center">Total (Kg)</th>
                                <th class="px-4 py-3 text-left min-w-[160px]">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80 bg-white dark:bg-zinc-900">
                            @foreach($entries as $entry)
                                @if($entry->status === 'Active')
                                <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition-colors"
                                    x-data="{
                                        farmWeight: '{{ $entry->farm_weight ?? '' }}',
                                        birdWeight: {{ (float) $entry->bird_weight }}
                                    }"
                                >
                                    <input type="hidden" name="entries[{{ $entry->id }}][id]" value="{{ $entry->id }}">
                                    <td class="px-4 py-3">
                                        <p class="font-bold text-zinc-900 dark:text-zinc-100 text-xs">{{ $entry->vendor->firm_name ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3">
                                        <p class="text-zinc-550 dark:text-zinc-450 text-xs truncate max-w-[120px]">{{ $entry->dealer->firm_name ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-center font-jetbrains font-bold text-xs text-zinc-500">{{ $entry->no_of_boxes }}</td>
                                    <td class="px-4 py-3 text-center font-jetbrains text-xs font-semibold text-zinc-700 dark:text-zinc-300" x-text="birdWeight.toFixed(2)"></td>
                                    <td class="px-4 py-3 bg-emerald-50/20 dark:bg-emerald-950/10">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            name="entries[{{ $entry->id }}][farm_weight]"
                                            x-model="farmWeight"
                                            placeholder="0.00"
                                            class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-2 py-1 text-xs font-jetbrains font-bold text-center focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all text-emerald-600"
                                        >
                                    </td>
                                    <td class="px-4 py-3 text-center font-jetbrains text-xs font-bold"
                                        :class="farmWeight !== '' ? 'text-rose-600' : 'text-zinc-400'"
                                        x-text="farmWeight !== '' ? (birdWeight - parseFloat(farmWeight || 0)).toFixed(2) : '-'">
                                    </td>
                                    <td class="px-4 py-3 text-center font-jetbrains text-xs font-bold text-zinc-800 dark:text-zinc-200"
                                        x-text="farmWeight !== '' ? (birdWeight - parseFloat(farmWeight || 0)).toFixed(2) : '-'">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input
                                            type="text"
                                            name="entries[{{ $entry->id }}][remarks]"
                                            value="{{ $entry->remarks ?? '' }}"
                                            placeholder="Optional remarks"
                                            class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-1 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                        >
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Audit & Action Reason --}}
                <div class="mt-5 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200/50 dark:border-zinc-700/50">
                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Reason for Adjustment</label>
                    <input
                        type="text"
                        name="reason"
                        required
                        placeholder="Why are you adjusting these entries?"
                        class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-4 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                    >
                </div>

                <x-slot:footer>
                    <x-button type="button" variant="ghost" x-on:click="$dispatch('close-modal', 'adjust-all-modal')">Cancel</x-button>
                    <x-button type="submit" form="adjust-all-form" variant="primary" icon="save">Save All Changes</x-button>
                </x-slot:footer>
            </form>
        </x-modal>

        {{-- Dealer Payment Modal --}}
        <x-modal name="dealer-payment-modal" title="Record Dealer Payment" subtitle="Record payment received from dealer for this entry" icon="payments" maxWidth="lg">
            <form id="dealer-payment-form" :action="dpFormAction" method="POST">
                @csrf
                <div class="mb-5 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                        <div>
                            <span class="text-xs text-zinc-500">Vendor:</span>
                            <p class="font-bold text-zinc-900 dark:text-zinc-100" x-text="dpEntryVendor"></p>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500">Dealer:</span>
                            <p class="font-bold text-zinc-900 dark:text-zinc-100" x-text="dpEntryDealer"></p>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500">Total Due:</span>
                            <p class="font-jetbrains font-bold text-emerald-600" x-text="'Rs ' + (dpEntryIncome - dpEntryCollected).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></p>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500">Already Collected:</span>
                            <p class="font-jetbrains font-bold" x-text="'Rs ' + dpEntryCollected.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Payment Date</label>
                        <input type="date" name="date" required x-model="dpDate" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Cash Amount (Rs)</label>
                        <input type="number" step="0.01" min="0" name="cash_amount" required x-model.number="dpCashAmount" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains text-lg font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Bank Amount (Rs)</label>
                        <input type="number" step="0.01" min="0" name="bank_amount" required x-model.number="dpBankAmount" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains text-lg font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Total</label>
                        <p class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2.5 text-sm font-jetbrains text-lg font-bold text-emerald-600" x-text="'Rs ' + (dpCashAmount + dpBankAmount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Payment Mode</label>
                        <select name="payment_mode" required x-model="dpMode" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                            @foreach(config('payments.modes') as $mode)
                                <option value="{{ $mode }}">{{ $mode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="dpBankAmount > 0" x-transition>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Bank Transfer Type</label>
                        <select name="bank_transfer_type" x-model="dpBankTransferType" :required="dpBankAmount > 0" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                            <option value="">Select type...</option>
                            <option value="UPI">UPI</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="NEFT">NEFT</option>
                            <option value="RTGS">RTGS</option>
                            <option value="IMPS">IMPS</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div x-show="dpBankAmount <= 0">
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Reference No</label>
                        <input type="text" name="reference_number" x-model="dpRefNo" placeholder="UPI ref / Cheque no / Tx ID" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Remarks</label>
                    <textarea name="notes" x-model="dpNotes" rows="2" placeholder="Optional notes" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm"></textarea>
                </div>

                <x-slot:footer>
                    <x-button type="button" variant="outline" x-on:click="$dispatch('close-modal', 'dealer-payment-modal')">Cancel</x-button>
                    <x-button type="submit" form="dealer-payment-form" variant="primary" icon="payments">Record Payment</x-button>
                </x-slot:footer>
            </form>
        </x-modal>

        {{-- Vendor Payment Modal --}}
        <x-modal name="vendor-payment-modal" title="Record Vendor Payment" subtitle="Record payment made to vendor for this entry" icon="account_balance_wallet" maxWidth="lg">
            <form id="vendor-payment-form" :action="vpFormAction" method="POST">
                @csrf
                <div class="mb-5 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                        <div>
                            <span class="text-xs text-zinc-500">Vendor:</span>
                            <p class="font-bold text-zinc-900 dark:text-zinc-100" x-text="vpEntryVendor"></p>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500">Dealer:</span>
                            <p class="font-bold text-zinc-900 dark:text-zinc-100" x-text="vpEntryDealer"></p>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500">Total Payable:</span>
                            <p class="font-jetbrains font-bold text-violet-600" x-text="'Rs ' + (vpEntryCost - vpEntryPaid).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></p>
                        </div>
                        <div>
                            <span class="text-xs text-zinc-500">Already Paid:</span>
                            <p class="font-jetbrains font-bold" x-text="'Rs ' + vpEntryPaid.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Payment Date</label>
                        <input type="date" name="date" required x-model="vpDate" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Cash Amount (Rs)</label>
                        <input type="number" step="0.01" min="0" name="cash_amount" required x-model.number="vpCashAmount" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains text-lg font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Bank Amount (Rs)</label>
                        <input type="number" step="0.01" min="0" name="bank_amount" required x-model.number="vpBankAmount" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains text-lg font-bold">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Total</label>
                        <p class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800 px-3 py-2.5 text-sm font-jetbrains text-lg font-bold text-emerald-600" x-text="'Rs ' + (vpCashAmount + vpBankAmount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Payment Mode</label>
                        <select name="payment_mode" required x-model="vpMode" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                            @foreach(config('payments.modes') as $mode)
                                <option value="{{ $mode }}">{{ $mode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div x-show="vpBankAmount > 0" x-transition>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Bank Transfer Type</label>
                        <select name="bank_transfer_type" x-model="vpBankTransferType" :required="vpBankAmount > 0" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                            <option value="">Select type...</option>
                            <option value="UPI">UPI</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="NEFT">NEFT</option>
                            <option value="RTGS">RTGS</option>
                            <option value="IMPS">IMPS</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div x-show="vpBankAmount <= 0">
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Reference No</label>
                        <input type="text" name="reference_number" x-model="vpRefNo" placeholder="UPI ref / Cheque no / Tx ID" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Remarks</label>
                    <textarea name="notes" x-model="vpNotes" rows="2" placeholder="Optional notes" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm"></textarea>
                </div>

                <x-slot:footer>
                    <x-button type="button" variant="outline" x-on:click="$dispatch('close-modal', 'vendor-payment-modal')">Cancel</x-button>
                    <x-button type="submit" form="vendor-payment-form" variant="primary" icon="account_balance_wallet">Record Payment</x-button>
                </x-slot:footer>
            </form>
        </x-modal>

        {{-- Lump-Sum Payment Modal --}}
        <x-modal name="lump-sum-payment-modal" title="Record Lump-Sum Payment" subtitle="Allocate a single payment across multiple entries" icon="payments" maxWidth="3xl">
            <form id="lump-sum-form" action="{{ route('billing.day-load.lumpsum-dealer-payment') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="dealer_id" :value="lsDealerId">
                <input type="hidden" name="date" :value="lsDate">
                <input type="hidden" name="cash_amount" :value="lsCashAmount">
                <input type="hidden" name="bank_amount" :value="lsBankAmount">
                <input type="hidden" name="payment_mode" :value="lsMode">
                <input type="hidden" name="bank_transfer_type" :value="lsBankTransferType">
                <input type="hidden" name="reference_number" :value="lsRefNo">
                <input type="hidden" name="notes" :value="lsNotes">
                <template x-for="(amount, entryId) in lsAllocations" :key="entryId">
                    <input type="hidden" :name="'allocations[' + entryId + ']'" :value="amount">
                </template>

                {{-- Dealer Select Step --}}
                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200/50 dark:border-zinc-700/50">
                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Select Dealer</label>
                    <select x-model="lsDealerId" @change="initLsDealer()" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2.5 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        <option value="0">Choose dealer...</option>
                        @foreach($dealers as $dealer)
                            <option value="{{ $dealer->id }}">{{ $dealer->firm_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Allocation Table --}}
                <template x-if="lsEntries.length > 0">
                    <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200/50 dark:border-zinc-700/50">
                        <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-3 flex items-center gap-1.5">
                            <span class="material-symbols-rounded text-xs text-zinc-400">playlist_add_check</span>
                            Allocate Payment Across Entries
                        </p>
                        <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-800">
                            <table class="w-full text-sm">
                                <thead class="bg-zinc-100 dark:bg-zinc-800/60 border-b border-zinc-200 dark:border-zinc-750">
                                    <tr class="text-xs font-bold text-zinc-500 uppercase">
                                        <th class="px-3 py-2 text-left">Vendor</th>
                                        <th class="px-3 py-2 text-right">Total (Rs)</th>
                                        <th class="px-3 py-2 text-right text-emerald-600">Collected (Rs)</th>
                                        <th class="px-3 py-2 text-right">Balance (Rs)</th>
                                        <th class="px-3 py-2 text-right min-w-[140px]">Allocate Now (Rs)</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-100 dark:divide-zinc-850">
                                    <template x-for="entry in lsEntries" :key="entry.id">
                                        <tr class="hover:bg-zinc-55 border-t border-zinc-100 dark:border-zinc-800">
                                            <td class="px-3 py-2.5 font-bold text-xs" x-text="entry.vendor"></td>
                                            <td class="px-3 py-2.5 text-right font-jetbrains text-xs" x-text="entry.dealer_income.toLocaleString('en-IN', {minimumFractionDigits: 2})"></td>
                                            <td class="px-3 py-2.5 text-right font-jetbrains text-emerald-600 text-xs" x-text="entry.dealer_collected.toLocaleString('en-IN', {minimumFractionDigits: 2})"></td>
                                            <td class="px-3 py-2.5 text-right font-jetbrains font-bold text-xs" x-text="entry.due.toLocaleString('en-IN', {minimumFractionDigits: 2})"></td>
                                            <td class="px-3 py-2.5 text-right">
                                                <input type="number" step="0.01" min="0" :max="entry.due"
                                                    x-model.number="lsAllocations[entry.id]"
                                                    @input="if (lsAllocations[entry.id] > entry.due) lsAllocations[entry.id] = entry.due; recalcAllocSum()"
                                                    class="w-28 rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-2 py-1 text-right text-sm font-jetbrains focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-bold text-emerald-600">
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="bg-zinc-50 dark:bg-zinc-850 font-bold border-t border-zinc-200 dark:border-zinc-750">
                                    <tr>
                                        <td class="px-3 py-2 text-xs text-zinc-400">TOTAL</td>
                                        <td class="px-3 py-2 text-right font-jetbrains text-xs" x-text="lsEntries.reduce((s, e) => s + e.dealer_income, 0).toLocaleString('en-IN', {minimumFractionDigits: 2})"></td>
                                        <td class="px-3 py-2 text-right font-jetbrains text-emerald-600 text-xs" x-text="lsEntries.reduce((s, e) => s + e.dealer_collected, 0).toLocaleString('en-IN', {minimumFractionDigits: 2})"></td>
                                        <td class="px-3 py-2 text-right font-jetbrains text-xs" x-text="lsEntries.reduce((s, e) => s + e.due, 0).toLocaleString('en-IN', {minimumFractionDigits: 2})"></td>
                                        <td class="px-3 py-2 text-right font-jetbrains text-emerald-600 text-xs" x-text="lsAllocSum.toLocaleString('en-IN', {minimumFractionDigits: 2})"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="mt-2.5 flex items-center justify-end gap-2 text-xs text-zinc-400 font-medium">
                            <span>Allocated: <strong class="font-jetbrains text-zinc-700 dark:text-zinc-300" x-text="'Rs ' + lsAllocSum.toLocaleString('en-IN', {minimumFractionDigits: 2})"></strong></span>
                            <span class="text-zinc-300 dark:text-zinc-600">/</span>
                            <span>Lump sum: <strong class="font-jetbrains text-zinc-700 dark:text-zinc-300" x-text="'Rs ' + lsTotalLump.toLocaleString('en-IN', {minimumFractionDigits: 2})"></strong></span>
                            <template x-if="lsAllocSum > lsTotalLump">
                                <span class="text-rose-600 font-bold ml-1 flex items-center gap-0.5">
                                    <span class="material-symbols-rounded text-sm">warning</span>
                                    Exceeds by Rs <span x-text="(lsAllocSum - lsTotalLump).toLocaleString('en-IN', {minimumFractionDigits: 2})"></span>
                                </span>
                            </template>
                        </div>
                    </div>
                </template>

                {{-- Payment Details Group --}}
                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200/50 dark:border-zinc-700/50">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-3 flex items-center gap-1.5">
                        <span class="material-symbols-rounded text-xs text-zinc-400">payments</span>
                        Payment Details
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Payment Date</label>
                            <input type="date" x-model="lsDate" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Cash Amount (Rs)</label>
                            <input type="number" step="0.01" min="0" x-model.number="lsCashAmount" @input="lsTotalLump = Math.round((lsCashAmount + lsBankAmount) * 100) / 100" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains text-lg font-bold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all text-emerald-600">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Bank Amount (Rs)</label>
                            <input type="number" step="0.01" min="0" x-model.number="lsBankAmount" @input="lsTotalLump = Math.round((lsCashAmount + lsBankAmount) * 100) / 100" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains text-lg font-bold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all text-emerald-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Total Lump Sum</label>
                            <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-3 py-2 text-emerald-600 dark:text-emerald-400 font-jetbrains text-lg font-extrabold flex items-center justify-between min-h-[44px]">
                                <span class="text-xs font-bold uppercase tracking-wider">Total</span>
                                <span x-text="'Rs ' + lsTotalLump.toLocaleString('en-IN', {minimumFractionDigits: 2})"></span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Payment Mode</label>
                            <select x-model="lsMode" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                                @foreach(config('payments.modes') as $mode)
                                    <option value="{{ $mode }}">{{ $mode }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div x-show="lsBankAmount > 0" x-transition>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Bank Transfer Type</label>
                            <select x-model="lsBankTransferType" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                                <option value="">Select type...</option>
                                <option value="UPI">UPI</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="NEFT">NEFT</option>
                                <option value="RTGS">RTGS</option>
                                <option value="IMPS">IMPS</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div x-show="lsBankAmount <= 0">
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Reference No</label>
                            <input type="text" x-model="lsRefNo" placeholder="UPI ref / Cheque no / Tx ID" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                        </div>
                    </div>

                    <div class="mb-2">
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Remarks</label>
                        <textarea x-model="lsNotes" rows="2" placeholder="Optional notes" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"></textarea>
                    </div>
                </div>

                <x-slot:footer>
                    <x-button type="button" variant="outline" x-on:click="$dispatch('close-modal', 'lump-sum-payment-modal')">Cancel</x-button>
                    <x-button type="submit" form="lump-sum-form" variant="primary" icon="payments" x-bind:disabled="lsAllocSum > lsTotalLump || lsAllocSum <= 0">Record Lump-Sum Payment</x-button>
                </x-slot:footer>
            </form>
        </x-modal>
    </div>
</div>

<script>
    function dayLoadBillingData() {
        return {
            transferSourceId: 0,
            transferSourceBoxes: 0,
            transferSourceWeight: 0,
            transferSourceVendor: '',
            transferSourceDealer: '',
            transferBatchId: 0,
            transferDate: '',
            transferMaxWeight: 0,
            transferWeight: 0,
            transferFormAction: '',
            editEntryId: 0,
            editFormAction: '',
            editVendorId: 0,
            editDealerId: 0,
            editPaperRate: 0,
            editBillingRate: 0,
            editCustomerRate: 0,
            editNoOfBoxes: 0,
            editBoxWeight: 0,
            editEmptyWeight: 0,
            editFarmWeight: '',
            editRemarks: '',
            dpEntryId: 0,
            dpFormAction: '',
            dpEntryVendor: '',
            dpEntryDealer: '',
            dpEntryIncome: 0,
            dpEntryCollected: 0,
            dpCashAmount: 0,
            dpBankAmount: 0,
            dpBankTransferType: '',
            dpDate: '{{ $date }}',
            dpMode: 'Cash',
            dpRefNo: '',
            dpNotes: '',
            vpEntryId: 0,
            vpFormAction: '',
            vpEntryVendor: '',
            vpEntryDealer: '',
            vpEntryCost: 0,
            vpEntryPaid: 0,
            vpCashAmount: 0,
            vpBankAmount: 0,
            vpBankTransferType: '',
            vpDate: '{{ $date }}',
            vpMode: 'Cash',
            vpRefNo: '',
            vpNotes: '',
            lsEntriesByDealer: {{ Js::from($lsEntriesByDealer) }},
            lsDealerId: 0,
            lsEntries: [],
            lsAllocations: {},
            lsAllocSum: 0,
            lsCashAmount: 0,
            lsBankAmount: 0,
            lsTotalLump: 0,
            lsDate: '{{ $date }}',
            lsMode: 'Cash',
            lsBankTransferType: '',
            lsRefNo: '',
            lsNotes: '',
            initLsDealer() {
                this.lsEntries = this.lsEntriesByDealer[this.lsDealerId] || [];
                this.lsAllocations = {};
                this.lsEntries.forEach(e => { this.lsAllocations[e.id] = 0; });
                this.recalcAllocSum();
            },
            recalcAllocSum() {
                let sum = 0;
                Object.values(this.lsAllocations).forEach(v => { sum += parseFloat(v) || 0; });
                this.lsAllocSum = Math.round(sum * 100) / 100;
            }
        };
    }
</script>
@endsection
