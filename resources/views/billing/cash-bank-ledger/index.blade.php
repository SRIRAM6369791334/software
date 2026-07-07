@extends('layouts.app')
@section('title', 'Cash & Bank Ledger')

@section('content')
<div class="animate-fade-in">
    <x-page-header title="Cash & Bank Ledger" subtitle="Daily cash-in-hand and bank transfer running balances">
        <x-slot:actions>
            <x-button variant="outline" icon="refresh" onclick="location.reload()">
                Refresh
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">Daily Ledger</h2>
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="date" name="start" value="{{ $startDate }}" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <input type="date" name="end" value="{{ $endDate }}" class="rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                <x-button type="submit" variant="outline" icon="filter_alt">Filter</x-button>
            </form>
        </div>

        <x-data-table :headers="['Date', 'Cash Income', 'Bank Income', 'Cash Expense', 'Opening Cash', 'Closing Cash', 'Opening Bank', 'Closing Bank', 'Total Balance', 'Status', '']">
            @forelse($ledgers as $ledger)
                @php
                    $totalBalance = (float) $ledger->closing_cash_balance + (float) $ledger->closing_bank_balance;
                @endphp
                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                    <td class="px-6 py-4">
                        <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $ledger->ledger_date->format('d M Y') }}</p>
                        <p class="text-xs text-zinc-500">{{ $ledger->ledger_date->format('l') }}</p>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-jetbrains font-bold text-emerald-600">Rs {{ number_format((float) $ledger->cash_income, 0) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-jetbrains font-bold text-blue-600">Rs {{ number_format((float) $ledger->bank_income, 0) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-jetbrains font-bold text-rose-600">Rs {{ number_format((float) $ledger->cash_expense, 0) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-jetbrains text-zinc-600 dark:text-zinc-400">Rs {{ number_format((float) $ledger->opening_cash_balance, 0) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-jetbrains font-bold {{ (float) $ledger->closing_cash_balance > 0 ? 'text-emerald-600' : 'text-zinc-500' }}">
                            Rs {{ number_format((float) $ledger->closing_cash_balance, 0) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-jetbrains text-zinc-600 dark:text-zinc-400">Rs {{ number_format((float) $ledger->opening_bank_balance, 0) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-jetbrains font-bold text-blue-600">Rs {{ number_format((float) $ledger->closing_bank_balance, 0) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-jetbrains font-bold text-indigo-600 text-base">Rs {{ number_format($totalBalance, 0) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @if($ledger->is_approved)
                            <x-badge variant="success">Approved</x-badge>
                        @else
                            <x-badge variant="zinc">Not Approved</x-badge>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if($ledger->is_approved)
                            <span class="text-xs text-zinc-500">
                                Approved by {{ $ledger->approvedBy?->name ?? 'Unknown' }}
                                @if($ledger->approved_at)
                                    on {{ $ledger->approved_at->format('d M Y, h:i A') }}
                                @endif
                                @if((float) ($ledger->approved_amount ?? 0) > 0)
                                    <br><span class="font-jetbrains text-emerald-600">Rs {{ number_format((float) $ledger->approved_amount, 0) }} swept</span>
                                @endif
                            </span>
                        @else
                            <form method="POST" action="{{ route('billing.cash-bank-ledger.approve', $ledger) }}" class="inline" onsubmit="return confirm('Are you sure you want to approve the ledger for {{ $ledger->ledger_date->format('d M Y') }}? This will sweep the closing cash balance of Rs {{ number_format((float) $ledger->closing_cash_balance, 0) }} into the bank account and is irreversible.')">
                                @csrf
                                <x-button type="submit" variant="primary" size="sm" icon="check_circle">
                                    Approve
                                </x-button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <x-slot:empty>
                    <x-empty-state icon="account_balance" title="No ledger entries found" description="Select a different date range or record some dealer payments first." />
                </x-slot:empty>
            @endforelse
        </x-data-table>
    </x-card>
</div>
@endsection
