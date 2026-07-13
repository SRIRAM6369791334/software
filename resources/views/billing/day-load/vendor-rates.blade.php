@extends('layouts.app')
@section('title', 'Set Vendor Final Rates')

@section('content')
<div class="animate-fade-in" x-data="vendorRatesApp()">
    <x-page-header title="Set Vendor Final Rates" subtitle="Update billing_rate for all entries of a vendor grouped by billing date">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('billing.day-load.index', ['date' => request('date', today()->format('Y-m-d'))]) }}" icon="arrow_back">
                Back to Day Load
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if (session('update_summary'))
        @php $summary = session('update_summary'); @endphp
        <x-card class="mb-6 border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20">
            <div class="flex items-start gap-3">
                <span class="material-symbols-rounded text-emerald-600 text-2xl mt-0.5">check_circle</span>
                <div>
                    <h3 class="font-bold text-emerald-800 dark:text-emerald-200">Vendor Final Rates Updated</h3>
                    <div class="mt-2 text-sm text-emerald-700 dark:text-emerald-300 space-y-1">
                        <p>Updated {{ $summary['dates_updated'] }} date(s) — {{ $summary['entries_updated'] }} entries</p>
                        <p>Vendor Cost: ₹{{ number_format($summary['cost_before'], 0) }} → ₹{{ number_format($summary['cost_after'], 0) }}
                            <span class="font-bold {{ $summary['difference'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                ({{ $summary['difference'] >= 0 ? '−' : '+' }}₹{{ number_format(abs($summary['difference']), 0) }})</span>
                        </p>
                        @if ($summary['status_changes']['Overpaid'] > 0)
                            <p class="text-amber-600 dark:text-amber-400">⚠ {{ $summary['status_changes']['Overpaid'] }} entry(ies) became Overpaid</p>
                        @endif
                        @if ($summary['status_changes']['Pending'] > 0)
                            <p>📄 {{ $summary['status_changes']['Pending'] }} entry(ies) → Pending</p>
                        @endif
                        @if ($summary['status_changes']['Unchanged'] > 0)
                            <p>{{ $summary['status_changes']['Unchanged'] }} entry(ies) — status unchanged</p>
                        @endif
                    </div>
                </div>
            </div>
        </x-card>
    @endif

    <x-card class="mb-6">
        <form method="GET" action="{{ route('billing.day-load.vendor-rates') }}">
            <div class="flex items-end gap-4">
                <div class="flex-1">
                    <x-form.select name="vendor_id" label="Select Vendor" required>
                        <option value="">Choose vendor...</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ (int) $selectedVendorId === $vendor->id ? 'selected' : '' }}>
                                {{ $vendor->firm_name }}
                            </option>
                        @endforeach
                    </x-form.select>
                </div>
                <x-button type="submit" variant="primary" icon="search">Load Dates</x-button>
            </div>
        </form>
    </x-card>

    @if($groupedEntries->isNotEmpty())
        <form method="POST" action="{{ route('billing.day-load.set-vendor-rates') }}" @submit.prevent="confirmAndSubmit($event)">
            @csrf
            <input type="hidden" name="vendor_id" value="{{ $selectedVendorId }}">

            <x-card>
                <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-4">
                    <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">
                        Entries for {{ $vendors->firstWhere('id', $selectedVendorId)?->firm_name ?? 'Vendor' }}
                    </h2>
                    <p class="text-xs text-zinc-500 mt-1">{{ $financialSummary['total_entries'] }} entries · {{ number_format($financialSummary['total_weight'], 2) }} kg total bird weight</p>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-zinc-200 dark:border-zinc-800 text-xs font-bold uppercase text-zinc-500">
                                <th class="px-4 py-3 text-left">Billing Date</th>
                                <th class="px-4 py-3 text-center">Entries</th>
                                <th class="px-4 py-3 text-right">Bird Weight</th>
                                <th class="px-4 py-3 text-right">Paper Rate</th>
                                <th class="px-4 py-3 text-right">Current Final Rate</th>
                                <th class="px-4 py-3 text-right">New Final Rate (₹/kg)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($groupedEntries as $date => $group)
                                <tr class="border-b border-zinc-100 dark:border-zinc-800/50 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                    <td class="px-4 py-3 font-medium text-zinc-900 dark:text-zinc-100">
                                        {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                                        <span class="text-zinc-400 text-[10px] block">{{ \Carbon\Carbon::parse($date)->format('l') }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-jetbrains font-bold">{{ $group['count'] }}</td>
                                    <td class="px-4 py-3 text-right font-jetbrains">{{ number_format($group['total_weight'], 2) }} kg</td>
                                    <td class="px-4 py-3 text-right font-jetbrains">₹{{ number_format($group['paper_rate'], 2) }}</td>
                                    <td class="px-4 py-3 text-right font-jetbrains">
                                        @if($group['current_rate'] > 0)
                                            <span class="text-zinc-500">₹{{ number_format($group['current_rate'], 2) }}</span>
                                        @else
                                            <span class="text-zinc-400 italic">Not set</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <input type="number" step="0.01" min="0"
                                               name="rates[{{ $date }}]"
                                               value="{{ old("rates.{$date}", $group['current_rate'] > 0 ? number_format($group['current_rate'], 2, '.', '') : '') }}"
                                               placeholder="0.00"
                                               @input.debounce="recalc()"
                                               class="w-28 text-right rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains font-bold focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-4">
                        <p class="text-xs font-bold uppercase text-zinc-500">Current Vendor Cost</p>
                        <p class="mt-1 font-jetbrains text-2xl font-black text-zinc-800 dark:text-zinc-100" x-text="'₹' + formatNumber(currentCost)"></p>
                    </div>
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-4">
                        <p class="text-xs font-bold uppercase text-zinc-500">New Vendor Cost</p>
                        <p class="mt-1 font-jetbrains text-2xl font-black" :class="newCostDiff >= 0 ? 'text-emerald-600' : 'text-rose-600'" x-text="'₹' + formatNumber(newCost)"></p>
                    </div>
                    <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-4">
                        <p class="text-xs font-bold uppercase text-zinc-500">Difference</p>
                        <p class="mt-1 font-jetbrains text-2xl font-black" :class="newCostDiff >= 0 ? 'text-emerald-600' : 'text-rose-600'">
                            <span x-text="newCostDiff >= 0 ? '−' : '+'"></span>₹<span x-text="formatNumber(Math.abs(newCostDiff))"></span>
                        </p>
                    </div>
                </div>

                <div class="mt-6">
                    <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Reason for update</label>
                    <textarea name="reason" rows="2" required placeholder="e.g. Vendor A called — final rates confirmed for 01-07 to 05-07"
                              class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-4 py-3 text-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all"></textarea>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <x-button variant="outline" href="{{ route('billing.day-load.index') }}" icon="cancel">Cancel</x-button>
                    <x-button type="submit" variant="primary" icon="save" x-bind:disabled="!hasChanges">
                        Preview & Confirm
                    </x-button>
                </div>
            </x-card>

            {{-- Confirmation Modal --}}
            <div x-show="showConfirm" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
                 x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100">
                <div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-2xl max-w-3xl w-full mx-4 max-h-[90vh] overflow-y-auto"
                     @click.away="showConfirm = false">
                    <div class="p-6 border-b border-zinc-200 dark:border-zinc-800">
                        <h3 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">Confirm Final Rate Update</h3>
                        <p class="text-sm text-zinc-500 mt-1">Vendor: {{ $vendors->firstWhere('id', $selectedVendorId)?->firm_name ?? '' }}</p>
                    </div>

                    <div class="p-6 space-y-6">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-xs font-bold uppercase text-zinc-500 border-b border-zinc-200 dark:border-zinc-800">
                                    <th class="px-3 py-2 text-left">Date</th>
                                    <th class="px-3 py-2 text-center">Entries</th>
                                    <th class="px-3 py-2 text-right">Old Final Rate</th>
                                    <th class="px-3 py-2 text-center"></th>
                                    <th class="px-3 py-2 text-right">New Final Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, idx) in confirmRows" :key="row.date">
                                    <tr class="border-b border-zinc-100 dark:border-zinc-800/50">
                                        <td class="px-3 py-2 font-medium" x-text="row.date"></td>
                                        <td class="px-3 py-2 text-center font-jetbrains" x-text="row.count"></td>
                                        <td class="px-3 py-2 text-right font-jetbrains" x-text="'₹' + row.oldRate.toFixed(2)"></td>
                                        <td class="px-3 py-2 text-center text-zinc-400">→</td>
                                        <td class="px-3 py-2 text-right font-jetbrains font-bold" x-text="'₹' + row.newRate.toFixed(2)" :class="row.changed ? 'text-emerald-600' : ''"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <div class="rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50 p-4">
                            <h4 class="text-xs font-bold uppercase text-zinc-500 mb-3">Financial Impact</h4>
                            <div class="grid grid-cols-3 gap-4 text-sm">
                                <div>
                                    <p class="text-zinc-500">Current Cost</p>
                                    <p class="font-jetbrains font-bold mt-1" x-text="'₹' + formatNumber(currentCost)"></p>
                                </div>
                                <div>
                                    <p class="text-zinc-500">New Cost</p>
                                    <p class="font-jetbrains font-bold mt-1" :class="newCostDiff >= 0 ? 'text-emerald-600' : 'text-rose-600'" x-text="'₹' + formatNumber(newCost)"></p>
                                </div>
                                <div>
                                    <p class="text-zinc-500">Difference</p>
                                    <p class="font-jetbrains font-bold mt-1" :class="newCostDiff >= 0 ? 'text-emerald-600' : 'text-rose-600'">
                                        <span x-text="newCostDiff >= 0 ? '−' : '+'"></span>₹<span x-text="formatNumber(Math.abs(newCostDiff))"></span>
                                    </p>
                                </div>
                            </div>
                            <p x-show="overpaidCount > 0" class="mt-3 text-xs text-amber-600 bg-amber-50 dark:bg-amber-900/20 rounded-lg px-3 py-2">
                                ⚠ <span x-text="overpaidCount"></span> entry(ies) will become Overpaid
                            </p>
                        </div>

                        <p class="text-sm text-zinc-500 bg-zinc-50 dark:bg-zinc-900/50 rounded-xl px-4 py-3">
                            Reason: <span class="font-medium text-zinc-700 dark:text-zinc-300" x-text="confirmReason"></span>
                        </p>
                    </div>

                    <div class="p-6 border-t border-zinc-200 dark:border-zinc-800 flex justify-end gap-3">
                        <x-button variant="outline" @click="showConfirm = false" icon="close">Cancel</x-button>
                        <x-button variant="primary" @click="doSubmit()" icon="check">Confirm & Update</x-button>
                    </div>
                </div>
            </div>
        </form>
    @elseif($selectedVendorId)
        <x-card>
            <div class="text-center py-8">
                <span class="material-symbols-rounded text-4xl text-zinc-300">info</span>
                <p class="mt-3 text-zinc-500">No active entries found for this vendor. All entries may already have final rates or belong to locked batches.</p>
            </div>
        </x-card>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('vendorRatesApp', () => ({
        showConfirm: false,
        confirmRows: [],
        confirmReason: '',
        currentCost: {{ $financialSummary['current_vendor_cost'] ?? 0 }},
        newCost: {{ $financialSummary['current_vendor_cost'] ?? 0 }},
        newCostDiff: 0,
        overpaidCount: 0,
        hasChanges: true,

        recalc() {
            const form = document.querySelector('form');
            if (!form) return;

            const rates = {};
            let newTotal = 0;
            let overpaid = 0;

            @if($groupedEntries->isNotEmpty())
                @foreach($groupedEntries as $date => $group)
                    (() => {
                        const input = form.querySelector('input[name="rates[{{ $date }}]"]');
                        if (!input) return;
                        const val = parseFloat(input.value) || 0;
                        rates['{{ $date }}'] = val;
                        @foreach($group['batch_ids'] as $bid)
                            // Approximate: weight × rate per date group
                        @endforeach
                        // simplified: use total_weight × rate
                        newTotal += {{ $group['total_weight'] }} * val;
                    })();
                @endforeach
            @endif

            // More accurate would be per-entry, but for preview we estimate
            // In practice the controller computes exact values
            this.newCost = Math.round(newTotal * 100) / 100;
            this.newCostDiff = this.currentCost - this.newCost;
        },

        confirmAndSubmit(event) {
            const form = event.target;
            const reason = form.querySelector('textarea[name="reason"]')?.value || '';

            if (!reason.trim()) {
                alert('Please enter a reason for the update.');
                return;
            }

            const rows = [];
            let newTotal = 0;
            let overpaid = 0;
            let hasAnyChange = false;

            @if($groupedEntries->isNotEmpty())
                @foreach($groupedEntries as $date => $group)
                    (() => {
                        const input = form.querySelector('input[name="rates[{{ $date }}]"]');
                        if (!input) return;
                        const val = parseFloat(input.value) || 0;
                        const oldRate = {{ $group['current_rate'] }};
                        const changed = val !== oldRate;
                        if (changed && val > 0) hasAnyChange = true;
                        rows.push({
                            date: '{{ \Carbon\Carbon::parse($date)->format('d M Y') }}',
                            count: {{ $group['count'] }},
                            oldRate: oldRate,
                            newRate: val,
                            changed: changed,
                        });
                        newTotal += {{ $group['total_weight'] }} * val;
                    })();
                @endforeach
            @endif

            if (!hasAnyChange) {
                alert('No rates have been changed. Adjust at least one rate before saving.');
                return;
            }

            this.confirmRows = rows;
            this.confirmReason = reason;
            this.newCost = Math.round(newTotal * 100) / 100;
            this.newCostDiff = Math.round((this.currentCost - this.newCost) * 100) / 100;
            this.showConfirm = true;
        },

        doSubmit() {
            this.showConfirm = false;
            const form = document.querySelector('form');
            if (form) form.submit();
        },

        formatNumber(num) {
            return Number(num).toLocaleString('en-IN', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        },
    }));
});
</script>
@endpush
