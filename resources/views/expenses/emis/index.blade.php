@extends('layouts.app')
@section('title', 'EMI Tracking')

@section('content')
<div class="animate-fade-in" x-data="{ 
    activeTab: '{{ request('tab', 'receive') }}', 
    activeEntity: null, 
    activeInvoice: null, 
    searchQuery: '', 
    timeframe: 'all',
    editId: null,
    editCategory: '',
    editDate: '',
    editDescription: '',
    editAmount: '',
    editAction: ''
}">
    <x-page-header title="EMI & Loan Installments" subtitle="Manage fixed monthly business repayments">
        <x-slot:actions>
            @php
                $totalAlertCount = count($overdueToReceive) + count($upcomingToReceive) + count($overdueToPay) + count($upcomingToPay);
            @endphp
            @if($totalAlertCount > 0)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 text-xs font-bold font-outfit border border-amber-200/50 dark:border-amber-900/50">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                {{ $totalAlertCount }} Alerts Active
            </span>
            @endif
            @can('create emis')
            <x-button variant="primary" href="{{ route('expenses.emis.create') }}" icon="add">
                Setup New EMI
            </x-button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <!-- Search and Filter Bar -->
    <div class="flex flex-col sm:flex-row gap-4 mb-6 items-center justify-between bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 shadow-sm">
        <div class="relative w-full sm:w-80">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-400">
                <span class="material-symbols-rounded text-lg">search</span>
            </span>
            <input type="text" x-model="searchQuery" placeholder="Search by name, loan, reference..." class="w-full pl-10 pr-4 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-outfit" />
        </div>
        
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider font-outfit">Filter Range:</label>
            <select x-model="timeframe" class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-outfit">
                <option value="all">Show All Upcoming</option>
                <option value="7">Next 7 Days</option>
                <option value="30">Next 30 Days</option>
                <option value="90">Next 90 Days</option>
            </select>
        </div>
    </div>

    <!-- Tab Navigation Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Tab 1: To Receive -->
        <button @click="activeTab = 'receive'; activeEntity = null; activeInvoice = null"
                :class="activeTab === 'receive' ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/10 dark:bg-emerald-950/10' : 'border-zinc-200 dark:border-zinc-800/50 bg-white dark:bg-zinc-900'"
                class="w-full border rounded-2xl p-5 text-left transition-all duration-200 group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-cabinet">To Receive</span>
                <span class="material-symbols-rounded text-emerald-500" :style="activeTab === 'receive' ? 'font-weight: bold;' : ''">call_received</span>
            </div>
            @php
                $totalReceivePending = collect($toReceiveEmis)->sum('pending_amount');
            @endphp
            <span class="font-jetbrains font-bold text-2xl text-zinc-900 dark:text-zinc-50 block leading-tight">
                <x-currency :amount="$totalReceivePending" />
            </span>
            <span class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 block font-outfit">From Customers & Dealers (Sales EMIs)</span>
        </button>

        <!-- Tab 2: To Pay -->
        <button @click="activeTab = 'pay'; activeEntity = null; activeInvoice = null"
                :class="activeTab === 'pay' ? 'border-amber-500 ring-2 ring-amber-500/20 bg-amber-50/10 dark:bg-amber-950/10' : 'border-zinc-200 dark:border-zinc-800/50 bg-white dark:bg-zinc-900'"
                class="w-full border rounded-2xl p-5 text-left transition-all duration-200 group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-cabinet">To Pay</span>
                <span class="material-symbols-rounded text-amber-500" :style="activeTab === 'pay' ? 'font-weight: bold;' : ''">call_made</span>
            </div>
            @php
                $totalPayPending = collect($toPayEmis)->sum('pending_amount');
            @endphp
            <span class="font-jetbrains font-bold text-2xl text-zinc-900 dark:text-zinc-50 block leading-tight">
                <x-currency :amount="$totalPayPending" />
            </span>
            <span class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 block font-outfit">To Vendors & Bank Loans (Purchases / Repayments)</span>
        </button>

        <!-- Tab 3: General Expenses -->
        <button @click="activeTab = 'expenses'; activeEntity = null; activeInvoice = null"
                :class="activeTab === 'expenses' ? 'border-rose-500 ring-2 ring-rose-500/20 bg-rose-50/10 dark:bg-rose-950/10' : 'border-zinc-200 dark:border-zinc-800/50 bg-white dark:bg-zinc-900'"
                class="w-full border rounded-2xl p-5 text-left transition-all duration-200 group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-cabinet">General Expenses</span>
                <span class="material-symbols-rounded text-rose-500" :style="activeTab === 'expenses' ? 'font-weight: bold;' : ''">receipt_long</span>
            </div>
            @php
                $totalExpenses = $totals['total_expenses'] ?? 0;
            @endphp
            <span class="font-jetbrains font-bold text-2xl text-zinc-900 dark:text-zinc-50 block leading-tight">
                <x-currency :amount="$totalExpenses" />
            </span>
            <span class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 block font-outfit">Operational Expenditures (Monthly Burn)</span>
        </button>
    </div>

    <!-- PANEL 1: TO RECEIVE -->
    <div x-show="activeTab === 'receive'" x-transition:enter="transition ease-out duration-200" class="space-y-4">
        
        <!-- Alerts for To Receive -->
        @if(count($overdueToReceive) > 0 || count($upcomingToReceive) > 0)
        <div class="bg-gradient-to-br from-amber-50/20 via-white to-zinc-50/20 dark:from-amber-950/10 dark:via-zinc-900 dark:to-zinc-950/20 border border-amber-200/60 dark:border-amber-900/50 rounded-2xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-amber-100 dark:border-amber-900/30 pb-3">
                <h3 class="text-sm font-black text-amber-800 dark:text-amber-400 font-cabinet flex items-center gap-2">
                    <span class="material-symbols-rounded text-lg">notifications_active</span>
                    Action Required: Early Warning Alerts ({{ count($overdueToReceive) + count($upcomingToReceive) }})
                </h3>
            </div>
            
            <div class="grid grid-cols-1 gap-3">
                {{-- Overdue Receivables --}}
                @foreach($overdueToReceive as $emi)
                    @php
                        $entityName = $emi->customer ? $emi->customer->name : ($emi->dealer ? ($emi->dealer->firm_name ?? $emi->dealer->name) : 'Unknown');
                    @endphp
                    <div x-show="searchQuery === '' || '{{ strtolower($entityName) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($emi->loan_name) }}'.includes(searchQuery.toLowerCase())" class="flex flex-col sm:flex-row sm:items-center justify-between bg-rose-50/30 dark:bg-rose-950/10 border border-rose-100 dark:border-rose-900/30 rounded-xl p-4 gap-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-rose-500 mt-0.5">warning</span>
                            <div>
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-50 font-cabinet">{{ $emi->loan_name }}</h4>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    {{ $entityName }} • <span class="font-semibold text-rose-600 dark:text-rose-400">Overdue (Due: {{ $emi->due_date->format('d M, Y') }})</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-6 font-outfit">
                            <div class="text-right">
                                <span class="text-xs text-zinc-400 block">Amount</span>
                                <span class="font-jetbrains font-bold text-rose-600 dark:text-rose-400"><x-currency :amount="$emi->amount" /></span>
                            </div>
                            @can('edit emis')
                            <form action="{{ route('expenses.emis.pay', $emi) }}" method="POST" class="inline" onsubmit="return confirm('Mark this installment as Paid?')">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                    Mark Paid
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                @endforeach

                {{-- Upcoming Receivables --}}
                @foreach($upcomingToReceive as $emi)
                    @php
                        $entityName = $emi->customer ? $emi->customer->name : ($emi->dealer ? ($emi->dealer->firm_name ?? $emi->dealer->name) : 'Unknown');
                        $daysDue = now()->startOfDay()->diffInDays($emi->due_date->startOfDay(), false);
                    @endphp
                    <div x-show="(searchQuery === '' || '{{ strtolower($entityName) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($emi->loan_name) }}'.includes(searchQuery.toLowerCase())) && (timeframe === 'all' || {{ $daysDue }} <= parseInt(timeframe))" class="flex flex-col sm:flex-row sm:items-center justify-between bg-amber-50/20 dark:bg-amber-950/5 border border-amber-100/50 dark:border-amber-900/20 rounded-xl p-4 gap-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-amber-500 mt-0.5">schedule</span>
                            <div>
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-50 font-cabinet">{{ $emi->loan_name }}</h4>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    {{ $entityName }} • <span class="font-semibold text-amber-600 dark:text-amber-400">Due in {{ $daysDue }} days ({{ $emi->due_date->format('d M') }})</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-6 font-outfit">
                            <div class="text-right">
                                <span class="text-xs text-zinc-400 block">Amount</span>
                                <span class="font-jetbrains font-bold text-zinc-900 dark:text-zinc-50"><x-currency :amount="$emi->amount" /></span>
                            </div>
                            @can('edit emis')
                            <form action="{{ route('expenses.emis.pay', $emi) }}" method="POST" class="inline" onsubmit="return confirm('Mark this installment as Paid?')">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                    Mark Paid
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @forelse($toReceiveEmis as $entityKey => $entity)
            <!-- LEVEL 1: ENTITY CARD -->
            <div x-show="searchQuery === '' || '{{ strtolower($entity['name']) }}'.includes(searchQuery.toLowerCase())" class="border border-zinc-200/60 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm overflow-hidden transition-all duration-300">
                <button @click="activeEntity = (activeEntity === '{{ $entityKey }}' ? null : '{{ $entityKey }}'); activeInvoice = null;" 
                        class="w-full flex items-center justify-between p-5 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm tracking-wide bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                            {{ strtoupper(substr($entity['name'], 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="font-cabinet font-bold text-zinc-900 dark:text-zinc-50 text-lg leading-tight">{{ $entity['name'] }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100/50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                                    {{ $entity['type'] }}
                                </span>
                                <span class="text-zinc-400 text-xs">•</span>
                                <span class="text-zinc-500 text-xs font-outfit">{{ $entity['total_installments'] }} Total Installments</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-5">
                        <div class="text-right font-outfit">
                            <span class="text-zinc-400 text-xs block uppercase tracking-wider">Pending Balance</span>
                            <span class="font-jetbrains font-bold text-base {{ $entity['pending_amount'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-500' }}">
                                <x-currency :amount="$entity['pending_amount']" />
                            </span>
                        </div>
                        <span class="material-symbols-rounded text-zinc-400 transition-transform duration-300"
                              :style="activeEntity === '{{ $entityKey }}' ? 'transform: rotate(180deg);' : ''">
                            keyboard_arrow_down
                        </span>
                    </div>
                </button>
                
                <!-- LEVEL 2: INVOICES -->
                <div x-show="activeEntity === '{{ $entityKey }}'" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     class="border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/20 dark:bg-zinc-900/20 p-5 space-y-3"
                     style="display: none;">
                    
                    @foreach($entity['invoices'] as $invoiceKey => $invoice)
                        @php $invoiceHash = md5($entityKey . '_' . $invoiceKey); @endphp
                        <div class="border border-zinc-200/50 dark:border-zinc-800/50 rounded-xl bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                            <button @click.stop="activeInvoice === '{{ $invoiceHash }}' ? activeInvoice = null : activeInvoice = '{{ $invoiceHash }}'"
                                    class="w-full flex items-center justify-between p-4 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors text-left">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-rounded text-zinc-400 text-lg">receipt_long</span>
                                    <div>
                                        <h4 class="font-semibold text-sm text-zinc-800 dark:text-zinc-200 font-cabinet">{{ $invoice['name'] }}</h4>
                                        <div class="flex items-center gap-2 mt-0.5 font-outfit">
                                            <span class="text-zinc-500 text-xs">
                                                {{ count($invoice['installments']) }} Installments
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 font-outfit">
                                    <div class="text-right">
                                        <span class="text-zinc-500 text-xs block">Invoice Total: <x-currency :amount="$invoice['total_amount']" /></span>
                                        @if($invoice['pending_amount'] > 0)
                                            <span class="text-[11px] font-semibold text-rose-500 dark:text-rose-400">
                                                Unpaid: <x-currency :amount="$invoice['pending_amount']" />
                                            </span>
                                        @else
                                            <span class="text-[11px] font-semibold text-emerald-500">Fully Closed</span>
                                        @endif
                                    </div>
                                    <span class="material-symbols-rounded text-zinc-400 text-sm transition-transform duration-300"
                                          :style="activeInvoice === '{{ $invoiceHash }}' ? 'transform: rotate(180deg);' : ''">
                                        expand_more
                                    </span>
                                </div>
                            </button>
                            
                            <!-- LEVEL 3: INSTALLMENTS TABLE -->
                            <div x-show="activeInvoice === '{{ $invoiceHash }}'" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-950/20"
                                 style="display: none;">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm font-outfit">
                                        <thead>
                                            <tr class="bg-zinc-50/50 dark:bg-zinc-800/50 text-[11px] text-zinc-500 uppercase tracking-widest border-b border-zinc-100 dark:border-zinc-800">
                                                <th class="px-6 py-3 font-semibold">Installment ID</th>
                                                <th class="px-6 py-3 font-semibold">Due Date</th>
                                                <th class="px-6 py-3 font-semibold text-right">Amount</th>
                                                <th class="px-6 py-3 font-semibold text-center">Status</th>
                                                <th class="px-6 py-3 font-semibold text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                                            @foreach($invoice['installments'] as $emi)
                                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                                    <td class="px-6 py-3 font-jetbrains text-xs text-zinc-500">
                                                        REF#{{ str_pad($emi->id, 4, '0', STR_PAD_LEFT) }}
                                                    </td>
                                                    <td class="px-6 py-3">
                                                        @php $isOverdue = $emi->status != 'Paid' && $emi->due_date < now(); @endphp
                                                        <span class="font-medium {{ $isOverdue ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-zinc-700 dark:text-zinc-300' }}">
                                                            {{ $emi->due_date->format('d M, Y') }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-3 font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 text-right">
                                                        <x-currency :amount="$emi->amount" />
                                                    </td>
                                                    <td class="px-6 py-3 text-center">
                                                        @php
                                                            $variant = $emi->status == 'Paid' ? 'success' : ($emi->status == 'Overdue' ? 'danger' : 'warning');
                                                        @endphp
                                                        <x-badge :variant="$variant">{{ $emi->status }}</x-badge>
                                                    </td>
                                                    <td class="px-6 py-3 text-center">
                                                        <div class="flex justify-center items-center gap-3">
                                                            @if($emi->status !== 'Paid')
                                                                @can('edit emis')
                                                                <form action="{{ route('expenses.emis.pay', $emi) }}" method="POST" class="inline" onsubmit="return confirm('Mark this EMI installment as Paid?')">
                                                                    @csrf
                                                                    <button type="submit" class="text-emerald-500 hover:text-emerald-700 transition-colors" title="Mark as Paid">
                                                                        <span class="material-symbols-rounded text-lg">check_circle</span>
                                                                    </button>
                                                                </form>

                                                                <form action="{{ route('expenses.emis.close-full', $emi) }}" method="POST" class="inline" onsubmit="return confirm('Close the entire loan group ({{ $emi->loan_name }}) and mark all remaining installments as Paid?')">
                                                                    @csrf
                                                                    <button type="submit" class="text-blue-500 hover:text-blue-700 transition-colors" title="Close Entire Loan (Pay Full)">
                                                                        <span class="material-symbols-rounded text-lg">assignment_turned_in</span>
                                                                    </button>
                                                                </form>
                                                                @endcan
                                                            @endif

                                                            @can('edit emis')
                                                            <a href="{{ route('expenses.emis.edit', $emi) }}" class="text-zinc-400 hover:text-amber-600 transition-colors" title="Edit EMI">
                                                                <span class="material-symbols-rounded text-lg">edit</span>
                                                            </a>
                                                            @endcan

                                                            @can('delete emis')
                                                            <form action="{{ route('expenses.emis.destroy', $emi) }}" method="POST" onsubmit="return confirm('Delete this EMI record?')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="text-zinc-400 hover:text-rose-600 transition-colors" title="Delete">
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
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <x-card>
                <x-empty-state 
                    icon="call_received" 
                    title="No EMIs to receive" 
                    description="You don't have any pending Customer or Dealer EMI receivables." />
            </x-card>
        @endforelse
    </div>

    <!-- PANEL 2: TO PAY -->
    <div x-show="activeTab === 'pay'" x-transition:enter="transition ease-out duration-200" class="space-y-4" style="display: none;">
        
        <!-- Alerts for To Pay -->
        @if(count($overdueToPay) > 0 || count($upcomingToPay) > 0)
        <div class="bg-gradient-to-br from-amber-50/20 via-white to-zinc-50/20 dark:from-amber-950/10 dark:via-zinc-900 dark:to-zinc-950/20 border border-amber-200/60 dark:border-amber-900/50 rounded-2xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-amber-100 dark:border-amber-900/30 pb-3">
                <h3 class="text-sm font-black text-amber-800 dark:text-amber-400 font-cabinet flex items-center gap-2">
                    <span class="material-symbols-rounded text-lg">notifications_active</span>
                    Action Required: Early Warning Alerts ({{ count($overdueToPay) + count($upcomingToPay) }})
                </h3>
            </div>
            
            <div class="grid grid-cols-1 gap-3">
                {{-- Overdue Payables --}}
                @foreach($overdueToPay as $emi)
                    @php
                        $entityName = $emi->vendor ? ($emi->vendor->firm_name ?? $emi->vendor->name) : ($emi->bank_name ?? 'Bank Loan');
                    @endphp
                    <div x-show="searchQuery === '' || '{{ strtolower($entityName) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($emi->loan_name) }}'.includes(searchQuery.toLowerCase())" class="flex flex-col sm:flex-row sm:items-center justify-between bg-rose-50/30 dark:bg-rose-950/10 border border-rose-100 dark:border-rose-900/30 rounded-xl p-4 gap-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-rose-500 mt-0.5">warning</span>
                            <div>
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-50 font-cabinet">{{ $emi->loan_name }}</h4>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    {{ $entityName }} • <span class="font-semibold text-rose-600 dark:text-rose-400">Overdue (Due: {{ $emi->due_date->format('d M, Y') }})</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-6 font-outfit">
                            <div class="text-right">
                                <span class="text-xs text-zinc-400 block">Amount</span>
                                <span class="font-jetbrains font-bold text-rose-600 dark:text-rose-400"><x-currency :amount="$emi->amount" /></span>
                            </div>
                            @can('edit emis')
                            <form action="{{ route('expenses.emis.pay', $emi) }}" method="POST" class="inline" onsubmit="return confirm('Mark this installment as Paid?')">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                    Mark Paid
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                @endforeach

                {{-- Upcoming Payables --}}
                @foreach($upcomingToPay as $emi)
                    @php
                        $entityName = $emi->vendor ? ($emi->vendor->firm_name ?? $emi->vendor->name) : ($emi->bank_name ?? 'Bank Loan');
                        $daysDue = now()->startOfDay()->diffInDays($emi->due_date->startOfDay(), false);
                    @endphp
                    <div x-show="(searchQuery === '' || '{{ strtolower($entityName) }}'.includes(searchQuery.toLowerCase()) || '{{ strtolower($emi->loan_name) }}'.includes(searchQuery.toLowerCase())) && (timeframe === 'all' || {{ $daysDue }} <= parseInt(timeframe))" class="flex flex-col sm:flex-row sm:items-center justify-between bg-amber-50/20 dark:bg-amber-950/5 border border-amber-100/50 dark:border-amber-900/20 rounded-xl p-4 gap-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-amber-500 mt-0.5">schedule</span>
                            <div>
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-50 font-cabinet">{{ $emi->loan_name }}</h4>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    {{ $entityName }} • <span class="font-semibold text-amber-600 dark:text-amber-400">Due in {{ $daysDue }} days ({{ $emi->due_date->format('d M') }})</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-6 font-outfit">
                            <div class="text-right">
                                <span class="text-xs text-zinc-400 block">Amount</span>
                                <span class="font-jetbrains font-bold text-zinc-900 dark:text-zinc-50"><x-currency :amount="$emi->amount" /></span>
                            </div>
                            @can('edit emis')
                            <form action="{{ route('expenses.emis.pay', $emi) }}" method="POST" class="inline" onsubmit="return confirm('Mark this installment as Paid?')">
                                @csrf
                                <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                    Mark Paid
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        @forelse($toPayEmis as $entityKey => $entity)
            <!-- LEVEL 1: ENTITY CARD -->
            <div x-show="searchQuery === '' || '{{ strtolower($entity['name']) }}'.includes(searchQuery.toLowerCase())" class="border border-zinc-200/60 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm overflow-hidden transition-all duration-300">
                <button @click="activeEntity = (activeEntity === '{{ $entityKey }}' ? null : '{{ $entityKey }}'); activeInvoice = null;" 
                        class="w-full flex items-center justify-between p-5 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm tracking-wide bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                            {{ strtoupper(substr($entity['name'], 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="font-cabinet font-bold text-zinc-900 dark:text-zinc-50 text-lg leading-tight">{{ $entity['name'] }}</h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-100/50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400">
                                    {{ $entity['type'] }}
                                </span>
                                <span class="text-zinc-400 text-xs">•</span>
                                <span class="text-zinc-500 text-xs font-outfit">{{ $entity['total_installments'] }} Total Installments</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-5">
                        <div class="text-right font-outfit">
                            <span class="text-zinc-400 text-xs block uppercase tracking-wider">Pending Balance</span>
                            <span class="font-jetbrains font-bold text-base {{ $entity['pending_amount'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-500' }}">
                                <x-currency :amount="$entity['pending_amount']" />
                            </span>
                        </div>
                        <span class="material-symbols-rounded text-zinc-400 transition-transform duration-300"
                              :style="activeEntity === '{{ $entityKey }}' ? 'transform: rotate(180deg);' : ''">
                            keyboard_arrow_down
                        </span>
                    </div>
                </button>
                
                <!-- LEVEL 2: INVOICES -->
                <div x-show="activeEntity === '{{ $entityKey }}'" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     class="border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/20 dark:bg-zinc-900/20 p-5 space-y-3"
                     style="display: none;">
                    
                    @foreach($entity['invoices'] as $invoiceKey => $invoice)
                        @php $invoiceHash = md5($entityKey . '_' . $invoiceKey); @endphp
                        <div class="border border-zinc-200/50 dark:border-zinc-800/50 rounded-xl bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                            <button @click.stop="activeInvoice === '{{ $invoiceHash }}' ? activeInvoice = null : activeInvoice = '{{ $invoiceHash }}'"
                                    class="w-full flex items-center justify-between p-4 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors text-left">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-rounded text-zinc-400 text-lg">receipt_long</span>
                                    <div>
                                        <h4 class="font-semibold text-sm text-zinc-800 dark:text-zinc-200 font-cabinet">{{ $invoice['name'] }}</h4>
                                        <div class="flex items-center gap-2 mt-0.5 font-outfit">
                                            <span class="text-zinc-500 text-xs">
                                                {{ count($invoice['installments']) }} Installments
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 font-outfit">
                                    <div class="text-right">
                                        <span class="text-zinc-500 text-xs block">Invoice Total: <x-currency :amount="$invoice['total_amount']" /></span>
                                        @if($invoice['pending_amount'] > 0)
                                            <span class="text-[11px] font-semibold text-rose-500 dark:text-rose-400">
                                                Unpaid: <x-currency :amount="$invoice['pending_amount']" />
                                            </span>
                                        @else
                                            <span class="text-[11px] font-semibold text-emerald-500">Fully Closed</span>
                                        @endif
                                    </div>
                                    <span class="material-symbols-rounded text-zinc-400 text-sm transition-transform duration-300"
                                          :style="activeInvoice === '{{ $invoiceHash }}' ? 'transform: rotate(180deg);' : ''">
                                        expand_more
                                    </span>
                                </div>
                            </button>
                            
                            <!-- LEVEL 3: INSTALLMENTS TABLE -->
                            <div x-show="activeInvoice === '{{ $invoiceHash }}'" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-950/20"
                                 style="display: none;">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm font-outfit">
                                        <thead>
                                            <tr class="bg-zinc-50/50 dark:bg-zinc-800/50 text-[11px] text-zinc-500 uppercase tracking-widest border-b border-zinc-100 dark:border-zinc-800">
                                                <th class="px-6 py-3 font-semibold">Installment ID</th>
                                                <th class="px-6 py-3 font-semibold">Due Date</th>
                                                <th class="px-6 py-3 font-semibold text-right">Amount</th>
                                                <th class="px-6 py-3 font-semibold text-center">Status</th>
                                                <th class="px-6 py-3 font-semibold text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                                            @foreach($invoice['installments'] as $emi)
                                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                                    <td class="px-6 py-3 font-jetbrains text-xs text-zinc-500">
                                                        REF#{{ str_pad($emi->id, 4, '0', STR_PAD_LEFT) }}
                                                    </td>
                                                    <td class="px-6 py-3">
                                                        @php $isOverdue = $emi->status != 'Paid' && $emi->due_date < now(); @endphp
                                                        <span class="font-medium {{ $isOverdue ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-zinc-700 dark:text-zinc-300' }}">
                                                            {{ $emi->due_date->format('d M, Y') }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-3 font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 text-right">
                                                        <x-currency :amount="$emi->amount" />
                                                    </td>
                                                    <td class="px-6 py-3 text-center">
                                                        @php
                                                            $variant = $emi->status == 'Paid' ? 'success' : ($emi->status == 'Overdue' ? 'danger' : 'warning');
                                                        @endphp
                                                        <x-badge :variant="$variant">{{ $emi->status }}</x-badge>
                                                    </td>
                                                    <td class="px-6 py-3 text-center">
                                                        <div class="flex justify-center items-center gap-3">
                                                            @if($emi->status !== 'Paid')
                                                                @can('edit emis')
                                                                <form action="{{ route('expenses.emis.pay', $emi) }}" method="POST" class="inline" onsubmit="return confirm('Mark this EMI installment as Paid?')">
                                                                    @csrf
                                                                    <button type="submit" class="text-emerald-500 hover:text-emerald-700 transition-colors" title="Mark as Paid">
                                                                        <span class="material-symbols-rounded text-lg">check_circle</span>
                                                                    </button>
                                                                </form>

                                                                <form action="{{ route('expenses.emis.close-full', $emi) }}" method="POST" class="inline" onsubmit="return confirm('Close the entire loan group ({{ $emi->loan_name }}) and mark all remaining installments as Paid?')">
                                                                    @csrf
                                                                    <button type="submit" class="text-blue-500 hover:text-blue-700 transition-colors" title="Close Entire Loan (Pay Full)">
                                                                        <span class="material-symbols-rounded text-lg">assignment_turned_in</span>
                                                                    </button>
                                                                </form>
                                                                @endcan
                                                            @endif

                                                            @can('edit emis')
                                                            <a href="{{ route('expenses.emis.edit', $emi) }}" class="text-zinc-400 hover:text-amber-600 transition-colors" title="Edit EMI">
                                                                <span class="material-symbols-rounded text-lg">edit</span>
                                                            </a>
                                                            @endcan

                                                            @can('delete emis')
                                                            <form action="{{ route('expenses.emis.destroy', $emi) }}" method="POST" onsubmit="return confirm('Delete this EMI record?')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="text-zinc-400 hover:text-rose-600 transition-colors" title="Delete">
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
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <x-card>
                <x-empty-state 
                    icon="call_made" 
                    title="No EMIs to pay" 
                    description="You don't have any pending Vendor or Bank Loan EMI payables." />
            </x-card>
        @endforelse
    </div>

    <!-- PANEL 3: GENERAL EXPENSES -->
    <div x-show="activeTab === 'expenses'" x-transition:enter="transition ease-out duration-200" class="space-y-4" style="display: none;">
        <x-card>
            <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50 flex justify-between items-center">
                <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50 font-semibold">General Expense Ledger</h2>
                @can('create expenses')
                <x-button variant="primary" x-data x-on:click="$dispatch('open-modal', 'add-expense')" icon="add">
                    Record Expense
                </x-button>
                @endcan
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
                            <div class="flex items-center gap-3">
                                @can('edit expenses')
                                <button type="button" @click="
                                    editId = {{ $e->id }};
                                    editCategory = '{{ $e->category }}';
                                    editDate = '{{ $e->date->format('Y-m-d') }}';
                                    editDescription = '{{ addslashes($e->description) }}';
                                    editAmount = '{{ $e->amount }}';
                                    editAction = '{{ route('expenses.update', $e) }}';
                                    $dispatch('open-modal', 'edit-expense');
                                " class="text-zinc-400 hover:text-amber-600 transition-colors" title="Edit">
                                    <span class="material-symbols-rounded text-lg">edit</span>
                                </button>
                                @endcan

                                @can('delete expenses')
                                <form action="{{ route('expenses.destroy', $e) }}" method="POST" onsubmit="return confirm('Delete this expense entry?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-zinc-400 hover:text-rose-600 transition-colors" title="Delete">
                                        <span class="material-symbols-rounded text-lg">delete</span>
                                    </button>
                                </form>
                                @endcan
                            </div>
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
                        {{ $expenses->appends(['tab' => 'expenses'])->links() }}
                    </x-slot:pagination>
                @endif
            </x-data-table>
        </x-card>
    </div>
</div>

{{-- Add Expense Modal --}}
<x-modal name="add-expense" title="Record Expense" subtitle="Log operational expenditures" icon="receipt_long" maxWidth="md" :show="$errors->any()">
    <form id="add-expense-form" action="{{ route('expenses.store') }}" method="POST">
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
            <x-button type="submit" form="add-expense-form" variant="primary" icon="check">Log Expense</x-button>
        </x-slot:footer>
    </form>
</x-modal>

{{-- Edit Expense Modal --}}
<x-modal name="edit-expense" title="Edit Expense" subtitle="Update operational expenditure details" icon="edit" maxWidth="md">
    <form :action="editAction" method="POST">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
            <x-form.select name="category" label="Category" required x-model="editCategory">
                @foreach(['Fuel','Salary','Transport','Utility','Misc','Purchase'] as $c)
                    <option value="{{ $c }}">{{ $c }}</option>
                @endforeach
            </x-form.select>
            <x-form.input type="date" name="date" label="Date" required x-model="editDate" />
        </div>

        <div class="mb-4">
            <x-form.input name="description" label="Description" required placeholder="What was this expense for?" x-model="editDescription" />
        </div>

        <div class="mb-6">
            <x-form.input type="number" name="amount" label="Amount (Rs)" required step="0.01" min="0.01" placeholder="0.00" class="text-xl font-bold" x-model="editAmount" />
        </div>

        <x-slot:footer>
            <x-button type="button" variant="outline" x-on:click="show = false">Cancel</x-button>
            <x-button type="submit" variant="primary" icon="check">Save Changes</x-button>
        </x-slot:footer>
    </form>
</x-modal>
@endsection
