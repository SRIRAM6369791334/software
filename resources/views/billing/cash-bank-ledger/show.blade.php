@extends('layouts.app')
@section('title', "Ledger Details - $date")

@section('content')
<div class="animate-fade-in">
    <x-page-header title="Ledger Details" :subtitle="Carbon\Carbon::parse($date)->format('l, d M Y')">
        <x-slot:actions>
            @if(!$ledger->is_approved)
                <form method="POST" action="{{ route('billing.cash-bank-ledger.approve', $ledger) }}" class="inline" onsubmit="return confirm('Approve ledger for {{ Carbon\Carbon::parse($date)->format('d M Y') }}? This will sweep closing cash of Rs {{ number_format((float) $ledger->closing_cash_balance, 0) }} into the bank account and is irreversible.')">
                    @csrf
                    <x-button type="submit" variant="primary" icon="check_circle">
                        Approve Day
                    </x-button>
                </form>
            @endif
            <x-button variant="outline" href="{{ route('payments.dealers.create') }}" icon="payments" target="_blank">
                Add Dealer Payment
            </x-button>
            <x-button variant="outline" href="{{ route('expenses.create') }}" icon="money_off" target="_blank">
                Add Expense
            </x-button>
            <x-button variant="outline" href="{{ route('billing.cash-bank-ledger.index') }}" icon="arrow_back">
                Back
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
        <x-stat-card label="Cash Income" value="Rs {{ number_format((float) $ledger->cash_income, 0) }}" icon="payments" color="emerald" />
        <x-stat-card label="Bank Income" value="Rs {{ number_format((float) $ledger->bank_income, 0) }}" icon="account_balance" color="blue" />
        <x-stat-card label="Cash Expense" value="Rs {{ number_format((float) $ledger->cash_expense, 0) }}" icon="money_off" color="rose" />
        <x-stat-card label="Total Balance" value="Rs {{ number_format((float) $ledger->closing_cash_balance + (float) $ledger->closing_bank_balance, 0) }}" icon="account_balance_wallet" color="indigo" />
    </div>

    {{-- Cash vs Bank Summary --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="p-5 rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100/50 dark:from-emerald-900/20 dark:to-emerald-800/10 border border-emerald-200/50 dark:border-emerald-800/30">
            <div class="flex items-center gap-3 mb-3">
                <span class="material-symbols-rounded text-emerald-600 text-[24px]">payments</span>
                <h3 class="font-cabinet font-bold text-emerald-800 dark:text-emerald-300">Cash Summary</h3>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600/70 mb-1">Opening</p>
                    <p class="font-jetbrains font-bold text-emerald-700 dark:text-emerald-300">Rs {{ number_format((float) $ledger->opening_cash_balance, 0) }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600/70 mb-1">Income</p>
                    <p class="font-jetbrains font-bold text-emerald-600">+ Rs {{ number_format((float) $ledger->cash_income, 0) }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600/70 mb-1">Expense</p>
                    <p class="font-jetbrains font-bold text-rose-600">- Rs {{ number_format((float) $ledger->cash_expense, 0) }}</p>
                </div>
                <div class="col-span-3 mt-1 pt-2 border-t border-emerald-200/50 dark:border-emerald-800/30">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-emerald-600/70 mb-1">Closing Cash</p>
                    <p class="font-jetbrains font-bold text-xl text-emerald-600">Rs {{ number_format((float) $ledger->closing_cash_balance, 0) }}</p>
                </div>
            </div>
        </div>
        <div class="p-5 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100/50 dark:from-blue-900/20 dark:to-blue-800/10 border border-blue-200/50 dark:border-blue-800/30">
            <div class="flex items-center gap-3 mb-3">
                <span class="material-symbols-rounded text-blue-600 text-[24px]">account_balance</span>
                <h3 class="font-cabinet font-bold text-blue-800 dark:text-blue-300">Bank Summary</h3>
            </div>
            <div class="grid grid-cols-3 gap-3 text-center">
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600/70 mb-1">Opening</p>
                    <p class="font-jetbrains font-bold text-blue-700 dark:text-blue-300">Rs {{ number_format((float) $ledger->opening_bank_balance, 0) }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600/70 mb-1">Income</p>
                    <p class="font-jetbrains font-bold text-blue-600">+ Rs {{ number_format((float) $ledger->bank_income, 0) }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600/70 mb-1">&nbsp;</p>
                    <p class="font-jetbrains font-bold text-blue-600">&nbsp;</p>
                </div>
                <div class="col-span-3 mt-1 pt-2 border-t border-blue-200/50 dark:border-blue-800/30">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-blue-600/70 mb-1">Closing Bank</p>
                    <p class="font-jetbrains font-bold text-xl text-blue-600">Rs {{ number_format((float) $ledger->closing_bank_balance, 0) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Dealer Payments --}}
    <x-card class="mb-6">
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50">
            <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                <span class="material-symbols-rounded text-emerald-500 text-[20px]">payments</span>
                Dealer Payments
                <span class="ml-auto text-sm font-normal text-zinc-500">
                    Cash: Rs {{ number_format($dealerPayments->sum('cash_amount'), 0) }} |
                    Bank: Rs {{ number_format($dealerPayments->sum('bank_amount'), 0) }} |
                    Total: Rs {{ number_format($dealerPayments->sum('cash_amount') + $dealerPayments->sum('bank_amount'), 0) }}
                </span>
            </h2>
        </div>
        @if($dealerPayments->isEmpty())
            <x-empty-state icon="payments" title="No dealer payments" description="No dealer payments recorded for this date." />
        @else
            <x-data-table :headers="['Dealer', 'Cash', 'Bank Transfer', 'Mode', 'Reference']">
                @foreach($dealerPayments as $p)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-zinc-900 dark:text-zinc-100">{{ $p->dealer?->firm_name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4">
                            @if((float) $p->cash_amount > 0)
                                <span class="font-jetbrains font-bold text-emerald-600">Rs {{ number_format((float) $p->cash_amount, 0) }}</span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if((float) $p->bank_amount > 0)
                                <span class="font-jetbrains font-bold text-blue-600">Rs {{ number_format((float) $p->bank_amount, 0) }}</span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <x-badge variant="{{ $p->payment_mode === 'Cash' ? 'emerald' : 'blue' }}">
                                {{ $p->payment_mode }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-500">{{ $p->reference_number ?? '—' }}</td>
                    </tr>
                @endforeach
            </x-data-table>
        @endif
    </x-card>

    {{-- Day-Load Entries --}}
    <x-card class="mb-6">
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50">
            <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                <span class="material-symbols-rounded text-amber-500 text-[20px]">local_shipping</span>
                Day-Load Entries
                @if($dayLoadBatch)
                    <span class="ml-auto text-sm font-normal text-zinc-500">
                        {{ $dayLoadBatch->entries->count() }} entries |
                        {{ number_format($dayLoadBatch->total_boxes ?? 0) }} boxes |
                        {{ number_format($dayLoadBatch->total_bird_weight ?? 0, 1) }} kg
                    </span>
                @endif
            </h2>
        </div>
        @if(!$dayLoadBatch || $dayLoadBatch->entries->isEmpty())
            <x-empty-state icon="local_shipping" title="No day-load entries" description="No day-load activity recorded for this date." />
        @else
            <x-data-table :headers="['Vendor', 'Dealer', 'Boxes', 'Weight', 'Dealer Income', 'Collected', 'Status']">
                @foreach($dayLoadBatch->entries as $e)
                    @php
                        $income = (float) $e->bird_weight * (float) $e->billing_rate;
                        $collected = (float) $e->dealer_collected;
                        $remaining = $income - $collected;
                    @endphp
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-zinc-900 dark:text-zinc-100">{{ $e->vendor?->firm_name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-zinc-700 dark:text-zinc-300">{{ $e->dealer?->firm_name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 font-jetbrains text-zinc-600 dark:text-zinc-400">{{ number_format((float) $e->no_of_boxes) }}</td>
                        <td class="px-6 py-4 font-jetbrains text-zinc-600 dark:text-zinc-400">{{ number_format((float) $e->bird_weight, 1) }} kg</td>
                        <td class="px-6 py-4 font-jetbrains font-bold text-zinc-900 dark:text-zinc-100">Rs {{ number_format($income, 0) }}</td>
                        <td class="px-6 py-4 font-jetbrains {{ $collected > 0 ? 'text-emerald-600' : 'text-zinc-400' }}">
                            Rs {{ number_format($collected, 0) }}
                            @if($remaining > 0)
                                <span class="text-rose-500 text-[11px] block">(Due: Rs {{ number_format($remaining, 0) }})</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $status = $e->dealer_payment_status ?? 'Pending';
                                $variant = match($status) {
                                    'Paid' => 'success',
                                    'Partial' => 'warning',
                                    'Overpaid' => 'info',
                                    default => 'zinc',
                                };
                            @endphp
                            <x-badge :variant="$variant">{{ $status }}</x-badge>
                        </td>
                    </tr>
                @endforeach
            </x-data-table>
        @endif
    </x-card>

    {{-- Customer Payments --}}
    <x-card class="mb-6">
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50">
            <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                <span class="material-symbols-rounded text-blue-500 text-[20px]">credit_card</span>
                Customer Payments
                <span class="ml-auto text-sm font-normal text-zinc-500">
                    Cash: Rs {{ number_format($customerPayments->sum('cod_amount'), 0) }} |
                    Bank: Rs {{ number_format($customerPayments->sum('bank_transfer_amount'), 0) }} |
                    Total: Rs {{ number_format($customerPayments->sum('cod_amount') + $customerPayments->sum('bank_transfer_amount'), 0) }}
                </span>
            </h2>
        </div>
        @if($customerPayments->isEmpty())
            <x-empty-state icon="credit_card" title="No customer payments" description="No customer payments recorded for this date." />
        @else
            <x-data-table :headers="['Customer', 'Cash (COD)', 'Bank Transfer', 'Mode', 'Type']">
                @foreach($customerPayments as $p)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-zinc-900 dark:text-zinc-100">{{ $p->customer?->name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4">
                            @if((float) $p->cod_amount > 0)
                                <span class="font-jetbrains font-bold text-emerald-600">Rs {{ number_format((float) $p->cod_amount, 0) }}</span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if((float) $p->bank_transfer_amount > 0)
                                <span class="font-jetbrains font-bold text-blue-600">Rs {{ number_format((float) $p->bank_transfer_amount, 0) }}</span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <x-badge variant="{{ $p->payment_mode === 'Cash' ? 'emerald' : 'blue' }}">
                                {{ $p->payment_mode }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-500">{{ $p->payment_type ?? '—' }}</td>
                    </tr>
                @endforeach
            </x-data-table>
        @endif
    </x-card>

    {{-- Vendor Payments --}}
    <x-card class="mb-6">
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50">
            <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                <span class="material-symbols-rounded text-purple-500 text-[20px]">local_shipping</span>
                Vendor Payments
                <span class="ml-auto text-sm font-normal text-zinc-500">
                    Cash: Rs {{ number_format($vendorPayments->sum('cash_amount'), 0) }} |
                    Bank: Rs {{ number_format($vendorPayments->sum('bank_amount'), 0) }} |
                    Total: Rs {{ number_format($vendorPayments->sum('cash_amount') + $vendorPayments->sum('bank_amount'), 0) }}
                </span>
            </h2>
        </div>
        @if($vendorPayments->isEmpty())
            <x-empty-state icon="local_shipping" title="No vendor payments" description="No vendor payments recorded for this date." />
        @else
            <x-data-table :headers="['Vendor', 'Cash', 'Bank Transfer', 'Mode', 'Reference']">
                @foreach($vendorPayments as $p)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-zinc-900 dark:text-zinc-100">{{ $p->vendor?->firm_name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4">
                            @if((float) $p->cash_amount > 0)
                                <span class="font-jetbrains font-bold text-emerald-600">Rs {{ number_format((float) $p->cash_amount, 0) }}</span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if((float) $p->bank_amount > 0)
                                <span class="font-jetbrains font-bold text-blue-600">Rs {{ number_format((float) $p->bank_amount, 0) }}</span>
                            @else
                                <span class="text-zinc-400">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <x-badge variant="{{ $p->payment_mode === 'Cash' ? 'emerald' : 'blue' }}">
                                {{ $p->payment_mode }}
                            </x-badge>
                        </td>
                        <td class="px-6 py-4 text-sm text-zinc-500">{{ $p->reference_number ?? '—' }}</td>
                    </tr>
                @endforeach
            </x-data-table>
        @endif
    </x-card>

    {{-- Expenses --}}
    <x-card class="mb-6">
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50">
            <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                <span class="material-symbols-rounded text-rose-500 text-[20px]">money_off</span>
                Expenses
                <span class="ml-auto text-sm font-normal text-zinc-500">Total: Rs {{ number_format($expenses->sum('amount'), 0) }}</span>
            </h2>
        </div>
        @if($expenses->isEmpty())
            <x-empty-state icon="money_off" title="No expenses" description="No expenses recorded for this date." />
        @else
            <x-data-table :headers="['Description', 'Category', 'Amount', 'Payment Method']">
                @foreach($expenses as $e)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-zinc-900 dark:text-zinc-100">{{ $e->description ?? '—' }}</td>
                        <td class="px-6 py-4">
                            <x-badge variant="zinc">{{ $e->category ?? 'Uncategorized' }}</x-badge>
                        </td>
                        <td class="px-6 py-4 font-jetbrains font-bold text-rose-600">Rs {{ number_format((float) $e->amount, 0) }}</td>
                        <td class="px-6 py-4 text-sm text-zinc-500">{{ $e->payment_method ?? '—' }}</td>
                    </tr>
                @endforeach
            </x-data-table>
        @endif
    </x-card>

    {{-- Balance Summary --}}
    <x-card>
        <div class="p-4">
            <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50 mb-4">Balance Summary</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-900">
                    <p class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-1">Opening Cash</p>
                    <p class="font-jetbrains font-bold text-lg text-zinc-900 dark:text-zinc-100">Rs {{ number_format((float) $ledger->opening_cash_balance, 0) }}</p>
                </div>
                <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-900/20">
                    <p class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-1">Closing Cash</p>
                    <p class="font-jetbrains font-bold text-lg text-emerald-600">Rs {{ number_format((float) $ledger->closing_cash_balance, 0) }}</p>
                </div>
                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-900">
                    <p class="text-xs font-bold uppercase tracking-wider text-zinc-500 mb-1">Opening Bank</p>
                    <p class="font-jetbrains font-bold text-lg text-zinc-900 dark:text-zinc-100">Rs {{ number_format((float) $ledger->opening_bank_balance, 0) }}</p>
                </div>
                <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20">
                    <p class="text-xs font-bold uppercase tracking-wider text-blue-600 mb-1">Closing Bank</p>
                    <p class="font-jetbrains font-bold text-lg text-blue-600">Rs {{ number_format((float) $ledger->closing_bank_balance, 0) }}</p>
                </div>
            </div>
        </div>
    </x-card>
</div>
@endsection
