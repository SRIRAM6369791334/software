@extends('layouts.app')
@section('title', 'Daily Load Billing')

@section('content')
<div class="animate-fade-in" x-data="dayLoadBillingData()">
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
                        @if($entry->status === 'Active' || $entry->status === 'Adjusted')
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

        <template x-teleport="body">
            <x-modal name="edit-entry-modal" title="Edit Entry" subtitle="Adjust rates, weights, or box count" icon="edit" maxWidth="3xl">
                <form id="edit-entry-form" :action="editFormAction" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" name="_method" value="PUT">

                    {{-- Primary Details --}}
                    <div>
                        <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-3">Primary Details</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <x-form.select name="vendor_id" label="Vendor" required x-model="editVendorId">
                                <option value="">Select vendor...</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->firm_name }}{{ $vendor->is_shop ? ' (Shop)' : '' }}</option>
                                @endforeach
                            </x-form.select>

                            <x-form.select name="dealer_id" label="Dealer" required x-model="editDealerId">
                                <option value="">Select dealer...</option>
                                @foreach($dealers as $dealer)
                                    <option value="{{ $dealer->id }}">{{ $dealer->firm_name }}</option>
                                @endforeach
                            </x-form.select>

                            <x-form.input type="number" name="no_of_boxes" label="Boxes" required min="1" x-model.number="editNoOfBoxes" icon="inventory_2" />
                        </div>
                    </div>

                    {{-- Pricing Rates --}}
                    <div>
                        <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-3">Pricing Rates (Rs)</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <x-form.input type="number" step="0.01" name="paper_rate" label="Paper Rate" required min="0" x-model.number="editPaperRate" icon="currency_rupee" />
                            <x-form.input type="number" step="0.01" name="billing_rate" label="vendor Rate" required min="0" x-model.number="editBillingRate" icon="currency_rupee" />
                            <x-form.input type="number" step="0.01" name="customer_rate" label="Customer Rate" required min="0" x-model.number="editCustomerRate" icon="currency_rupee" />
                        </div>
                    </div>

                    {{-- Weights --}}
                    <div>
                        <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-3">Load Weights (Kg)</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <x-form.input type="number" step="0.01" name="box_weight" label="Box Weight" required min="0" x-model.number="editBoxWeight" icon="scale" />
                            <x-form.input type="number" step="0.01" name="empty_weight" label="Empty Weight" required min="0" x-model.number="editEmptyWeight" icon="scale" />
                            <x-form.input type="number" step="0.01" name="farm_weight" label="Farm Weight" min="0" x-model="editFarmWeight" icon="scale" />
                        </div>
                    </div>

                    {{-- Remarks & Audit --}}
                    <div>
                        <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-3">Audit & Remarks</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <x-form.input type="text" name="remarks" label="Remarks" x-model="editRemarks" placeholder="Optional notes" icon="description" />
                            <x-form.input type="text" name="reason" label="Reason for Edit" required placeholder="Why are you editing this entry?" icon="help" />
                        </div>
                    </div>

                    <x-slot:footer>
                        <x-button type="button" variant="outline" x-on:click="$dispatch('close-modal', 'edit-entry-modal')">Cancel</x-button>
                        <x-button type="submit" form="edit-entry-form" variant="primary" icon="check" class="px-8">Save Changes</x-button>
                    </x-slot:footer>
                </form>
            </x-modal>
        </template>

        <template x-teleport="body">
            <x-modal name="transfer-boxes-modal" title="Transfer Weight" subtitle="Move weight of birds from one dealer/vendor to another" icon="swap_horiz" maxWidth="720">
                <form id="transfer-form" :action="transferFormAction" method="POST" class="space-y-6">
                    @csrf

                    {{-- Source Entry Details --}}
                    <div>
                        <p class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-3 flex items-center gap-1.5">
                            <span class="material-symbols-rounded text-sm">info</span>
                            Source Entry Details
                        </p>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-700/50 shadow-sm flex flex-col justify-center">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide block mb-0.5">Vendor</span>
                                <p class="font-extrabold text-zinc-850 dark:text-zinc-150 text-xs truncate" x-text="transferSourceVendor || '—'"></p>
                            </div>
                            <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-700/50 shadow-sm flex flex-col justify-center">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide block mb-0.5">Dealer</span>
                                <p class="font-extrabold text-zinc-850 dark:text-zinc-150 text-xs truncate" x-text="transferSourceDealer || '—'"></p>
                            </div>
                            <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-700/50 shadow-sm flex flex-col justify-center">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide block mb-0.5">Available Wt</span>
                                <p class="font-jetbrains font-extrabold text-xs text-indigo-600 dark:text-indigo-400" x-text="parseFloat(transferSourceWeight).toFixed(2) + ' kg'"></p>
                            </div>
                            <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-700/50 shadow-sm flex flex-col justify-center">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide block mb-0.5">Date</span>
                                <p class="font-extrabold text-zinc-850 dark:text-zinc-150 text-xs" x-text="transferDate || '—'"></p>
                            </div>
                        </div>
                    </div>

                    {{-- Input Fields --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <x-form.input 
                                type="number" 
                                name="transfer_weight" 
                                label="Weight to Transfer (kg)" 
                                required 
                                min="0.01" 
                                step="0.01" 
                                x-bind:max="transferMaxWeight" 
                                x-model.number="transferWeight" 
                                icon="scale"
                            />
                            <p class="mt-1.5 text-xs text-zinc-500">
                                Remaining: <span class="font-bold text-zinc-800 dark:text-zinc-200" x-text="parseFloat(transferSourceWeight - transferWeight).toFixed(2)"></span> kg
                            </p>
                        </div>

                        <x-form.select name="target_dealer_id" label="Target Dealer" required>
                            <option value="">Select dealer...</option>
                            @foreach($dealers as $dealer)
                                <option value="{{ $dealer->id }}">{{ $dealer->firm_name }}</option>
                            @endforeach
                        </x-form.select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-form.select name="target_vendor_id" label="Target Vendor" required>
                            <option value="">Select vendor...</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->firm_name }}{{ $vendor->is_shop ? ' (Shop)' : '' }}</option>
                            @endforeach
                        </x-form.select>

                        <x-form.input type="text" name="reason" label="Reason" required placeholder="e.g. Reassign weight to correct dealer" icon="description" />
                    </div>

                    <x-slot:footer>
                        <x-button type="button" variant="outline" x-on:click="$dispatch('close-modal', 'transfer-boxes-modal')">Cancel</x-button>
                        <x-button type="submit" form="transfer-form" variant="primary" icon="check" class="px-8">Transfer Weight</x-button>
                    </x-slot:footer>
                </form>
            </x-modal>
        </template>

        <template x-teleport="body">
            <x-modal name="set-farm-weight-modal" title="Set Farm Weight" subtitle="Enter total farm weight — it will be set at the batch level" icon="scale" maxWidth="4xl">
                <form id="set-farm-weight-form" action="{{ route('billing.day-load.set-farm-weight') }}" method="POST"
                      x-data="{
                          totalFarmWeight: '{{ $batch?->total_farm_weight ?? '' }}',
                          totalBirdWeight: {{ (float) ($batch?->total_bird_weight ?? 0) }},
                          entries: [
                              @foreach($entries->where('status', 'Active') as $entry)
                                  { id: {{ $entry->id }}, vendor: '{{ addslashes($entry->vendor->firm_name ?? '-') }}', dealer: '{{ addslashes($entry->dealer->firm_name ?? '-') }}', boxes: {{ $entry->no_of_boxes }}, birdWeight: {{ (float) $entry->bird_weight }} },
                              @endforeach
                          ],
                          get totalLoss() {
                              if (!this.totalFarmWeight || this.totalFarmWeight === '') return 0;
                              return (parseFloat(this.totalFarmWeight) - this.totalBirdWeight).toFixed(2);
                          }
                      }"
                      class="space-y-6"
                >
                    @csrf
                    <input type="hidden" name="batch_id" value="{{ $batch?->id }}">

                    {{-- Note about batch-level setting --}}
                    <div class="p-3.5 rounded-xl bg-amber-50 dark:bg-amber-950/20 border border-amber-200 dark:border-amber-900/50 flex gap-3 text-xs text-amber-800 dark:text-amber-300">
                        <span class="material-symbols-rounded text-lg">info</span>
                        <div>
                            <p class="font-bold mb-0.5">Batch-Level Weight Setting</p>
                            <p>Setting the farm weight here applies it to the entire day's batch. Individual entries' weights will not be modified proportionally.</p>
                        </div>
                    </div>

                    {{-- Summary Metrics & Main Input --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                        <div class="rounded-2xl border border-zinc-200/50 dark:border-zinc-700/50 bg-zinc-50 dark:bg-zinc-800/40 p-4 shadow-sm flex flex-col justify-center">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Total Bird Weight</p>
                            <p class="font-jetbrains text-2xl font-black text-indigo-600 dark:text-indigo-400" x-text="totalBirdWeight.toFixed(2) + ' kg'"></p>
                        </div>
                        <div class="rounded-2xl border border-zinc-200/50 dark:border-zinc-700/50 bg-zinc-50 dark:bg-zinc-800/40 p-4 shadow-sm flex flex-col justify-center">
                            <p class="text-[10px] font-bold uppercase tracking-wider text-zinc-400 mb-1">Total Loss</p>
                            <p class="font-jetbrains text-2xl font-black" :class="(totalFarmWeight && parseFloat(totalFarmWeight) > 0) ? (parseFloat(totalLoss) >= 0 ? 'text-emerald-600' : 'text-rose-600') : 'text-zinc-400'" x-text="(totalFarmWeight && parseFloat(totalFarmWeight) > 0) ? totalLoss + ' kg' : '—'"></p>
                        </div>
                        <div>
                            <x-form.input
                                type="number"
                                step="0.01"
                                min="0"
                                name="total_farm_weight"
                                label="Enter Total Farm Weight (Kg)"
                                x-model="totalFarmWeight"
                                placeholder="0.00"
                                required
                                icon="scale"
                            />
                        </div>
                    </div>

                    {{-- Active Entries Table --}}
                    <div class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800 max-h-[40vh] overflow-y-auto scrollbar-thin scrollbar-thumb-zinc-200 dark:scrollbar-thumb-zinc-800 scrollbar-track-transparent">
                        <table class="w-full text-sm">
                            <thead class="sticky top-0 z-10 bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-750">
                                <tr class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">
                                    <th class="px-4 py-3 text-left">Vendor</th>
                                    <th class="px-4 py-3 text-left">Dealer</th>
                                    <th class="px-4 py-3 text-center">Boxes</th>
                                    <th class="px-4 py-3 text-center">Bird Wt (Kg)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/80 bg-white dark:bg-zinc-900">
                                <template x-for="(entry, index) in entries" :key="entry.id">
                                    <tr class="hover:bg-zinc-50/60 dark:hover:bg-zinc-800/40 transition-colors">
                                        <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100 text-xs" x-text="entry.vendor"></td>
                                        <td class="px-4 py-3 text-zinc-550 dark:text-zinc-450 text-xs" x-text="entry.dealer"></td>
                                        <td class="px-4 py-3 text-center font-jetbrains font-bold text-xs text-zinc-500" x-text="entry.boxes"></td>
                                        <td class="px-4 py-3 text-center font-jetbrains text-xs font-semibold text-zinc-700 dark:text-zinc-300" x-text="entry.birdWeight.toFixed(2)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    {{-- Reason / Audit --}}
                    <div class="mb-2">
                        <x-form.input
                            type="text"
                            name="reason"
                            label="Reason for setting farm weight"
                            required
                            placeholder="Why are you setting farm weight?"
                            icon="description"
                        />
                    </div>

                    <x-slot:footer>
                        <x-button type="button" variant="outline" x-on:click="$dispatch('close-modal', 'set-farm-weight-modal')">Cancel</x-button>
                        <x-button type="submit" form="set-farm-weight-form" variant="primary" icon="check" class="px-8">Save & Set</x-button>
                    </x-slot:footer>
                </form>
            </x-modal>
        </template>

        <template x-teleport="body">
            <x-modal name="adjust-all-modal" title="Adjust All Entries" subtitle="Edit farm weight and remarks for all entries at once" icon="edit_note" maxWidth="6xl">
                <form id="adjust-all-form" action="{{ route('billing.day-load.bulk-update') }}" method="POST" class="space-y-6">
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
                                    <th class="px-4 py-3 text-center min-w-[130px] bg-emerald-50/50 dark:bg-emerald-950/20 text-emerald-600">Farm Weight (Kg)</th>
                                    <th class="px-4 py-3 text-center text-rose-600">Loss (Kg)</th>
                                    <th class="px-4 py-3 text-center">Total (Kg)</th>
                                    <th class="px-4 py-3 text-left min-w-[180px]">Remarks</th>
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
                                                class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-xs font-jetbrains font-bold text-center focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all text-emerald-600"
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
                                                class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                            >
                                        </td>
                                    </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Audit & Action Reason --}}
                    <div class="mb-2">
                        <x-form.input
                            type="text"
                            name="reason"
                            label="Reason for Adjustment"
                            required
                            placeholder="Why are you adjusting these entries?"
                            icon="description"
                        />
                    </div>

                    <x-slot:footer>
                        <x-button type="button" variant="outline" x-on:click="$dispatch('close-modal', 'adjust-all-modal')">Cancel</x-button>
                        <x-button type="submit" form="adjust-all-form" variant="primary" icon="check" class="px-8">Save All Changes</x-button>
                    </x-slot:footer>
                </form>
            </x-modal>
        </template>

        <template x-teleport="body">
            <x-modal name="dealer-payment-modal" title="Record Dealer Payment" subtitle="Record payment received from dealer for this entry" icon="payments" maxWidth="720">
                <form id="dealer-payment-form" :action="dpFormAction" method="POST" class="space-y-6">
                    @csrf

                    {{-- Due Summary Details --}}
                    <div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-700/50 shadow-sm flex flex-col justify-center">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide block mb-0.5">Vendor</span>
                                <p class="font-extrabold text-zinc-850 dark:text-zinc-150 text-xs truncate" x-text="dpEntryVendor"></p>
                            </div>
                            <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-700/50 shadow-sm flex flex-col justify-center">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide block mb-0.5">Dealer</span>
                                <p class="font-extrabold text-zinc-850 dark:text-zinc-150 text-xs truncate" x-text="dpEntryDealer"></p>
                            </div>
                            <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-700/50 shadow-sm flex flex-col justify-center">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide block mb-0.5">Total Due</span>
                                <p class="font-jetbrains font-extrabold text-xs text-rose-600" x-text="'Rs ' + (dpEntryIncome - dpEntryCollected).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></p>
                            </div>
                            <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-700/50 shadow-sm flex flex-col justify-center">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide block mb-0.5">Collected</span>
                                <p class="font-jetbrains font-extrabold text-xs text-emerald-600" x-text="'Rs ' + dpEntryCollected.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-form.input type="date" name="date" label="Payment Date" required x-model="dpDate" icon="calendar_month" />
                        <x-form.input type="number" step="0.01" min="0" name="cash_amount" label="Cash Amount (Rs)" required x-model.number="dpCashAmount" icon="payments" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-form.input type="number" step="0.01" min="0" name="bank_amount" label="Bank Amount (Rs)" required x-model.number="dpBankAmount" icon="account_balance" />
                        <div>
                            <label class="block mb-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit">Total Payment</label>
                            <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-3.5 py-2.5 text-emerald-600 dark:text-emerald-400 font-jetbrains text-lg font-extrabold flex items-center justify-between min-h-[46px]">
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-500 font-outfit">Total</span>
                                <span x-text="'Rs ' + (dpCashAmount + dpBankAmount).toLocaleString('en-IN', {minimumFractionDigits: 2})"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Method Selection --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-3">
                            Payment Mode <span class="text-emerald-500 font-bold ml-0.5">*</span>
                        </label>
                        <div class="grid grid-cols-4 gap-2.5">
                            @php 
                                $pmOptions = [
                                    ['value' => 'Cash', 'icon' => 'payments', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50'],
                                    ['value' => 'Bank Transfer', 'icon' => 'account_balance', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50'],
                                    ['value' => 'UPI', 'icon' => 'smartphone', 'color' => 'text-violet-500', 'bg' => 'bg-violet-50'],
                                    ['value' => 'Card', 'icon' => 'credit_card', 'color' => 'text-rose-500', 'bg' => 'bg-rose-50']
                                ]; 
                            @endphp
                            @foreach($pmOptions as $pm)
                            <label class="group relative flex flex-col items-center gap-2 py-4 px-1 rounded-2xl border-2 cursor-pointer transition-all duration-200 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/80 dark:has-[:checked]:bg-emerald-500/12 has-[:checked]:shadow-[0_0_0_1px_rgba(16,185,129,0.15),0_4px_12px_rgba(16,185,129,0.15)] border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600 bg-white/50 dark:bg-zinc-900/50">
                                <input type="radio" name="payment_mode" value="{{ $pm['value'] }}" x-model="dpMode" class="sr-only" required>
                                <div class="w-9 h-9 rounded-full {{ $pm['bg'] }} dark:{{ $pm['bg'] }}/10 flex items-center justify-center {{ $pm['color'] }}">
                                    <span class="material-symbols-rounded text-xl">{{ $pm['icon'] }}</span>
                                </div>
                                <span class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 group-has-[:checked]:text-emerald-700 dark:group-has-[:checked]:text-emerald-300 group-has-[:checked]:font-bold transition-all text-center leading-tight">{{ $pm['value'] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div x-show="dpBankAmount > 0" x-transition class="w-full">
                            <x-form.select name="bank_transfer_type" label="Bank Transfer Type" x-model="dpBankTransferType" x-bind:required="dpBankAmount > 0">
                                <option value="">Select type...</option>
                                <option value="UPI">UPI</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="NEFT">NEFT</option>
                                <option value="RTGS">RTGS</option>
                                <option value="IMPS">IMPS</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Other">Other</option>
                            </x-form.select>
                        </div>

                        <div x-show="dpBankAmount <= 0" x-transition class="w-full">
                            <x-form.input type="text" name="reference_number" label="Reference No" x-model="dpRefNo" placeholder="UPI ref / Cheque no / Tx ID" icon="description" />
                        </div>
                    </div>

                    <div class="mb-2">
                        <x-form.textarea name="notes" label="Remarks" x-model="dpNotes" rows="2" placeholder="Optional notes" />
                    </div>

                    <x-slot:footer>
                        <x-button type="button" variant="outline" x-on:click="$dispatch('close-modal', 'dealer-payment-modal')">Cancel</x-button>
                        <x-button type="submit" form="dealer-payment-form" variant="primary" icon="check" class="px-8">Record Payment</x-button>
                    </x-slot:footer>
                </form>
            </x-modal>
        </template>

        <template x-teleport="body">
            <x-modal name="vendor-payment-modal" title="Record Vendor Payment" subtitle="Record payment made to vendor for this entry" icon="account_balance_wallet" maxWidth="720">
                <form id="vendor-payment-form" :action="vpFormAction" method="POST" class="space-y-6">
                    @csrf

                    {{-- Payable Summary Details --}}
                    <div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-700/50 shadow-sm flex flex-col justify-center">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide block mb-0.5">Vendor</span>
                                <p class="font-extrabold text-zinc-850 dark:text-zinc-150 text-xs truncate" x-text="vpEntryVendor"></p>
                            </div>
                            <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-700/50 shadow-sm flex flex-col justify-center">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide block mb-0.5">Dealer</span>
                                <p class="font-extrabold text-zinc-850 dark:text-zinc-150 text-xs truncate" x-text="vpEntryDealer"></p>
                            </div>
                            <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-700/50 shadow-sm flex flex-col justify-center">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide block mb-0.5">Total Payable</span>
                                <p class="font-jetbrains font-extrabold text-xs text-rose-600" x-text="'Rs ' + (vpEntryCost - vpEntryPaid).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></p>
                            </div>
                            <div class="p-3 rounded-2xl bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200/50 dark:border-zinc-700/50 shadow-sm flex flex-col justify-center">
                                <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wide block mb-0.5">Already Paid</span>
                                <p class="font-jetbrains font-extrabold text-xs text-emerald-600" x-text="'Rs ' + vpEntryPaid.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-form.input type="date" name="date" label="Payment Date" required x-model="vpDate" icon="calendar_month" />
                        <x-form.input type="number" step="0.01" min="0" name="cash_amount" label="Cash Amount (Rs)" required x-model.number="vpCashAmount" icon="payments" />
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <x-form.input type="number" step="0.01" min="0" name="bank_amount" label="Bank Amount (Rs)" required x-model.number="vpBankAmount" icon="account_balance" />
                        <div>
                            <label class="block mb-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit">Total Payment</label>
                            <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-3.5 py-2.5 text-emerald-600 dark:text-emerald-400 font-jetbrains text-lg font-extrabold flex items-center justify-between min-h-[46px]">
                                <span class="text-xs font-bold uppercase tracking-wider text-emerald-500 font-outfit">Total</span>
                                <span x-text="'Rs ' + (vpCashAmount + vpBankAmount).toLocaleString('en-IN', {minimumFractionDigits: 2})"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Payment Method Selection --}}
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-3">
                            Payment Mode <span class="text-emerald-500 font-bold ml-0.5">*</span>
                        </label>
                        <div class="grid grid-cols-4 gap-2.5">
                            @php 
                                $pmOptions = [
                                    ['value' => 'Cash', 'icon' => 'payments', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50'],
                                    ['value' => 'Bank Transfer', 'icon' => 'account_balance', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50'],
                                    ['value' => 'UPI', 'icon' => 'smartphone', 'color' => 'text-violet-500', 'bg' => 'bg-violet-50'],
                                    ['value' => 'Card', 'icon' => 'credit_card', 'color' => 'text-rose-500', 'bg' => 'bg-rose-50']
                                ]; 
                            @endphp
                            @foreach($pmOptions as $pm)
                            <label class="group relative flex flex-col items-center gap-2 py-4 px-1 rounded-2xl border-2 cursor-pointer transition-all duration-200 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/80 dark:has-[:checked]:bg-emerald-500/12 has-[:checked]:shadow-[0_0_0_1px_rgba(16,185,129,0.15),0_4px_12px_rgba(16,185,129,0.15)] border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600 bg-white/50 dark:bg-zinc-900/50">
                                <input type="radio" name="payment_mode" value="{{ $pm['value'] }}" x-model="vpMode" class="sr-only" required>
                                <div class="w-9 h-9 rounded-full {{ $pm['bg'] }} dark:{{ $pm['bg'] }}/10 flex items-center justify-center {{ $pm['color'] }}">
                                    <span class="material-symbols-rounded text-xl">{{ $pm['icon'] }}</span>
                                </div>
                                <span class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 group-has-[:checked]:text-emerald-700 dark:group-has-[:checked]:text-emerald-300 group-has-[:checked]:font-bold transition-all text-center leading-tight">{{ $pm['value'] }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div x-show="vpBankAmount > 0" x-transition class="w-full">
                            <x-form.select name="bank_transfer_type" label="Bank Transfer Type" x-model="vpBankTransferType" x-bind:required="vpBankAmount > 0">
                                <option value="">Select type...</option>
                                <option value="UPI">UPI</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="NEFT">NEFT</option>
                                <option value="RTGS">RTGS</option>
                                <option value="IMPS">IMPS</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Other">Other</option>
                            </x-form.select>
                        </div>

                        <div x-show="vpBankAmount <= 0" x-transition class="w-full">
                            <x-form.input type="text" name="reference_number" label="Reference No" x-model="vpRefNo" placeholder="UPI ref / Cheque no / Tx ID" icon="description" />
                        </div>
                    </div>

                    <div class="mb-2">
                        <x-form.textarea name="notes" label="Remarks" x-model="vpNotes" rows="2" placeholder="Optional notes" />
                    </div>

                    <x-slot:footer>
                        <x-button type="button" variant="outline" x-on:click="$dispatch('close-modal', 'vendor-payment-modal')">Cancel</x-button>
                        <x-button type="submit" form="vendor-payment-form" variant="primary" icon="check" class="px-8">Record Payment</x-button>
                    </x-slot:footer>
                </form>
            </x-modal>
        </template>

        <template x-teleport="body">
            <x-modal name="lump-sum-payment-modal" title="Record Lump-Sum Payment" subtitle="Allocate a single payment across multiple entries" icon="payments" maxWidth="3xl">
                <form id="lump-sum-form" action="{{ route('billing.day-load.lumpsum-dealer-payment') }}" method="POST" class="space-y-6">
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
                    <div class="grid grid-cols-1">
                        <x-form.select name="dealer_id" label="Select Dealer" x-model="lsDealerId" @change="initLsDealer()">
                            <option value="0">Choose dealer...</option>
                            @foreach($dealers as $dealer)
                                <option value="{{ $dealer->id }}">{{ $dealer->firm_name }}</option>
                            @endforeach
                        </x-form.select>
                    </div>

                    {{-- Allocation Table --}}
                    <template x-if="lsEntries.length > 0">
                        <div class="space-y-3">
                            <p class="text-xs font-bold uppercase tracking-wider text-zinc-400 flex items-center gap-1.5">
                                <span class="material-symbols-rounded text-sm text-zinc-400">playlist_add_check</span>
                                Allocate Payment Across Entries
                            </p>
                            <div class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800">
                                <table class="w-full text-sm">
                                    <thead class="bg-zinc-50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-750">
                                        <tr class="text-xs font-bold text-zinc-450 uppercase">
                                            <th class="px-4 py-3 text-left">Vendor</th>
                                            <th class="px-4 py-3 text-right">Total (Rs)</th>
                                            <th class="px-4 py-3 text-right text-emerald-600">Collected (Rs)</th>
                                            <th class="px-4 py-3 text-right">Balance (Rs)</th>
                                            <th class="px-4 py-3 text-right min-w-[140px]">Allocate Now (Rs)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white dark:bg-zinc-900 divide-y divide-zinc-100 dark:divide-zinc-800/80">
                                        <template x-for="entry in lsEntries" :key="entry.id">
                                            <tr class="hover:bg-zinc-50/65 transition-colors border-t border-zinc-100 dark:border-zinc-800">
                                                <td class="px-4 py-3 font-bold text-xs" x-text="entry.vendor"></td>
                                                <td class="px-4 py-3 text-right font-jetbrains text-xs text-zinc-600 dark:text-zinc-400" x-text="entry.dealer_income.toLocaleString('en-IN', {minimumFractionDigits: 2})"></td>
                                                <td class="px-4 py-3 text-right font-jetbrains text-emerald-600 text-xs" x-text="entry.dealer_collected.toLocaleString('en-IN', {minimumFractionDigits: 2})"></td>
                                                <td class="px-4 py-3 text-right font-jetbrains font-bold text-xs text-zinc-850 dark:text-zinc-150" x-text="entry.due.toLocaleString('en-IN', {minimumFractionDigits: 2})"></td>
                                                <td class="px-4 py-3 text-right">
                                                    <input type="number" step="0.01" min="0" :max="entry.due"
                                                        x-model.number="lsAllocations[entry.id]"
                                                        @input="if (lsAllocations[entry.id] > entry.due) lsAllocations[entry.id] = entry.due; recalcAllocSum()"
                                                        class="w-32 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-right text-xs font-jetbrains focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all font-bold text-emerald-600">
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                    <tfoot class="bg-zinc-50 dark:bg-zinc-850 font-bold border-t border-zinc-200 dark:border-zinc-750">
                                        <tr>
                                            <td class="px-4 py-3 text-xs text-zinc-400 uppercase tracking-wider font-extrabold">TOTAL</td>
                                            <td class="px-4 py-3 text-right font-jetbrains text-xs text-zinc-600 dark:text-zinc-400" x-text="lsEntries.reduce((s, e) => s + e.dealer_income, 0).toLocaleString('en-IN', {minimumFractionDigits: 2})"></td>
                                            <td class="px-4 py-3 text-right font-jetbrains text-emerald-600 text-xs" x-text="lsEntries.reduce((s, e) => s + e.dealer_collected, 0).toLocaleString('en-IN', {minimumFractionDigits: 2})"></td>
                                            <td class="px-4 py-3 text-right font-jetbrains text-xs text-zinc-800 dark:text-zinc-200" x-text="lsEntries.reduce((s, e) => s + e.due, 0).toLocaleString('en-IN', {minimumFractionDigits: 2})"></td>
                                            <td class="px-4 py-3 text-right font-jetbrains text-emerald-600 text-xs" x-text="lsAllocSum.toLocaleString('en-IN', {minimumFractionDigits: 2})"></td>
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
                    <div>
                        <h4 class="text-xs font-bold text-zinc-400 uppercase tracking-wider mb-3">Payment Details</h4>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <x-form.input type="date" name="date" label="Payment Date" x-model="lsDate" icon="calendar_month" />
                            <x-form.input type="number" step="0.01" min="0" label="Cash Amount (Rs)" x-model.number="lsCashAmount" @input="lsTotalLump = Math.round((lsCashAmount + lsBankAmount) * 100) / 100" icon="payments" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-5">
                            <x-form.input type="number" step="0.01" min="0" label="Bank Amount (Rs)" x-model.number="lsBankAmount" @input="lsTotalLump = Math.round((lsCashAmount + lsBankAmount) * 100) / 100" icon="account_balance" />
                            <div>
                                <label class="block mb-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit">Total Lump Sum</label>
                                <div class="rounded-xl border border-emerald-500/20 bg-emerald-500/10 px-3.5 py-2.5 text-emerald-600 dark:text-emerald-400 font-jetbrains text-lg font-extrabold flex items-center justify-between min-h-[46px]">
                                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-500 font-outfit">Total</span>
                                    <span x-text="'Rs ' + lsTotalLump.toLocaleString('en-IN', {minimumFractionDigits: 2})"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Payment Method Selection --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-3">
                                Payment Mode <span class="text-emerald-500 font-bold ml-0.5">*</span>
                            </label>
                            <div class="grid grid-cols-4 gap-2.5">
                                @php 
                                    $pmOptions = [
                                        ['value' => 'Cash', 'icon' => 'payments', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50'],
                                        ['value' => 'Bank Transfer', 'icon' => 'account_balance', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50'],
                                        ['value' => 'UPI', 'icon' => 'smartphone', 'color' => 'text-violet-500', 'bg' => 'bg-violet-50'],
                                        ['value' => 'Card', 'icon' => 'credit_card', 'color' => 'text-rose-500', 'bg' => 'bg-rose-50']
                                    ]; 
                                @endphp
                                @foreach($pmOptions as $pm)
                                <label class="group relative flex flex-col items-center gap-2 py-4 px-1 rounded-2xl border-2 cursor-pointer transition-all duration-200 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/80 dark:has-[:checked]:bg-emerald-500/12 has-[:checked]:shadow-[0_0_0_1px_rgba(16,185,129,0.15),0_4px_12px_rgba(16,185,129,0.15)] border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600 bg-white/50 dark:bg-zinc-900/50">
                                    <input type="radio" name="payment_mode_ls" value="{{ $pm['value'] }}" x-model="lsMode" class="sr-only" required>
                                    <div class="w-9 h-9 rounded-full {{ $pm['bg'] }} dark:{{ $pm['bg'] }}/10 flex items-center justify-center {{ $pm['color'] }}">
                                        <span class="material-symbols-rounded text-xl">{{ $pm['icon'] }}</span>
                                    </div>
                                    <span class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 group-has-[:checked]:text-emerald-700 dark:group-has-[:checked]:text-emerald-300 group-has-[:checked]:font-bold transition-all text-center leading-tight">{{ $pm['value'] }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div x-show="lsBankAmount > 0" x-transition class="w-full">
                                <x-form.select name="bank_transfer_type" label="Bank Transfer Type" x-model="lsBankTransferType">
                                    <option value="">Select type...</option>
                                    <option value="UPI">UPI</option>
                                    <option value="Bank Transfer">Bank Transfer</option>
                                    <option value="NEFT">NEFT</option>
                                    <option value="RTGS">RTGS</option>
                                    <option value="IMPS">IMPS</option>
                                    <option value="Cheque">Cheque</option>
                                    <option value="Other">Other</option>
                                </x-form.select>
                            </div>

                            <div x-show="lsBankAmount <= 0" x-transition class="w-full">
                                <x-form.input type="text" name="reference_number" label="Reference No" x-model="lsRefNo" placeholder="UPI ref / Cheque no / Tx ID" icon="description" />
                            </div>
                        </div>

                        <div class="mt-5 mb-2">
                            <x-form.textarea name="notes" label="Remarks" x-model="lsNotes" rows="2" placeholder="Optional notes" />
                        </div>
                    </div>

                    <x-slot:footer>
                        <x-button type="button" variant="outline" x-on:click="$dispatch('close-modal', 'lump-sum-payment-modal')">Cancel</x-button>
                        <x-button type="submit" form="lump-sum-form" variant="primary" icon="check" x-bind:disabled="lsAllocSum > lsTotalLump || lsAllocSum <= 0" class="px-8">Record Lump-Sum Payment</x-button>
                    </x-slot:footer>
                </form>
            </x-modal>
        </template>
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
