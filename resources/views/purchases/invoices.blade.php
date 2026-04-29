@extends('layouts.app')
@section('title', 'Purchase Invoices')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Purchase Ledger</h1>
            <p class="text-sm text-slate-500 font-medium mt-1 uppercase tracking-widest italic">Historical Procurement Registry</p>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="primary" size="md" href="{{ route('purchases.create') }}">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></x-slot>
                New Entry
            </x-button>
        </div>
    </div>

    <x-card class="overflow-hidden p-0 border-none shadow-xl">
        <!-- Table Control Header -->
        <div class="px-8 py-6 bg-slate-50 border-b border-slate-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">Transaction Registry</h3>
            <form method="GET" class="w-full md:w-96 relative group">
                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Filter by vendor or item..."
                       class="w-full pl-11 pr-4 py-2.5 text-sm font-bold text-slate-700 bg-white border border-slate-200 rounded-2xl outline-none focus:ring-4 focus:ring-primary-500/10 transition-all">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] border-b border-slate-100 bg-white">
                        <th class="px-8 py-5">Source Vendor</th>
                        <th class="px-8 py-5">Inventory Asset</th>
                        <th class="px-8 py-5">Timeline</th>
                        <th class="px-8 py-5 text-right">Volume</th>
                        <th class="px-8 py-5 text-right">Valuation</th>
                        <th class="px-8 py-5 text-center">Protocol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50 bg-white">
                    @forelse($purchases as $p)
                        <tr class="hover:bg-slate-50/80 transition-colors group">
                            <td class="px-8 py-6">
                                <p class="text-sm font-black text-slate-900 group-hover:text-primary-600 transition-colors">{{ $p->vendor_name }}</p>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-0.5">Supplier</p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-3">
                                    <div class="w-1.5 h-6 bg-emerald-500 rounded-full"></div>
                                    <span class="text-sm font-bold text-slate-700">{{ $p->item }}</span>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-sm font-bold text-slate-500 italic">{{ $p->date->format('d M, Y') }}</td>
                            <td class="px-8 py-6 text-right font-mono">
                                <span class="text-sm font-black text-slate-900">{{ number_format($p->quantity, 2) }}</span>
                                <span class="text-[10px] font-black text-slate-400 uppercase ml-1">{{ $p->unit }}</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="text-sm font-black text-slate-900 tracking-tight">₹{{ number_format($p->total_amount, 0) }}</p>
                                <p class="text-[9px] font-black text-slate-400 uppercase mt-0.5">Incl. GST</p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex justify-center items-center gap-2">
                                    <x-button variant="ghost" size="sm" href="{{ route('purchases.show', $p->id) }}" class="!p-2" title="View Detail">
                                        <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></x-slot>
                                    </x-button>
                                    <x-button variant="ghost" size="sm" href="{{ route('purchases.edit', $p->id) }}" class="!p-2" title="Edit Record">
                                        <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></x-slot>
                                    </x-button>
                                    <form action="{{ route('purchases.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Wipe this transaction from ledger?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-400 hover:text-rose-500 hover:bg-rose-50 rounded-xl transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 mb-4">
                                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                    </div>
                                    <p class="text-sm font-black text-slate-400 uppercase tracking-widest">No Procurement Records Found</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchases->hasPages())
            <div class="px-8 py-6 bg-slate-50 border-t border-slate-100">
                {{ $purchases->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection
