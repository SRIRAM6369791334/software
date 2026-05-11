@extends('layouts.app')

@section('title', 'Stock Inventory')

@section('content')
<div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Stock Inventory</h1>
        <p class="text-gray-500 font-medium">Track stock levels and inventory transactions.</p>
    </div>
    <div class="flex flex-wrap gap-2">
        <button type="button" onclick="openModal('adjustModal')" class="px-4 py-2 rounded-xl bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700">
            Quick Adjust
        </button>
        <a href="{{ route('stock.batches.index') }}" class="px-4 py-2 rounded-xl bg-white border border-gray-200 text-sm font-semibold hover:bg-gray-50">
            Batch Management
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    @forelse($summaries as $item)
        <div class="bg-white border {{ $item->current_stock < $item->reorder_level ? 'border-red-200' : 'border-gray-200' }} rounded-2xl p-5 shadow-sm">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs text-gray-400 uppercase font-bold">{{ $item->category }}</p>
                    <h3 class="font-bold text-gray-900 mt-1">{{ $item->item_name }}</h3>
                </div>
                @if($item->current_stock < $item->reorder_level)
                    <span class="text-[10px] px-2 py-1 rounded-full bg-red-50 text-red-600 font-bold uppercase">Low</span>
                @endif
            </div>
            <div class="mt-6 flex items-baseline gap-2">
                <span class="text-3xl font-black {{ $item->current_stock < $item->reorder_level ? 'text-red-600' : 'text-gray-900' }}">
                    {{ number_format($item->current_stock, 1) }}
                </span>
                <span class="text-xs text-gray-500 uppercase font-bold">{{ $item->unit }}</span>
            </div>
            <p class="mt-2 text-xs text-gray-400">Reorder level: {{ number_format($item->reorder_level, 1) }}</p>
        </div>
    @empty
        <div class="bg-white border border-gray-200 rounded-2xl p-8 text-center text-gray-500 md:col-span-2 lg:col-span-4">
            No stock items found.
        </div>
    @endforelse
</div>

<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
    <div class="p-5 border-b border-gray-100 flex flex-col gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <h2 class="font-black text-gray-900">Transaction History</h2>
            <p class="text-sm text-gray-500">Movements from {{ $from }} to {{ $to }}.</p>
        </div>
        <form method="GET" class="flex flex-wrap gap-3">
            <input type="date" name="from" value="{{ $from }}" class="px-3 py-2 rounded-lg border border-gray-200 text-sm">
            <input type="date" name="to" value="{{ $to }}" class="px-3 py-2 rounded-lg border border-gray-200 text-sm">
            <button type="submit" class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-bold">Filter</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                <tr>
                    <th class="px-5 py-3 text-left">Date</th>
                    <th class="px-5 py-3 text-left">Item</th>
                    <th class="px-5 py-3 text-left">Type</th>
                    <th class="px-5 py-3 text-right">Quantity</th>
                    <th class="px-5 py-3 text-left">Notes</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($movements as $txn)
                    <tr>
                        <td class="px-5 py-4 whitespace-nowrap">{{ optional($txn->date)->format('Y-m-d') }}</td>
                        <td class="px-5 py-4 font-semibold">{{ $txn->item_name }}</td>
                        <td class="px-5 py-4">
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $txn->txn_type === 'OUT' ? 'bg-red-50 text-red-600' : ($txn->txn_type === 'IN' ? 'bg-emerald-50 text-emerald-600' : 'bg-amber-50 text-amber-600') }}">
                                {{ $txn->txn_type ?? $txn->type }}
                            </span>
                        </td>
                        <td class="px-5 py-4 text-right font-bold">
                            {{ $txn->txn_type === 'OUT' ? '-' : '+' }}{{ number_format($txn->quantity, 1) }} {{ $txn->unit }}
                        </td>
                        <td class="px-5 py-4 text-gray-500">{{ $txn->notes }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-5 py-12 text-center text-gray-500">No transactions found for this period.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-5 border-t border-gray-100">
        {{ $movements->appends(['from' => $from, 'to' => $to])->links() }}
    </div>
</div>

<div id="adjustModal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/40 p-4">
    <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-xl">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-black">Adjust Stock</h3>
            <button type="button" onclick="closeModal('adjustModal')" class="text-gray-500 hover:text-gray-900">Close</button>
        </div>
        <form method="POST" action="{{ route('stock.adjust') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Item</label>
                <select name="item_name" required class="w-full rounded-lg border border-gray-200 px-3 py-2">
                    @foreach($summaries as $item)
                        <option value="{{ $item->item_name }}">{{ $item->item_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Quantity Change</label>
                    <input type="number" step="0.001" name="quantity" required class="w-full rounded-lg border border-gray-200 px-3 py-2">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date</label>
                    <input type="date" name="date" value="{{ now()->toDateString() }}" required class="w-full rounded-lg border border-gray-200 px-3 py-2">
                </div>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Reason</label>
                <input type="text" name="reason" required class="w-full rounded-lg border border-gray-200 px-3 py-2">
            </div>
            <button type="submit" class="w-full rounded-lg bg-emerald-600 px-4 py-2 font-bold text-white hover:bg-emerald-700">Save Adjustment</button>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openModal(id) {
        document.getElementById(id)?.classList.remove('hidden');
        document.getElementById(id)?.classList.add('flex');
    }

    function closeModal(id) {
        document.getElementById(id)?.classList.add('hidden');
        document.getElementById(id)?.classList.remove('flex');
    }
</script>
@endpush
