@extends('layouts.app')
@section('title', 'Edit Purchase')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex items-center gap-4">
        <x-button variant="ghost" size="md" href="{{ route('purchases.show', $purchase->id) }}" class="!p-2">
            <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></x-slot>
        </x-button>
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Edit Entry</h1>
            <p class="text-sm text-slate-500 font-medium mt-1 uppercase tracking-widest italic">Modify Purchase Record</p>
        </div>
    </div>

    <div class="max-w-5xl">
        <form action="{{ route('purchases.update', $purchase->id) }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Supplier Info --}}
                <x-card class="relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-6 opacity-5">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 2a4 4 0 00-4 4v1H5a1 1 0 00-.994.89l-1 9A1 1 0 004 18h12a1 1 0 00.994-1.11l-1-9A1 1 0 0015 7h-1V6a4 4 0 10-4-4zm2 5V6a2 2 0 10-4 0v1h4zm-6 3a1 1 0 112 0 1 1 0 01-2 0zm7-1a1 1 0 100 2 1 1 0 000-2z" clip-rule="evenodd" /></svg>
                    </div>
                    <div class="flex items-center gap-3 mb-8">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-primary-100 text-primary-600 text-xs font-black">01</span>
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">Supplier Configuration</h3>
                    </div>
                    
                    <div class="space-y-6">
                        <x-input label="Vendor Firm Name *" name="vendor_name" value="{{ old('vendor_name', $purchase->vendor_name) }}" required />
                        <x-input label="Inward Date *" type="date" name="date" value="{{ old('date', $purchase->date->format('Y-m-d')) }}" required />
                    </div>
                </x-card>

                {{-- Item Details --}}
                <x-card class="relative overflow-hidden text-emerald-500">
                    <div class="absolute top-0 right-0 p-6 opacity-5">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a2 2 0 012-2h12a2 2 0 012 2v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4z" /></svg>
                    </div>
                    <div class="flex items-center gap-3 mb-8">
                        <span class="flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 text-xs font-black">02</span>
                        <h3 class="text-xs font-black text-slate-900 uppercase tracking-[0.2em]">Inventory Specification</h3>
                    </div>

                    <div class="space-y-6 text-slate-700">
                        <div class="space-y-2">
                            <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-1">Asset Classification *</label>
                            <select name="item" required class="w-full bg-slate-50 border-slate-200 rounded-2xl py-3 px-5 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all border">
                                @foreach(['Feed', 'Chicks', 'Medicines', 'Accessories'] as $item)
                                    <option value="{{ $item }}" {{ $purchase->item === $item ? 'selected' : '' }}>{{ $item }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-6">
                            <x-input label="Quantity *" type="number" name="quantity" id="qty" value="{{ old('quantity', $purchase->quantity) }}" step="0.01" required />
                            <x-input label="Unit Measure" name="unit" value="{{ old('unit', $purchase->unit) }}" />
                        </div>

                        <x-input label="Rate per Unit (₹) *" type="number" name="rate" id="rate" value="{{ old('rate', $purchase->rate) }}" step="0.01" required />
                    </div>
                </x-card>
            </div>

            {{-- Financial Visualizer --}}
            <x-card class="bg-slate-900 border-none shadow-2xl relative overflow-hidden group">
                <div class="absolute top-0 right-0 p-8 opacity-10 group-hover:opacity-20 transition-opacity">
                    <svg class="w-32 h-32 text-white" fill="currentColor" viewBox="0 0 20 20"><path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z" /><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.692C6.604 6.236 6 7.013 6 8s.604 1.764 1.324 2.216A4.535 4.535 0 009 10.908V12.9a4.535 4.535 0 00-1.676-.692 1 1 0 10-.324 1.973A6.535 6.535 0 019 14.908V16a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.692c.72-.452 1.324-1.23 1.324-2.216s-.604-1.764-1.324-2.216A4.535 4.535 0 0011 10.092V8.1a4.535 4.535 0 001.676.692 1 1 0 10.324-1.973A6.535 6.535 0 0111 5.092V4z" clip-rule="evenodd" /></svg>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-12 relative items-center">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">GST Benchmark (%)</label>
                        <input type="number" name="gst_percentage" id="gst_p" value="{{ old('gst_percentage', $purchase->gst_percentage) }}" step="0.1" 
                               class="w-full bg-slate-800 border-slate-700 rounded-2xl py-4 px-6 text-xl font-black text-white outline-none focus:ring-4 focus:ring-primary-500/20 transition-all border">
                    </div>
                    
                    <div class="space-y-2">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Calculated Tax</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-slate-500 font-bold text-lg">₹</span>
                            <input type="number" name="gst_amount" id="gst_a" value="{{ $purchase->gst_amount }}" step="0.01" readonly 
                                   class="bg-transparent border-none p-0 text-3xl font-black text-slate-300 w-full focus:ring-0">
                        </div>
                    </div>

                    <div class="bg-white/5 rounded-[2.5rem] p-8 border border-white/10 backdrop-blur-md">
                        <p class="text-[10px] font-black text-primary-400 uppercase tracking-[0.3em] mb-4">Total Payable</p>
                        <div class="flex items-baseline gap-2">
                            <span class="text-primary-500 font-black text-2xl">₹</span>
                            <input type="number" name="total_amount" id="total" value="{{ $purchase->total_amount }}" step="0.01" required readonly 
                                   class="bg-transparent border-none p-0 text-5xl font-black text-white w-full focus:ring-0">
                        </div>
                    </div>
                </div>
            </x-card>

            <div class="flex flex-col md:flex-row justify-end items-center gap-6">
                <a href="{{ route('purchases.show', $purchase->id) }}" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-rose-500 transition-colors">Discard Changes</a>
                <x-button variant="primary" size="lg" type="submit" class="w-full md:w-auto px-16 py-5 rounded-[2rem] shadow-primary-500/20">
                    <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></x-slot>
                    Update Record
                </x-button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    const qty = document.getElementById('qty');
    const rate = document.getElementById('rate');
    const gstP = document.getElementById('gst_p');
    const gstA = document.getElementById('gst_a');
    const total = document.getElementById('total');

    function calculate() {
        const q = parseFloat(qty.value) || 0;
        const r = parseFloat(rate.value) || 0;
        const gp = parseFloat(gstP.value) || 0;
        
        const taxable = q * r;
        const ga = taxable * (gp / 100);
        const t = taxable + ga;
        
        gstA.value = ga.toFixed(2);
        total.value = t.toFixed(2);
    }

    [qty, rate, gstP].forEach(el => el.addEventListener('input', calculate));
    calculate();
</script>
@endpush
@endsection
