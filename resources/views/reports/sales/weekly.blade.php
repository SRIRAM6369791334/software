@extends('layouts.app')
@section('title', 'Weekly Sales Report')

@section('content')
<div class="mb-6">
    <a href="{{ route('reports.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Analytics</a>
    <h1 class="text-2xl font-bold text-gray-900">Weekly Performance Logs</h1>
    <p class="text-sm text-gray-500 mt-0.5">Summary of scheduled weekly billing cycles</p>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-gray-100 bg-emerald-50/20">
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Week Period</th>
                    <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Qty (kg)</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Billed</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($bills as $bill)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="px-5 py-4">
                            <p class="font-bold text-gray-900">{{ $bill->period_start->format('d M') }} - {{ $bill->period_end->format('d M Y') }}</p>
                            <p class="text-[10px] text-gray-400 uppercase font-bold">Week Cycle</p>
                        </td>
                        <td class="px-5 py-4 font-bold text-gray-900">{{ $bill->customer->name }}</td>
                        <td class="px-5 py-4 text-right font-mono text-gray-600">{{ number_format($bill->quantity_kg, 2) }}</td>
                        <td class="px-5 py-4 text-right font-black text-emerald-900">₹{{ number_format($bill->amount, 2) }}</td>
                        <td class="px-5 py-4 text-center">
                            @php $st = $bill->status; $cls = $st == 'Paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'; @endphp
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-tight {{ $cls }}">{{ $st }}</span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <a href="{{ route('billing.weekly.show', $bill) }}" class="text-xs font-bold text-emerald-600 hover:underline">View Details</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400 italic">No weekly cycles recorded.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bills->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">{{ $bills->links() }}</div>
    @endif
</div>
@endsection
