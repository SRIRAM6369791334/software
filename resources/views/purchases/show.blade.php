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
            <div class="cm-table-wrap border border-slate-200 dark:border-gray-800 rounded-xl overflow-hidden mb-8">
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
                                            <span class="font-semibold text-slate-900 dark:text-slate-100 block">{{ $item->item_name }}</span>
                                            <span class="text-xs text-slate-400">Stock procurement & placement in {{ $item->warehouse->name ?? 'Default Warehouse' }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="cm-td text-right font-mono text-slate-800 dark:text-slate-200">
                                    {{ number_format($item->quantity, 2) }} {{ $item->unit }}
                                </td>
                                <td class="cm-td text-right text-slate-600 dark:text-slate-400">
                                    ₹{{ number_format($item->rate, 2) }}
                                </td>
                                <td class="cm-td text-right font-semibold text-slate-900 dark:text-slate-100">
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
    margin-bottom: 1.5rem;
    gap: 1rem;
    flex-wrap: wrap;
}
.cm-back-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--cm-accent-teal);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    text-decoration: none;
    transition: color 0.15s;
}
.cm-back-link:hover { color: var(--cm-accent-teal-hover); }
.cm-back-link .material-symbols-rounded { font-size: 16px; }

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
    padding: 8px 14px;
    background: var(--cm-card-bg);
    color: var(--cm-text-secondary);
    border: 1px solid var(--cm-card-border);
    border-radius: 8px;
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s, color 0.15s, border-color 0.15s;
    text-decoration: none;
}
.cm-btn-secondary:hover {
    background: var(--cm-bg);
    color: var(--cm-text-primary);
    border-color: var(--cm-text-muted);
}
.cm-btn-secondary .material-symbols-rounded { font-size: 16px; }

.cm-btn-ghost {
    display: inline-flex;
    align-items: center;
    gap: 6px;
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

/* ── Details Wrapper Card ── */
.cm-details-card {
    background: var(--cm-card-bg);
    border: 1px solid var(--cm-card-border);
    border-radius: 16px;
    padding: 2.25rem;
    box-shadow: var(--cm-shadow-md);
    max-width: 840px;
    margin: 0 auto;
}

/* ── Invoice Header ── */
.cm-invoice-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    border-bottom: 1px solid var(--cm-card-border);
}
.cm-brand-block { display: flex; flex-direction: column; }
.cm-brand-logo {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 1.125rem;
    font-weight: 900;
    color: var(--cm-text-primary);
    letter-spacing: -0.01em;
}
.cm-brand-logo .material-symbols-rounded { font-size: 22px; color: var(--cm-accent-teal); }
.cm-brand-sub { font-size: 0.75rem; color: var(--cm-text-secondary); margin-top: 4px; }

.cm-invoice-meta-block { display: flex; flex-direction: column; text-align: right; }
.cm-invoice-meta-title { font-size: 1.5rem; font-weight: 900; color: var(--cm-text-primary); letter-spacing: 0.05em; line-height: 1; }
.cm-invoice-meta-id { font-size: 0.8125rem; font-weight: 700; color: var(--cm-accent-teal); margin-top: 6px; }

/* ── Details Grid ── */
.cm-details-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 1.5rem;
    border-bottom: 1px solid var(--cm-card-border);
}
@media (max-width: 600px) {
    .cm-details-grid { grid-template-columns: 1fr; gap: 1.25rem; }
}
.cm-details-column { display: flex; flex-direction: column; }
.cm-details-label {
    font-size: 0.6875rem;
    font-weight: 700;
    color: var(--cm-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 4px;
}
.cm-details-value-firm { font-size: 1.125rem; font-weight: 800; color: var(--cm-text-primary); }
.cm-details-value { font-size: 0.9375rem; font-weight: 600; color: var(--cm-text-primary); }
.cm-details-sub-text { font-size: 0.75rem; color: var(--cm-text-muted); margin-top: 4px; }

/* ── Table Styling ── */
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
.cm-td { padding: 14px; vertical-align: middle; color: var(--cm-text-primary); }

.cm-table-indicator {
    width: 4px;
    height: 32px;
    background: var(--cm-accent-teal);
    border-radius: 20px;
    flex-shrink: 0;
    margin-top: 2px;
}

/* ── Badges ── */
.cm-badge-mode {
    display: inline-block;
    padding: 3px 8px;
    font-size: 0.6875rem;
    font-weight: 700;
    border-radius: 6px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.cm-badge-mode--neft { background-color: rgba(59, 130, 246, 0.12); color: #3b82f6; }
.cm-badge-mode--cheque { background-color: rgba(168, 85, 247, 0.12); color: #a855f7; }
.cm-badge-mode--upi { background-color: rgba(16, 185, 129, 0.12); color: #10b981; }
.cm-badge-mode--cash { background-color: rgba(245, 158, 11, 0.12); color: #f59e0b; }

/* ── Calculations Summary Block ── */
.cm-invoice-footer { display: flex; justify-content: flex-end; }
.cm-financial-summary-box { width: 100%; max-width: 320px; display: flex; flex-direction: column; gap: 10px; }
.cm-summary-line { display: flex; justify-content: space-between; align-items: center; font-size: 0.8125rem; }
.cm-summary-label { color: var(--cm-text-secondary); font-weight: 500; }
.cm-summary-val { color: var(--cm-text-primary); font-weight: 600; }

.cm-grand-net-row {
    border-top: 2px solid var(--cm-text-primary);
    padding-top: 10px;
    margin-top: 4px;
}
.cm-grand-label { font-size: 0.9375rem; font-weight: 800; color: var(--cm-text-primary); }
.cm-grand-val { font-size: 1.375rem; font-weight: 900; color: var(--cm-accent-teal); font-family: monospace; }
</style>
@endpush

