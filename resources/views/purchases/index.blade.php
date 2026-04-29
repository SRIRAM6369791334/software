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
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Vendor Name *</label>
                <input type="text" name="vendor_name" required value="{{ old('vendor_name') }}" placeholder="Vendor name"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Item *</label>
                <select name="item" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none" onchange="recalculate()">
                    <option value="">Select item…</option>
                    @foreach(['Feed','Chicks','Medicines','Accessories'] as $item)
                        <option value="{{ $item }}" {{ old('item') === $item ? 'selected' : '' }}>{{ $item }}</option>
                    @endforeach
                </select></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Date *</label>
                <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Quantity *</label>
                <input type="number" name="quantity" id="qty" required step="0.01" min="0.01" value="{{ old('quantity') }}"
                       oninput="recalculate()" placeholder="0.00"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Unit</label>
                <input type="text" name="unit" value="{{ old('unit', 'kg') }}" placeholder="kg"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Rate (₹) *</label>
                <input type="number" name="rate" id="rate" required step="0.01" min="0.01" value="{{ old('rate') }}"
                       oninput="recalculate()" placeholder="0.00"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">GST % *</label>
                <input type="number" name="gst_percentage" id="gst" required step="0.01" min="0" max="28" value="{{ old('gst_percentage', 18) }}"
                       oninput="recalculate()"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Payment Mode *</label>
                <select name="payment_mode" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    @foreach(['NEFT','Cheque','UPI','Cash'] as $mode)
                        <option value="{{ $mode }}" {{ old('payment_mode') === $mode ? 'selected' : '' }}>{{ $mode }}</option>
                    @endforeach
                </select></div>
        </div>

        {{-- Live Calculation --}}
        <div class="mt-5 p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between gap-6">
            <div class="text-sm text-gray-600"><span class="text-gray-400">Base Amount: </span><strong id="base-amt">₹0.00</strong></div>
            <div class="text-sm text-gray-600"><span class="text-gray-400">GST Amount: </span><strong id="gst-amt">₹0.00</strong></div>
            <div class="text-base font-bold text-emerald-700"><span class="text-gray-500 font-normal">Total: </span><span id="total-amt">₹0.00</span></div>
        </div>

        <div class="flex justify-end mt-5">
            <button type="submit"
                    class="px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
                Record Purchase
            </button>
        </div>
    </form>
</div>

{{-- Recent Purchases --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
        <h2 class="text-base font-semibold text-gray-900">Recent Purchases</h2>
        <form method="GET">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search..."
                   class="px-3 py-1.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
        </form>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Date</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Vendor</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Item</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">Qty</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">Rate</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">GST</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">Total</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-400 uppercase">Mode</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($purchases as $p)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3.5 text-gray-500">{{ $p->date->format('d M Y') }}</td>
                        <td class="px-5 py-3.5 font-medium text-gray-900">{{ $p->vendor_name }}</td>
                        <td class="px-5 py-3.5"><span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-xs rounded-full font-medium">{{ $p->item }}</span></td>
                        <td class="px-5 py-3.5 text-right font-mono text-gray-600">{{ number_format($p->quantity, 2) }} {{ $p->unit }}</td>
                        <td class="px-5 py-3.5 text-right font-mono text-gray-600">₹{{ number_format($p->rate, 2) }}</td>
                        <td class="px-5 py-3.5 text-right font-mono text-gray-500">₹{{ number_format($p->gst_amount, 2) }}</td>
                        <td class="px-5 py-3.5 text-right font-mono font-semibold text-gray-900">₹{{ number_format($p->total_amount, 0, '.', ',') }}</td>
                        <td class="px-5 py-3.5 text-center"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">{{ $p->payment_mode }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400">No purchases recorded</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $purchases->withQueryString()->links() }}</div>
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
    document.getElementById('base-amt').textContent = '₹' + base.toFixed(2);
    document.getElementById('gst-amt').textContent  = '₹' + gstAmt.toFixed(2);
    document.getElementById('total-amt').textContent = '₹' + total.toFixed(2);
}
</script>
@endpush
