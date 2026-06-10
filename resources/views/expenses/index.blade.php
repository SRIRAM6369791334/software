@extends('layouts.app')
@section('title', 'Expenses & EMI')

@section('content')
<div class="animate-fade-in">
    <x-page-header title="Expenses & EMI" subtitle="Manage operational burn and financial obligations">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('expenses.export') }}" icon="download">
                Export
            </x-button>
            @can('create expenses')
            <x-button variant="primary" x-data x-on:click="$dispatch('open-modal', 'add-expense')" icon="add">
                Record Expense
            </x-button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-stat-card 
            label="Monthly Burn" 
            value="Rs {{ number_format($totals['total_expenses'], 0) }}" 
            icon="trending_up" 
            color="rose" />
        <x-stat-card 
            label="EMI Total" 
            value="Rs {{ number_format($totals['total_emis'], 0) }}" 
            icon="account_balance" 
            color="amber" />
        <div class="rounded-2xl border border-zinc-200/80 dark:border-zinc-800/80 bg-white/70 dark:bg-zinc-900/70 backdrop-blur-xl p-6 shadow-sm flex flex-col justify-center">
            <p class="font-outfit text-sm font-medium text-emerald-600 dark:text-emerald-400">Financial Outlook</p>
            <p class="mt-2 font-outfit text-sm italic text-zinc-500 dark:text-zinc-400 font-medium">"Optimizing cash flow by tracking every penny."</p>
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Ledger Table Card --}}
        <div class="lg:col-span-2">
            <x-card>
                <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50">
                    <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">General Expense Ledger</h2>
                </div>
                
                <x-data-table :headers="['Date', 'Category', 'Description', 'Amount', 'Action']">
                    @forelse($expenses as $e)
                        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                            <td class="px-6 py-4 font-medium text-zinc-900 dark:text-zinc-100">
                                {{ $e->date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <x-badge variant="zinc">{{ $e->category }}</x-badge>
                            </td>
                            <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">
                                {{ $e->description }}
                            </td>
                            <td class="px-6 py-4 font-jetbrains font-medium text-rose-600 dark:text-rose-400">
                                <x-currency :amount="$e->amount" />
                            </td>
                            <td class="px-6 py-4">
                                @can('delete expenses')
                                <form action="{{ route('expenses.destroy', $e) }}" method="POST" onsubmit="return confirm('Delete this expense entry?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-zinc-400 hover:text-rose-600 transition-colors" title="Delete">
                                        <span class="material-symbols-rounded text-lg">delete</span>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <x-slot:empty>
                            <x-empty-state 
                                icon="receipt_long" 
                                title="No expenses recorded" 
                                description="No expenses logged in this cycle." />
                        </x-slot:empty>
                    @endforelse

                    @if($expenses->hasPages())
                        <x-slot:pagination>
                            {{ $expenses->links() }}
                        </x-slot:pagination>
                    @endif
                </x-data-table>
            </x-card>
        </div>

        {{-- EMI obligations --}}
        <div>
            <h3 class="text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-4 font-outfit">EMI Obligations</h3>
            <div class="flex flex-col gap-4">
                @forelse($emis as $emi)
                    @php
                        $statusMap = [
                            'Paid' => ['variant' => 'success', 'icon' => 'check_circle'],
                            'Overdue' => ['variant' => 'danger', 'icon' => 'warning'],
                            'Upcoming' => ['variant' => 'info', 'icon' => 'schedule']
                        ];
                        $style = $statusMap[$emi->status] ?? $statusMap['Upcoming'];
                    @endphp
                    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/50 dark:border-zinc-800/50 rounded-2xl p-5 hover:border-emerald-500/30 transition-colors">
                        <div class="flex items-center justify-between mb-2">
                            <div class="font-cabinet text-base font-bold text-zinc-900 dark:text-white">{{ $emi->item }}</div>
                            <span class="material-symbols-rounded text-xl {{ $emi->status == 'Paid' ? 'text-emerald-500' : ($emi->status == 'Overdue' ? 'text-rose-500' : 'text-blue-500') }}">{{ $style['icon'] }}</span>
                        </div>
                        <div class="flex items-center justify-between mt-4">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs text-zinc-500 uppercase tracking-wider">Due: {{ $emi->due_date->format('M d, Y') }}</span>
                                <x-badge :variant="$style['variant']">{{ $emi->status }}</x-badge>
                            </div>
                            <div class="font-jetbrains text-lg font-bold text-zinc-900 dark:text-white">
                                <x-currency :amount="$emi->amount" />
                            </div>
                        </div>
                    </div>
                @empty
                    <x-empty-state 
                        icon="account_balance" 
                        title="No Active EMIs" 
                        description="You have no upcoming EMI payments." />
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- Add Expense Modal --}}
<x-modal name="add-expense" title="Record Expense" subtitle="Log operational expenditures" icon="receipt_long" maxWidth="md">
    <form action="{{ route('expenses.store') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <x-form.select name="category" label="Category" required>
                @foreach(['Fuel','Salary','Transport','Utility','Misc'] as $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                @endforeach
            </x-form.select>
            <x-form.input type="date" name="date" label="Date" required value="{{ date('Y-m-d') }}" />
        </div>

        <div class="mb-4">
            <x-form.input name="description" label="Description" required placeholder="What was this expense for?" />
        </div>

        <div class="mb-6">
            <x-form.input type="number" name="amount" label="Amount (Rs)" required step="0.01" min="0.01" placeholder="0.00" class="text-xl font-bold" />
        </div>

        <x-slot:footer>
            <x-button type="button" variant="outline" x-on:click="show = false">Cancel</x-button>
            <x-button type="submit" variant="primary" icon="check">Log Expense</x-button>
        </x-slot:footer>
    </form>
</x-modal>
@endsection
