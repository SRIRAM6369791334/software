@extends('layouts.app')
@section('title', 'Dealer Payments')

@section('content')

<div class="animate-fade-in">
    <x-page-header title="Dealer Payouts" subtitle="Track payments made to suppliers and feed dealers">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('payments.dealers.export') }}" icon="download">
                Export
            </x-button>
            @can('create payments')
            <x-button variant="primary" href="{{ route('payments.dealers.create') }}" icon="add">
                Record Payout
            </x-button>
            @endcan
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
        
        <x-data-table :headers="['Dealer / Firm', 'Payout Date', 'Amount Paid', 'Payment Mode', 'Cash / Bank', 'Balance After', 'Actions']">
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
                    <td class="px-6 py-4">
                        <div class="flex flex-col items-start gap-0.5">
                            @php
                                $hasCash = !is_null($p->cash_amount) && $p->cash_amount > 0;
                                $hasBank = !is_null($p->bank_amount) && $p->bank_amount > 0;
                            @endphp
                            @if ($hasCash || $hasBank)
                                @if ($hasCash)
                                    <span class="text-xs font-jetbrains text-zinc-700 dark:text-zinc-300">
                                        Cash: <x-currency :amount="$p->cash_amount" />
                                    </span>
                                @endif
                                @if ($hasBank)
                                    <span class="text-xs font-jetbrains text-zinc-700 dark:text-zinc-300">
                                        Bank: <x-currency :amount="$p->bank_amount" />
                                        @if ($p->bank_transfer_type)
                                            <span class="text-[10px] text-zinc-400 ml-0.5">({{ $p->bank_transfer_type }})</span>
                                        @endif
                                    </span>
                                @endif
                            @else
                                <span class="text-xs text-zinc-400">—</span>
                            @endif
                        </div>
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

@endsection
