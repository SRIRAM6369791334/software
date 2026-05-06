@extends('layouts.app')
@section('title', 'Payment History - ' . $customer->name)

@section('content')
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
        <div>
            <a href="{{ route('masters.customers.show', $customer) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Details</a>
            <h1 class="text-2xl font-bold text-gray-900">Payment History</h1>
            <p class="text-sm text-gray-500 mt-0.5">{{ $customer->name }} | Complete Payment Ledger</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2">
            <a href="{{ route('payments.customers.create', ['customer_id' => $customer->id]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-semibold hover:bg-emerald-700 transition-all shadow-md">
                + Record Payment
            </a>
            <a href="{{ route('payments.customers.export', ['customer_id' => $customer->id]) }}" 
               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all shadow-sm">
                📥 Export
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Payments</p>
            <h3 class="text-xl font-black text-gray-900">{{ $payments->total() }}</h3>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Paid</p>
            <h3 class="text-xl font-black text-emerald-600">₹{{ number_format($totalPaid, 0) }}</h3>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Avg. Payment</p>
            <h3 class="text-xl font-black text-gray-900">₹{{ number_format($payments->total() > 0 ? $totalPaid / $payments->total() : 0, 0) }}</h3>
        </div>
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Last Payment</p>
            <h3 class="text-xl font-black text-gray-900">{{ $payments->first()?->date->format('d M y') ?? 'None' }}</h3>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        {{-- Tabs --}}
        <div class="flex border-b border-gray-100 bg-gray-50/50">
            <a href="{{ route('masters.customers.show', $customer) }}" class="px-6 py-4 text-sm font-semibold text-gray-500 hover:text-gray-900 transition-colors">Quick Overview</a>
            <a href="{{ route('masters.customers.billing-history', $customer) }}" class="px-6 py-4 text-sm font-semibold text-gray-500 hover:text-gray-900 transition-colors">Billing History</a>
            <a href="{{ route('masters.customers.payment-history', $customer) }}" class="px-6 py-4 text-sm font-bold text-emerald-600 border-b-2 border-emerald-600">Payment History</a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-gray-100 bg-gray-50/50">
                        <th class="px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Date</th>
                        <th class="px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Reference / Mode</th>
                        <th class="px-5 py-3 text-xs font-bold text-gray-400 uppercase tracking-wider">Notes</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Amount Paid</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Balance After</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($payments as $payment)
                        <tr class="hover:bg-gray-50/30 transition-colors">
                            <td class="px-5 py-4">
                                <p class="font-bold text-gray-900">{{ $payment->date->format('d M Y') }}</p>
                                <p class="text-[10px] text-gray-400 font-mono">{{ $payment->created_at->format('H:i') }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 text-[10px] font-black uppercase rounded tracking-wider border border-gray-200">
                                    {{ $payment->payment_mode }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-gray-500 italic text-xs">{{ $payment->notes ?: '—' }}</td>
                            <td class="px-5 py-4 text-right font-black text-emerald-600 text-base">₹{{ number_format($payment->amount, 0) }}</td>
                            <td class="px-5 py-4 text-right font-mono text-gray-400 font-bold">
                                ₹{{ number_format($payment->balance_after, 0) }}
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-16 text-center text-gray-400 italic">No payment records found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($payments->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/20">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
@endsection
