<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Purchase Invoice #{{ $purchase->id }}</title>
    <style>
        body { font-family: 'Inter', sans-serif; color: #111; padding: 40px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px; }
        .logo { font-size: 24px; font-weight: 900; }
        .invoice-info { text-align: right; }
        .section-title { font-size: 10px; font-weight: bold; color: #666; text-transform: uppercase; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { text-align: left; padding: 12px; border-bottom: 1px solid #eee; font-size: 12px; color: #666; }
        td { padding: 15px 12px; border-bottom: 1px solid #f9f9f9; font-size: 14px; }
        .total-section { float: right; width: 300px; }
        .total-row { display: flex; justify-content: space-between; padding: 8px 0; }
        .grand-total { border-top: 2px solid #000; margin-top: 10px; padding-top: 10px; font-weight: 900; font-size: 18px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; cursor: pointer;">Print Now</button>
        <button onclick="window.history.back()" style="padding: 10px 20px; cursor: pointer;">Back</button>
    </div>

    <div class="header">
        <div>
            <div class="logo">POULTRYPRO</div>
            <p style="font-size: 14px; color: #555;">Farm Management & Solutions</p>
        </div>
        <div class="invoice-info">
            <h1 style="margin: 0; font-size: 28px;">INVOICE</h1>
            <p style="margin: 5px 0;">#PUR{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; margin-bottom: 40px;">
        <div>
            <div class="section-title">Supplier</div>
            <div style="font-weight: bold; font-size: 16px;">{{ $purchase->vendor_name }}</div>
        </div>
        <div>
            <div class="section-title">Date</div>
            <div style="font-weight: bold;">{{ $purchase->date->format('d M, Y') }}</div>
        </div>
        <div>
            <div class="section-title">Payment Mode</div>
            <div style="font-weight: bold;">{{ $purchase->payment_mode }}</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item Description</th>
                <th style="text-align: right;">Quantity</th>
                <th style="text-align: right;">Rate (₹)</th>
                <th style="text-align: right;">Total (₹)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: bold;">{{ $purchase->item }}</td>
                <td style="text-align: right;">{{ number_format($purchase->quantity, 2) }} {{ $purchase->unit }}</td>
                <td style="text-align: right;">{{ number_format($purchase->rate, 2) }}</td>
                <td style="text-align: right;">{{ number_format($purchase->quantity * $purchase->rate, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span>Subtotal</span>
            <span>₹{{ number_format($purchase->quantity * $purchase->rate, 2) }}</span>
        </div>
        <div class="total-row">
            <span>GST ({{ $purchase->gst_percentage }}%)</span>
            <span>₹{{ number_format($purchase->gst_amount, 2) }}</span>
        </div>
        <div class="total-row grand-total">
            <span>Total Amount</span>
            <span>₹{{ number_format($purchase->total_amount, 2) }}</span>
        </div>
    </div>

    <div style="margin-top: 100px; font-size: 10px; color: #999; text-align: center; border-top: 1px solid #eee; padding-top: 20px;">
        This is a computer generated invoice and does not require a physical signature.
    </div>
</body>
</html>
