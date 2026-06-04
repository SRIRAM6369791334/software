@extends('layouts.app')
@section('title', 'Weekly Dealer Billing')@section('content')
<div class="cm-page">

    {{-- Top Bar Header --}}
    <div class="cm-topbar">
        <div>
            <h1 class="cm-page-title">Weekly Dealer Billing</h1>
            <p class="cm-page-sub">Create wholesale billing settlements, bulk route invoices, and manage ledger transactions</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('billing.weekly.export') }}" class="cm-btn-secondary flex items-center gap-1.5">
                <span class="material-symbols-rounded" style="font-size: 18px;">download</span>
                Export CSV
            </a>
        </div>
    </div>

    {{-- Bento Actor Portal Grid --}}
    <div class="cm-actor-grid mb-8">
        {{-- Card 1: Customer --}}
        <div class="cm-actor-card cm-actor-card--customer" onclick="window.location.href='{{ route('billing.daily.index') }}'">
            <div class="cm-actor-badge">
                <span class="material-symbols-rounded">person</span>
                <span>Customer</span>
            </div>
            <div class="cm-actor-content">
                <h3 class="cm-actor-title">Retail Customer</h3>
                <p class="cm-actor-desc">Manage retail billing, cash register sales, and daily customer accounts.</p>
            </div>
            <div class="cm-actor-actions">
                <a href="{{ route('billing.daily.index') }}" class="cm-actor-btn-primary" onclick="event.stopPropagation();">
                    <span class="material-symbols-rounded">arrow_forward</span>
                    Daily Billing
                </a>
            </div>
        </div>

        {{-- Card 2: Dealer --}}
        <div id="dealer-toggle-card" class="cm-actor-card cm-actor-card--dealer cm-active" onclick="toggleDealerPortal(event)">
            <div class="cm-actor-badge">
                <span class="material-symbols-rounded">group</span>
                <span>Dealer</span>
                <span class="cm-active-dot"></span>
            </div>
            <div class="cm-actor-content">
                <h3 class="cm-actor-title">Wholesale Dealer</h3>
                <p class="cm-actor-desc">Manage wholesale distribution ledger accounts, bulk orders, and weekly billing.</p>
            </div>
            <div class="cm-actor-actions">
                <span class="cm-actor-btn-primary">
                    <span class="material-symbols-rounded" id="dealer-icon-toggle">expand_less</span>
                    <span id="dealer-btn-text">Collapse Entry Portal</span>
                </span>
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
    <div id="dealer-form-container" class="cm-form-container-full mb-8">
        <div class="cm-card-form-large">
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4 mb-6">
                <div class="flex gap-4">
                    <button id="tab-single-btn" onclick="switchDealerTab('single')" class="px-4 py-2 text-sm font-bold border-b-2 border-indigo-600 text-indigo-600 focus:outline-none dark:border-indigo-400 dark:text-indigo-400">
                        Single Invoice
                    </button>
                    <button id="tab-bulk-btn" onclick="switchDealerTab('bulk')" class="px-4 py-2 text-sm font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white focus:outline-none">
                        Bulk Route Generation
                    </button>
                </div>
            </div>

            {{-- Single Invoice Form --}}
            <form id="form-single" action="{{ route('billing.weekly.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6 pb-6 border-b border-slate-200 dark:border-slate-800">
                    <div class="cm-form-group">
                        <label class="cm-form-label">1. Select Dealer <span class="cm-required">*</span></label>
                        <select name="customer_id" required class="cm-form-input cm-select">
                            <option value="">Select customer...</option>
                            @foreach($customers as $c)
                                <option value="{{ $c->id }}" {{ old('customer_id') == $c->id ? 'selected' : '' }}>{{ $c->name }} ({{ $c->route }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="cm-form-group">
                        <label class="cm-form-label">2. Period Start <span class="cm-required">*</span></label>
                        <input type="date" name="period_start" required value="{{ old('period_start') }}" class="cm-form-input">
                    </div>
                    <div class="cm-form-group">
                        <label class="cm-form-label">3. Period End <span class="cm-required">*</span></label>
                        <input type="date" name="period_end" required value="{{ old('period_end') }}" class="cm-form-input">
                    </div>
                    <div class="cm-form-group">
                        <label class="cm-form-label">4. Initial Status <span class="cm-required">*</span></label>
                        <select name="status" required class="cm-form-input cm-select">
                            <option value="Generated" {{ old('status') === 'Generated' ? 'selected' : '' }}>Generated</option>
                            <option value="Pending" {{ old('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                            <option value="Paid" {{ old('status') === 'Paid' ? 'selected' : '' }}>Paid</option>
                        </select>
                    </div>
                </div>

                {{-- 2. Dynamic Items Grid Table --}}
                <div class="mb-8">
                    <div class="flex items-center justify-between mb-4">
                        <div class="cm-table-header-sub">
                            <span class="material-symbols-rounded text-indigo-600">list_alt</span>
                            <span>Billing Items & Birds</span>
                        </div>
                        <button type="button" onclick="addWeeklyRow()" class="cm-btn-secondary">
                            <span class="material-symbols-rounded">add</span> Add Item
                        </button>
                    </div>

                    <div class="cm-table-card">
                        <div class="cm-table-wrap">
                            <table class="cm-table" id="weekly-items-table">
                                <thead>
                                    <tr>
                                        <th class="p-3">Item / Description</th>
                                        <th class="p-3 w-32 text-center">Qty / kg</th>
                                        <th class="p-3 w-40 text-right">Rate / kg</th>
                                        <th class="p-3 w-40 text-right">Subtotal</th>
                                        <th class="p-3 w-12 text-center"></th>
                                    </tr>
                                </thead>
                                <tbody id="weekly-items-body">
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
                                            <input type="number" name="items[0][qty]" step="0.01" required placeholder="0.00" class="cm-table-input text-center row-qty" oninput="recalcWeekly()">
                                        </td>
                                        <td class="p-3">
                                            <input type="number" name="items[0][rate]" step="0.01" required placeholder="0.00" class="cm-table-input text-right row-rate" oninput="recalcWeekly()">
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
                            <input type="number" name="gst_percentage" id="gst-percentage" value="18" readonly class="cm-form-input cm-tax-percentage-input cm-readonly">
                            <span class="text-xs text-slate-500 font-bold">% GST</span>
                            <span class="text-xs text-slate-400 font-medium ml-auto">Calculated GST: <span id="display-tax" class="font-mono text-slate-900 dark:text-slate-100 font-bold">₹0.00</span></span>
                        </div>
                    </div>

                    <div class="cm-glowing-grand-total" style="background: linear-gradient(135deg, #4f46e5, #6366f1); box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.25);">
                        <div class="cm-total-details">
                            <span class="cm-total-label">Grand Total Payable</span>
                            <span id="display-total" class="cm-total-value">₹0.00</span>
                            <input type="hidden" name="amount" id="total-hidden">
                        </div>
                        <button type="submit" class="cm-submit-total-btn" style="color: #4f46e5;">
                            <span class="material-symbols-rounded">receipt_long</span>
                            <span>Generate Invoice</span>
                        </button>
                    </div>
                </div>
            </form>

            {{-- Bulk Generation Form --}}
            <form id="form-bulk" action="{{ route('billing.weekly.bulkStore') }}" method="POST" class="cm-hidden">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                    <div class="space-y-3">
                        <label class="cm-form-label">1. Select Dealers</label>
                        <div class="h-[250px] overflow-y-auto pr-2 border border-slate-200 dark:border-slate-800 rounded-xl p-3 space-y-2 bg-slate-50 dark:bg-slate-900 custom-scrollbar">
                            @foreach($customers as $c)
                                <label class="flex items-center gap-3 p-2 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700/60 rounded-lg hover:bg-indigo-50/50 hover:border-indigo-100 transition-all cursor-pointer">
                                    <input type="checkbox" name="customer_ids[]" value="{{ $c->id }}" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500 border-slate-300">
                                    <div class="flex flex-col">
                                        <span class="text-xs font-bold text-slate-700 dark:text-slate-200">{{ $c->name }}</span>
                                        <span class="text-[9px] font-bold text-slate-400 uppercase">{{ $c->route }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="space-y-4">
                        <label class="cm-form-label">2. Global Billing Settings</label>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div class="cm-form-group">
                                <label class="text-[10px] font-bold text-slate-400 uppercase mb-1">From Date</label>
                                <input type="date" name="period_start" required class="cm-form-input">
                            </div>
                            <div class="cm-form-group">
                                <label class="text-[10px] font-bold text-slate-400 uppercase mb-1">To Date</label>
                                <input type="date" name="period_end" required class="cm-form-input">
                            </div>
                        </div>

                        <div class="cm-form-group">
                            <label class="text-[10px] font-bold text-slate-400 uppercase mb-1">Standard Flat Amount (Rs)</label>
                            <input type="number" name="amount" required step="0.01" placeholder="0.00" class="cm-form-input font-bold text-indigo-600 dark:text-indigo-400">
                        </div>

                        <div class="cm-form-group">
                            <label class="text-[10px] font-bold text-slate-400 uppercase mb-1">Initial Status</label>
                            <select name="status" class="cm-form-input cm-select">
                                <option value="Generated">Generated</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-slate-200 dark:border-slate-800 pt-4">
                    <button type="submit" class="cm-submit-total-btn" style="background: linear-gradient(135deg, #4f46e5, #6366f1); color: #ffffff; box-shadow: 0 4px 12px -2px rgba(99, 102, 241, 0.25);">
                        <span class="material-symbols-rounded">layers</span>
                        <span>Generate Bulk Bills</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Performance Stats Header --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-gradient-to-br from-white via-indigo-50/10 to-sky-50/10 dark:from-slate-900 dark:to-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-6 group">
            <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-950 text-indigo-600 dark:text-indigo-400 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-105 transition-transform">📋</div>
            <div>
                <h3 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Generated</h3>
                <p class="text-2xl font-black text-slate-950 dark:text-white">{{ $bills->total() }} <span class="text-xs text-slate-400 font-bold ml-1">Invoices</span></p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-white via-amber-50/10 to-sky-50/10 dark:from-slate-900 dark:to-slate-800 p-6 rounded-2xl border border-slate-200 dark:border-slate-800 shadow-sm flex items-center gap-6 group">
            <div class="w-14 h-14 bg-amber-50 dark:bg-amber-950 text-amber-600 dark:text-amber-400 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-105 transition-transform">⏳</div>
            <div>
                <h3 class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-1">Outstanding Dues</h3>
                <p class="text-2xl font-black text-slate-950 dark:text-white">Rs {{ number_format($bills->where('status', 'Pending')->sum(fn($b) => $b->net_amount ?? $b->amount), 0) }}</p>
            </div>
        </div>
        <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 p-6 rounded-2xl shadow-sm text-white flex items-center gap-6 group">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-105 transition-transform">💰</div>
            <div>
                <h3 class="text-[10px] font-black text-indigo-100 uppercase tracking-widest mb-1">Total Revenue</h3>
                <p class="text-2xl font-black">Rs {{ number_format($bills->where('status', 'Paid')->sum(fn($b) => $b->net_amount ?? $b->amount), 0) }}</p>
            </div>
        </div>
    </div>

    {{-- Main List Section --}}
    <div class="cm-table-card mb-8">
        <div class="cm-table-toolbar">
            <span class="cm-toolbar-title">Weekly Invoice Log</span>
            <form method="GET" class="cm-search-wrap">
                <span class="material-symbols-rounded cm-search-icon">search</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search customer or invoice..." class="cm-search-input">
            </form>
        </div>

        <div class="cm-table-wrap">
            <table class="cm-table">
                <thead>
                    <tr>
                        <th>Inv No</th>
                        <th>Customer</th>
                        <th>Period</th>
                        <th>Product Breakdown</th>
                        <th class="text-right">Quantity</th>
                        <th class="text-right">Net Amount</th>
                        <th class="text-center">Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bills as $bill)
                        <tr class="cm-tr">
                            <td class="cm-td">
                                <span class="font-mono text-xs font-bold text-slate-500">
                                    #{{ $bill->invoice_no ?? $bill->invoice_number }}
                                </span>
                            </td>
                            <td class="cm-td">
                                <div class="cm-identity">
                                    <div class="cm-avatar cm-avatar--{{ strtolower(substr($bill->customer->name ?? 'a', 0, 1)) }}">
                                        {{ substr($bill->customer->name ?? '?', 0, 1) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="cm-cust-name">{{ $bill->customer->name ?? '-' }}</span>
                                        <span class="cm-cust-meta">{{ $bill->customer->route ?? 'General Route' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="cm-td">
                                <div class="flex flex-col">
                                    <span class="font-semibold text-slate-800 dark:text-slate-200">{{ $bill->period_start->format('d M') }} - {{ $bill->period_end->format('d M') }}</span>
                                    <span class="text-[10px] text-slate-400 font-medium uppercase">{{ $bill->period_end->format('Y') }}</span>
                                </div>
                            </td>
                            <td class="cm-td">
                                <div class="cm-item-chips-flex" style="max-width: 250px;">
                                    @if($bill->items_description)
                                        @foreach(explode(',', $bill->items_description) as $item)
                                            @if(trim($item))
                                                <span class="cm-item-chip">{{ trim($item) }}</span>
                                            @endif
                                        @endforeach
                                    @else
                                        <span style="color: var(--cm-text-muted); font-size: 0.75rem;">—</span>
                                    @endif
                                </div>
                            </td>
                            <td class="cm-td text-right">
                                <span class="font-bold text-slate-900 dark:text-slate-100">{{ number_format($bill->quantity_kg, 2) }}</span>
                                <span class="text-[10px] text-slate-400 font-medium uppercase ml-0.5">kg</span>
                            </td>
                            <td class="cm-td text-right">
                                <div class="flex flex-col items-end">
                                    <span class="font-bold text-slate-900 dark:text-slate-100 text-sm">Rs {{ number_format($bill->net_amount ?? $bill->amount, 0) }}</span>
                                    <span class="text-[9px] text-indigo-600 font-extrabold uppercase tracking-tighter">Incl. GST</span>
                                </div>
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
                                    <a href="{{ route('billing.weekly.show', $bill) }}" target="_blank" class="cm-action-btn" title="Print Invoice">
                                        <span class="material-symbols-rounded" style="font-size: 16px;">print</span>
                                    </a>
                                    <a href="{{ route('billing.weekly.pdf', $bill) }}" class="cm-action-btn" title="Download PDF">
                                        <span class="material-symbols-rounded" style="font-size: 16px;">picture_as_pdf</span>
                                    </a>
                                    <a href="{{ route('billing.weekly.whatsapp', $bill) }}" target="_blank" class="cm-action-btn" title="WhatsApp Message" style="color: #10b981; border-color: rgba(16, 185, 129, 0.15)">
                                        <span class="material-symbols-rounded" style="font-size: 16px;">chat</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="cm-empty">
                                    <div class="cm-empty-icon">
                                        <span class="material-symbols-rounded">receipt_long</span>
                                    </div>
                                    <h3 class="cm-empty-title">No Bills Found</h3>
                                    <p class="cm-empty-sub">Start generating invoices for your dealers.</p>
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
let weeklyRowCount = 1;
const activeItems = @json($items);

function addWeeklyRow() {
    const body = document.getElementById('weekly-items-body');
    const newRow = document.createElement('tr');
    newRow.className = 'item-row border-t border-slate-100 dark:border-slate-800 transition-colors';
    
    let optionsHtml = activeItems.map(i => `
        <option value="${i.name}" ${i.name === 'Live Broiler Birds' ? 'selected' : ''}>
            ${i.name}
        </option>
    `).join('');

    newRow.innerHTML = `
        <td class="p-3">
            <select name="items[${weeklyRowCount}][name]" required class="cm-table-select cm-select">
                ${optionsHtml}
            </select>
        </td>
        <td class="p-3">
            <input type="number" name="items[${weeklyRowCount}][qty]" step="0.01" required placeholder="0.00" class="cm-table-input text-center row-qty" oninput="recalcWeekly()">
        </td>
        <td class="p-3">
            <input type="number" name="items[${weeklyRowCount}][rate]" step="0.01" required placeholder="0.00" class="cm-table-input text-right row-rate" oninput="recalcWeekly()">
        </td>
        <td class="p-3 text-right font-semibold text-slate-900 dark:text-slate-100 row-total">
            ₹0.00
        </td>
        <td class="p-3 text-center">
            <button type="button" onclick="this.closest('tr').remove(); recalcWeekly();" class="text-slate-400 hover:text-red-500 transition-colors">
                <span class="material-symbols-rounded" style="font-size: 18px;">close</span>
            </button>
        </td>
    `;
    body.appendChild(newRow);
    weeklyRowCount++;
}

function recalcWeekly() {
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

function switchDealerTab(mode) {
    const tabSingleBtn = document.getElementById('tab-single-btn');
    const tabBulkBtn = document.getElementById('tab-bulk-btn');
    const formSingle = document.getElementById('form-single');
    const formBulk = document.getElementById('form-bulk');

    if (mode === 'single') {
        tabSingleBtn.className = "px-4 py-2 text-sm font-bold border-b-2 border-indigo-600 text-indigo-600 focus:outline-none dark:border-indigo-400 dark:text-indigo-400";
        tabBulkBtn.className = "px-4 py-2 text-sm font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white focus:outline-none";
        formSingle.classList.remove('cm-hidden');
        formBulk.classList.add('cm-hidden');
    } else {
        tabBulkBtn.className = "px-4 py-2 text-sm font-bold border-b-2 border-indigo-600 text-indigo-600 focus:outline-none dark:border-indigo-400 dark:text-indigo-400";
        tabSingleBtn.className = "px-4 py-2 text-sm font-semibold text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white focus:outline-none";
        formBulk.classList.remove('cm-hidden');
        formSingle.classList.add('cm-hidden');
    }
}

function toggleDealerPortal(event) {
    const card = document.getElementById('dealer-toggle-card');
    const container = document.getElementById('dealer-form-container');
    const icon = document.getElementById('dealer-icon-toggle');
    const btnText = document.getElementById('dealer-btn-text');

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
    recalcWeekly();
});
</script>
@endpush
