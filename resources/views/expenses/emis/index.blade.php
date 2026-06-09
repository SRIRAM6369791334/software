@extends('layouts.app')
@section('title', 'EMI Tracking')

@section('content')
<div class="animate-fade-in">
    <x-page-header title="EMI & Loan Installments" subtitle="Manage fixed monthly business repayments">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('expenses.emis.alerts') }}" icon="notifications_active" class="!text-amber-600 !border-amber-200 hover:!bg-amber-50">
                Upcoming Alerts
            </x-button>
            <x-button variant="primary" href="{{ route('expenses.emis.create') }}" icon="add">
                Setup New EMI
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <x-card>
        <x-data-table :headers="['Loan Detail', 'Due Date', 'Type / Info', 'Amount', 'Status', 'Actions']">
            @forelse($emis as $emi)
                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <p class="font-cabinet font-bold text-zinc-900 dark:text-zinc-100">{{ $emi->loan_name }}</p>
                        <p class="font-jetbrains text-[10px] text-zinc-500 uppercase tracking-widest mt-0.5">REF#{{ str_pad($emi->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @php $isOverdue = $emi->status != 'Paid' && $emi->due_date < now(); @endphp
                        <span class="font-medium {{ $isOverdue ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                            {{ $emi->due_date->format('d M, Y') }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <x-badge variant="info">{{ $emi->emi_type ?? 'Bank Loan' }}</x-badge>
                        @if(($emi->emi_type ?? 'Bank Loan') === 'Bank Loan')
                            <p class="font-outfit text-xs text-zinc-500 italic mt-1.5">{{ $emi->bank_name ?? 'Bank Unknown' }}</p>
                        @endif
                    </td>
                    <td class="px-6 py-4 font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 text-right">
                        <x-currency :amount="$emi->amount" />
                    </td>
                    <td class="px-6 py-4 text-center">
                        @php
                            $variant = $emi->status == 'Paid' ? 'success' : ($emi->status == 'Overdue' ? 'danger' : 'warning');
                        @endphp
                        <x-badge :variant="$variant">{{ $emi->status }}</x-badge>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex justify-center gap-2">
                            <form action="{{ route('expenses.emis.destroy', $emi) }}" method="POST" onsubmit="return confirm('Delete this EMI record?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-zinc-400 hover:text-rose-600 transition-colors" title="Delete">
                                    <span class="material-symbols-rounded text-lg">delete</span>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <x-slot:empty>
                    <x-empty-state 
                        icon="account_balance" 
                        title="No EMI records found" 
                        description="You don't have any EMI or Loan installments set up." />
                </x-slot:empty>
            @endforelse

            @if($emis->hasPages())
                <x-slot:pagination>
                    {{ $emis->links() }}
                </x-slot:pagination>
            @endif
        </x-data-table>
    </x-card>
</div>
@endsection
