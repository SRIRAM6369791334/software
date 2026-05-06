@extends('layouts.app')
@section('title', 'Purchase Invoices')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Purchase Invoices</h1>
        <p class="text-sm text-gray-500 mt-0.5">Detailed history of all vendor purchases</p>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-base font-semibold text-gray-900">Invoice List</h2>
        <form method="GET" class="relative">
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search vendor or items..."
                   class="pl-9 pr-4 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Vendor</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Items Summary</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Date</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">GST</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">Total Amount</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($purchases as $p)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3.5 font-medium text-gray-900">{{ $p->vendor_name }}</td>
                        <td class="px-5 py-3.5">
                            @php
                                $firstItem = $p->items->first();
                                $othersCount = $p->items->count() - 1;
                            @endphp
                            <div class="flex flex-col">
                                <span class="text-gray-700 font-medium">{{ $firstItem?->item_name ?: 'No items' }}</span>
                                @if($othersCount > 0)
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">+ {{ $othersCount }} other products</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-gray-500">{{ $p->date->format('d M Y') }}</td>
                        <td class="px-5 py-3.5 text-right font-mono text-gray-400">₹{{ number_format($p->gst_amount, 2) }}</td>
                        <td class="px-5 py-3.5 text-right font-mono font-bold text-gray-900">₹{{ number_format($p->total_amount, 2) }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <div class="flex justify-center gap-2">
                                <a href="{{ route('purchases.show', $p->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded" title="View">👁️</a>
                                <a href="{{ route('purchases.edit', $p->id) }}" class="p-1.5 text-emerald-600 hover:bg-emerald-50 rounded" title="Edit">✏️</a>
                                <form action="{{ route('purchases.destroy', $p->id) }}" method="POST" onsubmit="return confirm('Delete this invoice?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded" title="Delete">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No invoices found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $purchases->withQueryString()->links() }}</div>
</div>
@endsection
