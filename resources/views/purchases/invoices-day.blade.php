@extends('layouts.app')
@section('title', 'Records for ' . \Carbon\Carbon::parse($date)->format('d M Y'))

@section('content')
<div class="animate-fade-in">
    <x-page-header title="{{ \Carbon\Carbon::parse($date)->format('d M Y') }}" subtitle="{{ \Carbon\Carbon::parse($date)->format('l') }}">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('purchases.invoices') }}" icon="arrow_back">
                Back to All Dates
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Day Stats --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <x-stat-card
            label="Day-Load Batches"
            value="{{ $dayStats['dayload_count'] }}"
            icon="local_shipping"
            color="blue" />
        <x-stat-card
            label="Birds Loaded"
            value="{{ number_format($dayStats['dayload_boxes']) }} boxes"
            icon="inventory_2"
            color="indigo" />
        <x-stat-card
            label="Purchases"
            value="{{ $dayStats['purchase_count'] }}"
            icon="receipt_long"
            color="emerald" />
        <x-stat-card
            label="Purchase Total"
            value="Rs {{ number_format($dayStats['purchase_total'], 2) }}"
            icon="payments"
            color="amber" />
    </div>

    {{-- Day-Load Entries Section --}}
    @if($dayLoadBatch && $dayLoadBatch->entries->count() > 0)
    <x-card class="mb-8">
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-rounded text-blue-600 dark:text-blue-400">local_shipping</span>
                <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">Day-Load Entries</h2>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                    {{ $dayLoadBatch->entries->count() }} entries
                </span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 border-b border-zinc-200/50 dark:border-zinc-800/50">
                        <th class="px-6 py-3 text-left">Vendor</th>
                        <th class="px-6 py-3 text-left">Dealer</th>
                        <th class="px-6 py-3 text-center">Boxes</th>
                        <th class="px-6 py-3 text-center">Bird Wt</th>
                        <th class="px-6 py-3 text-center">Farm Wt</th>
                        <th class="px-6 py-3 text-center">Loss</th>
                        <th class="px-6 py-3 text-center">Total</th>
                        <th class="px-6 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($dayLoadBatch->entries as $entry)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-bold text-zinc-900 dark:text-zinc-100 text-xs">{{ $entry->vendor->firm_name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-zinc-600 dark:text-zinc-400 text-xs">{{ $entry->dealer->firm_name ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4 text-center font-jetbrains font-bold text-xs">{{ $entry->no_of_boxes }}</td>
                            <td class="px-6 py-4 text-center font-jetbrains text-xs">{{ number_format((float) $entry->bird_weight, 2) }}</td>
                            <td class="px-6 py-4 text-center font-jetbrains text-xs">{{ $entry->farm_weight ? number_format((float) $entry->farm_weight, 2) : '—' }}</td>
                            <td class="px-6 py-4 text-center font-jetbrains text-xs {{ $entry->loss_weight ? 'text-rose-600 font-bold' : 'text-zinc-400' }}">
                                {{ $entry->loss_weight ? number_format((float) $entry->loss_weight, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-center font-jetbrains text-xs {{ $entry->total_weight ? 'text-emerald-600 font-bold' : 'text-zinc-400' }}">
                                {{ $entry->total_weight ? number_format((float) $entry->total_weight, 2) : '—' }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <x-badge variant="success">{{ $entry->status }}</x-badge>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t-2 border-zinc-200 dark:border-zinc-700">
                    <tr class="font-bold text-xs">
                        <td class="px-6 py-3 text-zinc-500" colspan="2">Totals</td>
                        <td class="px-6 py-3 text-center font-jetbrains text-blue-600 dark:text-blue-400">{{ $dayLoadBatch->total_boxes }}</td>
                        <td class="px-6 py-3 text-center font-jetbrains">{{ number_format((float) $dayLoadBatch->total_bird_weight, 2) }}</td>
                        <td class="px-6 py-3 text-center font-jetbrains">{{ number_format((float) $dayLoadBatch->total_farm_weight, 2) }}</td>
                        <td class="px-6 py-3 text-center font-jetbrains text-rose-600">{{ number_format((float) $dayLoadBatch->total_loss_weight, 2) }}</td>
                        <td class="px-6 py-3 text-center font-jetbrains text-emerald-600">{{ number_format((float) $dayLoadBatch->total_weight, 2) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </x-card>
    @endif

    {{-- Purchases Section --}}
    <x-card>
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50">
            <div class="flex items-center gap-2">
                <span class="material-symbols-rounded text-emerald-600 dark:text-emerald-400">receipt_long</span>
                <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">Purchase Entries</h2>
                @if($purchases->count() > 0)
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                    {{ $purchases->count() }} invoices
                </span>
                @endif
            </div>
        </div>

        @if($purchases->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-[11px] font-bold uppercase tracking-wider text-zinc-500 border-b border-zinc-200/50 dark:border-zinc-800/50">
                        <th class="px-6 py-3 text-left">Vendor</th>
                        <th class="px-6 py-3 text-left">Items</th>
                        <th class="px-6 py-3 text-left">Invoice No</th>
                        <th class="px-6 py-3 text-left">Payment</th>
                        <th class="px-6 py-3 text-right">GST</th>
                        <th class="px-6 py-3 text-right">Total</th>
                        <th class="px-6 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800">
                    @foreach($purchases as $p)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <x-avatar name="{{ $p->vendor_name }}" size="sm" />
                                    <div class="font-bold text-zinc-900 dark:text-zinc-100 text-xs">{{ $p->vendor_name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $firstItem = $p->items->first();
                                    $othersCount = $p->items->count() - 1;
                                @endphp
                                @if($firstItem)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-semibold">
                                        {{ $firstItem->item_name }} ({{ number_format($firstItem->quantity) }} {{ $firstItem->unit }})
                                    </span>
                                    @if($othersCount > 0)
                                        <span class="text-[10px] text-zinc-500 ml-1">+{{ $othersCount }}</span>
                                    @endif
                                @else
                                    <span class="text-zinc-400 text-xs">—</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-xs font-jetbrains text-zinc-500">{{ $p->invoice_no ?: '—' }}</td>
                            <td class="px-6 py-4 text-xs text-zinc-500">{{ $p->payment_mode }}</td>
                            <td class="px-6 py-4 text-right font-jetbrains text-xs text-zinc-500">Rs {{ number_format((float) $p->gst_amount, 2) }}</td>
                            <td class="px-6 py-4 text-right font-jetbrains font-bold text-xs text-zinc-900 dark:text-zinc-100">Rs {{ number_format((float) $p->total_amount, 2) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('purchases.show', $p->id) }}" class="text-zinc-400 hover:text-blue-600 transition-colors" title="View">
                                        <span class="material-symbols-rounded text-lg">visibility</span>
                                    </a>
                                    @can('edit purchases')
                                    <a href="{{ route('purchases.edit', $p->id) }}" class="text-zinc-400 hover:text-emerald-600 transition-colors" title="Edit">
                                        <span class="material-symbols-rounded text-lg">edit</span>
                                    </a>
                                    @endcan
                                    @can('delete purchases')
                                    <form action="{{ route('purchases.destroy', $p->id) }}" method="POST" class="delete-form inline">
                                        @csrf @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)" class="text-zinc-400 hover:text-rose-600 transition-colors" title="Delete">
                                            <span class="material-symbols-rounded text-lg">delete</span>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="p-8 text-center">
            <x-empty-state
                icon="receipt_long"
                title="No purchases on this date"
                description="No purchase invoices were recorded for this day." />
        </div>
        @endif
    </x-card>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(button) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete this purchase invoice and revert its stock movements!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d9488',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            background: document.documentElement.dataset.theme === 'dark' ? '#111827' : '#ffffff',
            color: document.documentElement.dataset.theme === 'dark' ? '#f3f4f6' : '#0f172a'
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('.delete-form').submit();
            }
        });
    }
</script>
@endpush
