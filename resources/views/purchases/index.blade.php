@extends('layouts.app')
@section('title', 'Purchase Entry')

@section('content')
<div class="cm-page">

    {{-- Top Bar Header --}}
    <div class="cm-topbar">
        <div>
            <h1 class="cm-page-title">Purchase Entry & Refills</h1>
            <p class="cm-page-sub">Record incoming inventory supply and map it to specific batches & locations</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('purchases.invoices') }}" class="cm-btn-ghost flex items-center gap-1.5">
                <span class="material-symbols-rounded" style="font-size: 18px;">receipt_long</span>
                Invoice Archive
            </a>
            <!-- <a href="{{ route('inventory.stock.index') }}" class="cm-btn-ghost flex items-center gap-1.5">
                <span class="material-symbols-rounded" style="font-size: 18px;">inventory_2</span>
                Stock Status
            </a> -->
            <a href="{{ route('purchases.export') }}" class="cm-btn-ghost flex items-center gap-1.5">
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
                <a href="{{ route('billing.daily.create') }}" class="cm-actor-btn-primary" onclick="event.stopPropagation();">
                    <span class="material-symbols-rounded">add_circle</span>
                    Create Customer Bill
                </a>
                <a href="{{ route('billing.daily.index') }}" class="cm-actor-btn-secondary" onclick="event.stopPropagation();">
                    View Daily List
                </a>
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
                <a href="{{ route('billing.weekly.bulk') }}" class="cm-actor-btn-primary" onclick="event.stopPropagation();">
                    <span class="material-symbols-rounded">layers</span>
                    Create Dealer bill
                </a>
                <a href="{{ route('billing.weekly.index') }}" class="cm-actor-btn-secondary" onclick="event.stopPropagation();">
                    View Weekly List
                </a>
            </div>
        </div>

        {{-- Card 3: Vendor --}}
        <div id="vendor-toggle-card" class="cm-actor-card cm-actor-card--vendor cm-active" onclick="toggleVendorPortal(event)">
            <div class="cm-actor-badge">
                <span class="material-symbols-rounded">local_shipping</span>
                <span>Vendor</span>
                <span class="cm-active-dot"></span>
            </div>
            <div class="cm-actor-content">
                <h3 class="cm-actor-title">Vendor (Procurement)</h3>
                <p class="cm-actor-desc">Record purchases of feed, medicine, and farm supplies. Track credit accounts and ledger dues.</p>
            </div>
            <div class="cm-actor-actions">
                <span class="cm-actor-btn-primary">
                    <span class="material-symbols-rounded" id="vendor-icon-toggle">expand_less</span>
                    <span id="vendor-btn-text">Collapse Entry Portal</span>
                </span>
            </div>
        </div>
    </div>

    {{-- Entry Form Block --}}
    <div id="vendor-form-container" class="cm-form-container-full mb-8">
        <form action="{{ route('purchases.store') }}" method="POST" id="purchase-form" class="cm-card-form-large">
            @csrf
            
            <div class="cm-form-section-title mb-6">
                <span class="material-symbols-rounded text-teal-600 dark:text-teal-400">add_shopping_cart</span>
                <h2>Record New Purchase Transaction</h2>
            </div>

            {{-- 1. Header Information Row --}}
            <div class="cm-form-grid-header">
                <div class="cm-form-group">
                    <label class="cm-form-label">1. Vendor / Partner <span class="cm-required">*</span></label>
                    <select name="vendor_name" required class="cm-form-input cm-select" onchange="updateVendorOutstanding()">
                        <option value="">Select supply partner...</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->firm_name }}" data-outstanding="{{ $vendor->outstanding_balance }}" {{ old('vendor_name') === $vendor->firm_name ? 'selected' : '' }}>
                                {{ $vendor->firm_name }} @if($vendor->outstanding_balance > 0) (Outstanding: ₹{{ number_format($vendor->outstanding_balance, 0) }}) @endif
                            </option>
                        @endforeach
                    </select>
                    
                    {{-- Dynamic Outstanding Ledger Info --}}
                    <div id="vendor-outstanding-info" class="mt-2 text-xs font-bold text-amber-600 dark:text-amber-400 cm-hidden flex items-center gap-1.5 bg-amber-500/10 border border-amber-500/20 px-2.5 py-1.5 rounded-lg">
                        <span class="material-symbols-rounded" style="font-size: 16px;">account_balance_wallet</span>
                        <span>Current Dues:</span>
                        <span id="vendor-outstanding-amount" class="font-mono text-amber-700 dark:text-amber-300 font-extrabold">₹0.00</span>
                    </div>
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">2. Invoice Number / Bill ID</label>
                    <input type="text" name="invoice_no" value="{{ old('invoice_no') }}" placeholder="e.g. INV-2026-99" class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">3. Billing Date <span class="cm-required">*</span></label>
                    <input type="date" name="date" required value="{{ old('date', date('Y-m-d')) }}" class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">4. Payment Mode <span class="cm-required">*</span></label>
                    <select name="payment_mode" required class="cm-form-input cm-select" onchange="toggleDueDateField()">
                        <option value="Cash" {{ old('payment_mode') === 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="UPI" {{ old('payment_mode', 'UPI') === 'UPI' ? 'selected' : '' }}>UPI</option>
                        <option value="NEFT" {{ old('payment_mode') === 'NEFT' ? 'selected' : '' }}>NEFT</option>
                        <option value="Cheque" {{ old('payment_mode') === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="Credit" {{ old('payment_mode') === 'Credit' ? 'selected' : '' }}>Credit</option>
                    </select>
                </div>
                
                {{-- Conditional Payment Due Date Field --}}
                <div class="cm-form-group cm-hidden" id="due-date-group">
                    <label class="cm-form-label">5. Payment Due Date <span class="cm-required">*</span></label>
                    <input type="date" name="due_date" id="due_date_input" value="{{ old('due_date') }}" class="cm-form-input font-semibold">
                </div>
            </div>

            {{-- 2. Dynamic Refill Rows Table --}}
            <div class="mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="cm-table-header-sub">
                        <span class="material-symbols-rounded text-slate-500">list_alt</span>
                        <span>Procured Products & Warehouse Placement</span>
                    </div>
                    <button type="button" onclick="addRow()" class="cm-btn-secondary">
                        <span class="material-symbols-rounded">add</span>
                        Add Item Row
                    </button>
                </div>

                <div class="cm-table-wrap border border-slate-200 dark:border-gray-800 rounded-xl overflow-hidden">
                    <table class="cm-table" id="items-table">
                        <thead>
                            <tr>
                                <th style="width: 30%;">Product / Item Master <span class="cm-required">*</span></th>
                                <!-- <th style="width: 20%;">Link to Flock Batch</th> -->
                                <!-- <th style="width: 20%;">Warehouse Location</th> -->
                                <th style="width: 10%;">Qty <span class="cm-required">*</span></th>
                                <th style="width: 8%;">Unit</th>
                                <th style="width: 12%;">Rate (₹) <span class="cm-required">*</span></th>
                                <th style="width: 10%; text-align: right;">Total Amount</th>
                                <th style="width: 5%;"></th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            <tr class="item-row">
                                <td class="p-3">
                                    <select name="items[0][item_id]" required onchange="updateUnit(this)" class="cm-table-select item-selector">
                                        <option value="">Select Product...</option>
                                        @foreach($items as $item)
                                            <option value="{{ $item->id }}" data-unit="{{ $item->base_unit }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }} ({{ $item->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <!-- <td class="p-3">
                                    <select name="items[0][batch_id]" class="cm-table-select">
                                        <option value="">General (No Batch Link)</option>
                                        @foreach($batches as $batch)
                                            <option value="{{ $batch->id }}">{{ $batch->batch_code }} - {{ $batch->breed }}</option>
                                        @endforeach
                                    </select>
                                </td> -->
                                <!-- <td class="p-3">
                                    <select name="items[0][warehouse_id]" class="cm-table-select">
                                        <option value="" selected>Placement Area... (Optional)</option>
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                </td> -->
                                <td class="p-3">
                                    <input type="number" name="items[0][qty]" step="0.01" required placeholder="0.00" class="cm-table-input row-qty" oninput="recalculate()">
                                </td>
                                <td class="p-3">
                                    <input type="text" name="items[0][unit]" value="kg" class="cm-table-input row-unit cm-readonly" readonly tabindex="-1">
                                </td>
                                <td class="p-3">
                                    <input type="number" name="items[0][rate]" step="0.01" required placeholder="0.00" class="cm-table-input row-rate" oninput="recalculate()">
                                </td>
                                <td class="p-3 text-right font-semibold text-slate-800 dark:text-slate-200">
                                    <span class="row-total">₹0.00</span>
                                </td>
                                <td class="p-3 text-center"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 3. Billing Summaries Block --}}
            <div class="cm-billing-summary-grid">
                <div class="cm-summary-info-box">
                    <label class="cm-form-label">Tax & Configuration</label>
                    <div class="cm-tax-fields">
                        <div class="cm-tax-percentage-input">
                            <label class="cm-small-label">GST %</label>
                            <input type="number" name="gst_percentage" id="gst-percentage" value="18" step="0.1" class="cm-form-input font-bold" oninput="recalculate()">
                        </div>
                        <div class="flex-1">
                            <label class="cm-small-label">Computed GST Value</label>
                            <input type="text" id="display-tax" readonly value="₹0.00" class="cm-form-input cm-readonly font-mono">
                        </div>
                    </div>
                </div>

                <div class="cm-glowing-grand-total">
                    <div class="cm-total-details">
                        <span class="cm-total-label">Final Grand Net Total</span>
                        <span id="display-total" class="cm-total-value">₹0.00</span>
                    </div>
                    <button type="submit" class="cm-submit-total-btn">
                        <span class="material-symbols-rounded">check_circle</span>
                        Confirm & Save Purchase Entry
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- 4. Recent Purchase Logs Directory --}}
    <div id="vendor-logs-container" class="cm-table-card mt-8">
        <div class="cm-table-toolbar">
            <h2 class="cm-toolbar-title">Recent Purchase Logs</h2>
            <form method="GET" class="cm-search-wrap">
                <span class="material-symbols-rounded cm-search-icon">search</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search vendor or product name..." class="cm-search-input">
            </form>
        </div>

        <div class="cm-table-wrap">
            <table class="cm-table">
                <thead>
                    <tr>
                        <th style="width: 15%;">Date</th>
                        <th style="width: 30%;">Vendor & Invoice ID</th>
                        <th style="width: 30%;">Refilled Products</th>
                        <th style="width: 12%; text-align: right;">Net Amount</th>
                        <th style="width: 10%; text-align: center;">Payment Mode</th>
                        <th style="width: 8%; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $p)
                        <tr class="cm-tr">
                            <td class="cm-td text-slate-500 font-medium">
                                {{ $p->date->format('d M Y') }}
                            </td>
                            <td class="cm-td">
                                <div class="cm-identity">
                                    <div class="cm-avatar cm-avatar--{{ strtolower(substr($p->vendor_name, 0, 1)) }}">
                                        {{ strtoupper(substr($p->vendor_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="cm-cust-name">{{ $p->vendor_name }}</span>
                                        <span class="cm-cust-meta font-mono">Bill ID: {{ $p->invoice_no ?: 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="cm-td">
                                <div class="cm-item-chips-flex">
                                    @foreach($p->items as $item)
                                        <span class="cm-item-chip">
                                            {{ $item->item_name }} <b class="ml-1">({{ number_format($item->quantity) }} {{ $item->unit }})</b>
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="cm-td text-right font-bold text-slate-900 dark:text-slate-100">
                                ₹{{ number_format($p->total_amount, 2) }}
                            </td>
                            <td class="cm-td text-center">
                                <span class="cm-badge-mode cm-badge-mode--{{ strtolower($p->payment_mode) }}">
                                    {{ $p->payment_mode }}
                                </span>
                                @if($p->payment_mode === 'Credit')
                                    @if($p->due_date)
                                        <div class="mt-1 text-[10px] font-bold uppercase tracking-wider {{ $p->due_date->isPast() ? 'text-red-500 animate-pulse' : 'text-slate-500 dark:text-slate-400' }}">
                                            Due: {{ $p->due_date->format('d M Y') }}
                                            @if($p->due_date->isPast())
                                                <span class="block text-[8px] text-red-600 dark:text-red-400 font-extrabold">(OVERDUE)</span>
                                            @endif
                                        </div>
                                    @else
                                        <div class="mt-1 text-[10px] font-semibold text-slate-400 uppercase tracking-wider">
                                            No Due Date
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="cm-td">
                                <div class="cm-actions">
                                    <a href="{{ route('purchases.show', $p->id) }}" class="cm-action-btn cm-action-btn--edit" title="View details">
                                        <span class="material-symbols-rounded" style="font-size: 18px;">visibility</span>
                                    </a>
                                    <a href="{{ route('purchases.edit', $p->id) }}" class="cm-action-btn cm-action-btn--edit" title="Edit purchase">
                                        <span class="material-symbols-rounded" style="font-size: 18px;">edit</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="cm-empty">
                                <div class="cm-empty-icon">
                                    <span class="material-symbols-rounded" style="font-size: 32px;">receipt</span>
                                </div>
                                <div class="cm-empty-title">No purchases matched</div>
                                <div class="cm-empty-sub">Adjust your filter query or record a new purchase above</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($purchases->hasPages())
            <div class="cm-pagination-footer">
                {{ $purchases->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@push('styles')
<style>
/* ── Theme Variables & Dark Mode Matrix ── */
:root {
    --cm-bg: #f8fafc;
    --cm-card-bg: #ffffff;
    --cm-card-border: #e2e8f0;
    --cm-text-primary: #0f172a;
    --cm-text-secondary: #475569;
    --cm-text-muted: #94a3b8;
    --cm-accent-teal: #0d9488;
    --cm-accent-teal-hover: #0f766e;
    --cm-accent-teal-light: #f0fdfa;
    --cm-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --cm-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
    --cm-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
}

[data-theme='dark'] {
    --cm-bg: #090d16;
    --cm-card-bg: #111827;
    --cm-card-border: #1f2937;
    --cm-text-primary: #f3f4f6;
    --cm-text-secondary: #9ca3af;
    --cm-text-muted: #6b7280;
    --cm-accent-teal-light: rgba(13, 148, 136, 0.1);
    --cm-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.5);
    --cm-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -2px rgba(0, 0, 0, 0.3);
    --cm-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -4px rgba(0, 0, 0, 0.4);
}

.cm-page { padding: 1rem 0 3rem; }
.cm-hidden { display: none !important; }

/* ── Top Bar ── */
.cm-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    gap: 1rem;
    flex-wrap: wrap;
}
.cm-page-title {
    font-size: 1.375rem;
    font-weight: 700;
    color: var(--cm-text-primary);
    letter-spacing: -0.02em;
}
.cm-page-sub {
    font-size: 0.8125rem;
    color: var(--cm-text-secondary);
    margin-top: 2px;
}

/* ── Buttons ── */
.cm-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 12px;
    background: var(--cm-card-bg);
    color: var(--cm-text-secondary);
    border: 1px solid var(--cm-card-border);
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}
.cm-btn-secondary:hover {
    background: var(--cm-bg);
    color: var(--cm-text-primary);
}
.cm-btn-secondary .material-symbols-rounded { font-size: 16px; }

.cm-btn-ghost {
    display: inline-flex;
    align-items: center;
    padding: 8px 14px;
    background: transparent;
    border: none;
    border-radius: 8px;
    font-size: 0.8125rem;
    color: var(--cm-text-secondary);
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
    text-decoration: none;
}
.cm-btn-ghost:hover { background: var(--cm-card-border); color: var(--cm-text-primary); }

/* ── Form Card ── */
.cm-card-form-large {
    background: var(--cm-card-bg);
    border: 1px solid var(--cm-card-border);
    border-radius: 16px;
    padding: 1.75rem;
    box-shadow: var(--cm-shadow-md);
}
.cm-form-section-title {
    display: flex;
    align-items: center;
    gap: 8px;
}
.cm-form-section-title h2 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--cm-text-primary);
    margin: 0;
}
.cm-form-section-title .material-symbols-rounded { font-size: 20px; }

/* Grid headers */
.cm-form-grid-header {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--cm-card-border);
}
@media (max-width: 900px) {
    .cm-form-grid-header { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 520px) {
    .cm-form-grid-header { grid-template-columns: 1fr; }
}

.cm-form-group { display: flex; flex-direction: column; }
.cm-form-label {
    font-size: 0.6875rem;
    font-weight: 700;
    color: var(--cm-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 6px;
}
.cm-required { color: #dc2626; }

.cm-form-input {
    width: 100%;
    padding: 9px 12px;
    border: 1px solid var(--cm-card-border);
    border-radius: 8px;
    font-size: 0.8125rem;
    background: var(--cm-bg);
    color: var(--cm-text-primary);
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s, background-color 0.15s;
}
.cm-form-input:focus {
    border-color: var(--cm-text-muted);
    box-shadow: 0 0 0 4px rgba(148,163,184,0.12);
    background: var(--cm-card-bg);
}
.cm-select { appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 12px center; background-size: 14px; padding-right: 32px; }

/* ── Table Row Elements ── */
.cm-table-header-sub {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8125rem;
    font-weight: 700;
    color: var(--cm-text-primary);
}
.cm-table-header-sub .material-symbols-rounded { font-size: 18px; }

.cm-table-select {
    width: 100%;
    padding: 7px 10px;
    border: 1px solid var(--cm-card-border);
    border-radius: 6px;
    font-size: 0.75rem;
    background: var(--cm-bg);
    color: var(--cm-text-primary);
    font-weight: 600;
    outline: none;
    transition: border-color 0.15s;
}
.cm-table-select:focus { border-color: var(--cm-text-muted); }

.cm-table-input {
    width: 100%;
    padding: 7px 10px;
    border: 1px solid var(--cm-card-border);
    border-radius: 6px;
    font-size: 0.75rem;
    background: var(--cm-bg);
    color: var(--cm-text-primary);
    outline: none;
    transition: border-color 0.15s;
}
.cm-table-input:focus { border-color: var(--cm-text-muted); }
.cm-readonly { background: var(--cm-card-border); opacity: 0.7; cursor: not-allowed; }

/* ── Billing Summary Block ── */
.cm-billing-summary-grid {
    display: grid;
    grid-template-columns: 4fr 5fr;
    gap: 2rem;
    margin-top: 1rem;
}
@media (max-width: 768px) {
    .cm-billing-summary-grid { grid-template-columns: 1fr; gap: 1.25rem; }
}

.cm-summary-info-box {
    background: var(--cm-bg);
    border: 1px solid var(--cm-card-border);
    border-radius: 12px;
    padding: 1.25rem;
}
.cm-tax-fields { display: flex; align-items: center; gap: 1rem; margin-top: 0.75rem; }
.cm-tax-percentage-input { width: 90px; }
.cm-small-label { font-size: 10px; font-weight: 600; color: var(--cm-text-secondary); text-transform: uppercase; margin-bottom: 4px; display: block; }

.cm-glowing-grand-total {
    background: linear-gradient(135deg, #0f766e, #0d9488);
    border-radius: 16px;
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1.5rem;
    box-shadow: 0 10px 15px -3px rgba(13, 148, 136, 0.25);
    color: #ffffff;
}
@media (max-width: 520px) {
    .cm-glowing-grand-total { flex-direction: column; align-items: stretch; text-align: center; }
}

.cm-total-details { display: flex; flex-direction: column; }
.cm-total-label { font-size: 0.6875rem; font-weight: 700; text-transform: uppercase; opacity: 0.85; letter-spacing: 0.08em; }
.cm-total-value { font-size: 1.75rem; font-weight: 900; font-family: monospace; line-height: 1; margin-top: 4px; }

.cm-submit-total-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    background: #ffffff;
    color: #0f766e;
    border: none;
    border-radius: 10px;
    font-size: 0.8125rem;
    font-weight: 800;
    cursor: pointer;
    transition: transform 0.15s, opacity 0.15s;
}
.cm-submit-total-btn:hover { transform: translateY(-1px); opacity: 0.95; }
.cm-submit-total-btn:active { transform: translateY(1px); }
.cm-submit-total-btn .material-symbols-rounded { font-size: 18px; }

/* ── Table Card ── */
.cm-table-card {
    background: var(--cm-card-bg);
    border: 1px solid var(--cm-card-border);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--cm-shadow-sm);
}
.cm-table-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--cm-card-border);
    gap: 10px;
    flex-wrap: wrap;
}
.cm-toolbar-title { font-size: 0.9375rem; font-weight: 700; color: var(--cm-text-primary); }

.cm-search-wrap { position: relative; width: 100%; max-width: 280px; }
.cm-search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--cm-text-muted); font-size: 18px; pointer-events: none; }
.cm-search-input { width: 100%; padding: 7px 12px 7px 34px; border: 1px solid var(--cm-card-border); border-radius: 8px; font-size: 0.8125rem; background: var(--cm-bg); color: var(--cm-text-primary); outline: none; transition: border-color 0.15s; }
.cm-search-input:focus { border-color: var(--cm-text-muted); }

