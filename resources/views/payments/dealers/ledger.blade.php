@extends('layouts.app')
@section('title', 'Dealer Ledger - ' . $dealer->firm_name)

@section('content')
<div class="flex items-center justify-between mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('payments.dealers.index') }}" class="p-2 hover:bg-sky-50 rounded-lg text-slate-400">←</a>
        <div>
            <h1 class="text-2xl font-bold text-slate-950">{{ $dealer->firm_name }}</h1>
            <p class="text-sm text-slate-500 mt-0.5">Account Statement & Ledger</p>
        </div>
    </div>
    <div class="bg-red-50 px-4 py-2 rounded-lg border border-red-100">
        <p class="text-[10px] text-red-600 font-bold uppercase tracking-widest">Pending Amount</p>
        <p class="text-xl font-black text-red-700">Rs {{ number_format($dealer->pending_amount, 2) }}</p>
    </div>
</div>

{{-- Dealer Info Card --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-4 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Contact Details</p>
        <p class="text-sm font-semibold text-slate-950">{{ $dealer->contact_person ?: '-' }}</p>
        <p class="text-sm text-slate-600"> {{ $dealer->phone }}</p>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-4 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Location & Route</p>
        <p class="text-sm font-semibold text-slate-950">{{ $dealer->location ?: '-' }}</p>
        <p class="text-sm text-slate-600 italic">Route: {{ $dealer->route ?: 'Unknown' }}</p>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-4 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-[10px] text-slate-400 font-bold uppercase mb-1">Tax Identification</p>
        <p class="text-sm font-semibold text-slate-950">{{ $dealer->gst_number ?: 'Not GST Registered' }}</p>
        <p class="text-xs text-slate-400 mt-1 italic">Soft ID: #DLR-{{ str_pad($dealer->id, 4, '0', STR_PAD_LEFT) }}</p>
    </div>
</div>

{{-- Ledger Table --}}
<div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-slate-200 flex items-center justify-between bg-gradient-to-r from-emerald-50/80 to-sky-50/80">
        <h2 class="text-base font-bold text-slate-950">Transaction History</h2>
        <button onclick="window.print()" class="text-xs font-semibold text-primary hover:text-indigo-700"> Print Statement</button>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-white text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-200">
                    <th class="px-5 py-4 text-left">Date</th>
                    <th class="px-5 py-4 text-left">Transaction Details</th>
                    <th class="px-5 py-4 text-center">Ref #</th>
                    <th class="px-5 py-4 text-right">Debit (Purchase)</th>
                    <th class="px-5 py-4 text-right">Credit (Payment)</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($payments as $p)
                    <tr class="hover:bg-gradient-to-r from-emerald-50/70 to-sky-50/70 transition-colors">
                        <td class="px-5 py-4 text-slate-500 font-mono">{{ $p->date->format('d M Y') }}</td>
                        <td class="px-5 py-4">
                            <p class="font-medium text-slate-950">Payment Received</p>
                            @if($p->notes)
                                <p class="text-xs text-slate-400 mt-1 italic">{{ $p->notes }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-center font-mono text-[10px] text-slate-400">PAY-{{ str_pad($p->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="px-5 py-4 text-right">-</td>
                        <td class="px-5 py-4 text-right font-mono font-bold text-emerald-600">Rs {{ number_format($p->amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-12 text-center text-slate-400 italic">No transactions recorded for this dealer.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-slate-200">{{ $payments->links() }}</div>
</div>

<style>
@media print {
    body { background: white !important; }
    nav, aside, header, .no-print, .pagination { display: none !important; }
    .shadow-sm, .border-slate-200 { border: none !important; box-shadow: none !important; }
}
</style>
@endsection
