@extends('layouts.app')
@section('title', 'Record Purchase Refill')

@section('content')
<div class="cm-page flex justify-center">
    <div class="w-full max-w-2xl">
        
        {{-- Back & Header --}}
        <div class="mb-6 flex items-center justify-between">
            <a href="{{ route('purchases.entry') }}" class="cm-btn-back flex items-center gap-1.5 text-xs font-bold text-teal-600 dark:text-teal-400 hover:opacity-80 transition-all uppercase tracking-wider">
                <span class="material-symbols-rounded" style="font-size: 16px;">arrow_back</span>
                Back to Dashboard
            </a>
            <span class="text-xs font-mono px-2.5 py-1 rounded-full bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-300 font-bold border border-teal-100 dark:border-teal-900/50">
                Single Refill Mode
            </span>
        </div>

        <div class="cm-form-container-centered">
            <form action="{{ route('purchases.store') }}" method="POST" id="purchase-create-form" class="cm-card-form-large">
                @csrf
                
                {{-- Form Section Title --}}
                <div class="cm-form-section-title mb-6 pb-4 border-b border-slate-100 dark:border-gray-800">
                    <span class="material-symbols-rounded text-teal-600 dark:text-teal-400">add_circle</span>
                    <div>
                        <h2>Record Single Refill Inward</h2>
                        <p class="text-xs text-slate-400 dark:text-slate-500 font-medium mt-0.5">Quickly entry stock refills directly into specific batch or warehouse placements</p>
                    </div>
                </div>

                {{-- 1. Supplier & Invoice Info Group --}}
                <div class="space-y-4 mb-6">
                    <h3 class="cm-sub-section-title">1. Supply Partner & Bill Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="cm-form-group">
                            <label class="cm-form-label">Vendor Name <span class="cm-required">*</span></label>
                            <select name="vendor_name" required class="cm-form-input cm-select">
                                <option value="">Select supply partner...</option>
                                @foreach($vendors as $vendor)
                                    <option value="{{ $vendor->firm_name }}" {{ ($vendor_name == $vendor->firm_name) ? 'selected' : '' }}>
                                        {{ $vendor->firm_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="cm-form-group">
                            <label class="cm-form-label">Invoice / Bill Number</label>
                            <input type="text" name="invoice_no" placeholder="e.g. INV-{{ date('Y') }}-001" class="cm-form-input">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="cm-form-group">
                            <label class="cm-form-label">Billing Date <span class="cm-required">*</span></label>
                            <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="cm-form-input font-semibold">
                        </div>
                        <div class="cm-form-group">
                            <label class="cm-form-label">Payment Mode <span class="cm-required">*</span></label>
                            <select name="payment_mode" required class="cm-form-input cm-select">
                                @foreach(['Cash', 'UPI', 'NEFT', 'Cheque'] as $mode)
                                    <option value="{{ $mode }}">{{ $mode }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                {{-- 2. Item details --}}
                <div class="space-y-4 mb-6 pt-4 border-t border-slate-100 dark:border-gray-800">
                    <h3 class="cm-sub-section-title">2. Product Details & Stock Placement</h3>
                    
                    <div class="cm-form-group">
                        <label class="cm-form-label">Product / Item Master <span class="cm-required">*</span></label>
                        <select name="items[0][item_id]" required onchange="updateUnit(this)" class="cm-form-input cm-select item-selector">
                            <option value="">Select product item...</option>
                            @foreach($items as $item)
                                <option value="{{ $item->id }}" data-unit="{{ $item->base_unit }}">
                                    {{ $item->name }} ({{ $item->code }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- <div class="cm-form-group">
                            <label class="cm-form-label">Link to Flock Batch</label>
                            <select name="items[0][batch_id]" class="cm-form-input cm-select">
                                <option value="">General / Farmwide (No Batch Link)</option>
                                @foreach($batches as $batch)
                                    <option value="{{ $batch->id }}">{{ $batch->batch_code }} - {{ $batch->breed }}</option>
                                @endforeach
                            </select>
                        </div> -->
                        <!-- <div class="cm-form-group">
                            <label class="cm-form-label">Warehouse Location</label>
                            <select name="items[0][warehouse_id]" class="cm-form-input cm-select">
                                <option value="" selected>Select storage location... (Optional)</option>
                                @foreach($warehouses as $wh)
                                    <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                @endforeach
                            </select>
                        </div> -->
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="cm-form-group">
                            <label class="cm-form-label">Quantity <span class="cm-required">*</span></label>
                            <input type="number" name="items[0][qty]" id="qty" step="0.01" required placeholder="0.00" oninput="recalculate()" class="cm-form-input font-bold">
                        </div>
                        <div class="cm-form-group">
                            <label class="cm-form-label">Unit of Measure</label>
                            <input type="text" name="items[0][unit]" id="unit" value="kg" readonly tabindex="-1" class="cm-form-input cm-readonly text-slate-500 font-semibold">
                        </div>
                        <div class="cm-form-group">
                            <label class="cm-form-label">Rate per Unit (₹) <span class="cm-required">*</span></label>
                            <input type="number" name="items[0][rate]" id="rate" step="0.01" required placeholder="0.00" oninput="recalculate()" class="cm-form-input font-bold">
                        </div>
                    </div>
                </div>

                {{-- 3. Billing Total and Tax details --}}
                <div class="cm-summary-block rounded-2xl p-5 border border-slate-100 dark:border-gray-800 bg-slate-50/50 dark:bg-slate-900/10 mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-end">
                        <div class="space-y-4">
                            <div class="cm-form-group">
                                <label class="cm-form-label">GST Tax Percentage (%)</label>
                                <input type="number" name="gst_percentage" id="gst-percentage" value="18" step="0.1" oninput="recalculate()" class="cm-form-input font-bold">
                            </div>
                            <div class="cm-form-group">
                                <label class="cm-form-label">Computed GST Value</label>
                                <input type="text" id="display-tax" readonly value="₹0.00" class="cm-form-input cm-readonly font-mono text-slate-500 font-semibold">
                            </div>
                        </div>
                        
                        <div class="cm-submit-total-box rounded-xl p-4 flex flex-col justify-between">
                            <div class="mb-3">
                                <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest block">Net Grand Total</span>
                                <span id="display-total" class="text-2xl font-black text-slate-900 dark:text-slate-100 font-mono">₹0.00</span>
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-lg flex items-center justify-center gap-1.5 transition-all duration-200">
                                <span class="material-symbols-rounded" style="font-size: 18px;">task_alt</span>
                                Save Purchase
                            </button>
                        </div>
                    </div>
                </div>

            </form>
        </div>

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
}

.cm-page { padding: 1rem 0 3rem; }

/* Header Elements */
.cm-btn-back {
    text-decoration: none;
    transition: all 0.2s ease;
}

/* Form Styles */
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
    gap: 12px;
}
.cm-form-section-title h2 {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--cm-text-primary);
    margin: 0;
}
.cm-form-section-title .material-symbols-rounded {
    font-size: 24px;
}

.cm-sub-section-title {
    font-size: 0.75rem;
    font-weight: 800;
    color: var(--cm-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.06em;
    margin-bottom: 0.5rem;
}

.cm-form-group {
    display: flex;
    flex-direction: column;
}

.cm-form-label {
    font-size: 0.6875rem;
    font-weight: 700;
    color: var(--cm-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 6px;
}

.cm-required {
    color: #dc2626;
}

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

.cm-readonly {
    background: var(--cm-bg) !important;
    opacity: 0.75;
    cursor: not-allowed;
}

.cm-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 12px center;
    background-size: 14px;
    padding-right: 32px;
}

/* Submission Styling */
.cm-submit-total-box {
    background: var(--cm-bg);
    border: 1px solid var(--cm-card-border);
}
</style>
@endpush

@push('scripts')
<script>
    function updateUnit(selectEl) {
        const option = selectEl.options[selectEl.selectedIndex];
        const unit = option ? option.getAttribute('data-unit') : 'kg';
        document.getElementById('unit').value = unit || 'kg';
    }

    function recalculate() {
        const qty = parseFloat(document.getElementById('qty').value) || 0;
        const rate = parseFloat(document.getElementById('rate').value) || 0;
        const subtotal = qty * rate;

        const gstPercentage = parseFloat(document.getElementById('gst-percentage').value) || 0;
        const gstAmount = subtotal * (gstPercentage / 100);
        const grandTotal = subtotal + gstAmount;

        document.getElementById('display-tax').value = '₹' + gstAmount.toFixed(2);
        document.getElementById('display-total').textContent = '₹' + grandTotal.toLocaleString('en-IN', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Set initial calculations on load
    document.addEventListener('DOMContentLoaded', () => {
        recalculate();
    });
</script>
@endpush