/* ── Table ── */
.cm-table-wrap { overflow-x: auto; }
.cm-table { width: 100%; border-collapse: collapse; font-size: 0.8125rem; }
.cm-table th {
    padding: 10px 14px;
    font-size: 0.6875rem;
    font-weight: 700;
    color: var(--cm-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    text-align: left;
    background: var(--cm-bg);
    white-space: nowrap;
}
.cm-tr { border-bottom: 1px solid var(--cm-card-border); transition: background-color 0.1s; }
.cm-tr:hover { background-color: var(--cm-bg); }
.cm-td { padding: 12px 14px; vertical-align: middle; color: var(--cm-text-primary); }

.cm-identity { display: flex; align-items: center; gap: 10px; }
.cm-avatar {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    flex-shrink: 0;
}
/* Dynamic avatar gradients */
.cm-avatar--a, .cm-avatar--e, .cm-avatar--i, .cm-avatar--m, .cm-avatar--q, .cm-avatar--u, .cm-avatar--y { background: linear-gradient(135deg, #10b981, #3b82f6); color: #ffffff; }
.cm-avatar--b, .cm-avatar--f, .cm-avatar--j, .cm-avatar--n, .cm-avatar--r, .cm-avatar--v, .cm-avatar--z { background: linear-gradient(135deg, #6366f1, #a855f7); color: #ffffff; }
.cm-avatar--c, .cm-avatar--g, .cm-avatar--k, .cm-avatar--o, .cm-avatar--s, .cm-avatar--w { background: linear-gradient(135deg, #f59e0b, #ec4899); color: #ffffff; }
.cm-avatar--d, .cm-avatar--h, .cm-avatar--l, .cm-avatar--p, .cm-avatar--t, .cm-avatar--x { background: linear-gradient(135deg, #ef4444, #f97316); color: #ffffff; }

.cm-cust-name { font-weight: 600; color: var(--cm-text-primary); display: block; }
.cm-cust-meta { font-size: 0.75rem; color: var(--cm-text-muted); margin-top: 1px; display: block; }

.cm-item-chips-flex { display: flex; flex-wrap: wrap; gap: 4px; }
.cm-item-chip {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    background: var(--cm-accent-teal-light);
    color: var(--cm-accent-teal);
    border: 1.5px solid rgba(13, 148, 136, 0.08);
    font-size: 0.6875rem;
    font-weight: 700;
    border-radius: 20px;
}

.cm-badge-mode {
    display: inline-block;
    padding: 3px 8px;
    font-size: 0.6875rem;
    font-weight: 800;
    border-radius: 6px;
    text-transform: uppercase;
}
.cm-badge-mode--neft { background: rgba(37, 99, 235, 0.1); color: #2563eb; }
.cm-badge-mode--cheque { background: rgba(148, 163, 184, 0.15); color: #475569; }
.cm-badge-mode--upi { background: rgba(13, 148, 136, 0.1); color: #0d9488; }
.cm-badge-mode--cash { background: rgba(245, 158, 11, 0.1); color: #d97706; }
.cm-badge-mode--credit { background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); }

/* ── Bento Actor Portal Grid ── */
.cm-actor-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.5rem;
}
@media (max-width: 960px) {
    .cm-actor-grid { grid-template-columns: 1fr; gap: 1rem; }
}

.cm-actor-card {
    background: rgba(255, 255, 255, 0.45);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(226, 232, 240, 0.8);
    border-radius: 20px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    min-height: 190px;
    cursor: pointer;
    position: relative;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 20px -2px rgba(0,0,0,0.02);
    overflow: hidden;
}

[data-theme='dark'] .cm-actor-card {
    background: rgba(17, 24, 39, 0.45);
    border: 1px solid rgba(31, 41, 55, 0.7);
    box-shadow: 0 4px 20px -2px rgba(0,0,0,0.4);
}

.cm-actor-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: transparent;
    transition: all 0.3s ease;
}

/* Hover effects */
.cm-actor-card:hover {
    transform: translateY(-4px) scale(1.01);
    box-shadow: 0 12px 25px -5px rgba(0,0,0,0.06), 0 8px 10px -6px rgba(0,0,0,0.06);
    border-color: rgba(13, 148, 136, 0.3);
}

[data-theme='dark'] .cm-actor-card:hover {
    box-shadow: 0 12px 25px -5px rgba(0,0,0,0.5), 0 8px 10px -6px rgba(0,0,0,0.5);
    border-color: rgba(13, 148, 136, 0.4);
}

/* Individual card top lines and gradients */
.cm-actor-card--customer::before {
    background: linear-gradient(90deg, #10b981, #3b82f6);
}
.cm-actor-card--dealer::before {
    background: linear-gradient(90deg, #6366f1, #a855f7);
}
.cm-actor-card--vendor::before {
    background: linear-gradient(90deg, #0d9488, #0f766e);
}

/* Active Vendor Card */
.cm-actor-card--vendor.cm-active {
    background: rgba(13, 148, 136, 0.04);
    border-color: rgba(13, 148, 136, 0.35);
    box-shadow: 0 8px 30px rgba(13, 148, 136, 0.08);
}
[data-theme='dark'] .cm-actor-card--vendor.cm-active {
    background: rgba(13, 148, 136, 0.06);
    border-color: rgba(13, 148, 136, 0.5);
}

/* Actor Badge */
.cm-actor-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 10px;
    background: #f1f5f9;
    color: #475569;
    border-radius: 30px;
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    width: fit-content;
    margin-bottom: 1rem;
    text-transform: uppercase;
}
[data-theme='dark'] .cm-actor-badge {
    background: #1f2937;
    color: #9ca3af;
}

.cm-actor-card--customer .cm-actor-badge { background: rgba(16, 185, 129, 0.1); color: #10b981; }
.cm-actor-card--dealer .cm-actor-badge { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
.cm-actor-card--vendor .cm-actor-badge { background: rgba(13, 148, 136, 0.1); color: #0d9488; }

.cm-actor-badge .material-symbols-rounded { font-size: 14px; }

/* Active Pulse Dot */
.cm-active-dot {
    width: 6px;
    height: 6px;
    background-color: #10b981;
    border-radius: 50%;
    margin-left: 2px;
    display: inline-block;
    box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    animation: cm-pulse-dot 1.6s infinite cubic-bezier(0.66, 0, 0, 1);
}

@keyframes cm-pulse-dot {
    0% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7);
    }
    70% {
        box-shadow: 0 0 0 6px rgba(16, 185, 129, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(16, 185, 129, 0);
    }
}

/* Content */
.cm-actor-content {
    margin-bottom: 1.25rem;
    flex-grow: 1;
}
.cm-actor-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--cm-text-primary);
    margin-bottom: 6px;
}
.cm-actor-desc {
    font-size: 0.75rem;
    color: var(--cm-text-secondary);
    line-height: 1.5;
    margin: 0;
}

/* Actions */
.cm-actor-actions {
    display: flex;
    align-items: center;
    gap: 8px;
    width: 100%;
}
.cm-actor-btn-primary {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 6px;
    padding: 8px 14px;
    background: linear-gradient(135deg, var(--cm-accent-teal), var(--cm-accent-teal-hover));
    color: #ffffff !important;
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.2s ease;
    box-shadow: 0 4px 12px -2px rgba(13, 148, 136, 0.2);
    flex-grow: 1;
    text-align: center;
    cursor: pointer;
    border: none;
}
.cm-actor-btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 6px 15px -2px rgba(13, 148, 136, 0.3);
    opacity: 0.95;
}
.cm-actor-btn-primary .material-symbols-rounded { font-size: 16px; }

.cm-actor-btn-secondary {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px 12px;
    background: var(--cm-bg);
    color: var(--cm-text-secondary) !important;
    border: 1px solid var(--cm-card-border);
    border-radius: 10px;
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}
.cm-actor-btn-secondary:hover {
    background: var(--cm-card-border);
    color: var(--cm-text-primary) !important;
}

/* Customer card custom colors */
.cm-actor-card--customer .cm-actor-btn-primary {
    background: linear-gradient(135deg, #10b981, #059669);
    box-shadow: 0 4px 12px -2px rgba(16, 185, 129, 0.2);
}
.cm-actor-card--customer .cm-actor-btn-primary:hover {
    box-shadow: 0 6px 15px -2px rgba(16, 185, 129, 0.3);
}

/* Dealer card custom colors */
.cm-actor-card--dealer .cm-actor-btn-primary {
    background: linear-gradient(135deg, #6366f1, #4f46e5);
    box-shadow: 0 4px 12px -2px rgba(99, 102, 241, 0.2);
}
.cm-actor-card--dealer .cm-actor-btn-primary:hover {
    box-shadow: 0 6px 15px -2px rgba(99, 102, 241, 0.3);
}

.cm-form-grid-header.has-due-date {
    grid-template-columns: repeat(5, 1fr);
}
@media (max-width: 1100px) {
    .cm-form-grid-header.has-due-date { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 900px) {
    .cm-form-grid-header.has-due-date { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 520px) {
    .cm-form-grid-header.has-due-date { grid-template-columns: 1fr; }
}

/* Actions */
.cm-actions { display: flex; align-items: center; justify-content: flex-end; gap: 6px; }
.cm-action-btn {
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid var(--cm-card-border);
    border-radius: 7px;
    background: transparent;
    cursor: pointer;
    color: var(--cm-text-muted);
    transition: border-color 0.15s, color 0.15s, background-color 0.15s;
    text-decoration: none;
}
.cm-action-btn:hover { border-color: var(--cm-text-muted); color: var(--cm-text-primary); background: var(--cm-bg); }

/* Empty state */
.cm-empty { padding: 3.5rem 1.5rem; text-align: center; }
.cm-empty-icon { width: 48px; height: 48px; border-radius: 10px; background: var(--cm-bg); color: var(--cm-text-muted); display: inline-flex; align-items: center; justify-content: center; margin-bottom: 0.75rem; }
.cm-empty-title { font-size: 0.875rem; font-weight: 700; color: var(--cm-text-primary); }
.cm-empty-sub { font-size: 0.75rem; color: var(--cm-text-secondary); margin-top: 2px; }

.cm-pagination-footer { padding: 1rem; border-top: 1px solid var(--cm-card-border); }
</style>
@endpush

@push('scripts')
<script>
let rowCount = 1;

const ITEM_OPTIONS = `@foreach($items as $item)<option value="{{ $item->id }}" data-unit="{{ $item->base_unit }}">{{ $item->name }} ({{ $item->code }})</option>@endforeach`;
const BATCH_OPTIONS = `@foreach($batches as $batch)<option value="{{ $batch->id }}">{{ $batch->batch_code }} - {{ $batch->breed }}</option>@endforeach`;
const WH_OPTIONS = `@foreach($warehouses as $wh)<option value="{{ $wh->id }}">{{ $wh->name }}</option>@endforeach`;

function addRow() {
    const body = document.getElementById('items-body');
    const newRow = document.createElement('tr');
    newRow.className = 'item-row';
    newRow.innerHTML = `
        <td class="p-3">
            <select name="items[${rowCount}][item_id]" required onchange="updateUnit(this)" class="cm-table-select item-selector">
                <option value="">Select Product...</option>
                ${ITEM_OPTIONS}
            </select>
        </td>
        <td class="p-3">
            <select name="items[${rowCount}][batch_id]" class="cm-table-select">
                <option value="">General (No Batch Link)</option>
                ${BATCH_OPTIONS}
            </select>
        </td>
        <td class="p-3">
            <select name="items[${rowCount}][warehouse_id]" class="cm-table-select">
                <option value="">Placement Area... (Optional)</option>
                ${WH_OPTIONS}
            </select>
        </td>
        <td class="p-3">
            <input type="number" name="items[${rowCount}][qty]" step="0.01" required placeholder="0.00" class="cm-table-input row-qty" oninput="recalculate()">
        </td>
        <td class="p-3">
            <input type="text" name="items[${rowCount}][unit]" value="kg" class="cm-table-input row-unit cm-readonly" readonly tabindex="-1">
        </td>
        <td class="p-3">
            <input type="number" name="items[${rowCount}][rate]" step="0.01" required placeholder="0.00" class="cm-table-input row-rate" oninput="recalculate()">
        </td>
        <td class="p-3 text-right font-semibold text-slate-800 dark:text-slate-200">
            <span class="row-total">₹0.00</span>
        </td>
        <td class="p-3 text-center">
            <button type="button" onclick="this.closest('tr').remove(); recalculate();" class="cm-action-btn cm-action-btn--danger" style="color: #ef4444; border-color: rgba(239, 68, 68, 0.2);" title="Remove row">
                <span class="material-symbols-rounded" style="font-size: 16px;">delete</span>
            </button>
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

function toggleVendorPortal(event) {
    if (event) event.stopPropagation();
    const formContainer = document.getElementById('vendor-form-container');
    const logsContainer = document.getElementById('vendor-logs-container');
    const toggleCard = document.getElementById('vendor-toggle-card');
    const iconToggle = document.getElementById('vendor-icon-toggle');
    const btnText = document.getElementById('vendor-btn-text');

    if (!formContainer || !logsContainer) return;

    const isCollapsed = formContainer.classList.contains('cm-hidden');

    if (isCollapsed) {
        // Expand
        formContainer.classList.remove('cm-hidden');
        logsContainer.classList.remove('cm-hidden');
        toggleCard.classList.add('cm-active');
        if (iconToggle) iconToggle.textContent = 'expand_less';
        if (btnText) btnText.textContent = 'Collapse Entry Portal';
    } else {
        // Collapse
        formContainer.classList.add('cm-hidden');
        logsContainer.classList.add('cm-hidden');
        toggleCard.classList.remove('cm-active');
        if (iconToggle) iconToggle.textContent = 'expand_more';
        if (btnText) btnText.textContent = 'Expand Entry Portal';
    }
}

function toggleDueDateField() {
    const paymentModeSelect = document.querySelector('select[name="payment_mode"]');
    const dueDateGroup = document.getElementById('due-date-group');
    const dueDateInput = document.getElementById('due_date_input');
    const gridHeader = document.querySelector('.cm-form-grid-header');
    
    if (!paymentModeSelect || !dueDateGroup) return;

    if (paymentModeSelect.value === 'Credit') {
        dueDateGroup.classList.remove('cm-hidden');
        if (gridHeader) gridHeader.classList.add('has-due-date');
        dueDateInput.required = true;
        
        // Default to 15 days in future if empty
        if (!dueDateInput.value) {
            const today = new Date();
            today.setDate(today.getDate() + 15);
            dueDateInput.value = today.toISOString().split('T')[0];
        }
    } else {
        dueDateGroup.classList.add('cm-hidden');
        if (gridHeader) gridHeader.classList.remove('has-due-date');
        dueDateInput.required = false;
        dueDateInput.value = '';
    }
}

function updateVendorOutstanding() {
    const vendorSelect = document.querySelector('select[name="vendor_name"]');
    const outstandingInfo = document.getElementById('vendor-outstanding-info');
    const outstandingAmount = document.getElementById('vendor-outstanding-amount');
    
    if (!vendorSelect || !outstandingInfo || !outstandingAmount) return;
    
    const selectedOption = vendorSelect.options[vendorSelect.selectedIndex];
    if (!selectedOption) return;
    
    const outstanding = parseFloat(selectedOption.getAttribute('data-outstanding')) || 0;
    
    if (outstanding > 0) {
        outstandingAmount.textContent = '₹' + outstanding.toLocaleString('en-IN', { minimumFractionDigits: 2 });
        outstandingInfo.classList.remove('cm-hidden');
    } else {
        outstandingInfo.classList.add('cm-hidden');
    }
}

// Initial unit update for first row if pre-selected
window.addEventListener('DOMContentLoaded', () => {
    const selector = document.querySelector('.item-selector');
    if (selector && selector.value) updateUnit(selector);
    
    toggleDueDateField();
    updateVendorOutstanding();
});
</script>
@endpush
