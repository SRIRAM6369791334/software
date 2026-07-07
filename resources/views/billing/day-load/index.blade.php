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
            <x-button variant="outline" href="{{ route('billing.weekly.index') }}" icon="receipt_long">
                Weekly Billing
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-7 gap-4 mb-8">
        <x-stat-card label="Billing Date" value="{{ \Carbon\Carbon::parse($date)->format('d M Y') }}" icon="calendar_today" color="blue" />
        <x-stat-card label="Day" value="{{ \Carbon\Carbon::parse($date)->format('l') }}" icon="event" color="emerald" />
        <x-stat-card label="Total Boxes" value="{{ number_format((float) ($batch?->total_boxes ?? 0), 0) }}" icon="inventory_2" color="amber" />
        <x-stat-card label="Bird Weight" value="{{ number_format((float) ($batch?->total_bird_weight ?? 0), 2) }} kg" icon="scale" color="indigo" />
        <x-stat-card label="Dealer Income" value="Rs {{ number_format($totalDealerIncome, 0) }}" icon="payments" color="teal" />
        <x-stat-card label="Vendor Cost" value="Rs {{ number_format($totalVendorCost, 0) }}" icon="shopping_cart" color="rose" />
        <x-stat-card label="Gross Margin" value="Rs {{ number_format($grossMargin, 0) }}" icon="trending_up" color="{{ $grossMargin >= 0 ? 'emerald' : 'red' }}" />
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
        <x-stat-card label="Dealer Collected" value="Rs {{ number_format($totalDealerCollected, 0) }}" icon="account_balance" color="emerald" />
        <x-stat-card label="Vendor Paid" value="Rs {{ number_format($totalVendorPaid, 0) }}" icon="payments" color="violet" />
        <x-stat-card label="Dealer Due" value="Rs {{ number_format($totalDealerDue, 0) }}" icon="pending" color="{{ $totalDealerDue > 0 ? 'amber' : 'emerald' }}" />
        <x-stat-card label="Vendor Due" value="Rs {{ number_format($totalVendorDue, 0) }}" icon="pending_actions" color="{{ $totalVendorDue > 0 ? 'amber' : 'emerald' }}" />
        <x-stat-card label="Collection %" value="{{ $collectionPct }}%" icon="pie_chart" color="indigo" />
    </div>

    @can('create bills')
    <x-card class="mb-8">
        <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
            <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">New Load Entry</h2>
        </div>

        <form action="{{ route('billing.day-load.store') }}" method="POST" x-data="{ paperRate: 0, customerRate: 0 }">
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
                <x-form.input type="number" step="0.01" name="billing_rate" label="Billing Rate / Vendor Rate" required />
                <x-form.input type="number" step="0.01" name="customer_rate" label="Customer Rate" required x-model.number="customerRate" />
                <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 p-4">
                    <p class="text-xs font-bold uppercase text-zinc-500">Customer vs Paper</p>
                    <p class="mt-2 font-jetbrains text-2xl font-black" :class="(customerRate - paperRate) >= 0 ? 'text-emerald-600' : 'text-rose-600'">
                        <span x-text="(customerRate - paperRate) >= 0 ? '+' : '-'"></span>Rs <span x-text="Math.abs(customerRate - paperRate).toFixed(2)"></span>
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
                    @endif
                @endcan
            </div>
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="date" name="date" value="{{ $date }}" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search vendor or dealer" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <x-button type="submit" variant="outline" icon="filter_alt">Filter</x-button>
            </form>
        </div>

        <x-data-table :headers="['Date', 'Vendor', 'Dealer', 'Rates', 'Margin', 'Boxes', 'Weights', 'Dealer Payment', 'Vendor Payment', 'Status', 'Actions']">
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
                        <div>Vendor: <span class="font-jetbrains">Rs {{ number_format((float) $entry->billing_rate, 2) }}</span></div>
                        <div>Customer: <span class="font-jetbrains">Rs {{ number_format((float) $entry->customer_rate, 2) }}</span></div>
                    </td>
                    <td class="px-6 py-4">
                        @php($diff = $entry->rate_difference)
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
                                @if($entry->no_of_boxes > 0)
                                <button
                                    type="button"
                                    x-on:click="
                                        $dispatch('open-modal', 'transfer-boxes-modal');
                                        $nextTick(() => {
                                            transferSourceId = {{ $entry->id }};
                                            transferSourceBoxes = {{ $entry->no_of_boxes }};
                                            transferSourceVendor = '{{ $entry->vendor->firm_name ?? '-' }}';
                                            transferSourceDealer = '{{ $entry->dealer->firm_name ?? '-' }}';
                                            transferBatchId = {{ $entry->batch_id }};
                                            transferDate = '{{ $entry->batch->billing_date->format('d M Y') }}';
                                            transferMaxBoxes = {{ $entry->no_of_boxes }};
                                            transferBoxes = {{ $entry->no_of_boxes }};
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
                                            dpAmount = {{ round($entry->dealer_income - (float) $entry->dealer_collected, 2) }};
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
                                            vpAmount = {{ round($entry->vendor_cost - (float) $entry->vendor_paid, 2) }};
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

    <div x-data="{
        transferSourceId: 0,
        transferSourceBoxes: 0,
        transferSourceVendor: '',
        transferSourceDealer: '',
        transferBatchId: 0,
        transferDate: '',
        transferMaxBoxes: 0,
        transferBoxes: 0,
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
        dpAmount: 0,
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
        vpAmount: 0,
        vpDate: '{{ $date }}',
        vpMode: 'Cash',
        vpRefNo: '',
        vpNotes: '',
    }">
        <x-modal name="edit-entry-modal" title="Edit Entry" subtitle="Adjust rates, weights, or box count" icon="edit" maxWidth="2xl">
            <form id="edit-entry-form" :action="editFormAction" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PUT">

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Vendor</label>
                        <select name="vendor_id" required x-model="editVendorId" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                            <option value="">Select vendor...</option>
                            @foreach($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->firm_name }}{{ $vendor->is_shop ? ' (Shop)' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Dealer</label>
                        <select name="dealer_id" required x-model="editDealerId" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                            <option value="">Select dealer...</option>
                            @foreach($dealers as $dealer)
                                <option value="{{ $dealer->id }}">{{ $dealer->firm_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Boxes</label>
                        <input type="number" name="no_of_boxes" min="1" x-model.number="editNoOfBoxes" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Paper Rate</label>
                        <input type="number" step="0.01" name="paper_rate" min="0" x-model.number="editPaperRate" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Billing Rate</label>
                        <input type="number" step="0.01" name="billing_rate" min="0" x-model.number="editBillingRate" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Customer Rate</label>
                        <input type="number" step="0.01" name="customer_rate" min="0" x-model.number="editCustomerRate" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Box Weight</label>
                        <input type="number" step="0.01" name="box_weight" min="0" x-model.number="editBoxWeight" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Empty Weight</label>
                        <input type="number" step="0.01" name="empty_weight" min="0" x-model.number="editEmptyWeight" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Farm Weight</label>
                        <input type="number" step="0.01" name="farm_weight" min="0" x-model="editFarmWeight" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Remarks</label>
                        <input type="text" name="remarks" x-model="editRemarks" placeholder="Optional" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Reason for Edit</label>
                        <input type="text" name="reason" required placeholder="Why are you editing this entry?" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                    </div>
                </div>

                <x-slot:footer>
                    <x-button type="button" variant="outline" x-on:click="$dispatch('close-modal', 'edit-entry-modal')">Cancel</x-button>
                    <x-button type="submit" form="edit-entry-form" variant="primary" icon="save">Save Changes</x-button>
                </x-slot:footer>
            </form>
        </x-modal>

        <x-modal name="transfer-boxes-modal" title="Transfer Boxes" subtitle="Move boxes from one dealer to another" icon="swap_horiz" maxWidth="lg">
            <form id="transfer-form" :action="transferFormAction" method="POST">
                @csrf

                <div class="mb-5 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700">
                    <p class="text-xs font-bold uppercase text-zinc-500 mb-3">Source Entry</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                        <div>
                            <span class="text-zinc-500">Vendor:</span>
                            <p class="font-bold text-zinc-900 dark:text-zinc-100" x-text="transferSourceVendor"></p>
                        </div>
                        <div>
                            <span class="text-zinc-500">Dealer:</span>
                            <p class="font-bold text-zinc-900 dark:text-zinc-100" x-text="transferSourceDealer"></p>
                        </div>
                        <div>
                            <span class="text-zinc-500">Available Boxes:</span>
                            <p class="font-jetbrains font-bold text-lg text-zinc-900 dark:text-zinc-100" x-text="transferSourceBoxes"></p>
                        </div>
                        <div>
                            <span class="text-zinc-500">Date:</span>
                            <p class="font-bold text-zinc-900 dark:text-zinc-100" x-text="transferDate"></p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Boxes to Transfer</label>
                        <input
                            type="number"
                            name="transfer_boxes"
                            min="1"
                            :max="transferMaxBoxes"
                            x-model.number="transferBoxes"
                            required
                            class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains"
                        >
                        <p class="mt-1 text-xs text-zinc-500">
                            Remaining: <span class="font-bold" x-text="transferSourceBoxes - transferBoxes"></span> boxes
                        </p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Target Dealer</label>
                        <select name="target_dealer_id" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
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
                        <select name="target_vendor_id" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
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
                            placeholder="e.g. Reassign boxes to correct dealer"
                            class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm"
                        >
                    </div>
                </div>

                <x-slot:footer>
                    <x-button type="button" variant="outline" x-on:click="$dispatch('close-modal', 'transfer-boxes-modal')">Cancel</x-button>
                    <x-button type="submit" form="transfer-form" variant="primary" icon="swap_horiz">Transfer Boxes</x-button>
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

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 p-4">
                        <p class="text-xs font-bold uppercase text-zinc-500 mb-1">Total Bird Weight</p>
                        <p class="font-jetbrains text-2xl font-black text-indigo-600 dark:text-indigo-400" x-text="totalBirdWeight.toFixed(2)"></p>
                    </div>
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/50 p-4">
                        <p class="text-xs font-bold uppercase text-zinc-500 mb-1">Total Loss</p>
                        <p class="font-jetbrains text-2xl font-black" :class="parseFloat(totalLoss) >= 0 ? 'text-rose-600' : 'text-emerald-600'" x-text="totalFarmWeight ? totalLoss : '—'"></p>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Enter Total Farm Weight</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            name="total_farm_weight"
                            x-model="totalFarmWeight"
                            placeholder="0.00"
                            required
                            class="w-full rounded-xl border-2 border-emerald-300 dark:border-emerald-600 bg-white dark:bg-zinc-900 px-4 py-3 text-lg font-jetbrains focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                        >
                    </div>
                </div>

                <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700 max-h-[40vh] overflow-y-auto mb-4">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-zinc-50 dark:bg-zinc-800 text-[11px] font-bold uppercase tracking-wider text-zinc-500">
                                <th class="px-4 py-3 text-left">Vendor</th>
                                <th class="px-4 py-3 text-left">Dealer</th>
                                <th class="px-4 py-3 text-center">Boxes</th>
                                <th class="px-4 py-3 text-center">Bird Wt</th>
                                <th class="px-4 py-3 text-center">Farm Wt</th>
                                <th class="px-4 py-3 text-center">Loss</th>
                                <th class="px-4 py-3 text-center">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            <template x-for="(entry, index) in entries" :key="entry.id">
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/50 transition-colors">
                                    <td class="px-4 py-3 font-bold text-zinc-900 dark:text-zinc-100 text-xs" x-text="entry.vendor"></td>
                                    <td class="px-4 py-3 text-zinc-600 dark:text-zinc-400 text-xs" x-text="entry.dealer"></td>
                                    <td class="px-4 py-3 text-center font-jetbrains font-bold text-xs" x-text="entry.boxes"></td>
                                    <td class="px-4 py-3 text-center font-jetbrains text-xs" x-text="entry.birdWeight.toFixed(2)"></td>
                                    <td class="px-4 py-3 text-center font-jetbrains text-xs font-bold text-emerald-600"
                                        x-text="totalFarmWeight ? (totalFarmWeight * entry.proportion).toFixed(2) : '—'"></td>
                                    <td class="px-4 py-3 text-center font-jetbrains text-xs font-bold text-rose-600"
                                        x-text="totalFarmWeight ? (entry.birdWeight - (totalFarmWeight * entry.proportion)).toFixed(2) : '—'"></td>
                                    <td class="px-4 py-3 text-center font-jetbrains text-xs font-bold text-zinc-900 dark:text-zinc-100"
                                        x-text="totalFarmWeight ? (entry.birdWeight - (totalFarmWeight * entry.proportion)).toFixed(2) : '—'"></td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <div class="mb-4">
                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Reason</label>
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

                <div class="overflow-x-auto rounded-xl border border-zinc-200 dark:border-zinc-700 max-h-[55vh] overflow-y-auto">
                    <table class="w-full text-sm">
                        <thead class="sticky top-0 z-10">
                            <tr class="bg-zinc-50 dark:bg-zinc-800 text-[11px] font-bold uppercase tracking-wider text-zinc-500">
                                <th class="px-4 py-3 text-left">Vendor</th>
                                <th class="px-4 py-3 text-left">Dealer</th>
                                <th class="px-4 py-3 text-center">Boxes</th>
                                <th class="px-4 py-3 text-center">Bird Wt</th>
                                <th class="px-4 py-3 text-center min-w-[120px]">Farm Weight</th>
                                <th class="px-4 py-3 text-center">Loss</th>
                                <th class="px-4 py-3 text-center">Total</th>
                                <th class="px-4 py-3 text-left min-w-[140px]">Remarks</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach($entries as $entry)
                                @if($entry->status === 'Active')
                                <tr class="hover:bg-zinc-50/80 dark:hover:bg-zinc-800/50 transition-colors"
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
                                        <p class="text-zinc-600 dark:text-zinc-400 text-xs">{{ $entry->dealer->firm_name ?? '-' }}</p>
                                    </td>
                                    <td class="px-4 py-3 text-center font-jetbrains font-bold text-xs">{{ $entry->no_of_boxes }}</td>
                                    <td class="px-4 py-3 text-center font-jetbrains text-xs" x-text="birdWeight.toFixed(2)"></td>
                                    <td class="px-4 py-3">
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            name="entries[{{ $entry->id }}][farm_weight]"
                                            x-model="farmWeight"
                                            placeholder="0.00"
                                            class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-1.5 text-xs font-jetbrains text-center focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                        >
                                    </td>
                                    <td class="px-4 py-3 text-center font-jetbrains text-xs font-bold"
                                        :class="farmWeight !== '' ? 'text-rose-600' : 'text-zinc-400'"
                                        x-text="farmWeight !== '' ? (birdWeight - parseFloat(farmWeight || 0)).toFixed(2) : '-'">
                                    </td>
                                    <td class="px-4 py-3 text-center font-jetbrains text-xs font-bold"
                                        :class="farmWeight !== '' ? 'text-emerald-600' : 'text-zinc-400'"
                                        x-text="farmWeight !== '' ? (birdWeight - parseFloat(farmWeight || 0)).toFixed(2) : '-'">
                                    </td>
                                    <td class="px-4 py-3">
                                        <input
                                            type="text"
                                            name="entries[{{ $entry->id }}][remarks]"
                                            value="{{ $entry->remarks ?? '' }}"
                                            placeholder="Optional"
                                            class="w-full rounded-lg border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-1.5 text-xs focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"
                                        >
                                    </td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-5 p-4 rounded-xl bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700">
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
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Amount (Rs)</label>
                        <input type="number" step="0.01" min="0.01" name="amount" required x-model.number="dpAmount" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains text-lg font-bold">
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
                    <div>
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
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Amount (Rs)</label>
                        <input type="number" step="0.01" min="0.01" name="amount" required x-model.number="vpAmount" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains text-lg font-bold">
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
                    <div>
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
    </div>
</div>
@endsection
