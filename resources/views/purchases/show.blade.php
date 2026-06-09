@extends('layouts.app')
@section('title', 'Purchase Invoice #' . $purchase->id)

@section('content')
<div class="cm-page">

    {{-- Top Bar Header --}}
    <div class="cm-topbar mb-6">
        <div>
            <a href="{{ route('purchases.entry') }}" class="cm-back-link flex items-center gap-1">
                <span class="material-symbols-rounded">arrow_back</span>
                Back to Purchase Entry
            </a>
            <h1 class="cm-page-title mt-2">Purchase Invoice Details</h1>
            <p class="cm-page-sub">Reference ID: #PUR{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('purchases.print', $purchase->id) }}" class="cm-btn-secondary flex items-center gap-1.5" target="_blank">
                <span class="material-symbols-rounded" style="font-size: 18px;">print</span>
                Print Invoice
            </a>
            <a href="{{ route('purchases.edit', $purchase->id) }}" class="cm-btn-ghost flex items-center gap-1.5 bg-teal-50 hover:bg-teal-100 dark:bg-teal-950/40 dark:hover:bg-teal-900/60 text-teal-600 dark:text-teal-400">
                <span class="material-symbols-rounded" style="font-size: 18px;">edit</span>
                Edit Entry
            </a>
        </div>
    </div>

    {{-- Details Wrapper --}}
    <div class="cm-form-container-full">
        <div class="cm-details-card">
            
            {{-- 1. Invoice Metadata Header --}}
            <div class="cm-invoice-header mb-8 pb-6">
                <div class="cm-brand-block">
                    <div class="cm-brand-logo">
                        <span class="material-symbols-rounded">layers</span>
                        <span>POULTRYPRO</span>
                    </div>
                    <p class="cm-brand-sub">Farm Management & Supply Chain Solutions</p>
                </div>
                <div class="cm-invoice-meta-block">
                    <span class="cm-invoice-meta-title">INVOICE</span>
                    <span class="cm-invoice-meta-id">#PUR{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>

            {{-- 2. Partner & Billing Details Grid --}}
            <div class="cm-details-grid mb-8 pb-6">
                <div class="cm-details-column">
                    <div class="cm-details-label">Procured From (Vendor)</div>
                    <div class="cm-details-value-firm">{{ $purchase->vendor_name }}</div>
                    <p class="cm-details-sub-text">Registered Partner Master Record</p>
                </div>
                <div class="cm-details-column">
                    <div class="cm-details-label">Billing Date</div>
                    <div class="cm-details-value">{{ $purchase->date->format('d F, Y') }}</div>
                    <p class="cm-details-sub-text">Inward Transaction Date</p>
                </div>
                <div class="cm-details-column">
                    <div class="cm-details-label">Payment State / Mode</div>
                    <div>
                        <span class="cm-badge-mode cm-badge-mode--{{ strtolower($purchase->payment_mode) }}">
                            {{ $purchase->payment_mode }}
                        </span>
                    </div>
                    <p class="cm-details-sub-text">Settled Payment Mode</p>
                </div>
            </div>

            {{-- 3. Itemized List Table --}}
            <div class="cm-table-wrap border border-zinc-200 dark:border-gray-800 rounded-xl overflow-hidden mb-8">
                <table class="cm-table">
                    <thead>
                        <tr>
                            <th style="width: 45%;">Item / Product Description</th>
                            <th style="width: 20%; text-align: right;">Quantity</th>
                            <th style="width: 15%; text-align: right;">Unit Rate</th>
                            <th style="width: 20%; text-align: right;">Taxable Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $computedSubtotal = 0; @endphp
                        @foreach($purchase->items as $item)
                            @php 
                                $rowTotal = $item->quantity * $item->rate;
                                $computedSubtotal += $rowTotal;
                            @endphp
                            <tr class="cm-tr">
                                <td class="cm-td">
                                    <div class="flex items-start gap-2.5">
                                        <div class="cm-table-indicator"></div>
                                        <div>
                                            <span class="font-semibold text-zinc-900 dark:text-zinc-100 block">{{ $item->item_name }}</span>
                                            <span class="text-xs text-zinc-400">Stock procurement & placement in {{ $item->warehouse->name ?? 'Default Warehouse' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="cm-td text-right font-mono text-zinc-800 dark:text-zinc-200">
                                    {{ number_format($item->quantity, 2) }} {{ $item->unit }}
                                </td>
                                <td class="cm-td text-right text-zinc-600 dark:text-zinc-400">
                                    ₹{{ number_format($item->rate, 2) }}
                                </td>
                                <td class="cm-td text-right font-semibold text-zinc-900 dark:text-zinc-100">
                                    ₹{{ number_format($rowTotal, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- 4. Financial Calculations Summary --}}
            <div class="cm-invoice-footer">
                <div class="cm-financial-summary-box">
                    <div class="cm-summary-line">
                        <span class="cm-summary-label">Subtotal (Taxable)</span>
                        <span class="cm-summary-val font-mono">₹{{ number_format($computedSubtotal, 2) }}</span>
                    </div>
                    <div class="cm-summary-line">
                        <span class="cm-summary-label">Integrated GST ({{ $purchase->gst_percentage }}%)</span>
                        <span class="cm-summary-val font-mono">₹{{ number_format($purchase->gst_amount, 2) }}</span>
                    </div>
                    <div class="cm-summary-line cm-grand-net-row">
                        <span class="cm-grand-label">Grand Net Total</span>
                        <span class="cm-grand-val">₹{{ number_format($purchase->total_amount, 2) }}</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection

@push('styles')
@include('partials.cm-style')
@endpush


