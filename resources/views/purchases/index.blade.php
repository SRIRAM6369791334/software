@extends('layouts.app')
@section('title', 'Purchase Entry')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Purchase Entry</h1>
        <p class="text-sm text-gray-500 mt-0.5">Record incoming purchases from vendors</p>
    </div>
    <a href="{{ route('purchases.export') }}" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 hover:bg-gray-50 text-sm font-medium rounded-lg transition-colors">
        ⬇ Export CSV
    </a>
</div>

{{-- Entry Form --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 mb-6">
    <form action="{{ route('purchases.store') }}" method="POST" id="purchase-form">
        @csrf
        
        {{-- Header Info --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8 pb-6 border-b border-gray-100">
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">1. Vendor Name *</label>
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
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">2. Billing Date *</label>
                <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}"
                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">3. Payment Mode *</label>
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
                        {{-- First Row (Default) --}}
                        <tr class="border-t border-gray-50 item-row">
                            <td class="p-3">
                                <input type="text" name="items[0][name]" list="item-options" required placeholder="Select or type..." class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm">
                            </td>
                            <td class="p-3">
                                <input type="number" name="items[0][qty]" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm row-qty" oninput="recalculate()">
                            </td>
                            <td class="p-3">
                                <input type="text" name="items[0][unit]" value="kg" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm">
                            </td>
                            <td class="p-3">
                                <input type="number" name="items[0][rate]" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm row-rate" oninput="recalculate()">
                            </td>
                            <td class="p-3 text-right">
                                <span class="font-bold text-gray-900 row-total">₹0.00</span>
                            </td>
                            <td class="p-3 text-center">
                                {{-- First row can't be deleted --}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <datalist id="item-options">
            @foreach(['Feed','Chicks','Medicines','Accessories'] as $item)
                <option value="{{ $item }}">
            @endforeach
        </datalist>

        {{-- Summary and Submit --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
            <div class="space-y-4">
                <div class="p-4 bg-gray-50 border border-gray-100 rounded-xl">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Tax Configuration</label>
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
                    <span class="text-xs font-bold uppercase tracking-widest opacity-80">Final Net Bill</span>
                    <span id="display-total" class="text-3xl font-black">₹0.00</span>
                </div>
                <button type="submit" class="w-full py-3 bg-white text-emerald-700 font-black rounded-xl hover:bg-emerald-50 transition-all active:scale-95 shadow-sm">
                    Confirm & Record Purchase 💾
                </button>
            </div>
        </div>
    </form>
</div>

{{-- Recent Purchases --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-base font-semibold text-gray-900">Recent Purchases</h2>
        <form method="GET">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search vendor or items..."
                   class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Date</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Vendor</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Items</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">Total Amount</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-400 uppercase">Mode</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($purchases as $p)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3.5 text-gray-500">{{ $p->date->format('d M Y') }}</td>
                        <td class="px-5 py-3.5 font-medium text-gray-900">{{ $p->vendor_name }}</td>
                        <td class="px-5 py-3.5">
                            @php
                                $firstItem = $p->items->first();
                                $othersCount = $p->items->count() - 1;
                            @endphp
                            <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs rounded-full font-medium">
                                {{ $firstItem?->item_name }}
                                @if($othersCount > 0)
                                    <span class="opacity-60">+{{ $othersCount }} more</span>
                                @endif
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right font-mono font-semibold text-gray-900">₹{{ number_format($p->total_amount, 2) }}</td>
                        <td class="px-5 py-3.5 text-center"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">{{ $p->payment_mode }}</span></td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('purchases.show', $p->id) }}" class="text-emerald-600 font-bold hover:underline">View 📄</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No purchases recorded</td></tr>
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

function addRow() {
    const body = document.getElementById('items-body');
    const newRow = document.createElement('tr');
    newRow.className = 'border-t border-gray-50 item-row';
    newRow.innerHTML = `
        <td class="p-3">
            <input type="text" name="items[${rowCount}][name]" list="item-options" required placeholder="Select or type..." class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm">
        </td>
        <td class="p-3">
            <input type="number" name="items[${rowCount}][qty]" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm row-qty" oninput="recalculate()">
        </td>
        <td class="p-3">
            <input type="text" name="items[${rowCount}][unit]" value="kg" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm">
        </td>
        <td class="p-3">
            <input type="number" name="items[${rowCount}][rate]" step="0.01" required placeholder="0.00" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg focus:border-emerald-500 outline-none text-sm row-rate" oninput="recalculate()">
        </td>
        <td class="p-3 text-right">
            <span class="font-bold text-gray-900 row-total">₹0.00</span>
        </td>
        <td class="p-3 text-center">
            <button type="button" onclick="this.closest('tr').remove(); recalculate();" class="text-red-400 hover:text-red-600 transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            </button>
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
    
    const gstPercentage = parseFloat(document.getElementById('gst-percentage').value) || 0;
    const gstAmt = subtotal * gstPercentage / 100;
    const finalTotal = subtotal + gstAmt;
    
    document.getElementById('display-tax').value = '₹' + gstAmt.toFixed(2);
    document.getElementById('display-total').textContent = '₹' + finalTotal.toLocaleString('en-IN', { minimumFractionDigits: 2 });
}
</script>
@endpush
