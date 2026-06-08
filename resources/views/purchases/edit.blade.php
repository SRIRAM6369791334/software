@extends('layouts.app')
@section('title', 'Edit Purchase Refill')

@section('content')
<div class="cm-page">

    {{-- Top Bar Header --}}
    <div class="cm-topbar mb-6">
        <div>
            <a href="{{ route('purchases.show', $purchase->id) }}" class="cm-btn-back flex items-center gap-1.5 text-xs font-bold text-teal-600 dark:text-teal-400 hover:opacity-80 transition-all uppercase tracking-wider mb-2">
                <span class="material-symbols-rounded" style="font-size: 16px;">arrow_back</span>
                Back to Invoice
            </a>
            <h1 class="cm-page-title">Edit Purchase Entry</h1>
            <p class="cm-page-sub">Update supplier information, dynamic transaction items, or warehouse placements</p>
        </div>
        <div class="flex gap-2">
            <span class="text-xs font-mono px-3 py-1.5 rounded-lg bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 font-bold border border-teal-100 dark:border-teal-900/50">
                Invoice ID: #{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}
            </span>
        </div>
    </div>

    {{-- Edit Form Block --}}
    <div class="cm-form-container-full mb-8">
        <form action="{{ route('purchases.update', $purchase->id) }}" method="POST" id="purchase-form" class="cm-card-form-large">
            @csrf
            @method('PUT')
            
            <div class="cm-form-section-title mb-6">
                <span class="material-symbols-rounded text-teal-600 dark:text-teal-400">edit_document</span>
                <h2>Update Purchase Transaction Details</h2>
            </div>

            {{-- 1. Header Information Row --}}
            <div class="cm-form-grid-header">
                <div class="cm-form-group">
                    <label class="cm-form-label">1. Vendor / Partner <span class="cm-required">*</span></label>
                    <select name="vendor_name" required class="cm-form-input cm-select">
                        <option value="">Select supply partner...</option>
                        @foreach($vendors as $vendor)
                            <option value="{{ $vendor->firm_name }}" {{ $purchase->vendor_name === $vendor->firm_name ? 'selected' : '' }}>
                                {{ $vendor->firm_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">2. Invoice Number / Bill ID</label>
                    <input type="text" name="invoice_no" value="{{ old('invoice_no', $purchase->invoice_no) }}" placeholder="e.g. INV-2026-99" class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">3. Billing Date <span class="cm-required">*</span></label>
                    <input type="date" name="date" required value="{{ old('date', $purchase->date->format('Y-m-d')) }}" class="cm-form-input font-semibold">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">4. Payment Mode <span class="cm-required">*</span></label>
                    <select name="payment_mode" required class="cm-form-input cm-select" onchange="toggleDueDateField()">
                        <option value="Cash" {{ $purchase->payment_mode === 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="UPI" {{ $purchase->payment_mode === 'UPI' ? 'selected' : '' }}>UPI</option>
                        <option value="NEFT" {{ $purchase->payment_mode === 'NEFT' ? 'selected' : '' }}>NEFT</option>
                        <option value="Cheque" {{ $purchase->payment_mode === 'Cheque' ? 'selected' : '' }}>Cheque</option>
                        <option value="Credit" {{ $purchase->payment_mode === 'Credit' ? 'selected' : '' }}>Credit</option>
                    </select>
                </div>
                
                {{-- Conditional Payment Due Date Field --}}
                <div class="cm-form-group cm-hidden" id="due-date-group">
                    <label class="cm-form-label">5. Payment Due Date <span class="cm-required">*</span></label>
                    <input type="date" name="due_date" id="due_date_input" value="{{ old('due_date', $purchase->due_date ? $purchase->due_date->format('Y-m-d') : '') }}" class="cm-form-input font-semibold">
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
                            @foreach($purchase->items as $index => $item)
                            <tr class="item-row">
                                <td class="p-3">
                                    <select name="items[{{ $index }}][item_id]" required onchange="updateUnit(this)" class="cm-table-select item-selector">
                                        <option value="">Select Product...</option>
                                        @foreach($items as $masterItem)
                                            <option value="{{ $masterItem->id }}" data-unit="{{ $masterItem->base_unit }}" {{ $item->item_id == $masterItem->id ? 'selected' : '' }}>
                                                {{ $masterItem->name }} ({{ $masterItem->code }})
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <!-- <td class="p-3">
                                    <select name="items[{{ $index }}][batch_id]" class="cm-table-select">
                                        <option value="">General (No Batch Link)</option>
                                        @foreach($batches as $batch)
                                            <option value="{{ $batch->id }}" {{ $item->batch_id == $batch->id ? 'selected' : '' }}>{{ $batch->batch_code }} - {{ $batch->breed }}</option>
                                        @endforeach
                                    </select>
                                </td> -->
                                <!-- <td class="p-3">
                                    <select name="items[{{ $index }}][warehouse_id]" class="cm-table-select">
                                        <option value="">Placement Area... (Optional)</option>
                                        @foreach($warehouses as $wh)
                                            <option value="{{ $wh->id }}" {{ $item->warehouse_id == $wh->id ? 'selected' : '' }}>{{ $wh->name }}</option>
                                        @endforeach
                                    </select>
                                </td> -->
                                <td class="p-3">
                                    <input type="number" name="items[{{ $index }}][qty]" value="{{ $item->quantity }}" step="0.01" required placeholder="0.00" class="cm-table-input row-qty font-bold" oninput="recalculate()">
                                </td>
                                <td class="p-3">
                                    <input type="text" name="items[{{ $index }}][unit]" value="{{ $item->unit }}" class="cm-table-input row-unit cm-readonly" readonly tabindex="-1">
                                </td>
                                <td class="p-3">
                                    <input type="number" name="items[{{ $index }}][rate]" value="{{ $item->rate }}" step="0.01" required placeholder="0.00" class="cm-table-input row-rate font-bold" oninput="recalculate()">
                                </td>
                                <td class="p-3 text-right font-semibold text-slate-800 dark:text-slate-200">
                                    <span class="row-total">₹{{ number_format($item->quantity * $item->rate, 2) }}</span>
                                </td>
                                <td class="p-3 text-center">
                                    @if($index > 0)
                                    <button type="button" onclick="this.closest('tr').remove(); recalculate();" class="cm-action-btn cm-action-btn--danger" style="color: #ef4444; border-color: rgba(239, 68, 68, 0.2);" title="Remove row">
                                        <span class="material-symbols-rounded" style="font-size: 16px;">delete</span>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
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
                            <input type="number" name="gst_percentage" id="gst-percentage" value="{{ $purchase->gst_percentage }}" step="0.1" class="cm-form-input font-bold" oninput="recalculate()">
                        </div>
                        <div class="flex-1">
                            <label class="cm-small-label">Computed GST Value</label>
                            <input type="text" id="display-tax" readonly value="₹{{ number_format($purchase->gst_amount, 2) }}" class="cm-form-input cm-readonly font-mono text-slate-500 font-semibold">
                        </div>
                    </div>
                </div>

                <div class="cm-glowing-grand-total">
                    <div class="cm-total-details">
                        <span class="cm-total-label">Final Grand Net Total</span>
                        <span id="display-total" class="cm-total-value">₹{{ number_format($purchase->total_amount, 2) }}</span>
                    </div>
                    <button type="submit" class="cm-submit-total-btn">
                        <span class="material-symbols-rounded">check_circle</span>
                        Update Purchase Transaction
                    </button>
                </div>
            </div>
        </form>
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

/* ── Top Bar ── */
.cm-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
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

.cm-btn-back {
    text-decoration: none;
    transition: all 0.2s ease;
}

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
    border-color: var(--cm-accent-teal);
    box-shadow: 0 0 0 4px rgba(13, 148, 136, 0.12);
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
.cm-table-select:focus { border-color: var(--cm-accent-teal); }

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
.cm-table-input:focus { border-color: var(--cm-accent-teal); }
.cm-readonly { background: var(--cm-card-border); opacity: 0.75; cursor: not-allowed; }

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

/* ── Table ── */

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
.item-row { border-bottom: 1px solid var(--cm-card-border); transition: background-color 0.1s; }
.item-row:hover { background-color: var(--cm-bg); }

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
</style>
@endpush

@push('scripts')
<script>
let rowCount = {{ $purchase->items->count() }};

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
                \${ITEM_OPTIONS}
            </select>
        </td>
        <td class="p-3">
            <select name="items[${rowCount}][batch_id]" class="cm-table-select">
                <option value="">General (No Batch Link)</option>
                \${BATCH_OPTIONS}
            </select>
        </td>
        <td class="p-3">
            <select name="items[${rowCount}][warehouse_id]" class="cm-table-select">
                <option value="">Placement Area... (Optional)</option>
                \${WH_OPTIONS}
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

window.addEventListener('DOMContentLoaded', () => {
    recalculate();
    toggleDueDateField();
});
</script>
@endpush
