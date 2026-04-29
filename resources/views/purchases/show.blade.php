@extends('layouts.app')
@section('title', 'Purchase Invoice #' . $purchase->id)

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <a href="{{ route('purchases.invoices') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Invoices</a>
        <h1 class="text-2xl font-bold text-gray-900">Purchase Invoice</h1>
        <p class="text-sm text-gray-500 mt-0.5">Reference: #PUR{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('purchases.print', $purchase->id) }}" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-bold hover:bg-gray-800 shadow-sm transition-all">Print 🖨️</a>
        <a href="{{ route('purchases.edit', $purchase->id) }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition-all">Edit</a>
    </div>
</div>

<div class="max-w-4xl">
    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden p-8">
        <div class="flex justify-between items-start mb-10 pb-8 border-b border-gray-100">
            <div class="space-y-4">
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Supplier</h3>
                    <h2 class="text-xl font-black text-gray-900">{{ $purchase->vendor_name }}</h2>
                </div>
                <div class="text-xs text-gray-500 space-y-1">
                    <p>Contact Info from Master Record</p>
                    <p class="italic">Recorded via Purchase Entry</p>
                </div>
            </div>
            <div class="text-right space-y-4">
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Billing Date</h3>
                    <p class="text font-bold text-gray-900">{{ $purchase->date->format('d F, Y') }}</p>
                </div>
                <div>
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Payment Mode</h3>
                    <span class="px-2 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black uppercase rounded">{{ $purchase->payment_mode }}</span>
                </div>
            </div>
        </div>

        <table class="w-full text-sm mb-10">
            <thead>
                <tr class="text-left text-xs font-bold text-gray-400 uppercase border-b border-gray-100">
                    <th class="pb-3">Item Description</th>
                    <th class="pb-3 text-right">Qty</th>
                    <th class="pb-3 text-right">Rate</th>
                    <th class="pb-3 text-right">Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <tr class="text-gray-900">
                    <td class="py-6">
                        <p class="font-bold border-l-4 border-emerald-500 pl-3">{{ $purchase->item }}</p>
                        <p class="text-xs text-gray-400 pl-3">Standard stock procurement</p>
                    </td>
                    <td class="py-6 text-right font-mono">{{ number_format($purchase->quantity, 2) }} {{ $purchase->unit }}</td>
                    <td class="py-6 text-right">₹{{ number_format($purchase->rate, 2) }}</td>
                    <td class="py-6 text-right font-bold">₹{{ number_format($purchase->quantity * $purchase->rate, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="flex justify-end">
            <div class="w-full md:w-64 space-y-3">
                <div class="flex justify-between text-sm text-gray-500">
                    <span>Subtotal</span>
                    <span>₹{{ number_format($purchase->quantity * $purchase->rate, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-500">
                    <span>GST ({{ $purchase->gst_percentage }}%)</span>
                    <span>₹{{ number_format($purchase->gst_amount, 2) }}</span>
                </div>
                <div class="flex justify-between pt-3 border-t-2 border-gray-900 text-lg font-black text-gray-900">
                    <span>Total Amount</span>
                    <span>₹{{ number_format($purchase->total_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
