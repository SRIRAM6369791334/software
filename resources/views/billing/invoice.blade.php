@extends('layouts.app')
@section('title', 'Invoice #' . ($bill->invoice_number ?? $bill->id))

@section('content')
<div class="max-w-3xl mx-auto bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-8 border border-slate-200 shadow-lg rounded-xl my-4" id="invoice-print">
    <div class="flex justify-between items-start border-b pb-6 mb-6">
        <div>
            <h1 class="text-3xl font-black text-emerald-600 tracking-tighter italic">Flockwise <span class="text-slate-950 not-italic tracking-normal font-bold">BizTrack</span></h1>
            <p class="text-xs text-slate-400 mt-1 uppercase tracking-widest font-semibold text-center bg-emerald-50 py-1 rounded">Poultry Management Solutions</p>
        </div>
        <div class="text-right">
            <h2 class="text-xl font-bold text-slate-950">INVOICE</h2>
            <p class="text-sm text-slate-500 font-mono mt-1">#INV-{{ str_pad($bill->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-12 mb-10">
        <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Bill To</p>
            <h3 class="text-lg font-bold text-slate-950">{{ $bill->customer->name ?? 'N/A' }}</h3>
            <p class="text-sm text-slate-600 mt-1 leading-relaxed">{{ $bill->customer->address ?? 'No address provided' }}</p>
            <p class="text-sm text-slate-600 font-medium mt-1"> {{ $bill->customer->phone ?? 'N/A' }}</p>
            @if($bill->customer->gst_number)
                <p class="text-xs text-slate-400 mt-2">GSTIN: <span class="text-slate-700 font-mono">{{ $bill->customer->gst_number }}</span></p>
            @endif
        </div>
        <div class="text-right">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Invoice Details</p>
            <div class="space-y-2">
                <p class="text-sm text-slate-600">Date: <span class="font-semibold text-slate-950">{{ date('d M Y') }}</span></p>
                <p class="text-sm text-slate-600">Period: <span class="font-semibold text-slate-950 italic">{{ $bill->period_start->format('d M') }} - {{ $bill->period_end->format('d M Y') }}</span></p>
                <p class="text-sm text-slate-600">Status: <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase">{{ $bill->status }}</span></p>
            </div>
        </div>
    </div>

    <div class="mb-10 overflow-hidden rounded-xl border border-slate-200">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-emerald-50 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-200">
                    <th class="px-6 py-4">Description</th>
                    <th class="px-6 py-4 text-right">Quantity</th>
                    <th class="px-6 py-4 text-right">Unit Price</th>
                    <th class="px-6 py-4 text-right">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <tr>
                    <td class="px-6 py-5">
                        <p class="text-sm font-bold text-slate-950">{{ $bill->items_description ?: 'Livestock/Poultry Products' }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">Weekly supply for cycle {{ $bill->period_start->format('M d') }}</p>
                    </td>
                    <td class="px-6 py-5 text-right font-mono text-sm text-slate-600">{{ number_format($bill->quantity_kg, 2) }} kg</td>
                    <td class="px-6 py-5 text-right font-mono text-sm text-slate-600">Rs {{ number_format($bill->amount / ($bill->quantity_kg ?: 1), 2) }}</td>
                    <td class="px-6 py-5 text-right font-mono font-bold text-slate-950">Rs {{ number_format($bill->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="flex justify-end mb-12">
        <div class="w-64 bg-gradient-to-r from-emerald-50/80 to-sky-50/80 rounded-2xl p-6 border border-slate-200">
            <div class="flex justify-between items-center mb-3">
                <span class="text-xs text-slate-500 font-medium">Subtotal</span>
                <span class="text-sm font-mono text-slate-950 font-semibold">Rs {{ number_format($bill->amount, 2) }}</span>
            </div>
            <div class="flex justify-between items-center mb-4 pb-4 border-b border-slate-200">
                <span class="text-xs text-slate-500 font-medium">Tax (0%)</span>
                <span class="text-sm font-mono text-slate-950 font-semibold">Rs 0.00</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm font-bold text-slate-950 uppercase tracking-wider">Total Due</span>
                <span class="text-xl font-black text-emerald-600 font-mono">Rs {{ number_format($bill->amount, 2) }}</span>
            </div>
        </div>
    </div>

    <div class="border-t pt-8 text-center">
        <p class="text-sm text-slate-950 font-bold mb-1">Thank you for your business!</p>
        <p class="text-xs text-slate-400">Please settle the payment within 7 days of invoice generation.</p>
        <div class="mt-8 flex justify-center gap-4 no-print">
            <button onclick="window.print()" class="px-6 py-2 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 text-white text-sm font-bold rounded-lg hover:bg-emerald-50 transition-all shadow-lg hover:shadow-slate-200/60"> Print Invoice</button>
            <button onclick="window.close()" class="px-6 py-2 border border-slate-200 text-slate-600 text-sm font-bold rounded-lg hover:bg-emerald-50 transition-all">Close</button>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, nav, aside, header { display: none !important; }
    body { background-color: white !important; }
    #invoice-print { 
        margin: 0 !important; 
        padding: 0 !important; 
        border: none !important; 
        box-shadow: none !important; 
        width: 100% !important; 
        max-width: none !important; 
    }
}
</style>
@endsection
