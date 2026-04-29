@extends('layouts.app')
@section('title', 'Weekly Billing')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Weekly Billing</h1>
        <p class="text-sm text-gray-500 mt-0.5">Generate and manage weekly customer invoices</p>
    </div>
    <div class="flex gap-2">
        <button onclick="document.getElementById('bulk-bill-modal').classList.remove('hidden')"
                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
            📦 Bulk Generate
        </button>
        <button onclick="document.getElementById('add-bill-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
            + Single Bill
        </button>
        <a href="{{ route('billing.weekly.export') }}" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-sm font-medium rounded-lg transition-colors">⬇ CSV</a>
    </div>
</div>

<form method="GET" class="mb-4 max-w-sm">
    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
        <input type="text" name="search" value="{{ $search }}" placeholder="Search by customer..."
               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
    </div>
</form>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Inv #</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Customer</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Period</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">Qty (kg)</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">Amount</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-400 uppercase">Status</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($bills as $bill)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3.5 font-mono text-xs text-gray-400">{{ $bill->invoice_number }}</td>
                        <td class="px-5 py-3.5 font-medium text-gray-900">{{ $bill->customer->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-gray-500 text-xs">
                            {{ $bill->period_start->format('d M') }} - {{ $bill->period_end->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3.5 text-right font-mono">{{ number_format($bill->quantity_kg, 2) }}</td>
                        <td class="px-5 py-3.5 text-right font-mono font-semibold text-gray-900">₹{{ number_format($bill->amount, 0, '.', ',') }}</td>
                        <td class="px-5 py-3.5 text-center">
                            @php
                                $statusColors = ['Generated'=>'bg-blue-50 text-blue-700','Pending'=>'bg-amber-50 text-amber-700','Paid'=>'bg-emerald-50 text-emerald-700'];
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$bill->status] ?? 'bg-gray-100' }}">
                                {{ $bill->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-center flex justify-center gap-2">
                            <a href="{{ route('billing.weekly.show', $bill) }}" target="_blank" class="p-1.5 text-gray-400 hover:text-indigo-600 transition-colors" title="Print Invoice">
                                👁️
                            </a>
                            <a href="{{ route('billing.weekly.whatsapp', $bill) }}" target="_blank" class="p-1.5 text-gray-400 hover:text-emerald-600 transition-colors" title="Send WhatsApp">
                                💬
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">No weekly bills generated yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $bills->withQueryString()->links() }}</div>
</div>

{{-- Bulk Modal --}}
<div id="bulk-bill-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-base font-semibold text-gray-900">Bulk Weekly Billing</h2>
            <button onclick="document.getElementById('bulk-bill-modal').classList.add('hidden')" class="text-gray-400 text-xl">✕</button>
        </div>
        <form action="{{ route('billing.weekly.bulk') }}" method="POST" class="p-6">
            @csrf
            <div class="grid grid-cols-2 gap-6 mb-6">
                <div class="space-y-4">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Select Customers</p>
                    <div class="max-h-48 overflow-y-auto border rounded-lg p-2 space-y-1">
                        @foreach($customers as $c)
                            <label class="flex items-center gap-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                                <input type="checkbox" name="customer_ids[]" value="{{ $c->id }}" class="rounded text-indigo-600">
                                <span class="text-sm">{{ $c->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="space-y-4">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Bill Details</p>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Period Start *</label>
                        <input type="date" name="period_start" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Period End *</label>
                        <input type="date" name="period_end" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Standard Amount (₹) *</label>
                        <input type="number" name="amount" required step="0.01" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg">
                            <option value="Generated">Generated</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 pt-4 border-t">
                <button type="button" onclick="document.getElementById('bulk-bill-modal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600">Cancel</button>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg shadow-sm">Generate Bulk Bills</button>
            </div>
        </form>
    </div>
</div>

{{-- Add Modal --}}
<div id="add-bill-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-base font-semibold text-gray-900">Generate Single Weekly Bill</h2>
            <button onclick="document.getElementById('add-bill-modal').classList.add('hidden')" class="text-gray-400 text-xl">✕</button>
        </div>
        <form action="{{ route('billing.weekly.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Customer *</label>
                <select name="customer_id" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Select customer…</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} (Route: {{ $c->route }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Period Start *</label>
                    <input type="date" name="period_start" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Period End *</label>
                    <input type="date" name="period_end" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Quantity (kg)</label>
                    <input type="number" name="quantity_kg" step="0.01" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Total Amount (₹) *</label>
                    <input type="number" name="amount" required step="0.01" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="Generated">Generated</option>
                    <option value="Pending">Pending</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('add-bill-modal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Generate</button>
            </div>
        </form>
    </div>
</div>
@endsection
