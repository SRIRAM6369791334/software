@extends('layouts.pdf')
@section('title', 'INVOICE — ' . ($bill->invoice_no ?? 'INV'))
@section('meta', "Invoice No: {$bill->invoice_no} | Period: {$bill->period_start->format('d M')} – {$bill->period_end->format('d M Y')}")

@push('styles')
<style>
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111827; }
    .header-band { background-color: #065f46; color: #fff; padding: 18px 24px; margin-bottom: 0; }
    .header-band h1 { font-size: 18px; font-weight: bold; margin: 0; }
    .header-band p  { font-size: 9px; margin: 3px 0 0; color: #a7f3d0; text-transform: uppercase; letter-spacing: 2px; }
    .inv-no { text-align: right; }
    .inv-no .no { font-size: 17px; font-weight: bold; font-family: monospace; }
    .badge { display: inline-block; padding: 2px 10px; border-radius: 20px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
    .badge-paid    { background: #6ee7b7; color: #065f46; }
    .badge-pending { background: #fcd34d; color: #78350f; }

    /* Party row */
    .party-row { width: 100%; border-collapse: collapse; margin: 0; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
    .party-row td { padding: 12px 24px; vertical-align: top; width: 50%; }
    .section-label { font-size: 8px; font-weight: bold; color: #6b7280; text-transform: uppercase; letter-spacing: 1.5px; margin-bottom: 5px; }

    /* Data table */
    .data-table { width: 100%; border-collapse: collapse; font-size: 10px; }
    .data-table thead tr { background-color: #ecfdf5; }
    .data-table th { padding: 7px 14px; font-size: 8px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; border-bottom: 1px solid #d1d5db; text-align: left; }
    .data-table th.text-right { text-align: right; }
    .data-table td { padding: 8px 14px; border-bottom: 1px solid #f3f4f6; }
    .data-table td.text-right { text-align: right; }
    .data-table tfoot td { background: #f0fdf4; font-weight: bold; border-top: 2px solid #6ee7b7; }

    /* Section header bar */
    .section-bar { background: #f3f4f6; padding: 7px 14px; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #374151; border-top: 1px solid #e5e7eb; border-bottom: 1px solid #e5e7eb; }
    .section-bar-green  { background: #ecfdf5; color: #065f46; }
    .section-bar-indigo { background: #eef2ff; color: #3730a3; }
    .section-bar-amber  { background: #fffbeb; color: #78350f; }

    /* Summary box */
    .summary-box { float: right; width: 240px; margin-top: 10px; }
    .summary-row { display: flex; justify-content: space-between; padding: 4px 0; font-size: 10px; border-bottom: 1px solid #f3f4f6; }
    .summary-total { display: flex; justify-content: space-between; padding: 8px 0 0; font-size: 13px; font-weight: bold; }

    /* Split boxes */
    .split-table { width: 100%; border-collapse: collapse; }
    .split-table td { padding: 12px 14px; width: 50%; vertical-align: top; }
    .split-box { border: 2px solid #d1d5db; border-radius: 6px; padding: 12px; }
    .split-box.paid   { border-color: #6ee7b7; background: #f0fdf4; }
    .split-box.pending { border-color: #fcd34d; background: #fffbeb; }

    /* Payment history */
    .pay-table { width: 100%; border-collapse: collapse; font-size: 10px; }
    .pay-table th { padding: 6px 14px; font-size: 8px; text-transform: uppercase; letter-spacing: 1px; color: #6b7280; background: #f9fafb; border-bottom: 1px solid #e5e7eb; }
    .pay-table th.tr { text-align: right; }
    .pay-table td { padding: 7px 14px; border-bottom: 1px solid #f3f4f6; }
    .pay-table td.tr { text-align: right; font-family: monospace; }
    .pay-footer { background: #fef3c7; padding: 10px 14px; }
    .pay-footer.paid-bg { background: #f0fdf4; }
    .pay-footer table { width: 100%; font-size: 10px; }
    .pay-footer td.tr { text-align: right; }

    .footer-band { margin-top: 30px; text-align: center; font-size: 9px; color: #9ca3af; }
</style>
@endpush

@section('content')

{{-- HEADER --}}
<table style="width:100%; border-collapse:collapse;" class="header-band">
    <tr>
        <td style="padding:18px 24px;">
            <div style="font-size:18px; font-weight:bold; color:#fff;">🐔 FlockWise BizTrack</div>
            <div style="font-size:9px; color:#a7f3d0; text-transform:uppercase; letter-spacing:2px; margin-top:3px;">Poultry Management Solutions</div>
        </td>
        <td style="padding:18px 24px; text-align:right;">
            <div style="font-size:9px; color:#a7f3d0; text-transform:uppercase;">Invoice</div>
            <div style="font-size:17px; font-weight:bold; font-family:monospace; color:#fff;">
                {{ $bill->invoice_no ?? ('INV-W-' . str_pad($bill->id, 4, '0', STR_PAD_LEFT)) }}
            </div>
            <div style="margin-top:4px;">
                @if($bill->status === 'Paid')
                    <span class="badge badge-paid">✅ PAID</span>
                @else
                    <span class="badge badge-pending">⏳ {{ strtoupper($bill->status) }}</span>
                @endif
            </div>
        </td>
    </tr>
</table>

{{-- DEALER + PERIOD --}}
<table class="party-row">
    <tr>
        <td>
            <div class="section-label">Bill To</div>
            <div style="font-size:13px; font-weight:bold;">{{ $bill->dealer?->firm_name ?? 'N/A' }}</div>
            <div style="color:#4b5563; margin-top:3px;">{{ $bill->dealer?->location ?? '' }}</div>
            <div style="color:#4b5563;">📞 {{ $bill->dealer?->phone ?? 'N/A' }}</div>
            @if($bill->dealer?->gst_number)
                <div style="color:#9ca3af; font-size:9px; margin-top:3px;">GSTIN: {{ $bill->dealer->gst_number }}</div>
            @endif
        </td>
        <td style="text-align:right;">
            <div class="section-label">Billing Period</div>
            <div style="font-size:12px; font-weight:bold;">
                {{ $bill->period_start?->format('d M Y') }} — {{ $bill->period_end?->format('d M Y') }}
            </div>
            <div style="color:#6b7280; font-size:10px; margin-top:4px;">Generated: {{ now()->format('d M Y') }}</div>
            <div style="color:#6b7280; font-size:10px;">Payment Mode: {{ $bill->payment_mode ?? 'Credit' }}</div>
        </td>
    </tr>
</table>

{{-- SECTION 2: DAY-LOAD ENTRIES --}}
<div class="section-bar section-bar-green">📦 Day-Load Entries</div>
@if(isset($dayLoadEntries) && $dayLoadEntries->isNotEmpty())
<table class="data-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Day</th>
            <th>Vendor</th>
            <th class="text-right">Weight (kg)</th>
            <th class="text-right">Customer Rate</th>
            <th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($dayLoadEntries as $entry)
            @php
                $kg    = (float) $entry->bird_weight;
                $rate  = (float) $entry->customer_rate;
                $total = round($kg * $rate, 2);
            @endphp
            <tr>
                <td>{{ $entry->batch?->billing_date?->format('d M Y') ?? '—' }}</td>
                <td style="color:#6b7280;">{{ $entry->batch?->billing_date?->format('l') ?? '' }}</td>
                <td>{{ $entry->vendor?->firm_name ?? '—' }}</td>
                <td class="text-right" style="font-family:monospace;">{{ number_format($kg, 2) }} kg</td>
                <td class="text-right" style="font-family:monospace;">₹{{ number_format($rate, 2) }}</td>
                <td class="text-right" style="font-weight:bold; font-family:monospace; color:#065f46;">₹{{ number_format($total, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3" style="color:#065f46; font-size:9px; text-transform:uppercase; letter-spacing:1px;">Day-Load Total</td>
            <td class="text-right" style="font-family:monospace; color:#065f46;">{{ number_format($dayLoadEntries->sum('bird_weight'), 2) }} kg</td>
            <td></td>
            <td class="text-right" style="font-size:13px; font-family:monospace; color:#065f46;">₹{{ number_format($dayLoadTotal ?? 0, 2) }}</td>
        </tr>
    </tfoot>
</table>
@else
<table class="data-table">
    <thead>
        <tr>
            <th>Description</th>
            <th class="text-right">Qty (kg)</th>
            <th class="text-right">Rate</th>
            <th class="text-right">Total</th>
        </tr>
    </thead>
    <tbody>
        @forelse($bill->items as $item)
        <tr>
            <td style="font-weight:bold;">{{ $item->item_name }}</td>
            <td class="text-right" style="font-family:monospace;">{{ number_format($item->quantity_kg, 2) }}</td>
            <td class="text-right" style="font-family:monospace;">₹{{ number_format($item->rate_per_kg, 2) }}</td>
            <td class="text-right" style="font-weight:bold; font-family:monospace; color:#4338ca;">₹{{ number_format($item->quantity_kg * $item->rate_per_kg, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="4" style="text-align:center; color:#9ca3af; padding:12px;">No items found.</td></tr>
        @endforelse
    </tbody>
</table>
@endif

{{-- SECTION 3: FINANCIAL SUMMARY --}}
<div class="section-bar">💰 Financial Summary</div>
<div style="padding: 10px 24px;">
    <div class="summary-box">
        <div class="summary-row">
            <span style="color:#6b7280;">Previous Pending</span>
            <span style="font-family:monospace; color:#dc2626;">₹{{ number_format((float)($bill->previous_outstanding ?? 0), 2) }}</span>
        </div>
        <div class="summary-row">
            <span style="color:#6b7280;">+ This Week's Day-Load</span>
            <span style="font-family:monospace;">₹{{ number_format($dayLoadTotal ?? $bill->amount, 2) }}</span>
        </div>
        @if((float)($bill->payments_during_week ?? 0) > 0)
        <div class="summary-row">
            <span style="color:#6b7280;">- Payments This Week</span>
            <span style="font-family:monospace; color:#059669;">₹{{ number_format((float)($bill->payments_during_week ?? 0), 2) }}</span>
        </div>
        @endif
        @if(($bill->gst_amount ?? 0) > 0)
        <div class="summary-row">
            <span style="color:#6b7280;">+ GST ({{ $bill->gst_percentage ?? 18 }}%)</span>
            <span style="font-family:monospace;">₹{{ number_format((float)($bill->gst_amount ?? 0), 2) }}</span>
        </div>
        @endif
        <div class="summary-total">
            <span>Net Invoice Amount</span>
            <span style="font-family:monospace; color:#059669;">₹{{ number_format((float)($bill->net_amount ?? $bill->amount), 2) }}</span>
        </div>
    </div>
    <div style="clear:both;"></div>
</div>

{{-- SECTION 4: PAYMENT SCHEDULE --}}
<div class="section-bar section-bar-amber">🗓️ Payment Schedule (Monday/Friday Split)</div>
<table class="split-table">
    <tr>
        <td>
            <div class="split-box {{ ($bill->monday_payment_status ?? '') === 'Paid' ? 'paid' : 'pending' }}">
                <div style="font-size:9px; font-weight:bold; text-transform:uppercase; margin-bottom:5px;">
                    {{ ($bill->monday_payment_status ?? '') === 'Paid' ? '✅' : '⏳' }} Monday Split (50%)
                </div>
                <div style="font-size:14px; font-weight:bold; font-family:monospace;">
                    ₹{{ number_format((float)($bill->monday_payment_amount ?? 0), 2) }}
                </div>
                <div style="font-size:9px; margin-top:4px;">Status: <strong>{{ $bill->monday_payment_status ?? 'Unpaid' }}</strong></div>
            </div>
        </td>
        <td>
            <div class="split-box {{ ($bill->friday_payment_status ?? '') === 'Paid' ? 'paid' : 'pending' }}">
                <div style="font-size:9px; font-weight:bold; text-transform:uppercase; margin-bottom:5px;">
                    {{ ($bill->friday_payment_status ?? '') === 'Paid' ? '✅' : '⏳' }} Friday Split (50%)
                </div>
                <div style="font-size:14px; font-weight:bold; font-family:monospace;">
                    ₹{{ number_format((float)($bill->friday_payment_amount ?? 0), 2) }}
                </div>
                <div style="font-size:9px; margin-top:4px;">Status: <strong>{{ $bill->friday_payment_status ?? 'Unpaid' }}</strong></div>
            </div>
        </td>
    </tr>
</table>

{{-- SECTION 5: PAYMENT HISTORY --}}
<div class="section-bar section-bar-indigo">📜 Payment History</div>
@if(isset($allPayments) && $allPayments->isNotEmpty())
<table class="pay-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Description</th>
            <th>Mode</th>
            <th class="tr">Cash</th>
            <th class="tr">Bank</th>
            <th class="tr">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($allPayments as $payment)
            @php
                $note = $payment->notes ?? '';
                if (str_contains(strtolower($note), 'monday')) $displayDesc = 'Monday Split Payment';
                elseif (str_contains(strtolower($note), 'friday')) $displayDesc = 'Friday Split Payment';
                elseif (str_contains(strtolower($note), 'allocated')) $displayDesc = 'Day-Load Payment';
                else $displayDesc = $note ?: 'Ledger Payment';
            @endphp
            <tr>
                <td>
                    {{ $payment->date?->format('d M Y') }}
                    <div style="font-size:8px; color:#9ca3af;">{{ $payment->date?->format('l') }}</div>
                </td>
                <td style="color:#4b5563;">{{ $displayDesc }}</td>
                <td>
                    {{ $payment->payment_mode }}
                    @if($payment->bank_transfer_type) ({{ $payment->bank_transfer_type }}) @endif
                </td>
                <td class="tr" style="color:#059669;">
                    {{ (float)$payment->cash_amount > 0 ? '₹' . number_format((float)$payment->cash_amount, 2) : '—' }}
                </td>
                <td class="tr" style="color:#2563eb;">
                    {{ (float)$payment->bank_amount > 0 ? '₹' . number_format((float)$payment->bank_amount, 2) : '—' }}
                </td>
                <td class="tr" style="font-weight:bold;">₹{{ number_format((float)$payment->amount, 2) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
@else
<div style="padding:14px 24px; color:#9ca3af; font-size:10px; text-align:center;">
    No payments recorded yet.
</div>
@endif

{{-- Payment Summary Footer --}}
<div class="pay-footer {{ ($remainingDue ?? 0) <= 0 ? 'paid-bg' : '' }}">
    <table>
        <tr>
            <td style="color:#6b7280;">Total Invoice Amount</td>
            <td class="tr" style="font-weight:bold; font-family:monospace;">₹{{ number_format((float)($bill->net_amount ?? 0), 2) }}</td>
        </tr>
        <tr>
            <td style="color:#059669;">Total Paid</td>
            <td class="tr" style="font-weight:bold; font-family:monospace; color:#059669;">₹{{ number_format($totalPaid ?? 0, 2) }}</td>
        </tr>
        <tr>
            <td style="font-weight:bold; font-size:12px; padding-top:6px; border-top:1px solid #d1d5db; {{ ($remainingDue ?? 0) <= 0 ? 'color:#059669;' : 'color:#dc2626;' }}">
                {{ ($remainingDue ?? 0) <= 0 ? '✅ Fully Paid' : '⏳ Remaining Due' }}
            </td>
            <td class="tr" style="font-weight:bold; font-size:14px; font-family:monospace; padding-top:6px; border-top:1px solid #d1d5db; {{ ($remainingDue ?? 0) <= 0 ? 'color:#059669;' : 'color:#dc2626;' }}">
                ₹{{ number_format($remainingDue ?? 0, 2) }}
            </td>
        </tr>
    </table>
</div>

{{-- FOOTER --}}
<div class="footer-band">
    <div style="font-weight:bold; color:#374151; margin-bottom:4px;">Thank you for your continued partnership! 🙏</div>
    <div>Please ensure payment is cleared within the weekly credit cycle.</div>
    <div style="margin-top:8px;">NO SIGNATURE REQUIRED • COMPUTER GENERATED • AUTH VERIFIED</div>
</div>

@endsection
