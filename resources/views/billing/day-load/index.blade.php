@extends('layouts.app')
@section('title', 'Daily Load Billing')

@section('content')
<div class="animate-fade-in">
    <x-page-header title="Daily Load Billing" subtitle="Track vendor loads, dealer rates, box weights, and paper-rate variance">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('billing.weekly.index') }}" icon="receipt_long">
                Weekly Billing
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <x-stat-card label="Billing Date" value="{{ \Carbon\Carbon::parse($date)->format('d M Y') }}" icon="calendar_today" color="blue" />
        <x-stat-card label="Day" value="{{ \Carbon\Carbon::parse($date)->format('l') }}" icon="event" color="emerald" />
        <x-stat-card label="Total Boxes" value="{{ number_format((float) ($batch?->total_boxes ?? 0), 0) }}" icon="inventory_2" color="amber" />
        <x-stat-card label="Bird Weight" value="{{ number_format((float) ($batch?->total_bird_weight ?? 0), 2) }} kg" icon="scale" color="indigo" />
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
            <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">Load Entries</h2>
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="date" name="date" value="{{ $date }}" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <input type="text" name="search" value="{{ $search }}" placeholder="Search vendor or dealer" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <x-button type="submit" variant="outline" icon="filter_alt">Filter</x-button>
            </form>
        </div>

        <x-data-table :headers="['Date', 'Vendor', 'Dealer', 'Rates', 'Paper Diff', 'Boxes', 'Weights', 'Status']">
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
                    </td>
                    <td class="px-6 py-4 text-center">
                        <x-badge variant="success">{{ $entry->status }}</x-badge>
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
</div>
@endsection
