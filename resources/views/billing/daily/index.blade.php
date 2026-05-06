@extends('layouts.app')
@section('title', 'Daily Billing')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Daily Billing</h1>
        <p class="text-gray-500 font-medium">Record counter sales and generate instant receipts</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <button onclick="document.getElementById('add-bill-modal').classList.remove('hidden')"
                class="px-6 py-4 bg-emerald-600 text-white text-sm font-black rounded-[1.5rem] hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-95">
            + Record New Sale 🛒
        </button>
        <a href="{{ route('billing.daily.export') }}" class="px-6 py-4 bg-white border border-gray-200 text-gray-400 hover:text-gray-900 text-sm font-bold rounded-[1.5rem] transition-all">
            ⬇ Export CSV
        </a>
    </div>
</div>

{{-- Sales Insights Header --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 flex items-center gap-6 group hover:border-emerald-200 transition-all">
        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">📉</div>
        <div>
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Today's Sales</h3>
            <p class="text-2xl font-black text-gray-900">₹{{ number_format($bills->where('date', date('Y-m-d'))->sum('net_amount'), 0) }}</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 flex items-center gap-6 group hover:border-blue-200 transition-all">
        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">📦</div>
        <div>
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Avg Ticket Size</h3>
            <p class="text-2xl font-black text-gray-900">₹{{ number_format($bills->avg('net_amount') ?: 0, 0) }}</p>
        </div>
    </div>
    <div class="bg-gray-900 p-6 rounded-[2.5rem] shadow-xl shadow-gray-900/20 text-white flex items-center gap-6">
        <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-2xl">⚡</div>
        <div>
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Counter Cash</h3>
            <p class="text-2xl font-black text-white">₹{{ number_format($bills->sum('net_amount'), 0) }}</p>
        </div>
    </div>
</div>

{{-- Daily Sales List --}}
<div class="bg-white rounded-[2.5rem] border border-gray-200 shadow-2xl overflow-hidden mb-12">
    <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gray-50/50">
        <form method="GET" class="relative w-full max-w-md">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search customer, item or route..."
                   class="w-full pl-12 pr-4 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-medium text-sm">
        </form>
        <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-widest">
            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            Showing {{ $bills->count() }} Recent Sales
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-gray-50/50 text-gray-400 font-black uppercase text-[10px] tracking-widest border-b border-gray-100">
                    <th class="px-8 py-5">Sale Date</th>
                    <th class="px-8 py-5">Customer</th>
                    <th class="px-8 py-5">Product Breakdown</th>
                    <th class="px-8 py-5 text-right">Net Weight</th>
                    <th class="px-8 py-5 text-right">Net Amount</th>
                    <th class="px-8 py-5 text-center">Status</th>
                    <th class="px-8 py-5 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($bills as $bill)
                    <tr class="hover:bg-emerald-50/30 transition-all group">
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="font-black text-gray-900 leading-tight">{{ $bill->date->format('d M') }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $bill->date->format('l, Y') }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center font-black text-gray-500 group-hover:bg-emerald-100 group-hover:text-emerald-600 transition-all">
                                    {{ substr($bill->customer->name ?? '?', 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-black text-gray-900 tracking-tight">{{ $bill->customer->name ?? '—' }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $bill->customer->phone ?? 'NO PHONE' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            @php
                                $firstItem = $bill->items->first();
                                $othersCount = $bill->items->count() - 1;
                            @endphp
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-gray-100 rounded-lg font-bold text-gray-700 text-xs border border-gray-200">{{ $firstItem?->item_name }}</span>
                                @if($othersCount > 0)
                                    <span class="px-2 py-1 bg-emerald-50 text-emerald-600 font-black text-[9px] rounded-md tracking-tighter">+{{ $othersCount }} OTHERS</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <span class="font-black text-gray-900">{{ number_format($bill->items->sum('quantity_kg'), 1) }}</span>
                            <span class="text-[10px] text-gray-400 font-bold uppercase ml-0.5">kg</span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <span class="text-lg font-black text-gray-900">₹{{ number_format($bill->net_amount, 0) }}</span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            @php
                                $statusMap = [
                                    'Generated' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => 'GENERATED'],
                                    'Pending'   => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'label' => 'PENDING'],
                                    'Paid'      => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'label' => 'PAID'],
                                ];
                                $st = $statusMap[$bill->status] ?? $statusMap['Pending'];
                            @endphp
                            <span class="px-3 py-1.5 {{ $st['bg'] }} {{ $st['text'] }} text-[9px] font-black rounded-lg tracking-[0.1em] shadow-sm">
                                {{ $st['label'] }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right flex items-center justify-end gap-2">
                            <a href="{{ route('billing.daily.invoice', $bill->id) }}" target="_blank" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-emerald-600 hover:border-emerald-200 hover:shadow-lg transition-all font-black text-xs uppercase tracking-tighter">
                                Print 📄
                            </a>
                            <a href="{{ route('billing.daily.pdf', $bill->id) }}"
                               class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-200 rounded-xl text-gray-400 hover:text-red-600 hover:border-red-200 hover:shadow-lg transition-all font-black text-xs uppercase tracking-tighter" title="Download PDF">
                                PDF 📥
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="text-6xl mb-6">🛒</div>
                                <h3 class="text-xl font-black text-gray-900">No Sales Recorded</h3>
                                <p class="text-gray-400 font-medium mt-1">Ready to record your first daily sale?</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bills->hasPages())
        <div class="p-8 border-t border-gray-50 bg-gray-50/30">
            {{ $bills->withQueryString()->links() }}
        </div>
    @endif
</div>

{{-- Record Sale Modal (Upgraded) --}}
<div id="add-bill-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/40 backdrop-blur-md transition-all">
    <div class="bg-white rounded-[3.5rem] shadow-2xl w-full max-w-5xl border border-white/20 overflow-hidden">
        <div class="flex items-center justify-between px-10 py-8 border-b border-gray-50 bg-gray-50/30">
            <div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight uppercase tracking-widest">Record Daily Sale 💸</h2>
                <p class="text-sm text-gray-500 font-medium">Add birds or items to generate an instant invoice</p>
            </div>
            <button onclick="document.getElementById('add-bill-modal').classList.add('hidden')" 
                    class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-gray-200 text-gray-400 hover:text-red-500 transition-all shadow-sm">✕</button>
        </div>

        <form action="{{ route('billing.daily.store') }}" method="POST" class="p-10">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">1. Customer</label>
                    <select name="customer_id" required class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold text-gray-900">
                        <option value="">Select customer…</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">2. Sale Date</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}" 
                           class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">3. Status</label>
                    <select name="status" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold">
                        <option value="Generated">Generated</option>
                        <option value="Pending">Pending</option>
                        <option value="Paid">Paid (Cash)</option>
                    </select>
                </div>
            </div>

            <div class="mb-10">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-[0.2em]">Inventory Items / birds</h3>
                    <button type="button" onclick="addSaleRow()" class="px-5 py-2.5 bg-emerald-50 text-emerald-700 text-[10px] font-black rounded-xl hover:bg-emerald-100 transition-all border border-emerald-100 shadow-sm uppercase">+ Add Item</button>
                </div>
                
                <div class="border border-gray-100 rounded-[2.5rem] overflow-hidden shadow-inner bg-gray-50/30">
                    <table class="w-full text-sm text-left" id="sale-items-table">
                        <thead>
                            <tr class="bg-gray-50/50 text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-100">
                                <th class="px-8 py-4">Item / Description</th>
                                <th class="px-8 py-4 w-32 text-center">Qty (Kg)</th>
                                <th class="px-8 py-4 w-40 text-right">Rate / Kg</th>
                                <th class="px-8 py-4 w-40 text-right">Subtotal</th>
                                <th class="px-8 py-4 w-12 text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="sale-items-body" class="divide-y divide-gray-50">
                            <tr class="item-row group">
                                <td class="px-6 py-4">
                                    <input type="text" name="items[0][name]" required placeholder="e.g. Live Broiler Birds" 
                                           class="w-full px-4 py-3 bg-transparent border-none focus:ring-0 font-black text-gray-900 outline-none placeholder-gray-300">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" name="items[0][qty]" step="0.01" required placeholder="0.00" 
                                           class="w-full px-4 py-3 bg-transparent border-none focus:ring-0 font-black text-center text-gray-900 outline-none row-qty" oninput="recalcSales()">
                                </td>
                                <td class="px-6 py-4">
                                    <input type="number" name="items[0][rate]" step="0.01" required placeholder="0.00" 
                                           class="w-full px-4 py-3 bg-transparent border-none focus:ring-0 font-black text-right text-gray-900 outline-none row-rate" oninput="recalcSales()">
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <span class="text-base font-black text-gray-900 row-total tracking-tighter">₹0.00</span>
                                </td>
                                <td class="px-4 py-4 text-center"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Totals Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center p-10 bg-gray-900 rounded-[3rem] shadow-2xl relative overflow-hidden">
                <div class="absolute -right-10 -top-10 opacity-5 text-[15rem] font-black pointer-events-none select-none text-white">BILL</div>
                
                <div class="flex items-center gap-12 relative z-10">
                    <div class="w-32">
                        <label class="block text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-3">Tax % (GST)</label>
                        <input type="number" name="gst_percentage" id="gst-percentage" value="18" 
                               class="w-full px-5 py-4 bg-white/10 border border-white/20 rounded-2xl text-xl font-black text-white outline-none focus:border-emerald-500 transition-all" oninput="recalcSales()">
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-emerald-400 uppercase tracking-widest mb-2">Calculated GST</p>
                        <p id="display-tax" class="text-2xl font-black text-white/50 tracking-tighter">₹0.00</p>
                    </div>
                </div>

                <div class="text-right relative z-10">
                    <p class="text-[10px] font-black text-emerald-400 uppercase tracking-[0.3em] mb-3">Grand Total Payable</p>
                    <p id="display-total" class="text-6xl font-black text-white tracking-tighter drop-shadow-2xl">₹0.00</p>
                    <input type="hidden" name="amount" id="total-hidden">
                </div>
            </div>

            <div class="flex justify-end gap-6 mt-12">
                <button type="button" onclick="document.getElementById('add-bill-modal').classList.add('hidden')" class="px-8 py-4 text-sm font-black text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest">Cancel</button>
                <button type="submit" class="px-12 py-5 bg-emerald-600 text-white font-black rounded-3xl hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-95 transform">
                    Generate Final Invoice 🚀
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
    newRow.className = 'item-row group border-t border-gray-50 transition-colors hover:bg-gray-50/50';
    newRow.innerHTML = `
        <td class="px-6 py-4">
            <input type="text" name="items[${saleRowCount}][name]" required class="w-full px-4 py-3 bg-transparent border-none focus:ring-0 font-black text-gray-900 outline-none">
        </td>
        <td class="px-6 py-4">
            <input type="number" name="items[${saleRowCount}][qty]" step="0.01" required class="w-full px-4 py-3 bg-transparent border-none focus:ring-0 font-black text-center text-gray-900 outline-none row-qty" oninput="recalcSales()">
        </td>
        <td class="px-6 py-4">
            <input type="number" name="items[${saleRowCount}][rate]" step="0.01" required class="w-full px-4 py-3 bg-transparent border-none focus:ring-0 font-black text-right text-gray-900 outline-none row-rate" oninput="recalcSales()">
        </td>
        <td class="px-8 py-4 text-right">
            <span class="text-base font-black text-gray-900 row-total tracking-tighter">₹0.00</span>
        </td>
        <td class="px-4 py-4 text-center">
            <button type="button" onclick="this.closest('tr').remove(); recalcSales();" class="text-gray-300 hover:text-red-500 transition-colors text-lg">✕</button>
        </td>
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
        row.querySelector('.row-total').textContent = '₹' + total.toLocaleString('en-IN', { minimumFractionDigits: 2 });
        subtotal += total;
    });

    const gstP = parseFloat(document.getElementById('gst-percentage').value) || 0;
    const gstA = subtotal * gstP / 100;
    const final = subtotal + gstA;

    document.getElementById('display-tax').textContent = '₹' + gstA.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    document.getElementById('display-total').textContent = '₹' + final.toLocaleString('en-IN', { minimumFractionDigits: 0 });
    document.getElementById('total-hidden').value = final.toFixed(2);
}

// Auto-run on load if any values exist
window.onload = recalcSales;
</script>
@endpush
