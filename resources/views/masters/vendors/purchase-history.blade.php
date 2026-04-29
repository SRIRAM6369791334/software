@extends('layouts.app')
@section('title', 'Purchase History - ' . $vendor->firm_name)

@section('content')
<div class="mb-6">
    <a href="{{ route('masters.vendors.show', $vendor) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Vendor Details</a>
    <h1 class="text-2xl font-bold text-gray-900">Full Purchase History</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ $vendor->firm_name }} | Complete supply logs</p>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="flex border-b border-gray-100 bg-gray-50/30">
        <a href="{{ route('masters.vendors.show', $vendor) }}" class="px-6 py-4 text-sm font-semibold text-gray-500 hover:text-gray-900">Quick Look</a>
        <a href="{{ route('masters.vendors.purchase-history', $vendor) }}" class="px-6 py-4 text-sm font-bold text-indigo-600 border-b-2 border-indigo-600">Full Purchase History</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Item Details</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Quantity</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Rate</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">GST Amount</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Bill</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Mode</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($purchases as $purchase)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-4 font-semibold text-gray-900">{{ $purchase->date->format('d M Y') }}</td>
                        <td class="px-5 py-4">
                            <p class="font-bold text-gray-900">{{ $purchase->item }}</p>
                            <p class="text-[10px] text-gray-400 font-mono">Invoice #PUR{{ $purchase->id }}</p>
                        </td>
                        <td class="px-5 py-4 text-right font-mono text-gray-600">{{ number_format($purchase->quantity, 2) }} {{ $purchase->unit }}</td>
                        <td class="px-5 py-4 text-right text-gray-500">₹{{ number_format($purchase->rate, 2) }}</td>
                        <td class="px-5 py-4 text-right text-indigo-400">₹{{ number_format($purchase->gst_amount, 2) }}</td>
                        <td class="px-5 py-4 text-right font-bold text-gray-900">₹{{ number_format($purchase->total_amount, 2) }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2 py-0.5 bg-gray-100 text-gray-700 text-[10px] font-bold uppercase rounded tracking-wider">{{ $purchase->payment_mode }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400 italic">No supply history found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($purchases->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">
        {{ $purchases->links() }}
    </div>
    @endif
</div>
@endsection
