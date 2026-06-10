@extends('layouts.pdf')
@section('title', 'TAX INVOICE (WEEKLY)')
@section('meta', "Invoice No: {$bill->invoice_no}")

@push('styles')
<style>
    .invoice-details { width: 100%; margin-bottom: 30px; }
    .invoice-details td { vertical-align: top; width: 50%; }
    .section-label { font-size: 10px; font-weight: bold; color: #4f46e5; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 10px; }
    .total-box { float: right; width: 200px; background-color: #1e1b4b; color: #fff; padding: 20px; border-radius: 8px; margin-top: 20px; }
    .total-row { margin-bottom: 8px; font-size: 10px; text-transform: uppercase; opacity: 0.8; }
    .total-row span { float: right; }
    .grand-total { margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.2); font-size: 16px; font-weight: bold; color: #818cf8; }
    .grand-total span { float: right; }
</style>
@endpush

@section('content')

<table class="invoice-details">
    <tr>
        <td>
            <div class="section-label">Bill To Customer</div>
            <div style="font-size: 14px; font-weight: bold; margin-bottom: 5px; color: #111827;">{{ $bill->customer->name ?? 'N/A' }}</div>
            <div style="color: #4b5563; line-height: 1.4;">
                {{ $bill->customer->address ?? 'No address provided' }}<br>
                <strong>Phone: {{ $bill->customer->phone ?? 'N/A' }}</strong>
                @if($bill->customer->gst_number)
                    <br><span style="font-size: 10px; color: #9ca3af;">GSTIN: {{ $bill->customer->gst_number }}</span>
                @endif
            </div>
        </td>
        <td style="padding-left: 40px;">
            <div class="section-label">Billing Period</div>
            <div style="font-size: 12px; font-weight: bold; color: #111827;">
                {{ $bill->period_start->format('d M') }} - {{ $bill->period_end->format('d M, Y') }}
            </div>
            <table style="font-size: 10px; width: 100%; margin-top: 10px;">
                <tr>
                    <td style="color: #6b7280; padding: 4px 0; border: none;">Status:</td>
                    <td class="text-right font-bold" style="border: none;">{{ strtoupper($bill->status) }}</td>
                </tr>
                <tr>
                    <td style="color: #6b7280; padding: 4px 0; border: none;">Billing Type:</td>
                    <td class="text-right font-bold" style="border: none;">Wholesale Weekly</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th>Service / Item Description</th>
            <th class="text-center">Quantity</th>
            <th class="text-right">Unit Price (Avg)</th>
            <th class="text-right">Taxable Amt</th>
        </tr>
    </thead>
    <tbody>
        @forelse($bill->items as $item)
        <tr>
            <td>
                <div class="font-bold text-zinc-900">{{ $item->item_name }}</div>
                <div style="font-size: 8px; color: #9ca3af; margin-top: 3px;">WEEKLY SUPPLY ITEM</div>
            </td>
            <td class="text-center">{{ number_format($item->quantity_kg, 2) }} KG</td>
            <td class="text-right">Rs {{ number_format($item->rate_per_kg, 2) }}</td>
            <td class="text-right font-bold text-indigo-600">Rs {{ number_format($item->quantity_kg * $item->rate_per_kg, 2) }}</td>
        </tr>
        @empty
        <tr>
            <td>
                <div class="font-bold text-zinc-900">{{ $bill->items_description ?? 'Poultry Sales' }}</div>
                <div style="font-size: 8px; color: #9ca3af; margin-top: 3px;">CONSOLIDATED WEEKLY PROCUREMENT</div>
            </td>
            <td class="text-center">{{ number_format($bill->quantity_kg, 2) }} KG</td>
            <td class="text-right">Rs {{ number_format($bill->amount / max(1, $bill->quantity_kg), 2) }}</td>
            <td class="text-right font-bold text-indigo-600">Rs {{ number_format($bill->amount, 2) }}</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="total-box">
    <div class="total-row">
        Subtotal
        <span>Rs {{ number_format($bill->amount, 2) }}</span>
    </div>
    <div class="total-row" style="margin-bottom: 15px;">
        GST ({{ $bill->gst_percentage }}%)
        <span>Rs {{ number_format($bill->gst_amount, 2) }}</span>
    </div>
    <div class="grand-total">
        Total Net
        <span>Rs {{ number_format($bill->net_amount, 2) }}</span>
    </div>
</div>

<div style="clear: both; margin-top: 80px; text-align: center;">
    <div style="font-size: 9px; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 10px;">
        NO SIGNATURE REQUIRED &bull; COMPUTER GENERATED &bull; AUTH VERIFIED
    </div>
    <p style="font-weight: bold; color: #374151; margin: 5px 0;">Thank you for your continued partnership!</p>
    <p style="font-size: 10px; color: #6b7280; margin: 0;">Please ensure payment is cleared within the weekly credit cycle.</p>
</div>

@endsection
