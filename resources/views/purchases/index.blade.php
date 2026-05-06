@extends('layouts.app')
@section('title', 'Purchase Entry')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Purchase Entry</h1>
        <p class="text-sm text-gray-500 mt-0.5">Record incoming stock and link to flock batches</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('inventory.stock.index') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 hover:bg-gray-50 text-sm font-medium rounded-lg transition-colors">
            📦 Stock Status
        </a>
        <a href="{{ route('purchases.export') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 hover:bg-gray-50 text-sm font-medium rounded-lg transition-colors">
            ⬇ Export CSV
        </a>
    </div>
</div>

{{-- Entry Form --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <form action="{{ route('purchases.store') }}" method="POST" id="purchase-form">
        @csrf
        
        {{-- Header Info --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8 pb-6 border-b border-gray-100">
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">1. Vendor *</label>
                <select name="vendor_name" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    <option value="">Select vendor…</option>
                    @foreach($vendors as $vendor)
                        <option value="{{ $vendor->firm_name }}" {{ old('vendor_name') === $vendor->firm_name ? 'selected' : '' }}>
                            {{ $vendor->firm_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">2. Invoice No</label>
                <input type="text" name="invoice_no" value="{{ old('invoice_no') }}" placeholder="Enter Bill #"
                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">3. Billing Date *</label>
                <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}"
                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">4. Payment Mode *</label>
                <select name="payment_mode" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    @foreach(['NEFT','Cheque','UPI','Cash'] as $mode)
                        <option value="{{ $mode }}" {{ old('payment_mode') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Dynamic Items Table --}}
        <div class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-tight">Refill Items & Stock Linkages</h3>
                <button type="button" onclick="addRow()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-bold rounded-lg hover:bg-emerald-100 transition-colors border border-emerald-100">
                    + Add Product Row
                </button>
            </div>
            
            <div class="overflow-x-auto border border-gray-100 rounded-xl">
                <table class="w-full text-sm" id="items-table">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase min-w-[200px]">Product / Item</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase min-w-[150px]">Link to Batch (Flock)</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase min-w-[150px]">Warehouse</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase w-24">Qty</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase w-20">Unit</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase w-32">Rate (₹)</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase w-32 text-right">Total (₹)</th>
                            <th class="px-4 py-3 text-[10px] font-bold text-gray-400 uppercase w-10"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        {{-- First Row (Default) --}}
                        <tr class="border-t border-gray-50 item-row">
                            <td class="p-3">
                                <select name="items[0][item_id]" required onchange="updateUnit(this)" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm font-bold item-selector">
                                    <option value="">Select Item Master...</option>
                                    @foreach($items as $item)
                                        <option value="{{ $item->id }}" data-unit="{{ $item->base_unit }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }} ({{ $item->code }})
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-3">
                                <select name="items[0][batch_id]" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm">
                                    <option value="">General (No Batch)</option>
                                    @foreach($batches as $batch)
                                        <option value="{{ $batch->id }}">{{ $batch->batch_code }} - {{ $batch->breed }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-3">
                                <select name="items[0][warehouse_id]" required class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm">
                                    <option value="">Select Location...</option>
                                    @foreach($warehouses as $wh)
                                        <option value="{{ $wh->id }}" {{ $loop->first ? 'selected' : '' }}>{{ $wh->name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="p-3">
                                <input type="number" name="items[0][qty]" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm row-qty" oninput="recalculate()">
                            </td>
                            <td class="p-3">
                                <input type="text" name="items[0][unit]" value="kg" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-[10px] font-bold uppercase row-unit">
                            </td>
                            <td class="p-3">
                                <input type="number" name="items[0][rate]" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm row-rate" oninput="recalculate()">
                            </td>
                            <td class="p-3 text-right">
                                <span class="font-bold text-gray-900 row-total">₹0.00</span>
                            </td>
                            <td class="p-3 text-center"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Summary and Submit --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 border border-gray-100 rounded-xl">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Tax & Billing Configuration</label>
                    <div class="flex items-center gap-4">
                        <div class="w-24">
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">GST %</label>
                            <input type="number" name="gst_percentage" id="gst-percentage" value="18" step="0.1" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm" oninput="recalculate()">
                        </div>
                        <div class="flex-1">
                            <label class="block text-[10px] font-medium text-gray-500 mb-1">Total Tax Amount (₹)</label>
                            <input type="text" id="display-tax" readonly value="₹0.00" class="w-full px-3 py-2 bg-gray-100 border border-gray-100 rounded-lg text-sm text-gray-500 font-mono">
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-emerald-600 rounded-2xl p-6 shadow-xl shadow-emerald-600/20 text-white">
                <div class="flex items-center justify-between mb-4">
                    <span class="text-xs font-bold uppercase tracking-widest opacity-80">Final Net Bill Value</span>
                    <span id="display-total" class="text-3xl font-black">₹0.00</span>
                </div>
                <button type="submit" class="w-full py-3 bg-white text-emerald-700 font-black rounded-xl hover:bg-emerald-50 transition-all active:scale-95 shadow-sm">
                    Confirm & Refill Inventory 💾
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Recent Purchases --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-base font-semibold text-gray-900">Recent Purchase Logs</h2>
        <form method="GET">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search vendor or invoice..."
                   class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase">Date</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase">Vendor & Invoice</th>
                    <th class="px-5 py-3.5 text-xs font-semibold text-gray-400 uppercase">Refilled Items</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">Net Amount</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-400 uppercase">Mode</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($purchases as $p)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3.5 text-gray-500">{{ $p->date->format('d M Y') }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900">{{ $p->vendor_name }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tight">Inv: {{ $p->invoice_no ?: 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex flex-wrap gap-1">
                                @foreach($p->items as $item)
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] rounded-full font-bold">
                                        {{ $item->item_name }} ({{ number_format($item->quantity) }} {{ $item->unit }})
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-right font-black text-gray-900">₹{{ number_format($p->total_amount, 2) }}</td>
                        <td class="px-5 py-3.5 text-center"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-[10px] font-black uppercase rounded-full tracking-widest">{{ $p->payment_mode }}</span></td>
                        <td class="px-5 py-3.5 text-right">
                            <div class="flex justify-end gap-2">
                                <a href="{{ route('purchases.show', $p->id) }}" class="text-emerald-600 font-bold hover:underline text-xs">View</a>
                                <a href="{{ route('purchases.edit', $p->id) }}" class="text-blue-600 font-bold hover:underline text-xs">Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No purchases recorded yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $purchases->withQueryString()->links() }}</div>
</div>
@endsection

@push('scripts')
<script>
let rowCount = 1;

const ITEM_OPTIONS = `@foreach($items as $item)<option value="{{ $item->id }}" data-unit="{{ $item->base_unit }}">{{ $item->name }} ({{ $item->code }})</option>@endforeach`;
const BATCH_OPTIONS = `@foreach($batches as $batch)<option value="{{ $batch->id }}">{{ $batch->batch_code }} - {{ $batch->breed }}</option>@endforeach`;
const WH_OPTIONS = `@foreach($warehouses as $wh)<option value="{{ $wh->id }}">{{ $wh->name }}</option>@endforeach`;

function addRow() {
    const body = document.getElementById('items-body');
    const newRow = document.createElement('tr');
    newRow.className = 'border-t border-gray-50 item-row';
    newRow.innerHTML = `
        <td class="p-3">
            <select name="items[${rowCount}][item_id]" required onchange="updateUnit(this)" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm font-bold item-selector">
                <option value="">Select Item Master...</option>
                ${ITEM_OPTIONS}
            </select>
        </td>
        <td class="p-3">
            <select name="items[${rowCount}][batch_id]" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm">
                <option value="">General (No Batch)</option>
                ${BATCH_OPTIONS}
            </select>
        </td>
        <td class="p-3">
            <select name="items[${rowCount}][warehouse_id]" required class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm">
                <option value="">Select Location...</option>
                ${WH_OPTIONS}
            </select>
        </td>
        <td class="p-3">
            <input type="number" name="items[${rowCount}][qty]" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm row-qty" oninput="recalculate()">
        </td>
        <td class="p-3">
            <input type="text" name="items[${rowCount}][unit]" value="kg" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-[10px] font-bold uppercase row-unit">
        </td>
        <td class="p-3">
            <input type="number" name="items[${rowCount}][rate]" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm row-rate" oninput="recalculate()">
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

function updateUnit(select) {
    const unit = select.options[select.selectedIndex].getAttribute('data-unit');
    const row = select.closest('tr');
    if (unit) {
        row.querySelector('.row-unit').value = unit;
    }
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
    
    const gstPercentage = parseFloat(document.getElementById('gst-percentage').value) || 0;
    const gstAmt = subtotal * gstPercentage / 100;
    const finalTotal = subtotal + gstAmt;
    
    document.getElementById('display-tax').value = '₹' + gstAmt.toFixed(2);
    document.getElementById('display-total').textContent = '₹' + finalTotal.toLocaleString('en-IN', { minimumFractionDigits: 2 });
}

// Initial unit update for first row if pre-selected
window.onload = function() {
    const selector = document.querySelector('.item-selector');
    if (selector && selector.value) updateUnit(selector);
}
</script>
@endpush
