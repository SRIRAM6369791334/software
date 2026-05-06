@extends('layouts.app')
@section('title', 'Daily Sale Invoice #' . $bill->id)

@section('content')
<div class="max-w-4xl mx-auto bg-white p-10 border border-gray-200 shadow-2xl rounded-2xl my-6 relative overflow-hidden" id="invoice-print">
    {{-- Decorative Header Accent --}}
    <div class="absolute top-0 left-0 w-full h-2 bg-emerald-600"></div>

    <div class="flex justify-between items-start border-b border-gray-100 pb-8 mb-8">
        <div>
            <h1 class="text-4xl font-black text-emerald-600 tracking-tighter italic">Flockwise <span class="text-gray-900 not-italic tracking-normal font-bold">BizTrack</span></h1>
            <p class="text-[10px] text-gray-400 mt-1.5 uppercase tracking-[0.2em] font-black bg-gray-50 px-3 py-1 rounded-full inline-block">Poultry Management Solutions</p>
            <div class="mt-6 text-sm text-gray-500 space-y-1">
                <p class="font-bold text-gray-900">Poultry Farm Unit #1</p>
                <p>Tamil Nadu, India</p>
                <p>📞 +91 98765 43210</p>
            </div>
        </div>
        <div class="text-right">
            <div class="bg-emerald-50 text-emerald-700 px-4 py-2 rounded-xl inline-block mb-4">
                <h2 class="text-xl font-black uppercase tracking-tight">Tax Invoice</h2>
            </div>
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Invoice Number</p>
            <p class="text-lg font-black text-gray-900 font-mono">{{ $bill->invoice_number }}</p>
            <div class="mt-4">
                <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Date of Issue</p>
                <p class="text-sm font-bold text-gray-900">{{ $bill->date->format('d F, Y') }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-16 mb-12">
        <div>
            <p class="text-[10px] font-black text-emerald-600 uppercase tracking-widest mb-3 border-b border-emerald-100 pb-1 inline-block">Bill To Customer</p>
            <h3 class="text-2xl font-black text-gray-900">{{ $bill->customer->name ?? 'N/A' }}</h3>
            <div class="mt-3 text-sm text-gray-600 leading-relaxed space-y-1">
                <p>{{ $bill->customer->address ?? 'No address provided' }}</p>
                <p class="font-bold text-gray-900">📞 {{ $bill->customer->phone ?? 'N/A' }}</p>
                @if($bill->customer->gst_number)
                    <div class="mt-4 pt-4 border-t border-gray-50">
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Customer GSTIN</p>
                        <p class="text-sm font-mono font-bold text-gray-700">{{ $bill->customer->gst_number }}</p>
                    </div>
                @endif
            </div>
        </div>
        <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4">Payment Summary</p>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Payment Status</span>
                    <span class="px-3 py-1 rounded-full bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest">{{ $bill->status }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-xs text-gray-500">Billing Type</span>
                    <span class="text-xs font-bold text-gray-900">Daily Retail Sale</span>
                </div>
                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                    <span class="text-xs font-bold text-gray-900">Currency</span>
                    <span class="text-xs font-bold text-gray-900 italic">Indian Rupee (INR)</span>
                </div>
            </div>
        </div>
    </div>

    <div class="mb-12 overflow-hidden rounded-2xl border border-gray-100 shadow-sm">
        <table class="w-full text-left">
            <thead>
                <tr class="bg-gray-900 text-[10px] font-black text-white uppercase tracking-widest">
                    <th class="px-8 py-4">Item Description</th>
                    <th class="px-8 py-4 text-center">Quantity</th>
                    <th class="px-8 py-4 text-right">Unit Price</th>
                    <th class="px-8 py-4 text-right">Taxable Amt</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($bill->items as $item)
                <tr class="text-gray-900 hover:bg-gray-50 transition-colors">
                    <td class="px-8 py-6">
                        <p class="font-black text-gray-900">{{ $item->item_name }}</p>
                        <p class="text-[10px] text-gray-400 mt-0.5 uppercase tracking-tighter font-bold">Standard Poultry Product</p>
                    </td>
                    <td class="px-8 py-6 text-center font-mono font-bold text-gray-600">{{ number_format($item->quantity_kg, 2) }} {{ $item->unit }}</td>
                    <td class="px-8 py-6 text-right font-mono text-gray-600">₹{{ number_format($item->rate_per_kg, 2) }}</td>
                    <td class="px-8 py-6 text-right font-mono font-black text-gray-900">₹{{ number_format($item->quantity_kg * $item->rate_per_kg, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="flex justify-end mb-16">
        <div class="w-72 bg-gray-900 rounded-3xl p-8 text-white shadow-2xl relative overflow-hidden">
            {{-- Decorative Circle --}}
            <div class="absolute -top-10 -right-10 w-32 h-32 bg-emerald-500/20 rounded-full"></div>
            
            <div class="space-y-4 relative z-10">
                <div class="flex justify-between items-center text-xs opacity-60 font-bold uppercase tracking-widest">
                    <span>Subtotal</span>
                    <span>₹{{ number_format($bill->amount, 2) }}</span>
                </div>
                <div class="flex justify-between items-center text-xs opacity-60 font-bold uppercase tracking-widest pb-4 border-b border-white/10">
                    <span>GST ({{ $bill->gst_percentage }}%)</span>
                    <span>₹{{ number_format($bill->gst_amount, 2) }}</span>
                </div>
                <div class="flex justify-between items-center pt-2">
                    <span class="text-xs font-black uppercase tracking-[0.2em] text-emerald-400">Total Net</span>
                    <span class="text-3xl font-black font-mono">₹{{ number_format($bill->net_amount, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-100 pt-10 text-center">
        <div class="flex justify-center gap-12 mb-8 text-[10px] font-black text-gray-400 uppercase tracking-[0.3em]">
            <span>No Signature Required</span>
            <span class="text-emerald-500">Computer Generated</span>
            <span>Auth Verified</span>
        </div>
        <p class="text-sm text-gray-900 font-black mb-1">Thank you for choosing Flockwise BizTrack!</p>
        <p class="text-xs text-gray-400 font-medium">Please settle the payment according to the agreed credit terms.</p>
        
        <div class="mt-10 flex justify-center gap-4 no-print">
            <button onclick="window.print()" class="px-8 py-3 bg-white border-2 border-emerald-600 text-emerald-600 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-emerald-50 transition-all shadow-xl shadow-emerald-600/5 active:scale-95">🖨️ Print Invoice</button>
            <a href="{{ route('billing.daily.pdf', $bill) }}" class="px-8 py-3 bg-emerald-600 text-white text-xs font-black uppercase tracking-widest rounded-xl hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-95 flex items-center gap-2">📄 Download PDF</a>
            <button onclick="window.close()" class="px-8 py-3 border-2 border-gray-100 text-gray-400 text-xs font-black uppercase tracking-widest rounded-xl hover:bg-gray-50 transition-all active:scale-95">Close Window</button>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, nav, aside, header { display: none !important; }
    body { background-color: white !important; padding: 0 !important; margin: 0 !important; }
    #invoice-print { 
        margin: 0 !important; 
        padding: 40px !important; 
        border: none !important; 
        box-shadow: none !important; 
        width: 100% !important; 
        max-width: none !important; 
        border-radius: 0 !important;
    }
}
</style>
@endsection
