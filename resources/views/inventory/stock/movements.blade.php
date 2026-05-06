@extends('layouts.app')

@section('title', 'Stock Ledgers')

@section('content')
<div class="mb-8">
    <a href="{{ route('inventory.stock.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Dashboard</a>
    <h1 class="text-2xl font-bold text-gray-900">Stock Movements Ledger</h1>
    <p class="text-sm text-gray-500 mt-0.5">Comprehensive audit trail of all inventory transactions</p>
</div>

{{-- Movements Table --}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-gray-50 bg-gray-50/30">
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Date</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Item / Batch</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Type / Source</th>
                    <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-400 uppercase tracking-widest">Quantity</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Location</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Remarks</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($movements as $ledger)
                    <tr class="hover:bg-gray-50/20 transition-colors">
                        <td class="px-6 py-4 text-center">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900">{{ $ledger->transaction_date->format('d M') }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $ledger->transaction_date->format('Y') }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900">{{ $ledger->item->name }}</span>
                                @if($ledger->batch)
                                    <span class="text-[10px] text-emerald-600 font-bold uppercase tracking-tight">Flock: {{ $ledger->batch->batch_code }}</span>
                                @else
                                    <span class="text-[10px] text-gray-400 font-medium uppercase italic">General Stock</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-widest w-fit
                                    {{ $ledger->type === 'IN' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $ledger->type }}
                                </span>
                                <span class="text-[11px] font-bold text-gray-600">{{ $ledger->source_type }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-black {{ $ledger->type === 'IN' ? 'text-emerald-600' : 'text-red-600' }}">
                                {{ $ledger->type === 'IN' ? '+' : '-' }}{{ number_format($ledger->quantity, 2) }}
                            </span>
                            <span class="text-[10px] text-gray-400 font-bold ml-1 uppercase">{{ $ledger->unit }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-xs font-bold text-gray-500 uppercase">{{ $ledger->warehouse ? $ledger->warehouse->name : 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-[11px] text-gray-400 italic line-clamp-1">{{ $ledger->remarks ?: '—' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-20 text-center">
                            <div class="flex flex-col items-center opacity-40">
                                <span class="text-5xl mb-4">📜</span>
                                <p class="text-sm font-bold uppercase tracking-widest">No stock movements recorded yet</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($movements->hasPages())
    <div class="px-6 py-4 border-t border-gray-50 bg-gray-50/30">
        {{ $movements->links() }}
    </div>
    @endif
</div>
@endsection
