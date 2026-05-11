@extends('layouts.app')
@section('title', 'Purchase History - ' . $dealer->firm_name)

@section('content')
<div class="mb-6">
    <a href="{{ route('masters.dealers.show', $dealer) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Dealer Details</a>
    <h1 class="text-2xl font-bold text-slate-950">Dealer Purchase History</h1>
    <p class="text-sm text-slate-500 mt-0.5">{{ $dealer->firm_name }} | Inventory inward tracking</p>
</div>

<div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    {{-- Tabs --}}
    <div class="flex border-b border-slate-200 bg-gradient-to-r from-emerald-50/70 to-sky-50/70">
        <a href="{{ route('masters.dealers.show', $dealer) }}" class="px-6 py-4 text-sm font-semibold text-slate-500 hover:text-slate-950">Overview</a>
        <a href="{{ route('masters.dealers.purchase-history', $dealer) }}" class="px-6 py-4 text-sm font-bold text-emerald-600 border-b-2 border-emerald-600">Purchase Orders</a>
        <a href="{{ route('masters.dealers.outstanding-report', $dealer) }}" class="px-6 py-4 text-sm font-semibold text-slate-500 hover:text-slate-950">Outstanding Report</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-slate-200 bg-gradient-to-r from-emerald-50/80 to-sky-50/80">
                    <th class="px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Date</th>
                    <th class="px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Item Purchased</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">Quantity</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">Rate</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Amount</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Mode</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($purchases as $purchase)
                    <tr class="hover:bg-gradient-to-r from-emerald-50/80 to-sky-50/80 transition-colors">
                        <td class="px-5 py-4 font-semibold text-slate-950">{{ $purchase->date->format('d M Y') }}</td>
                        <td class="px-5 py-4">
                            <p class="font-bold text-slate-950">{{ $purchase->item }}</p>
                            <p class="text-[10px] text-slate-400 font-mono uppercase tracking-tighter">ID: #PUR-{{ $purchase->id }}</p>
                        </td>
                        <td class="px-5 py-4 text-right font-mono text-slate-600">{{ number_format($purchase->quantity, 2) }} {{ $purchase->unit }}</td>
                        <td class="px-5 py-4 text-right text-slate-600">Rs {{ number_format($purchase->rate, 2) }}</td>
                        <td class="px-5 py-4 text-right font-bold text-slate-950">Rs {{ number_format($purchase->total_amount, 2) }}</td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2 py-0.5 bg-sky-50 text-slate-700 text-[10px] font-bold uppercase rounded tracking-wider">{{ $purchase->payment_mode }}</span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-slate-400 italic">No purchase records found for this dealer.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($purchases->hasPages())
    <div class="px-5 py-4 border-t border-slate-200">
        {{ $purchases->links() }}
    </div>
    @endif
</div>
@endsection
