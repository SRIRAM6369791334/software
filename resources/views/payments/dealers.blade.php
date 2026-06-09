@extends('layouts.app')
@section('title', 'Dealer Payments')

@section('content')

<div class="animate-fade-in">
    <x-page-header title="Dealer Payouts" subtitle="Track payments made to suppliers and feed dealers">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('payments.dealers.export') }}" icon="download">
                Export
            </x-button>
            <x-button variant="primary" x-data x-on:click="$dispatch('open-modal', 'add-payment')" icon="add">
                Record Payout
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-stat-card 
            label="Total Paid Out" 
            value="Rs {{ number_format($payments->sum('amount'), 0) }}" 
            icon="payments" 
            color="blue" />
        <x-stat-card 
            label="Payable to Dealers" 
            value="Rs {{ number_format($dealers->sum('pending_amount'), 0) }}" 
            icon="error" 
            color="rose" />
        <x-stat-card 
            label="Active Suppliers" 
            value="{{ $dealers->where('pending_amount', '>', 0)->count() }}" 
            icon="group" 
            color="emerald" 
            trend="with Balances" 
            :trendUp="true" />
    </div>

    {{-- Table Card --}}
    <x-card>
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50">
            <form method="GET" class="relative max-w-sm">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-400">
                    <span class="material-symbols-rounded text-xl">search</span>
                </div>
                <input type="text" name="search" value="{{ $search ?? '' }}" class="bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 p-2.5 transition-colors font-outfit" placeholder="Search dealer or reference…">
            </form>
        </div>
        
        <x-data-table :headers="['Dealer / Firm', 'Payout Date', 'Amount Paid', 'Payment Mode', 'Balance After', 'Actions']">
            @forelse($payments as $p)
                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <x-avatar :name="$p->dealer->firm_name ?? '?'" size="sm" />
                            <div>
                                <p class="font-cabinet font-bold text-zinc-900 dark:text-zinc-100">{{ $p->dealer->firm_name ?? '-' }}</p>
                                <p class="font-outfit text-xs text-zinc-500">{{ $p->dealer->contact_person ?? 'NO CONTACT' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $p->date->format('d M, Y') }}</p>
                        <p class="text-xs text-zinc-500">{{ $p->date->format('l') }}</p>
                    </td>
                    <td class="px-6 py-4 font-jetbrains font-medium text-rose-600 dark:text-rose-400 text-right">
                        <x-currency :amount="$p->amount" />
                    </td>
                    <td class="px-6 py-4 text-center">
                        <x-badge variant="zinc">{{ $p->payment_mode }}</x-badge>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="font-jetbrains font-medium text-zinc-900 dark:text-zinc-100">
                            <x-currency :amount="$p->pending_balance_after" />
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <x-button variant="outline" href="{{ route('payments.dealers.ledger', $p->dealer_id) }}" size="sm">
                            Ledger
                        </x-button>
                    </td>
                </tr>
            @empty
                <x-slot:empty>
                    <x-empty-state 
                        icon="account_balance_wallet" 
                        title="No Payouts Recorded" 
                        description="Ready to clear your dealer balances?" />
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
<x-modal name="add-payment" title="Record Payout" subtitle="Enter payment made to clear supplier dues" icon="payments" iconColor="blue" maxWidth="md">
    <form action="{{ route('payments.dealers.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <x-form.select name="dealer_id" label="Dealer" required>
                <option value="">Choose dealer…</option>
                @foreach($dealers as $d)
                    <option value="{{ $d->id }}">{{ $d->firm_name }} (Pending: Rs {{ number_format($d->pending_amount, 0) }})</option>
                @endforeach
            </x-form.select>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <x-form.input type="number" name="amount" label="Amount Paid (Rs)" required step="0.01" min="0.01" placeholder="0.00" class="text-xl font-bold" />
            <x-form.input type="date" name="date" label="Payment Date" required value="{{ date('Y-m-d') }}" />
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-2 font-outfit">Payment Mode <span class="text-rose-500">*</span></label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                @foreach(['NEFT','Cheque','UPI','Cash'] as $mode)
                    <label class="flex items-center gap-2 p-2 border border-zinc-200 dark:border-zinc-700 rounded-lg cursor-pointer bg-zinc-50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-700 transition-colors">
                        <input type="radio" name="payment_mode" value="{{ $mode }}" {{ $loop->first ? 'checked' : '' }} class="text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm font-outfit text-zinc-700 dark:text-zinc-300">{{ $mode }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="mb-6">
            <x-form.input type="text" name="notes" label="Transaction Reference" placeholder="Transaction ID, Cheque #, or reference..." />
        </div>

        <x-slot:footer>
            <x-button type="button" variant="outline" x-on:click="show = false">Cancel</x-button>
            <x-button type="submit" variant="primary" icon="check">Confirm Payout</x-button>
        </x-slot:footer>
    </form>
</x-modal>
@endsection
