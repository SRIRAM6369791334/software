@extends('layouts.app')
@section('title', 'Purchase Entry')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Purchase Entry</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Record incoming inventory and asset purchases</p>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="secondary" size="md" href="{{ route('purchases.export') }}">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></x-slot>
                Export Registry
            </x-button>
        </div>
    </div>

    <!-- Entry Form -->
    <x-card padding="false">
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">New Purchase Record</h2>
            <x-badge variant="primary">Manual Entry</x-badge>
        </div>
        <form action="{{ route('purchases.store') }}" method="POST" id="purchase-form" class="p-8 lg:p-10">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-2">
                    <x-input label="Vendor Name *" name="vendor_name" required value="{{ old('vendor_name') }}" placeholder="Enter vendor or supplier name" />
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-1">Purchase Item *</label>
                    <select name="item" required class="w-full bg-slate-50 border-slate-200 rounded-2xl py-3 px-5 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all" onchange="recalculate()">
                        <option value="">Select category…</option>
                        @foreach(['Feed','Chicks','Medicines','Accessories'] as $item)
                            <option value="{{ $item }}" {{ old('item') === $item ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <x-input label="Transaction Date *" type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}" />
                
                <x-input label="Quantity *" type="number" name="quantity" id="qty" required step="0.01" min="0.01" value="{{ old('quantity') }}" oninput="recalculate()" placeholder="0.00" />
                <x-input label="Unit" name="unit" value="{{ old('unit', 'kg') }}" placeholder="kg, bags, units" />
                <x-input label="Rate (₹) *" type="number" name="rate" id="rate" required step="0.01" min="0.01" value="{{ old('rate') }}" oninput="recalculate()" placeholder="0.00" />
                <x-input label="GST % *" type="number" name="gst_percentage" id="gst" required step="0.01" min="0" max="28" value="{{ old('gst_percentage', 18) }}" oninput="recalculate()" />
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mt-10">
                <div class="lg:col-span-2">
                    <div class="p-6 bg-slate-900 rounded-[2rem] flex flex-col sm:flex-row items-center justify-around gap-8 relative overflow-hidden">
                        <!-- Decoration -->
                        <div class="absolute top-0 right-0 w-32 h-32 bg-primary-500/10 rounded-full blur-3xl"></div>
                        <div class="absolute bottom-0 left-0 w-32 h-32 bg-blue-500/10 rounded-full blur-3xl"></div>
                        
                        <div class="text-center sm:text-left z-10">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Base Subtotal</p>
                            <p class="text-xl font-black text-white mt-1" id="base-amt">₹0.00</p>
                        </div>
                        <div class="w-px h-10 bg-slate-800 hidden sm:block"></div>
                        <div class="text-center sm:text-left z-10">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">GST Component</p>
                            <p class="text-xl font-black text-primary-400 mt-1" id="gst-amt">₹0.00</p>
                        </div>
                        <div class="w-px h-10 bg-slate-800 hidden sm:block"></div>
                        <div class="text-center sm:text-left z-10">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Total Payable</p>
                            <p class="text-3xl font-black text-white mt-1 tracking-tight" id="total-amt">₹0.00</p>
                        </div>
                    </div>
                </div>
                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-1">Payment Mode *</label>
                        <select name="payment_mode" required class="w-full bg-slate-50 border-slate-200 rounded-2xl py-3 px-5 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all">
                            @foreach(['NEFT','Cheque','UPI','Cash'] as $mode)
                                <option value="{{ $mode }}" {{ old('payment_mode') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <x-button variant="primary" size="lg" type="submit" class="w-full shadow-2xl shadow-primary-500/20">
                        Record Transaction
                    </x-button>
                </div>
            </div>
        </form>
    </x-card>

    <!-- Recent Purchases Registry -->
    <x-card padding="false">
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Purchase Registry</h2>
                <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Audit trail of incoming stock</p>
            </div>
            <form method="GET" class="relative group">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                </div>
                <input type="text" name="search" value="{{ $search }}" placeholder="Quick filter..." 
                       class="w-full md:w-64 bg-white border-slate-200 rounded-xl py-2 pl-9 pr-4 text-xs font-bold text-slate-700 focus:ring-4 focus:ring-primary-500/10 transition-all outline-none">
            </form>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-100 bg-slate-50/30">
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Transaction</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Category</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Volume</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Unit Rate</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Tax (GST)</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Final Amount</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center">Mode</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($purchases as $p)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div>
                                    <p class="font-extrabold text-slate-900">{{ $p->vendor_name }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">{{ $p->date->format('d M, Y') }}</p>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[10px] rounded-full font-black uppercase tracking-wider">{{ $p->item }}</span>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="text-slate-900 font-extrabold">{{ number_format($p->quantity, 2) }} <span class="text-[10px] text-slate-400 uppercase">{{ $p->unit }}</span></p>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="text-slate-600 font-bold text-xs font-mono">₹{{ number_format($p->rate, 2) }}</p>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="text-slate-400 font-bold text-xs font-mono">₹{{ number_format($p->gst_amount, 2) }}</p>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="text-slate-900 font-black text-base">₹{{ number_format($p->total_amount, 0) }}</p>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <x-badge variant="slate">{{ $p->payment_mode }}</x-badge>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-8 py-12 text-center text-slate-400 font-medium italic">No purchase records found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($purchases->hasPages())
            <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/30">
                {{ $purchases->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</div>
@endsection

@push('scripts')
<script>
function recalculate() {
    const qty  = parseFloat(document.getElementById('qty').value)  || 0;
    const rate = parseFloat(document.getElementById('rate').value) || 0;
    const gst  = parseFloat(document.getElementById('gst').value)  || 0;
    const base = qty * rate;
    const gstAmt  = base * gst / 100;
    const total   = base + gstAmt;
    document.getElementById('base-amt').textContent = '₹' + base.toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('gst-amt').textContent  = '₹' + gstAmt.toLocaleString('en-IN', {minimumFractionDigits: 2});
    document.getElementById('total-amt').textContent = '₹' + total.toLocaleString('en-IN', {minimumFractionDigits: 2});
}
// Initial call
recalculate();
</script>
@endpush
