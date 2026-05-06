<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice #{{ $bill->invoice_no }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .header-bar {
            height: 10px;
            background-color: #4f46e5; /* indigo-600 (Weekly Theme) */
        }
        .container {
            padding: 40px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .company-header h1 {
            font-size: 28px;
            margin: 0;
            color: #4f46e5;
            letter-spacing: -1px;
        }
        .company-header p {
            margin: 5px 0;
            color: #999;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 10px;
        }
        .invoice-title {
            text-align: right;
        }
        .invoice-title h2 {
            font-size: 20px;
            margin: 0;
            text-transform: uppercase;
            color: #111;
        }
        .invoice-title p {
            margin: 5px 0;
            color: #666;
        }
        .details-table {
            margin-top: 40px;
            margin-bottom: 40px;
        }
        .details-table td {
            vertical-align: top;
            width: 50%;
        }
        .section-label {
            font-size: 10px;
            font-weight: bold;
            color: #4f46e5;
            text-transform: uppercase;
            border-bottom: 1px solid #eef2ff;
            display: inline-block;
            margin-bottom: 10px;
        }
        .items-table {
            margin-top: 20px;
        }
        .items-table th {
            background-color: #111;
            color: #fff;
            padding: 12px 15px;
            text-align: left;
            font-size: 10px;
            text-transform: uppercase;
        }
        .items-table td {
            padding: 15px;
            border-bottom: 1px solid #f3f4f6;
        }
        .total-box {
            float: right;
            width: 250px;
            background-color: #111;
            color: #fff;
            padding: 25px;
            border-radius: 15px;
            margin-top: 40px;
        }
        .total-row {
            margin-bottom: 10px;
            font-size: 10px;
            text-transform: uppercase;
            opacity: 0.7;
        }
        .total-row span {
            float: right;
        }
        .grand-total {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255,255,255,0.1);
            font-size: 18px;
            font-weight: bold;
            color: #818cf8; /* indigo-400 */
        }
        .grand-total span {
            float: right;
        }
        .footer {
            margin-top: 100px;
            text-align: center;
            border-top: 1px solid #f3f4f6;
            padding-top: 30px;
        }
        .footer p {
            margin: 5px 0;
            color: #999;
        }
        .footer .auth {
            font-size: 10px;
            font-weight: bold;
            color: #ccc;
            text-transform: uppercase;
            margin-bottom: 15px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>
    <div class="header-bar"></div>
    <div class="container">
        <table>
            <tr>
                <td class="company-header">
                    <h1>Flockwise BizTrack</h1>
                    <p>Poultry Management Solutions</p>
                    <div style="margin-top: 15px; color: #666;">
                        <strong>Poultry Farm Unit #1</strong><br>
                        Tamil Nadu, India<br>
                        Phone: +91 98765 43210
                    </div>
                </td>
                <td class="invoice-title">
                    <div style="background-color: #eef2ff; padding: 10px 20px; display: inline-block; border-radius: 5px;">
                        <h2 style="color: #3730a3;">TAX INVOICE (WEEKLY)</h2>
                    </div>
                    <p style="margin-top: 15px;">
                        <span style="font-size: 10px; color: #999; text-transform: uppercase;">Invoice No:</span><br>
                        <span style="font-size: 14px; font-weight: bold;">{{ $bill->invoice_no }}</span>
                    </p>
                    <p>
                        <span style="font-size: 10px; color: #999; text-transform: uppercase;">Date Issued:</span><br>
                        <span style="font-weight: bold;">{{ $bill->created_at->format('d M, Y') }}</span>
                    </p>
                </td>
            </tr>
        </table>

        <table class="details-table">
            <tr>
                <td>
                    <div class="section-label">Bill To Customer</div>
                    <div style="font-size: 16px; font-weight: bold; margin-bottom: 5px;">{{ $bill->customer->name ?? 'N/A' }}</div>
                    <div style="color: #666; line-height: 1.4;">
                        {{ $bill->customer->address ?? 'No address provided' }}<br>
                        <strong>Phone: {{ $bill->customer->phone ?? 'N/A' }}</strong>
                        @if($bill->customer->gst_number)
                            <br><span style="font-size: 10px; color: #999;">GSTIN: {{ $bill->customer->gst_number }}</span>
                        @endif
                    </div>
                </td>
                <td style="padding-left: 50px;">
                    <div class="section-label">Billing Period</div>
                    <div style="font-size: 14px; font-weight: bold;">
                        {{ $bill->period_start->format('d M') }} — {{ $bill->period_end->format('d M, Y') }}
                    </div>
                    <table style="font-size: 11px; margin-top: 10px;">
                        <tr>
                            <td style="color: #666; padding: 3px 0;">Status:</td>
                            <td class="text-right font-bold">{{ strtoupper($bill->status) }}</td>
                        </tr>
                        <tr>
                            <td style="color: #666; padding: 3px 0;">Billing Type:</td>
                            <td class="text-right font-bold">Wholesale Weekly</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Service / Item Description</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-right">Unit Price (Avg)</th>
                    <th class="text-right">Taxable Amt</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div style="font-weight: bold;">{{ $bill->items_description ?? 'Poultry Sales' }}</div>
                        <div style="font-size: 9px; color: #999; margin-top: 3px;">CONSOLIDATED WEEKLY PROCUREMENT</div>
                    </td>
                    <td class="text-center">{{ number_format($bill->quantity_kg, 2) }} KG</td>
                    <td class="text-right">₹{{ number_format($bill->amount / max(1, $bill->quantity_kg), 2) }}</td>
                    <td class="text-right font-bold">₹{{ number_format($bill->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total-box" style="background-color: #1e1b4b;">
            <div class="total-row">
                Subtotal
                <span>₹{{ number_format($bill->amount, 2) }}</span>
            </div>
            <div class="total-row" style="margin-bottom: 20px;">
                GST ({{ $bill->gst_percentage }}%)
                <span>₹{{ number_format($bill->gst_amount, 2) }}</span>
            </div>
            <div class="grand-total" style="color: #c7d2fe;">
                Total Net
                <span>₹{{ number_format($bill->net_amount, 2) }}</span>
            </div>
        </div>

        <div style="clear: both;"></div>

        <div class="footer">
            <div class="auth">
                NO SIGNATURE REQUIRED &bull; COMPUTER GENERATED &bull; AUTH VERIFIED
            </div>
            <p style="font-weight: bold; color: #333;">Thank you for your continued partnership!</p>
            <p style="font-size: 10px;">Please ensure payment is cleared within the weekly credit cycle.</p>
        </div>
    </div>
</body>
</html>
