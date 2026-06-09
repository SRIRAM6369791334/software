@extends('layouts.app')
@section('title', 'Customer Payments')

@section('content')

<div class="animate-fade-in">
    <x-page-header title="Customer Collections" subtitle="Manage inbound payments and customer ledgers">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('payments.customers.export') }}" icon="download">
                Export
            </x-button>
            <x-button variant="primary" x-data x-on:click="$dispatch('open-modal', 'add-payment')" icon="add">
                Record Collection
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-stat-card 
            label="Total Collected" 
            value="Rs {{ number_format($payments->sum('amount'), 0) }}" 
            icon="payments" 
            color="emerald" />
        <x-stat-card 
            label="Total Outstanding" 
            value="Rs {{ number_format($customers->sum('balance'), 0) }}" 
            icon="error" 
            color="rose" />
        <x-stat-card 
            label="Recent Collections" 
            value="{{ $payments->where('date', '>=', now()->subDays(7))->count() }}" 
            icon="history" 
            color="blue" 
            trend="This Week" 
            :trendUp="true" />
    </div>

    {{-- Table Card --}}
    <x-card>
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50">
            <form method="GET" class="relative max-w-sm">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-400">
                    <span class="material-symbols-rounded text-xl">search</span>
                </div>
                <input type="text" name="search" value="{{ $search }}" class="bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 p-2.5 transition-colors font-outfit" placeholder="Search customer or reference…">
            </form>
        </div>
        
        <x-data-table :headers="['Customer', 'Collection Date', 'Amount Received', 'Payment Mode', 'Receipt Type', 'Balance After']">
            @forelse($payments as $p)
                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <x-avatar :name="$p->customer->name ?? '?'" size="sm" />
                            <div>
                                <p class="font-cabinet font-bold text-zinc-900 dark:text-zinc-100">{{ $p->customer->name ?? '-' }}</p>
                                <p class="font-outfit text-xs text-zinc-500">{{ $p->customer->phone ?? 'NO PHONE' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $p->date->format('d M, Y') }}</p>
                        <p class="text-xs text-zinc-500">{{ $p->date->format('l') }}</p>
                    </td>
                    <td class="px-6 py-4 font-jetbrains font-medium text-emerald-600 dark:text-emerald-400 text-right">
                        <x-currency :amount="$p->amount" />
                    </td>
                    <td class="px-6 py-4 text-center">
                        <x-badge variant="zinc">{{ $p->payment_mode }}</x-badge>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $typeMap = [
                                'Full' => 'success',
                                'Part' => 'warning',
                                'Advance' => 'info',
                            ];
                            $badgeVariant = $typeMap[$p->payment_type] ?? 'zinc';
                        @endphp
                        <x-badge :variant="$badgeVariant">{{ strtoupper($p->payment_type) }}</x-badge>
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($p->balance_after > 0)
                            <span class="font-jetbrains text-rose-600 dark:text-rose-400 font-medium">
                                <x-currency :amount="$p->balance_after" />
                            </span>
                        @else
                            <x-badge variant="success">CLEARED</x-badge>
                        @endif
                    </td>
                </tr>
            @empty
                <x-slot:empty>
                    <x-empty-state 
                        icon="payments" 
                        title="No Collections Found" 
                        description="Record your first customer payment today." />
                </x-slot:empty>
            @endforelse

            @if($payments->hasPages())
                <x-slot:pagination>
                    {{ $payments->withQueryString()->links() }}
                </x-slot:pagination>
            @endif
        </x-data-table>
    </x-card>

</div>

{{-- Add Payment Modal --}}
<x-modal name="add-payment" title="Record Collection" subtitle="Enter payment details to update customer ledger" icon="payments" maxWidth="md">
    <form action="{{ route('payments.customers.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <x-form.select name="customer_id" label="Customer" required>
                <option value="">Choose customer…</option>
                @foreach($customers as $c)
                    <option value="{{ $c->id }}">{{ $c->name }} (Pending: Rs {{ number_format($c->balance, 0) }})</option>
                @endforeach
            </x-form.select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <x-form.input type="number" name="amount" label="Amount (Rs)" required step="0.01" min="0.01" placeholder="0.00" class="text-xl font-bold" />
            <x-form.input type="date" name="date" label="Payment Date" required value="{{ date('Y-m-d') }}" />
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <x-form.select name="payment_mode" label="Payment Mode" required>
                @foreach(['Cash','UPI','NEFT','Cheque'] as $m)<option value="{{ $m }}">{{ $m }}</option>@endforeach
            </x-form.select>
            <x-form.select name="payment_type" label="Receipt Type" required>
                @foreach(['Part','Full','Advance'] as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
            </x-form.select>
        </div>

        <div class="mb-6">
            <x-form.input name="notes" label="Remarks / Reference" placeholder="e.g. UPI Transaction ID or Cheque Number..." />
        </div>

        <x-slot:footer>
            <x-button type="button" variant="outline" x-on:click="show = false">Cancel</x-button>
            <x-button type="submit" variant="primary" icon="check">Record Collection</x-button>
        </x-slot:footer>
    </form>
</x-modal>
@endsection
