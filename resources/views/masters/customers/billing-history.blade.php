@extends('layouts.app')
@section('title', 'Billing History - ' . $customer->name)

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
        <div>
            <a href="{{ route('masters.customers.show', $customer) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Details</a>
            <h1 class="text-2xl font-bold text-slate-950">Billing History</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $customer->name }} | Complete Billing Ledger</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2">
            <a href="{{ route('billing.weekly.export', ['customer_id' => $customer->id]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-lg text-sm font-semibold text-slate-700 hover:bg-emerald-50 transition-all shadow-sm">
                 Export CSV
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Bills</p>
            <h3 class="text-xl font-black text-slate-950">{{ $bills->total() }}</h3>
        </div>
        <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Billed</p>
            <h3 class="text-xl font-black text-slate-950">Rs {{ number_format($totalBilled, 0) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Avg. Bill Value</p>
            <h3 class="text-xl font-black text-emerald-600">Rs {{ number_format($bills->total() > 0 ? $totalBilled / $bills->total() : 0, 0) }}</h3>
        </div>
        <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-4 rounded-xl border border-slate-200 shadow-sm">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Current Balance</p>
            <h3 class="text-xl font-black text-red-600">Rs {{ number_format($customer->balance, 0) }}</h3>
        </div>
    </div>

    <div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        {{-- Tabs --}}
        <div class="flex border-b border-slate-200 bg-gradient-to-r from-emerald-50/80 to-sky-50/80">
            <a href="{{ route('masters.customers.show', $customer) }}" class="px-6 py-4 text-sm font-semibold text-slate-500 hover:text-slate-950 transition-colors">Quick Overview</a>
            <a href="{{ route('masters.customers.billing-history', $customer) }}" class="px-6 py-4 text-sm font-bold text-emerald-600 border-b-2 border-emerald-600">Billing History</a>
            <a href="{{ route('masters.customers.payment-history', $customer) }}" class="px-6 py-4 text-sm font-semibold text-slate-500 hover:text-slate-950 transition-colors">Payment History</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-200 bg-gradient-to-r from-emerald-50/80 to-sky-50/80">
                        <th class="px-5 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Bill ID</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Period</th>
                        <th class="px-5 py-3 text-xs font-bold text-slate-400 uppercase tracking-wider">Description</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Qty</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-400 uppercase tracking-wider">Amount</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($bills as $bill)
                        <tr class="hover:bg-gradient-to-r from-emerald-50/70 to-sky-50/70 transition-colors">
                            <td class="px-5 py-4 font-mono font-bold text-slate-400 text-xs">#WB-{{ str_pad($bill->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-5 py-4">
                                <p class="font-bold text-slate-950">{{ $bill->period_end->format('d M Y') }}</p>
                                <p class="text-[10px] text-slate-500 uppercase tracking-tight">{{ $bill->period_start->format('d M') }} - {{ $bill->period_end->format('d M') }}</p>
                            </td>
                            <td class="px-5 py-4 text-slate-600 truncate max-w-[200px]" title="{{ $bill->items_description }}">
                                {{ $bill->items_description }}
                            </td>
                            <td class="px-5 py-4 text-right font-mono text-slate-600">{{ number_format($bill->quantity_kg, 1) }} <span class="text-[10px] text-slate-400">kg</span></td>
                            <td class="px-5 py-4 text-right font-black text-slate-950">Rs {{ number_format($bill->amount, 0) }}</td>
                            <td class="px-5 py-4 text-center">
                                @php
                                    $statusClasses = [
                                        'Generated' => 'bg-blue-100 text-blue-700',
                                        'Pending'   => 'bg-amber-100 text-amber-700 border border-amber-200',
                                        'Paid'      => 'bg-emerald-100 text-emerald-700',
                                    ];
                                @endphp
                                <span class="px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-wider {{ $statusClasses[$bill->status] ?? 'bg-slate-100' }}">
                                    {{ $bill->status }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <a href="{{ route('billing.weekly.show', $bill) }}" 
                                   class="inline-flex items-center px-3 py-1 bg-emerald-50 text-emerald-700 rounded-md text-xs font-bold hover:bg-emerald-100 transition-colors">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-16 text-center text-slate-400 italic">No billing records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($bills->hasPages())
        <div class="px-5 py-4 border-t border-slate-200 bg-slate-50/20">
            {{ $bills->links() }}
        </div>
        @endif
    </div>
@endsection
