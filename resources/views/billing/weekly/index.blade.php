@extends('layouts.app')
@section('title', 'Dealer Billing System')

@section('content')
<div class="animate-fade-in" x-data="{ activeTab: 'invoices', payBillId: null, payPart: '', payAmount: 0, payCashAmount: 0, payBankAmount: 0, payBankTransferType: '' }">
    <x-page-header title="Dealer Billing & Purchases" subtitle="Record daily purchases, calculate weekly cycles, manage Monday/Friday split payments, and view ledger logs">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('billing.weekly.dealer-invoice') }}" icon="receipt_long">
                Dealer Invoice
            </x-button>
            <x-button variant="outline" href="{{ route('billing.weekly.export') }}" icon="download">
                Export Log
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Performance Stats Header --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-stat-card 
            label="Weekly Invoices" 
            value="{{ $bills->total() }}" 
            icon="receipt_long" 
            color="indigo" />
        <x-stat-card 
            label="Outstanding Dues" 
            value="Rs {{ number_format($outstandingDuesTotal, 0) }}" 
            icon="pending_actions" 
            color="amber" />
        <div class="rounded-2xl bg-gradient-to-br from-indigo-500 to-indigo-600 dark:from-indigo-600 dark:to-indigo-800 p-6 shadow-sm text-white flex items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-indigo-500/20">
            <div>
                <p class="font-outfit text-sm font-medium text-indigo-100">Total Revenue</p>
                <p class="font-jetbrains mt-2 text-3xl font-bold tracking-tight">Rs {{ number_format($paidRevenueTotal, 0) }}</p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                <span class="material-symbols-rounded text-2xl">account_balance_wallet</span>
            </div>
        </div>
    </div>

    {{-- Tabs Navigation --}}
    <div class="border-b border-zinc-200 dark:border-zinc-800 mb-8 flex flex-wrap gap-2">
        <button @click="activeTab = 'invoices'" :class="activeTab === 'invoices' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-white'" class="px-5 py-3 text-sm font-bold border-b-2 transition-colors duration-200 focus:outline-none flex items-center gap-2">
            <span class="material-symbols-rounded text-lg">receipt_long</span>
            Weekly Invoices
        </button>
        <!-- <button @click="activeTab = 'record_purchase'" :class="activeTab === 'record_purchase' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-white'" class="px-5 py-3 text-sm font-bold border-b-2 transition-colors duration-200 focus:outline-none flex items-center gap-2">
            <span class="material-symbols-rounded text-lg">add_circle</span>
            Record Daily Purchase
        </button> -->
        <button @click="activeTab = 'purchase_log'" :class="activeTab === 'purchase_log' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-white'" class="px-5 py-3 text-sm font-bold border-b-2 transition-colors duration-200 focus:outline-none flex items-center gap-2">
            <span class="material-symbols-rounded text-lg">history</span>
            Purchase Log
        </button>
        <button @click="activeTab = 'generate_invoice'" :class="activeTab === 'generate_invoice' ? 'border-indigo-600 text-indigo-600 dark:border-indigo-400 dark:text-indigo-400' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-white'" class="px-5 py-3 text-sm font-bold border-b-2 transition-colors duration-200 focus:outline-none flex items-center gap-2">
            <span class="material-symbols-rounded text-lg">calculate</span>
            Generate Weekly Bill
        </button>
    </div>

    {{-- Tab 1: Weekly Invoices --}}
    <div x-show="activeTab === 'invoices'" class="space-y-6">
        <x-card>
            <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">Weekly Invoice Log</h2>
                <form method="GET" class="relative max-w-sm w-full sm:w-auto">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-400">
                        <span class="material-symbols-rounded text-xl">search</span>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" class="bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 p-2.5 transition-colors font-outfit" placeholder="Search invoice or dealer...">
                </form>
            </div>

            <x-data-table :headers="['Inv No', 'Dealer', 'Period', 'Outstanding & Weekly Payments', 'Total Amount', 'Split Payments Status', 'Payment Summary', 'Status', 'Actions']">
                @forelse($bills as $bill)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="font-jetbrains text-xs font-bold text-zinc-500">
                                #{{ $bill->invoice_no ?? $bill->invoice_number }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <x-avatar :name="$bill->dealer->firm_name ?? 'd'" size="sm" />
                                <div>
                                    <p class="font-cabinet font-bold text-zinc-900 dark:text-zinc-100">{{ $bill->dealer->firm_name ?? '-' }}</p>
                                    <p class="font-outfit text-xs text-zinc-500">{{ $bill->dealer->route ?? 'General Route' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $bill->period_start->format('d M') }} - {{ $bill->period_end->format('d M') }}</p>
                            <p class="text-[10px] text-zinc-500 font-medium uppercase tracking-wider">{{ $bill->period_end->format('Y') }}</p>
                        </td>
                        <td class="px-6 py-4 text-xs font-medium text-zinc-600 dark:text-zinc-400">
                            <div class="space-y-1">
                                <p>Prev Bal: <span class="font-jetbrains font-bold">₹{{ number_format($bill->previous_outstanding, 2) }}</span></p>
                                <p>Payments: <span class="font-jetbrains font-bold">₹{{ number_format($bill->payments_during_week, 2) }}</span></p>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-jetbrains font-medium text-indigo-600 dark:text-indigo-400">
                            <div class="flex flex-col">
                                <span class="font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 text-sm">Rs {{ number_format($bill->net_amount, 2) }}</span>
                                <span class="text-[9px] text-indigo-600 font-bold uppercase tracking-tighter">Incl. GST</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-xs">
                            <div class="space-y-2">
                                {{-- Monday Split Part --}}
                                <div class="flex items-center justify-between gap-4">
                                    <span>Mon (50%): <span class="font-jetbrains font-bold">₹{{ number_format($bill->monday_payment_amount, 2) }}</span></span>
                                    @if($bill->monday_payment_status === 'Paid')
                                        <x-badge variant="success">PAID</x-badge>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <x-badge variant="warning">PENDING</x-badge>
                                            <button @click="payBillId = {{ $bill->id }}; payPart = 'monday'; payAmount = {{ $bill->monday_payment_amount }}; payCashAmount = {{ $bill->monday_payment_amount }}; payBankAmount = 0; payBankTransferType = '';" class="bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950 dark:hover:bg-indigo-900 text-indigo-700 dark:text-indigo-300 text-[10px] font-bold px-2 py-1 rounded transition-all">Pay</button>
                                        </div>
                                    @endif
                                </div>
                                {{-- Friday Split Part --}}
                                <div class="flex items-center justify-between gap-4">
                                    <span>Fri (50%): <span class="font-jetbrains font-bold">₹{{ number_format($bill->friday_payment_amount, 2) }}</span></span>
                                    @if($bill->friday_payment_status === 'Paid')
                                        <x-badge variant="success">PAID</x-badge>
                                    @else
                                        <div class="flex items-center gap-2">
                                            <x-badge variant="warning">PENDING</x-badge>
                                            <button @click="payBillId = {{ $bill->id }}; payPart = 'friday'; payAmount = {{ $bill->friday_payment_amount }}; payCashAmount = {{ $bill->friday_payment_amount }}; payBankAmount = 0; payBankTransferType = '';" class="bg-indigo-50 hover:bg-indigo-100 dark:bg-indigo-950 dark:hover:bg-indigo-900 text-indigo-700 dark:text-indigo-300 text-[10px] font-bold px-2 py-1 rounded transition-all">Pay</button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        {{-- Payment Summary Column --}}
                        @php
                            $periodPaid = \App\Models\DealerPayment::where('dealer_id', $bill->dealer_id)
                                ->whereBetween('date', [
                                    $bill->period_start->format('Y-m-d'),
                                    $bill->period_end->format('Y-m-d'),
                                ])->sum('amount');
                            $periodRemaining = max(0, (float)$bill->net_amount - (float)$periodPaid);
                        @endphp
                        <td class="px-6 py-4">
                            <div class="space-y-1 text-xs">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                    <span class="text-zinc-500">Paid:</span>
                                    <span class="font-jetbrains font-bold text-emerald-600">₹{{ number_format($periodPaid, 0) }}</span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    @if($periodRemaining <= 0)
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></span>
                                        <span class="font-bold text-emerald-600 text-[10px] uppercase tracking-wider">✅ Fully Paid</span>
                                    @else
                                        <span class="w-2 h-2 rounded-full bg-rose-500 flex-shrink-0"></span>
                                        <span class="text-zinc-500">Due:</span>
                                        <span class="font-jetbrains font-bold text-rose-600">₹{{ number_format($periodRemaining, 0) }}</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            @php
                                $statusMap = [
                                    'Generated' => 'info',
                                    'Pending'   => 'warning',
                                    'Paid'      => 'success',
                                ];
                                $st = $statusMap[$bill->status] ?? 'warning';
                            @endphp
                            <x-badge :variant="$st">{{ strtoupper($bill->status) }}</x-badge>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('billing.weekly.show', $bill) }}" target="_blank" class="text-zinc-400 hover:text-indigo-600 transition-colors" title="Print Invoice">
                                    <span class="material-symbols-rounded text-lg">print</span>
                                </a>
                                <a href="{{ route('billing.weekly.pdf', $bill) }}" class="text-zinc-400 hover:text-rose-600 transition-colors" title="Download PDF">
                                    <span class="material-symbols-rounded text-lg">picture_as_pdf</span>
                                </a>
                                <a href="{{ route('billing.weekly.whatsapp', $bill) }}" target="_blank" class="text-emerald-500 hover:text-emerald-600 transition-colors" title="WhatsApp Message">
                                    <span class="material-symbols-rounded text-lg">chat</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <x-slot:empty>
                        <x-empty-state 
                            icon="receipt_long" 
                            title="No Weekly Bills Found" 
                            description="Start generating invoices for your dealers." />
                    </x-slot:empty>
                @endforelse

                @if($bills->hasPages())
                    <x-slot:pagination>
                        {{ $bills->appends(request()->except('bills_page'))->links() }}
                    </x-slot:pagination>
                @endif
            </x-data-table>
        </x-card>
    </div>

    {{-- Tab 2: Record Daily Purchase --}}
    <div x-show="activeTab === 'record_purchase'">
        <x-card class="max-w-4xl mx-auto">
            <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
                <h2 class="text-lg font-extrabold text-zinc-900 dark:text-zinc-50 font-cabinet">Record Dealer Daily Purchase</h2>
                <p class="text-xs text-zinc-500 mt-1">Record daily purchases by a dealer. Stock will be reduced immediately upon saving.</p>
            </div>

            <form action="{{ route('billing.weekly.purchase.store') }}" method="POST" id="dealer-purchase-form">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <x-form.select name="dealer_id" label="Dealer" required>
                        <option value="">Select dealer...</option>
                        @foreach($dealers as $d)
                            <option value="{{ $d->id }}">{{ $d->firm_name }} ({{ $d->route }})</option>
                        @endforeach
                    </x-form.select>
                    <x-form.input type="date" name="date" label="Purchase Date" required value="{{ today()->format('Y-m-d') }}" />
                </div>

                <div class="mb-6">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit">Items & Weights</label>
                        <x-button type="button" variant="outline" size="sm" icon="add" onclick="addPurchaseRow()">Add Item</x-button>
                    </div>
                    
                    <div class="bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                        <table class="w-full text-sm text-left text-zinc-600 dark:text-zinc-400 font-outfit" id="purchase-items-table">
                            <thead class="text-xs text-zinc-500 dark:text-zinc-400 uppercase bg-zinc-100/50 dark:bg-zinc-800 font-cabinet">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Item Name</th>
                                    <th class="px-4 py-3 font-semibold text-center w-28">Qty/kg</th>
                                    <th class="px-4 py-3 font-semibold text-right w-36">Rate/kg</th>
                                    <th class="px-4 py-3 font-semibold text-right w-36">Subtotal</th>
                                    <th class="px-4 py-3 text-center w-12"></th>
                                </tr>
                            </thead>
                            <tbody id="purchase-items-body" class="divide-y divide-zinc-200 dark:divide-zinc-700">
                                <tr class="purchase-item-row">
                                    <td class="p-2">
                                        <select name="items[0][name]" required class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2 transition-colors">
                                            @foreach($items as $item)
                                                <option value="{{ $item->name }}" {{ $item->name === 'Live Broiler Birds' ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-2">
                                        <input type="number" name="items[0][qty]" step="0.01" required placeholder="0.00" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2 text-center p-qty" oninput="recalcPurchase()">
                                    </td>
                                    <td class="p-2">
                                        <input type="number" name="items[0][rate]" step="0.01" required placeholder="0.00" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2 text-right p-rate" oninput="recalcPurchase()">
                                    </td>
                                    <td class="p-2 text-right font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 p-row-total">
                                        ₹0.00
                                    </td>
                                    <td class="p-2 text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-end">
                    <div class="bg-zinc-50 dark:bg-zinc-800/30 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5">
                        <label class="block text-sm font-bold text-zinc-700 dark:text-zinc-300 mb-2 font-outfit">GST (18%)</label>
                        <div id="p-display-tax" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-lg p-3 font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 text-sm">₹0.00</div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-indigo-600 to-purple-700 rounded-xl p-6 shadow-xl shadow-indigo-500/20 text-white flex flex-col sm:flex-row items-center justify-between gap-6">
                        <div class="flex flex-col">
                            <span class="text-indigo-100 text-xs font-bold uppercase tracking-wider block mb-1 font-outfit">Grand Total</span>
                            <span id="p-display-total" class="text-3xl font-black font-jetbrains tracking-tight">₹0.00</span>
                        </div>
                        <button type="submit" class="w-full sm:w-auto bg-white text-indigo-700 hover:bg-indigo-50 px-6 py-3 rounded-lg font-bold flex items-center justify-center gap-2 transition-transform active:scale-95 shadow-md">
                            <span class="material-symbols-rounded">save</span>
                            Record Purchase
                        </button>
                    </div>
                </div>
            </form>
        </x-card>
    </div>

    {{-- Tab 3: Purchase Log --}}
    <div x-show="activeTab === 'purchase_log'">
        <x-card>
            <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50 flex items-center justify-between">
                <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">Daily Purchase Log</h2>
            </div>

            <x-data-table :headers="['Date', 'Invoice No', 'Dealer', 'Product Breakdown', 'Total Qty', 'Net Amount', 'Weekly Invoice Status']">
                @forelse($purchases as $pur)
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                        <td class="px-6 py-4">
                            <p class="font-medium text-zinc-900 dark:text-zinc-100">{{ $pur->date->format('d M Y') }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-jetbrains text-xs font-bold text-zinc-500">
                                #{{ $pur->invoice_no }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <x-avatar :name="$pur->dealer->firm_name ?? 'd'" size="sm" />
                                <div>
                                    <p class="font-cabinet font-bold text-zinc-900 dark:text-zinc-100">{{ $pur->dealer->firm_name ?? '-' }}</p>
                                    <p class="font-outfit text-xs text-zinc-500">{{ $pur->dealer->route ?? 'General Route' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-wrap items-center gap-2 max-w-[200px]">
                                @foreach(explode(',', $pur->items_description) as $item)
                                    @if(trim($item))
                                        <x-badge variant="zinc">{{ trim($item) }}</x-badge>
                                    @endif
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-jetbrains font-bold text-zinc-900 dark:text-zinc-100">{{ number_format($pur->quantity_kg, 2) }}</span>
                            <span class="text-[10px] text-zinc-500 font-medium uppercase ml-0.5">kg</span>
                        </td>
                        <td class="px-6 py-4 font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 text-right">
                            ₹{{ number_format($pur->net_amount, 2) }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($pur->weekly_bill_id)
                                <a href="{{ route('billing.weekly.show', $pur->weekly_bill_id) }}" target="_blank" class="text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-bold hover:underline flex items-center justify-center gap-1">
                                    <span class="material-symbols-rounded text-sm">link</span>
                                    #{{ $pur->weeklyBill->invoice_no ?? 'Weekly Bill' }}
                                </a>
                            @else
                                <x-badge variant="warning">Not Invoiced</x-badge>
                            @endif
                        </td>
                    </tr>
                @empty
                    <x-slot:empty>
                        <x-empty-state 
                            icon="history" 
                            title="No Purchase Logs Found" 
                            description="Dealer daily purchases will appear here." />
                    </x-slot:empty>
                @endforelse

                @if($purchases->hasPages())
                    <x-slot:pagination>
                        {{ $purchases->appends(request()->except('purchases_page'))->links() }}
                    </x-slot:pagination>
                @endif
            </x-data-table>
        </x-card>
    </div>

    {{-- Tab 4: Generate Weekly Invoice --}}
    <div x-show="activeTab === 'generate_invoice'">
        <x-card class="max-w-2xl mx-auto" x-data="{ previewLoaded: false, prevOutstanding: 0, totalPurchases: 0, totalPayments: 0, netInvoice: 0, purchasesCount: 0 }"
            @preview-update.window="
                previewLoaded = true;
                prevOutstanding = $event.detail.prevOutstanding;
                totalPurchases = $event.detail.totalPurchases;
                totalPayments = $event.detail.totalPayments;
                netInvoice = $event.detail.netInvoice;
                purchasesCount = $event.detail.purchasesCount;
            ">
            <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
                <h2 class="text-lg font-extrabold text-zinc-900 dark:text-zinc-50 font-cabinet">Generate Weekly Invoice</h2>
                <p class="text-xs text-zinc-500 mt-1">Select a dealer and period. Compile purchases and payments into a weekly invoice with split payments.</p>
            </div>

            <form action="{{ route('billing.weekly.generate') }}" method="POST" id="generate-weekly-bill-form">
                @csrf
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-2">Dealer <span class="text-red-500">*</span></label>
                        <select name="dealer_id" id="gen-dealer-id" required class="appearance-none block w-full pl-3 pr-10 py-2.5 min-h-[44px] text-base border border-zinc-200 dark:border-zinc-700 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 sm:text-sm rounded-xl bg-white/30 dark:bg-zinc-900/30 text-zinc-900 dark:text-zinc-100 transition-all">
                            <option value="">Select dealer...</option>
                            @foreach($dealers as $d)
                                <option value="{{ $d->id }}">{{ $d->firm_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-2">From Date <span class="text-red-500">*</span></label>
                            <input type="date" name="period_start" id="gen-period-start" required class="block w-full bg-white/30 dark:bg-zinc-900/30 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 p-3 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-2">To Date <span class="text-red-500">*</span></label>
                            <input type="date" name="period_end" id="gen-period-end" required class="block w-full bg-white/30 dark:bg-zinc-900/30 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 p-3 transition-all">
                        </div>
                    </div>
                </div>

                <div class="mb-6 flex gap-4">
                    <button type="button" onclick="previewWeeklyBilling(this)" class="w-full bg-zinc-800 hover:bg-zinc-700 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-rounded">analytics</span>
                        Calculate & Preview Bill
                    </button>
                </div>

                {{-- Preview Section --}}
                <div x-show="previewLoaded" x-transition class="bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 mb-6 space-y-4">
                    <h3 class="text-sm font-bold text-zinc-700 dark:text-zinc-300 font-cabinet uppercase tracking-wider">Calculation Details</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm font-outfit text-zinc-600 dark:text-zinc-400">
                        <div>Previous Outstanding Balance:</div>
                        <div class="text-right font-jetbrains font-bold text-zinc-900 dark:text-white" x-text="'₹' + prevOutstanding.toLocaleString('en-IN', { minimumFractionDigits: 2 })"></div>
                        
                        <div>Current Week's Purchases (<span x-text="purchasesCount"></span> logs):</div>
                        <div class="text-right font-jetbrains font-bold text-zinc-900 dark:text-white text-emerald-600 dark:text-emerald-400" x-text="'+ ₹' + totalPurchases.toLocaleString('en-IN', { minimumFractionDigits: 2 })"></div>
                        
                        <div>Current Week's Payments:</div>
                        <div class="text-right font-jetbrains font-bold text-zinc-900 dark:text-white text-rose-600 dark:text-rose-400" x-text="'- ₹' + totalPayments.toLocaleString('en-IN', { minimumFractionDigits: 2 })"></div>
                        
                        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2 font-bold text-zinc-800 dark:text-zinc-200">Net Weekly Invoice Amount:</div>
                        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2 text-right font-jetbrains font-black text-indigo-600 dark:text-indigo-400 text-lg" x-text="'₹' + netInvoice.toLocaleString('en-IN', { minimumFractionDigits: 2 })"></div>
                    </div>

                    {{-- Split Schedule Preview --}}
                    <div class="bg-white dark:bg-zinc-900/50 border border-zinc-200 dark:border-zinc-700 rounded-lg p-3 space-y-2">
                        <p class="text-xs font-bold text-zinc-500 uppercase tracking-wide">Split Payment Schedule</p>
                        <div class="flex justify-between text-xs font-outfit text-zinc-600 dark:text-zinc-400">
                            <span>Monday Part (50%):</span>
                            <span class="font-jetbrains font-bold text-zinc-900 dark:text-white" x-text="'₹' + (netInvoice/2).toLocaleString('en-IN', { minimumFractionDigits: 2 })"></span>
                        </div>
                        <div class="flex justify-between text-xs font-outfit text-zinc-600 dark:text-zinc-400">
                            <span>Friday Part (50%):</span>
                            <span class="font-jetbrains font-bold text-zinc-900 dark:text-white" x-text="'₹' + (netInvoice - netInvoice/2).toLocaleString('en-IN', { minimumFractionDigits: 2 })"></span>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg shadow-indigo-500/20 transition-transform active:scale-95 flex items-center justify-center gap-2">
                        <span class="material-symbols-rounded">verified</span>
                        Confirm & Generate Weekly Bill
                    </button>
                </div>
            </form>
        </x-card>
    </div>

    {{-- Split Payment Modal --}}
    <div x-show="payBillId !== null" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-900/60 backdrop-blur-sm" x-transition>
        <x-card class="w-full max-w-md shadow-2xl" @click.away="payBillId = null">
            <div class="flex justify-between items-center pb-4 border-b border-zinc-200 dark:border-zinc-800 mb-6">
                <h3 class="text-lg font-bold text-zinc-950 dark:text-zinc-50 font-cabinet">Record Split Payment</h3>
                <button @click="payBillId = null" class="text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>

            <form :action="`{{ url('billing/weekly') }}/${payBillId}/pay-split/${payPart}`" method="POST">
                @csrf
                <div class="space-y-4 mb-6">
                    <div class="bg-zinc-50 dark:bg-zinc-800 rounded-lg p-3 text-center">
                        <span class="text-xs text-zinc-500 uppercase font-bold block mb-1">Total Expected Amount</span>
                        <span class="text-2xl font-black font-jetbrains text-indigo-600 dark:text-indigo-400" x-text="'₹' + payAmount.toLocaleString('en-IN', { minimumFractionDigits: 2 })"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Cash Amount (Rs) <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.01" min="0" name="cash_amount" required x-model.number="payCashAmount" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains text-lg font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Bank Amount (Rs) <span class="text-rose-500">*</span></label>
                            <input type="number" step="0.01" min="0" name="bank_amount" required x-model.number="payBankAmount" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm font-jetbrains text-lg font-bold">
                        </div>
                    </div>

                    <div class="bg-indigo-50 dark:bg-indigo-950/20 rounded-lg p-2.5 text-center border border-indigo-100 dark:border-indigo-900">
                        <span class="text-[10px] text-zinc-500 uppercase font-bold block">Total Entered</span>
                        <span class="text-lg font-black font-jetbrains text-emerald-600 dark:text-emerald-400" x-text="'₹' + (payCashAmount + payBankAmount).toLocaleString('en-IN', { minimumFractionDigits: 2 })"></span>
                        <p class="text-[10px] mt-1 font-bold text-rose-500" x-show="Math.abs(payCashAmount + payBankAmount - payAmount) > 0.01">
                            Must equal expected ₹<span x-text="payAmount.toLocaleString('en-IN', { minimumFractionDigits: 2 })"></span>
                        </p>
                    </div>

                    <x-form.select name="payment_mode" label="Payment Mode" required>
                        <option value="Cash">Cash</option>
                        <option value="UPI">UPI</option>
                        <option value="NEFT">NEFT</option>
                        <option value="Cheque(Bank Transfer)">Cheque(Bank Transfer)</option>
                    </x-form.select>

                    <div x-show="payBankAmount > 0" x-transition>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-1">Bank Transfer Type</label>
                        <select name="bank_transfer_type" x-model="payBankTransferType" :required="payBankAmount > 0" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm">
                            <option value="">Select type...</option>
                            <option value="UPI">UPI</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="NEFT">NEFT</option>
                            <option value="RTGS">RTGS</option>
                            <option value="IMPS">IMPS</option>
                            <option value="Cheque">Cheque</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <x-form.input type="text" name="notes" label="Notes (Optional)" placeholder="e.g. Received via GPay" />
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" @click="payBillId = null" class="px-4 py-2 text-sm font-semibold text-zinc-500 hover:text-zinc-700 bg-zinc-100 hover:bg-zinc-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 rounded-lg transition-colors">Cancel</button>
                    <button type="submit" class="px-4 py-2 text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg transition-all shadow-md shadow-indigo-500/10">Confirm Payment</button>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection

@push('scripts')
<script>
let purchaseRowCount = 1;
const activeItems = @json($items);

function addPurchaseRow() {
    const body = document.getElementById('purchase-items-body');
    const newRow = document.createElement('tr');
    newRow.className = 'purchase-item-row border-t border-zinc-200 dark:border-zinc-700';
    
    let optionsHtml = activeItems.map(i => `
        <option value="${i.name}" ${i.name === 'Live Broiler Birds' ? 'selected' : ''}>
            ${i.name}
        </option>
    `).join('');

    newRow.innerHTML = `
        <td class="p-2">
            <select name="items[${purchaseRowCount}][name]" required class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2 transition-colors">
                ${optionsHtml}
            </select>
        </td>
        <td class="p-2">
            <input type="number" name="items[${purchaseRowCount}][qty]" step="0.01" required placeholder="0.00" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2 text-center p-qty" oninput="recalcPurchase()">
        </td>
        <td class="p-2">
            <input type="number" name="items[${purchaseRowCount}][rate]" step="0.01" required placeholder="0.00" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block w-full p-2 text-right p-rate" oninput="recalcPurchase()">
        </td>
        <td class="p-2 text-right font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 p-row-total">
            ₹0.00
        </td>
        <td class="p-2 text-center">
            <button type="button" onclick="this.closest('tr').remove(); recalcPurchase();" class="text-zinc-400 hover:text-rose-500 transition-colors p-1">
                <span class="material-symbols-rounded text-lg block">close</span>
            </button>
        </td>
    `;
    body.appendChild(newRow);
    purchaseRowCount++;
}

function recalcPurchase() {
    let subtotal = 0;
    document.querySelectorAll('.purchase-item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.p-qty').value) || 0;
        const rate = parseFloat(row.querySelector('.p-rate').value) || 0;
        const total = qty * rate;
        row.querySelector('.p-row-total').textContent = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2 });
        subtotal += total;
    });

    const gstA = subtotal * 18 / 100;
    const final = subtotal + gstA;

    document.getElementById('p-display-tax').textContent = '₹' + gstA.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    document.getElementById('p-display-total').textContent = '₹' + final.toLocaleString('en-IN', { minimumFractionDigits: 2 });
}

function previewWeeklyBilling(btn) {
    const dealerEl = document.getElementById('gen-dealer-id') || document.querySelector('select[name="dealer_id"]');
    const startEl = document.getElementById('gen-period-start') || document.querySelector('input[name="period_start"]');
    const endEl = document.getElementById('gen-period-end') || document.querySelector('input[name="period_end"]');
    const dealerId = dealerEl ? dealerEl.value : '';
    const start = startEl ? startEl.value : '';
    const end = endEl ? endEl.value : '';

    if (!dealerId || !start || !end) {
        alert("Please fill dealer, start date, and end date.");
        return;
    }

    btn.disabled = true;
    btn.innerHTML = `<span class="material-symbols-rounded animate-spin">sync</span> Loading...`;

    fetch(`{{ route('billing.weekly.calculate-preview') }}?dealer_id=${dealerId}&period_start=${start}&period_end=${end}`)
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-rounded">analytics</span> Calculate & Preview Bill`;

            if (data.success) {
                window.dispatchEvent(new CustomEvent('preview-update', {
                    detail: {
                        prevOutstanding: parseFloat(data.previous_outstanding),
                        totalPurchases: parseFloat(data.total_purchases),
                        totalPayments: parseFloat(data.total_payments),
                        netInvoice: parseFloat(data.net_invoice_amount),
                        purchasesCount: data.purchases_count,
                    }
                }));
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-rounded">analytics</span> Calculate & Preview Bill`;
            alert("Calculation failed: " + err.message);
        });
}

// Auto-run on load
window.addEventListener('DOMContentLoaded', () => {
    recalcPurchase();
    
    // Auto fill date range to last week (Monday to Sunday)
    const startInput = document.getElementById('gen-period-start') || document.querySelector('input[name="period_start"]');
    const endInput = document.getElementById('gen-period-end') || document.querySelector('input[name="period_end"]');
    if (startInput && endInput && !startInput.value && !endInput.value) {
        const today = new Date();
        const dayOfWeek = today.getDay(); // Sunday = 0, Monday = 1
        
        // Let's set start to previous Monday, and end to previous Sunday
        const mondayOffset = dayOfWeek === 0 ? -6 : 1 - dayOfWeek;
        const prevMonday = new Date(today);
        prevMonday.setDate(today.getDate() + mondayOffset - 7);
        
        const prevSunday = new Date(today);
        prevSunday.setDate(today.getDate() + mondayOffset - 1);
        
        startInput.value = prevMonday.toISOString().split('T')[0];
        endInput.value = prevSunday.toISOString().split('T')[0];
    }
});
</script>
@endpush
