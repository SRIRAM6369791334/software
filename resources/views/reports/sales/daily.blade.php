@extends('layouts.app')
@section('title', 'Daily Sales Report')

@section('content')
<div class="mb-6">
    <a href="{{ route('reports.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Analytics</a>
    <h1 class="text-2xl font-bold text-gray-900">Daily Sales Logs</h1>
    <p class="text-sm text-gray-500 mt-0.5">Comprehensive list of all daily transactions</p>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Description</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Qty (kg)</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Rate</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Total</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($bills as $bill)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="px-5 py-4 font-semibold text-gray-900">{{ $bill->date->format('d M Y') }}</td>
                        <td class="px-5 py-4 font-bold text-gray-900">{{ $bill->customer->name }}</td>
                        <td class="px-5 py-4 text-gray-500 italic">{{ Str::limit($bill->items_description, 20) }}</td>
                        <td class="px-5 py-4 text-right font-mono text-gray-600">{{ number_format($bill->quantity_kg, 2) }}</td>
                        <td class="px-5 py-4 text-right text-gray-400">₹{{ number_format($bill->rate_per_kg, 2) }}</td>
                        <td class="px-5 py-4 text-right font-black text-gray-900">₹{{ number_format($bill->amount, 2) }}</td>
                        <td class="px-5 py-4 text-center">
                            @php $st = $bill->status; $cls = $st == 'Paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'; @endphp
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-tight {{ $cls }}">{{ $st }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400 italic">No daily sales recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bills->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">{{ $bills->links() }}</div>
    @endif
</div>
@endsection
