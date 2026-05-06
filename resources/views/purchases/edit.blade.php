@extends('layouts.app')
@section('title', 'Edit Purchase')

@section('content')
<div class="mb-6">
    <a href="{{ route('purchases.show', $purchase->id) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Invoice</a>
    <h1 class="text-2xl font-bold text-gray-900">Edit Purchase Entry</h1>
</div>

<div class="max-w-4xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('purchases.update', $purchase->id) }}" method="POST" id="purchase-form" class="p-6">
            @csrf
            @method('PUT')
            
            {{-- Header Info --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 pb-6 border-b border-gray-100">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Supplier</label>
                    <select name="vendor_name" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->firm_name }}" {{ $purchase->vendor_name === $vendor->firm_name ? 'selected' : '' }}>
                                {{ $vendor->firm_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Billing Date</label>
                    <input type="date" name="date" required value="{{ old('date', $purchase->date->format('Y-m-d')) }}"
                           class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Payment Mode</label>
                    <select name="payment_mode" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg">
                        @foreach(['NEFT','Cheque','UPI','Cash'] as $mode)
                            <option value="{{ $mode }}" {{ $purchase->payment_mode === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-gray-900 uppercase tracking-tight">Purchase Items</h3>
                    <button type="button" onclick="addRow()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg hover:bg-emerald-100 transition-colors">
                        + Add New Item
                    </button>
                </div>
                
                <div class="overflow-x-auto border border-gray-100 rounded-xl">
                    <table class="w-full text-sm" id="items-table">
                        <thead>
                            <tr class="bg-gray-50 text-left">
                                <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase">Item Description</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase w-24">Qty</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase w-20">Unit</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase w-32">Rate (₹)</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase w-32 text-right">Total (₹)</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            @foreach($purchase->items as $index => $item)
                            <tr class="border-t border-gray-50 item-row">
                                <td class="p-3">
                                    <input type="text" name="items[{{ $index }}][name]" value="{{ $item->item_name }}" required class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm">
                                </td>
                                <td class="p-3">
                                    <input type="number" name="items[{{ $index }}][qty]" value="{{ $item->quantity }}" step="0.01" required class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm row-qty" oninput="recalculate()">
                                </td>
                                <td class="p-3">
                                    <input type="text" name="items[{{ $index }}][unit]" value="{{ $item->unit }}" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm">
                                </td>
                                <td class="p-3">
                                    <input type="number" name="items[{{ $index }}][rate]" value="{{ $item->rate }}" step="0.01" required class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm row-rate" oninput="recalculate()">
                                </td>
                                <td class="p-3 text-right">
                                    <span class="font-bold text-gray-900 row-total">₹{{ number_format($item->quantity * $item->rate, 2) }}</span>
                                </td>
                                <td class="p-3 text-center">
                                    @if($index > 0)
                                    <button type="button" onclick="this.closest('tr').remove(); recalculate();" class="text-red-400 hover:text-red-600 transition-colors">🗑️</button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Summary --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
                <div class="p-4 bg-gray-50 border border-gray-100 rounded-xl">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Tax configuration</label>
                    <div class="flex items-center gap-4">
                        <div class="w-24">
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">GST %</label>
                            <input type="number" name="gst_percentage" id="gst-percentage" value="{{ $purchase->gst_percentage }}" step="0.1" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm" oninput="recalculate()">
                        </div>
                        <div class="flex-1">
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">GST Amount (₹)</label>
                            <input type="text" id="display-tax" readonly value="₹{{ number_format($purchase->gst_amount, 2) }}" class="w-full px-3 py-2 bg-gray-100 border border-gray-100 rounded-lg text-sm text-gray-500 font-mono">
                        </div>
                    </div>
                </div>

                <div class="bg-gray-900 rounded-2xl p-6 text-white shadow-xl">
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-xs font-bold uppercase opacity-80">Total Bill Amount</span>
                        <span id="display-total" class="text-3xl font-black">₹{{ number_format($purchase->total_amount, 2) }}</span>
                    </div>
                    <button type="submit" class="w-full py-3 bg-emerald-600 text-white font-black rounded-xl hover:bg-emerald-700 transition-all active:scale-95 shadow-sm">
                        Update Purchase Entry 💾
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let rowCount = {{ $purchase->items->count() }};

function addRow() {
    const body = document.getElementById('items-body');
    const newRow = document.createElement('tr');
    newRow.className = 'border-t border-gray-50 item-row';
    newRow.innerHTML = `
        <td class="p-3">
            <input type="text" name="items[${rowCount}][name]" required class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm">
        </td>
        <td class="p-3">
            <input type="number" name="items[${rowCount}][qty]" step="0.01" required class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm row-qty" oninput="recalculate()">
        </td>
        <td class="p-3">
            <input type="text" name="items[${rowCount}][unit]" value="kg" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm">
        </td>
        <td class="p-3">
            <input type="number" name="items[${rowCount}][rate]" step="0.01" required class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm row-rate" oninput="recalculate()">
        </td>
        <td class="p-3 text-right">
            <span class="font-bold text-gray-900 row-total">₹0.00</span>
        </td>
        <td class="p-3 text-center">
            <button type="button" onclick="this.closest('tr').remove(); recalculate();" class="text-red-400 hover:text-red-600 transition-colors">🗑️</button>
        </td>
    `;
    body.appendChild(newRow);
    rowCount++;
}

function recalculate() {
    let subtotal = 0;
    const rows = document.querySelectorAll('.item-row');
    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
        const rate = parseFloat(row.querySelector('.row-rate').value) || 0;
        const total = qty * rate;
        row.querySelector('.row-total').textContent = '₹' + total.toFixed(2);
        subtotal += total;
    });
    const gstP = parseFloat(document.getElementById('gst-percentage').value) || 0;
    const gstA = subtotal * gstP / 100;
    const final = subtotal + gstA;
    document.getElementById('display-tax').value = '₹' + gstA.toFixed(2);
    document.getElementById('display-total').textContent = '₹' + final.toLocaleString('en-IN', { minimumFractionDigits: 2 });
}
</script>
@endsection
