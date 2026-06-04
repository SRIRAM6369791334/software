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
@include('partials.cm-style')
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

