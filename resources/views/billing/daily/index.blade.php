@extends('layouts.app')
@section('title', 'Daily Customer Billing')@section('content')
<div class="cm-page">

    {{-- Top Bar Header --}}
    <div class="cm-topbar">
        <div>
            <h1 class="cm-page-title">Daily Customer Billing</h1>
            <p class="cm-page-sub">Record counter sales, calculate GST automatically, and issue receipts</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('billing.daily.export') }}" class="cm-export-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
    </svg>
        Export
    </a>
        </div>
    </div>

    {{-- Bento Actor Portal Grid --}}
    <div class="cm-actor-grid mb-8">
        {{-- Card 1: Customer --}}
        <div id="customer-toggle-card" class="cm-actor-card cm-actor-card--customer cm-active" onclick="toggleCustomerPortal(event)">
            <div class="cm-actor-badge">
                <span class="material-symbols-rounded">person</span>
                <span>Customer</span>
                <span class="cm-active-dot"></span>
            </div>
            <div class="cm-actor-content">
                <h3 class="cm-actor-title">Retail Customer</h3>
                <p class="cm-actor-desc">Manage retail billing, cash register sales, and daily customer accounts.</p>
            </div>
            <div class="cm-actor-actions">
                <span class="cm-actor-btn-primary">
                    <span class="material-symbols-rounded" id="customer-icon-toggle">expand_less</span>
                    <span id="customer-btn-text">Collapse Entry Portal</span>
                </span>
            </div>
        </div>

        {{-- Card 2: Dealer --}}
        <div class="cm-actor-card cm-actor-card--dealer" onclick="window.location.href='{{ route('billing.weekly.index') }}'">
            <div class="cm-actor-badge">
                <span class="material-symbols-rounded">group</span>
                <span>Dealer</span>
            </div>
            <div class="cm-actor-content">
                <h3 class="cm-actor-title">Wholesale Dealer</h3>
                <p class="cm-actor-desc">Manage wholesale distribution ledger accounts, bulk orders, and weekly billing.</p>
            </div>
            <div class="cm-actor-actions">
                <a href="{{ route('billing.weekly.index') }}" class="cm-actor-btn-primary" onclick="event.stopPropagation();">
                    <span class="material-symbols-rounded">arrow_forward</span>
                    Dealer Billing
                </a>
            </div>
        </div>

        {{-- Card 3: Vendor --}}
        <div class="cm-actor-card cm-actor-card--vendor" onclick="window.location.href='{{ route('purchases.entry') }}'">
            <div class="cm-actor-badge">
                <span class="material-symbols-rounded">local_shipping</span>
                <span>Vendor</span>
            </div>
            <div class="cm-actor-content">
                <h3 class="cm-actor-title">Vendor (Procurement)</h3>
                <p class="cm-actor-desc">Record purchases of feed, medicine, and farm supplies. Track credit accounts and ledger dues.</p>
            </div>
            <div class="cm-actor-actions">
                <a href="{{ route('purchases.entry') }}" class="cm-actor-btn-primary" onclick="event.stopPropagation();">
                    <span class="material-symbols-rounded">arrow_forward</span>
                    Vendor Billing
                </a>
            </div>
        </div>
    </div>

    {{-- Entry Form Block --}}
    <div id="customer-form-container" class="cm-form-container-full mb-8">
        <form action="{{ route('billing.daily.store') }}" method="POST" id="daily-sale-form" class="cm-card-form-large">
            @csrf
            
            <div class="cm-form-section-title mb-6">
                <span class="material-symbols-rounded text-emerald-600 dark:text-emerald-400">person_add</span>
                <h2>Record New Retail Sale</h2>
            </div>

            {{-- 1. Header Information Row --}}
            <div class="cm-form-grid-header">
                <div class="cm-form-group">
                    <label class="cm-form-label">1. Customer <span class="cm-required">*</span></label>
                    <select name="customer_id" required class="cm-form-input cm-select">
                        <option value="">Select customer...</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">2. Sale Date <span class="cm-required">*</span></label>
                    <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}" class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">3. Payment Status <span class="cm-required">*</span></label>
                    <select name="status" required class="cm-form-input cm-select">
                        <option value="Generated" {{ old('status') === 'Generated' ? 'selected' : '' }}>Generated</option>
                        <option value="Pending" {{ old('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                        <option value="Paid" {{ old('status', 'Paid') === 'Paid' ? 'selected' : '' }}>Paid (Cash)</option>
                    </select>
                </div>
            </div>

            {{-- 2. Dynamic Refill Rows Table --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="cm-table-header-sub">
                        <span class="material-symbols-rounded text-emerald-600">list_alt</span>
                        <span>Sale Items & Birds</span>
                    </div>
                    <button type="button" onclick="addSaleRow()" class="cm-btn-secondary">
                        <span class="material-symbols-rounded">add</span> Add Item
                    </button>
                </div>

                <div class="cm-table-card">
                    <div class="cm-table-wrap">
                        <table class="cm-table" id="sale-items-table">
                            <thead>
                                <tr>
                                    <th class="p-3">Item / Description</th>
                                    <th class="p-3 w-32 text-center">Qty / kg</th>
                                    <th class="p-3 w-40 text-right">Rate / kg</th>
                                    <th class="p-3 w-40 text-right">Subtotal</th>
                                    <th class="p-3 w-12 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="sale-items-body">
                                <tr class="item-row group">
                                    <td class="p-3">
                                        <select name="items[0][name]" required class="cm-table-select cm-select">
                                            @foreach($items as $item)
                                                <option value="{{ $item->name }}" {{ $item->name === 'Live Broiler Birds' ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="p-3">
                                        <input type="number" name="items[0][qty]" step="0.01" required placeholder="0.00" class="cm-table-input text-center row-qty" oninput="recalcSales()">
                                    </td>
                                    <td class="p-3">
                                        <input type="number" name="items[0][rate]" step="0.01" required placeholder="0.00" class="cm-table-input text-right row-rate" oninput="recalcSales()">
                                    </td>
                                    <td class="p-3 text-right font-semibold text-slate-900 dark:text-slate-100 row-total">
                                        ₹0.00
                                    </td>
                                    <td class="p-3 text-center"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- 3. Billing Summary Section --}}
            <div class="cm-billing-summary-grid">
                <div class="cm-summary-info-box flex flex-col justify-center">
                    <label class="cm-small-label">Tax Settings (GST)</label>
                    <div class="cm-tax-fields">
                        <input type="number" name="gst_percentage" id="gst-percentage" value="18" min="0" max="28" class="cm-form-input cm-tax-percentage-input" oninput="recalcSales()">
                        <span class="text-xs text-slate-500 font-bold">% GST</span>
                        <span class="text-xs text-slate-400 font-medium ml-auto">Calculated GST: <span id="display-tax" class="font-mono text-slate-900 dark:text-slate-100 font-bold">₹0.00</span></span>
                    </div>
                </div>

                <div class="cm-glowing-grand-total">
                    <div class="cm-total-details">
                        <span class="cm-total-label">Grand Total Payable (மொத்த தொகை)</span>
                        <span id="display-total" class="cm-total-value">₹0.00</span>
                        <input type="hidden" name="amount" id="total-hidden">
                    </div>
                    <button type="submit" class="cm-submit-total-btn">
                        <span class="material-symbols-rounded">receipt_long</span>
                        <span>Generate Invoice</span>
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- Insights Header Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-white via-emerald-50/10 to-sky-50/10 dark:from-slate-900 dark:to-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-6 group hover:border-emerald-200 transition-all">
            <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-950 text-emerald-600 dark:text-emerald-400 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-105 transition-transform">📊</div>
            <div>
                <h3 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Today's Sales</h3>
                <p class="text-2xl font-black text-slate-950 dark:text-white">Rs {{ number_format($bills->where('date', date('Y-m-d'))->sum('net_amount'), 0) }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-white via-emerald-50/10 to-sky-50/10 dark:from-slate-900 dark:to-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-6 group hover:border-indigo-200 transition-all">
            <div class="w-14 h-14 bg-blue-50 dark:bg-blue-950 text-blue-600 dark:text-blue-400 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-105 transition-transform">📈</div>
            <div>
                <h3 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Avg Ticket Size</h3>
                <p class="text-2xl font-black text-slate-950 dark:text-white">Rs {{ number_format($bills->avg('net_amount') ?: 0, 0) }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-emerald-600 to-emerald-700 p-6 rounded-2xl shadow-sm text-white flex items-center gap-6 group">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-105 transition-transform">⚡</div>
            <div>
                <h3 class="text-[10px] font-black text-emerald-100 uppercase tracking-widest mb-1">Total Counter Cash</h3>
                <p class="text-2xl font-black text-white">Rs {{ number_format($bills->sum('net_amount'), 0) }}</p>
            </div>
        </div>
    </div>

    {{-- Daily Sales Logs Table Card --}}
    <div class="cm-table-card mb-8">
        <div class="cm-table-toolbar">
            <span class="cm-toolbar-title">Recent Counter Sales</span>
            <form method="GET" class="cm-search-wrap">
                <span class="material-symbols-rounded cm-search-icon">search</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search customer, item or route..." class="cm-search-input">
            </form>
        </div>

        <div class="cm-table-wrap">
            <table class="cm-table">
                <thead>
                    <tr>
                        <th>Sale Date</th>
                        <th>Customer</th>
                        <th>Product Breakdown</th>
                        <th class="text-right">Net Weight</th>
                        <th class="text-right">Net Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                        <tr class="cm-tr">
                            <td class="cm-td">
                                <div class="flex flex-col">
                                    <span class="font-bold text-slate-900 dark:text-slate-100">{{ $bill->date->format('d M') }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium uppercase tracking-tighter">{{ $bill->date->format('l, Y') }}</span>
                                </div>
                            </td>
                            <td class="cm-td">
                                <div class="cm-identity">
                                    <div class="cm-avatar cm-avatar--{{ strtolower(substr($bill->customer->name ?? 'a', 0, 1)) }}">
                                        {{ substr($bill->customer->name ?? '?', 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="cm-cust-name">{{ $bill->customer->name ?? '-' }}</span>
                                        <span class="cm-cust-meta">{{ $bill->customer->phone ?? 'NO PHONE' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="cm-td">
                                @php
                                    $firstItem = $bill->items->first();
                                    $othersCount = $bill->items->count() - 1;
                                @endphp
                                <div class="flex items-center gap-2">
                                    <span class="cm-item-chip">{{ $firstItem?->item_name }}</span>
                                    @if($othersCount > 0)
                                        <span class="px-2 py-0.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 font-extrabold text-[9px] rounded-md">+{{ $othersCount }} OTHERS</span>
                                    @endif
                                </div>
                            </td>
                            <td class="cm-td text-right">
                                <span class="font-bold text-slate-900 dark:text-slate-100">{{ number_format($bill->items->sum('quantity_kg'), 1) }}</span>
                                <span class="text-[10px] text-slate-400 font-medium uppercase ml-0.5">kg</span>
                            </td>
                            <td class="cm-td text-right">
                                <span class="font-bold text-slate-900 dark:text-slate-100 text-sm">Rs {{ number_format($bill->net_amount, 0) }}</span>
                            </td>
                            <td class="cm-td text-center">
                                @php
                                    $statusMap = [
                                        'Generated' => ['bg' => 'rgba(37, 99, 235, 0.1)', 'text' => '#2563eb', 'label' => 'GENERATED'],
                                        'Pending'   => ['bg' => 'rgba(245, 158, 11, 0.1)', 'text' => '#d97706', 'label' => 'PENDING'],
                                        'Paid'      => ['bg' => 'rgba(16, 185, 129, 0.1)', 'text' => '#10b981', 'label' => 'PAID'],
                                    ];
                                    $st = $statusMap[$bill->status] ?? $statusMap['Pending'];
                                @endphp
                                <span class="inline-block px-2.5 py-1 text-[9px] font-black rounded-md tracking-wider" style="background: {{ $st['bg'] }}; color: {{ $st['text'] }}">
                                    {{ $st['label'] }}
                                </span>
                            </td>
                            <td class="cm-td text-right">
                                <div class="cm-actions">
                                    <a href="{{ route('billing.daily.invoice', $bill->id) }}" target="_blank" class="cm-action-btn" title="Print Invoice">
                                        <span class="material-symbols-rounded" style="font-size: 16px;">print</span>
                                    </a>
                                    <a href="{{ route('billing.daily.pdf', $bill->id) }}" class="cm-action-btn" title="Download PDF">
                                        <span class="material-symbols-rounded" style="font-size: 16px;">picture_as_pdf</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="cm-empty">
                                    <div class="cm-empty-icon">
                                        <span class="material-symbols-rounded">receipt_long</span>
                                    </div>
                                    <h3 class="cm-empty-title">No Sales Recorded</h3>
                                    <p class="cm-empty-sub">Ready to record your first daily sale?</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($bills->hasPages())
            <div class="cm-pagination-footer">
                {{ $bills->withQueryString()->links() }}
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
let saleRowCount = 1;
const activeItems = @json($items);

function addSaleRow() {
    const body = document.getElementById('sale-items-body');
    const newRow = document.createElement('tr');
    newRow.className = 'item-row border-t border-slate-100 dark:border-slate-800 transition-colors';
    
    let optionsHtml = activeItems.map(i => `
        <option value="${i.name}" ${i.name === 'Live Broiler Birds' ? 'selected' : ''}>
            ${i.name}
        </option>
    `).join('');

    newRow.innerHTML = `
        <td class="p-3">
            <select name="items[${saleRowCount}][name]" required class="cm-table-select cm-select">
                ${optionsHtml}
            </select>
        </td>
        <td class="p-3">
            <input type="number" name="items[${saleRowCount}][qty]" step="0.01" required placeholder="0.00" class="cm-table-input text-center row-qty" oninput="recalcSales()">
        </td>
        <td class="p-3">
            <input type="number" name="items[${saleRowCount}][rate]" step="0.01" required placeholder="0.00" class="cm-table-input text-right row-rate" oninput="recalcSales()">
        </td>
        <td class="p-3 text-right font-semibold text-slate-900 dark:text-slate-100 row-total">
            ₹0.00
        </td>
        <td class="p-3 text-center">
            <button type="button" onclick="this.closest('tr').remove(); recalcSales();" class="text-slate-400 hover:text-red-500 transition-colors">
                <span class="material-symbols-rounded" style="font-size: 18px;">close</span>
            </button>
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
    document.getElementById('display-total').textContent = '₹' + final.toLocaleString('en-IN', { minimumFractionDigits: 2 });
    document.getElementById('total-hidden').value = final.toFixed(2);
}

function toggleCustomerPortal(event) {
    const card = document.getElementById('customer-toggle-card');
    const container = document.getElementById('customer-form-container');
    const icon = document.getElementById('customer-icon-toggle');
    const btnText = document.getElementById('customer-btn-text');

    if (container.classList.contains('cm-hidden')) {
        container.classList.remove('cm-hidden');
        card.classList.add('cm-active');
        icon.textContent = 'expand_less';
        btnText.textContent = 'Collapse Entry Portal';
    } else {
        container.classList.add('cm-hidden');
        card.classList.remove('cm-active');
        icon.textContent = 'expand_more';
        btnText.textContent = 'Expand Entry Portal';
    }
}

// Auto-run on load
window.addEventListener('DOMContentLoaded', () => {
    recalcSales();
});
</script>
@endpush
