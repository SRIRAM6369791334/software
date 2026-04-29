@extends('layouts.app')
@section('title', 'Payment History - ' . $customer->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('masters.customers.show', $customer) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Customer Details</a>
    <h1 class="text-2xl font-bold text-gray-900">Payment History</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ $customer->name }} | Ledger of all payments received</p>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    {{-- Tabs --}}
    <div class="flex border-b border-gray-100">
        <a href="{{ route('masters.customers.show', $customer) }}" class="px-6 py-4 text-sm font-semibold text-gray-500 hover:text-gray-900">Quick Overview</a>
        <a href="{{ route('masters.customers.billing-history', $customer) }}" class="px-6 py-4 text-sm font-semibold text-gray-500 hover:text-gray-900">Billing History</a>
        <a href="{{ route('masters.customers.payment-history', $customer) }}" class="px-6 py-4 text-sm font-bold text-emerald-600 border-b-2 border-emerald-600">Payment History</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Reference / Mode</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Notes</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Amount Paid</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Balance After</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payments as $payment)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4">
                            <p class="font-bold text-gray-900">{{ $payment->date->format('d M Y') }}</p>
                            <p class="text-[10px] text-gray-400 font-mono">{{ $payment->created_at->format('H:i') }}</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-[10px] font-bold uppercase rounded tracking-wider">{{ $payment->payment_mode }}</span>
                        </td>
                        <td class="px-5 py-4 text-gray-500">{{ $payment->notes ?: '—' }}</td>
                        <td class="px-5 py-4 text-right font-bold text-emerald-600">₹{{ number_format($payment->amount, 2) }}</td>
                        <td class="px-5 py-4 text-right font-mono text-gray-400 italic">
                            {{-- Assuming we start tracking balance after in future, showing current/placeholder for now --}}
                            —
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-gray-400 italic">No payment records found for this customer.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($payments->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $payments->links() }}
    </div>
    @endif
</div>
@endsection
