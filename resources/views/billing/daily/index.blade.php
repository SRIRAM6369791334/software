@extends('layouts.app')
@section('title', 'Daily Billing')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Daily Billing</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage daily sales transactions and invoices</p>
    </div>
    <div class="flex gap-2">
        <button onclick="document.getElementById('add-bill-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
            + Record Sale
        </button>
        <a href="{{ route('billing.daily.export') }}" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-sm font-medium rounded-lg transition-colors">⬇ Export CSV</a>
    </div>
</div>

<form method="GET" class="mb-4 max-w-sm">
    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
        <input type="text" name="search" value="{{ $search }}" placeholder="Search by customer or item..."
               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
    </div>
</form>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Date</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Customer</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Products</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">Total Qty</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">Net Amount</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-400 uppercase">Status</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($bills as $bill)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3.5 text-gray-500">{{ $bill->date->format('d M Y') }}</td>
                        <td class="px-5 py-3.5 font-medium text-gray-900">{{ $bill->customer->name ?? '—' }}</td>
                        <td class="px-5 py-3.5">
                            @php
                                $firstItem = $bill->items->first();
                                $othersCount = $bill->items->count() - 1;
                            @endphp
                            <span class="text-gray-700 font-medium">{{ $firstItem?->item_name }}</span>
                            @if($othersCount > 0)
                                <span class="text-[10px] text-emerald-600 font-bold ml-1">+{{ $othersCount }} MORE</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right font-mono text-gray-500">{{ number_format($bill->items->sum('quantity_kg'), 2) }} kg</td>
                        <td class="px-5 py-3.5 text-right font-mono font-bold text-gray-900">₹{{ number_format($bill->net_amount, 2) }}</td>
                        <td class="px-5 py-3.5 text-center">
                            @php
                                $statusColors = ['Generated'=>'bg-blue-50 text-blue-700','Pending'=>'bg-amber-50 text-amber-700','Paid'=>'bg-emerald-50 text-emerald-700'];
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$bill->status] ?? 'bg-gray-100' }}">
                                {{ $bill->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <a href="{{ route('billing.daily.invoice', $bill->id) }}" target="_blank" class="text-emerald-600 font-bold hover:underline">Bill 📄</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">No daily bills recorded yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $bills->withQueryString()->links() }}</div>
</div>

{{-- Add Modal --}}
<div id="add-bill-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl border border-gray-100 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-base font-black text-gray-900 uppercase tracking-tight">Record New Daily Sale 🛒</h2>
            <button onclick="document.getElementById('add-bill-modal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-2xl transition-colors">✕</button>
        </div>
        <form action="{{ route('billing.daily.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Select Customer *</label>
                    <select name="customer_id" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                        <option value="">Choose...</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Date *</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl">
                        <option value="Generated">Generated</option>
                        <option value="Pending">Pending</option>
                        <option value="Paid">Paid</option>
                    </select>
                </div>
            </div>

            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-[10px] font-bold text-gray-900 uppercase tracking-widest">Sale Items</h3>
                    <button type="button" onclick="addSaleRow()" class="px-3 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black rounded-lg hover:bg-emerald-100 uppercase">+ Add Row</button>
                </div>
                <div class="border border-gray-100 rounded-xl overflow-hidden">
                    <table class="w-full text-xs" id="sale-items-table">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-400 uppercase font-bold">Item / Description</th>
                                <th class="px-4 py-2 text-left text-gray-400 uppercase font-bold w-24">Qty (kg)</th>
                                <th class="px-4 py-2 text-left text-gray-400 uppercase font-bold w-32">Rate / kg</th>
                                <th class="px-4 py-2 text-right text-gray-400 uppercase font-bold w-32">Total (₹)</th>
                                <th class="px-4 py-2 w-10"></th>
                            </tr>
                        </thead>
                        <tbody id="sale-items-body">
                            <tr class="item-row border-t border-gray-50">
                                <td class="p-2"><input type="text" name="items[0][name]" required placeholder="e.g. Broiler Birds" class="w-full px-3 py-1.5 border-none bg-transparent outline-none"></td>
                                <td class="p-2"><input type="number" name="items[0][qty]" step="0.01" required placeholder="0.00" class="w-full px-3 py-1.5 border-none bg-transparent outline-none row-qty" oninput="recalcSales()"></td>
                                <td class="p-2"><input type="number" name="items[0][rate]" step="0.01" required placeholder="0.00" class="w-full px-3 py-1.5 border-none bg-transparent outline-none row-rate" oninput="recalcSales()"></td>
                                <td class="p-2 text-right font-bold text-gray-900"><span class="row-total">₹0.00</span></td>
                                <td class="p-2"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center bg-gray-50 p-6 rounded-2xl border border-gray-100">
                <div class="flex items-center gap-6">
                    <div class="w-24">
                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">GST %</label>
                        <input type="number" name="gst_percentage" id="gst-percentage" value="18" class="w-full px-3 py-2 bg-white border border-gray-200 rounded-lg text-sm font-bold" oninput="recalcSales()">
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Tax Amount</p>
                        <p id="display-tax" class="text-sm font-mono font-bold text-gray-600">₹0.00</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mb-1">Total Net Payable</p>
                    <p id="display-total" class="text-3xl font-black text-gray-900">₹0.00</p>
                    <input type="hidden" name="amount" id="total-hidden">
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <button type="button" onclick="document.getElementById('add-bill-modal').classList.add('hidden')" class="px-6 py-2.5 text-sm font-bold text-gray-400 hover:text-gray-600">Cancel</button>
                <button type="submit" class="px-10 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-black rounded-xl shadow-lg shadow-emerald-500/20 active:scale-95 transition-all">
                    Generate Invoice & Record Sale 🚀
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
let saleRowCount = 1;

function addSaleRow() {
    const body = document.getElementById('sale-items-body');
    const newRow = document.createElement('tr');
    newRow.className = 'item-row border-t border-gray-50';
    newRow.innerHTML = `
        <td class="p-2"><input type="text" name="items[${saleRowCount}][name]" required class="w-full px-3 py-1.5 border-none bg-transparent outline-none"></td>
        <td class="p-2"><input type="number" name="items[${saleRowCount}][qty]" step="0.01" required class="w-full px-3 py-1.5 border-none bg-transparent outline-none row-qty" oninput="recalcSales()"></td>
        <td class="p-2"><input type="number" name="items[${saleRowCount}][rate]" step="0.01" required class="w-full px-3 py-1.5 border-none bg-transparent outline-none row-rate" oninput="recalcSales()"></td>
        <td class="p-2 text-right font-bold text-gray-900"><span class="row-total">₹0.00</span></td>
        <td class="p-2 text-center text-red-400 cursor-pointer hover:text-red-600" onclick="this.closest('tr').remove(); recalcSales();">✕</td>
    `;
    body.appendChild(newRow);
    saleRowCount++;
}

function recalcSales() {
    let subtotal = 0;
    document.querySelectorAll('.item-row').forEach(row => {
        const qty = parseFloat(row.querySelector('.row-qty').value) || 0;
        const rate = parseFloat(row.querySelector('.row-rate').value) || 0;
        const total = qty * rate;
        row.querySelector('.row-total').textContent = '₹' + total.toFixed(2);
        subtotal += total;
    });

    const gstP = parseFloat(document.getElementById('gst-percentage').value) || 0;
    const gstA = subtotal * gstP / 100;
    const final = subtotal + gstA;

    document.getElementById('display-tax').textContent = '₹' + gstA.toFixed(2);
    document.getElementById('display-total').textContent = '₹' + final.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    document.getElementById('total-hidden').value = subtotal.toFixed(2);
}
</script>
@endpush
