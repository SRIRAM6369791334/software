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
            background-color: #059669; /* emerald-600 */
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
            color: #059669;
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
            color: #059669;
            text-transform: uppercase;
            border-bottom: 1px solid #ecfdf5;
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
            color: #34d399; /* emerald-400 */
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
                    <div style="background-color: #ecfdf5; padding: 10px 20px; display: inline-block; border-radius: 5px;">
                        <h2 style="color: #065f46;">TAX INVOICE</h2>
                    </div>
                    <p style="margin-top: 15px;">
                        <span style="font-size: 10px; color: #999; text-transform: uppercase;">Invoice No:</span><br>
                        <span style="font-size: 14px; font-weight: bold;">{{ $bill->invoice_no }}</span>
                    </p>
                    <p>
                        <span style="font-size: 10px; color: #999; text-transform: uppercase;">Date:</span><br>
                        <span style="font-weight: bold;">{{ $bill->date->format('d M, Y') }}</span>
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
                    <div class="section-label">Payment Summary</div>
                    <table style="font-size: 11px;">
                        <tr>
                            <td style="color: #666; padding: 3px 0;">Status:</td>
                            <td class="text-right font-bold">{{ strtoupper($bill->status) }}</td>
                        </tr>
                        <tr>
                            <td style="color: #666; padding: 3px 0;">Billing Type:</td>
                            <td class="text-right font-bold">Daily Retail Sale</td>
                        </tr>
                        <tr>
                            <td style="color: #666; padding: 3px 0;">Currency:</td>
                            <td class="text-right font-bold italic">INR</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th class="text-center">Quantity</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right">Taxable Amt</th>
                </tr>
            </thead>
            <tbody>
                @foreach($bill->items as $item)
                <tr>
                    <td>
                        <div style="font-weight: bold;">{{ $item->item_name }}</div>
                        <div style="font-size: 9px; color: #999; margin-top: 3px;">POULTRY PRODUCT</div>
                    </td>
                    <td class="text-center">{{ number_format($item->quantity_kg, 2) }} {{ $item->unit }}</td>
                    <td class="text-right">₹{{ number_format($item->rate_per_kg, 2) }}</td>
                    <td class="text-right font-bold">₹{{ number_format($item->quantity_kg * $item->rate_per_kg, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="total-box">
            <div class="total-row">
                Subtotal
                <span>₹{{ number_format($bill->amount, 2) }}</span>
            </div>
            <div class="total-row" style="margin-bottom: 20px;">
                GST ({{ $bill->gst_percentage }}%)
                <span>₹{{ number_format($bill->gst_amount, 2) }}</span>
            </div>
            <div class="grand-total">
                Total Net
                <span>₹{{ number_format($bill->net_amount, 2) }}</span>
            </div>
        </div>

        <div style="clear: both;"></div>

        <div class="footer">
            <div class="auth">
                NO SIGNATURE REQUIRED &bull; COMPUTER GENERATED &bull; AUTH VERIFIED
            </div>
            <p style="font-weight: bold; color: #333;">Thank you for choosing Flockwise BizTrack!</p>
            <p style="font-size: 10px;">Please settle the payment according to the agreed credit terms.</p>
        </div>
    </div>
</body>
</html>
