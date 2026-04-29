@extends('layouts.app')
@section('title', 'Billing History - ' . $customer->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('masters.customers.show', $customer) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Customer Details</a>
    <h1 class="text-2xl font-bold text-gray-900">Billing History</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ $customer->name }} | Detailed list of all generated bills</p>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    {{-- Tabs --}}
    <div class="flex border-b border-gray-100">
        <a href="{{ route('masters.customers.show', $customer) }}" class="px-6 py-4 text-sm font-semibold text-gray-500 hover:text-gray-900">Quick Overview</a>
        <a href="{{ route('masters.customers.billing-history', $customer) }}" class="px-6 py-4 text-sm font-bold text-emerald-600 border-b-2 border-emerald-600">Billing History</a>
        <a href="{{ route('masters.customers.payment-history', $customer) }}" class="px-6 py-4 text-sm font-semibold text-gray-500 hover:text-gray-900">Payment History</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Bill ID</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Period / Date</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Description</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Qty (kg)</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Amount</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($bills as $bill)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4 font-mono font-bold text-gray-400">#WB-{{ str_pad($bill->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-gray-900">{{ $bill->period_start->format('d M') }} — {{ $bill->period_end->format('d M Y') }}</p>
                        </td>
                        <td class="px-5 py-4 text-gray-500 italic">{{ Str::limit($bill->items_description, 30) }}</td>
                        <td class="px-5 py-4 text-right font-mono text-gray-600">{{ number_format($bill->quantity_kg, 2) }}</td>
                        <td class="px-5 py-4 text-right font-bold text-gray-900">₹{{ number_format($bill->amount, 2) }}</td>
                        <td class="px-5 py-4 text-center">
                            @php
                                $colors = ['Generated' => 'bg-blue-50 text-blue-700', 'Pending' => 'bg-amber-50 text-amber-700', 'Paid' => 'bg-emerald-50 text-emerald-700'];
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-tight {{ $colors[$bill->status] ?? 'bg-gray-50' }}">
                                {{ $bill->status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="{{ route('billing.weekly.show', $bill) }}" class="text-emerald-600 hover:text-emerald-700 font-bold">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400 italic">No billing records found for this customer.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($bills->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $bills->links() }}
    </div>
    @endif
</div>
@endsection
