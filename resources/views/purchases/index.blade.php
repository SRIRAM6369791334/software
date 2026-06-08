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
            <a href="{{ route('purchases.export') }}" class="cm-export-btn">
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
@include('partials.cm-style')
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

