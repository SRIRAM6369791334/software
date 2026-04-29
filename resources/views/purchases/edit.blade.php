@extends('layouts.app')
@section('title', 'Edit Purchase')

@section('content')
<div class="mb-6">
    <a href="{{ route('purchases.show', $purchase->id) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Invoice</a>
    <h1 class="text-2xl font-bold text-gray-900">Edit Purchase Entry</h1>
</div>

<div class="max-w-4xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('purchases.update', $purchase->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2">1. Supplier Info</h3>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-700 uppercase">Vendor Name</label>
                        <input type="text" name="vendor_name" value="{{ old('vendor_name', $purchase->vendor_name) }}" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-700 uppercase">Purchase Date</label>
                        <input type="date" name="date" value="{{ old('date', $purchase->date->format('Y-m-d')) }}" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2">2. Item Details</h3>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-700 uppercase">Item Type</label>
                        <select name="item" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                            @foreach(['Feed', 'Chicks', 'Medicines', 'Accessories'] as $item)
                                <option value="{{ $item }}" {{ $purchase->item === $item ? 'selected' : '' }}>{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">Quantity</label>
                            <input type="number" name="quantity" id="qty" value="{{ old('quantity', $purchase->quantity) }}" step="0.01" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase">Unit</label>
                            <input type="text" name="unit" value="{{ old('unit', $purchase->unit) }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                        </div>
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-700 uppercase">Rate (₹)</label>
                        <input type="number" name="rate" id="rate" value="{{ old('rate', $purchase->rate) }}" step="0.01" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-6 border border-gray-100">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-end">
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-700 uppercase">GST Percentage (%)</label>
                        <input type="number" name="gst_percentage" id="gst_p" value="{{ old('gst_percentage', $purchase->gst_percentage) }}" step="0.1" class="w-full px-4 py-2 bg-white border border-gray-200 rounded-lg">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-700 uppercase">GST Amount (₹)</label>
                        <input type="number" name="gst_amount" id="gst_a" value="{{ $purchase->gst_amount }}" readonly class="w-full px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg text-gray-500 font-mono">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-emerald-700 uppercase tracking-tight">Total Amount (₹)</label>
                        <input type="number" name="total_amount" id="total" value="{{ $purchase->total_amount }}" required readonly class="w-full px-4 py-3 bg-emerald-100 border border-emerald-300 text-emerald-900 text-lg font-black rounded-lg">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 gap-3">
                <a href="{{ route('purchases.show', $purchase->id) }}" class="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700">Cancel</a>
                <button type="submit" class="px-10 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-lg transition-all active:scale-95">
                    Update Entry 💾
                </button>
            </div>
        </form>
    </div>
</div>

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
@endsection
