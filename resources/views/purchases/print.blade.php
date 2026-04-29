<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice_#PUR{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #F97316;
            --slate-900: #0F172A;
            --slate-600: #475569;
            --slate-400: #94A3B8;
            --slate-100: #F1F5F9;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Inter', sans-serif; 
            color: var(--slate-900); 
            line-height: 1.5;
            background: white;
            -webkit-print-color-adjust: exact;
        }
        .print-container { max-width: 800px; margin: 0 auto; padding: 40px; }
        
        /* Header */
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 60px; }
        .brand-logo { font-size: 24px; font-weight: 900; letter-spacing: -0.05em; color: var(--primary); }
        .brand-sub { font-size: 10px; font-weight: 800; color: var(--slate-400); text-transform: uppercase; letter-spacing: 0.2em; }
        .invoice-badge { text-align: right; }
        .invoice-badge h1 { font-size: 48px; font-weight: 900; color: var(--slate-100); margin-bottom: -15px; }
        .invoice-badge p { font-size: 14px; font-weight: 700; color: var(--slate-600); position: relative; z-index: 1; }

        /* Meta Grid */
        .meta-grid { display: grid; grid-cols-3; display: flex; justify-content: space-between; margin-bottom: 60px; padding-bottom: 40px; border-bottom: 2px solid var(--slate-100); }
        .meta-item label { display: block; font-size: 9px; font-weight: 800; color: var(--slate-400); text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 8px; }
        .meta-item span { display: block; font-size: 14px; font-weight: 700; color: var(--slate-900); }

        /* Table */
        table { width: 100%; border-collapse: collapse; margin-bottom: 40px; }
        th { text-align: left; padding: 16px; font-size: 10px; font-weight: 800; text-transform: uppercase; letter-spacing: 0.1em; color: var(--slate-400); border-bottom: 2px solid var(--slate-100); }
        td { padding: 20px 16px; font-size: 14px; border-bottom: 1px solid var(--slate-100); }
        .td-main { font-weight: 700; color: var(--slate-900); }
        .td-sub { font-size: 12px; color: var(--slate-500); }

        /* Calculation */
        .calc-wrapper { display: flex; justify-content: flex-end; }
        .calc-table { width: 300px; }
        .calc-row { display: flex; justify-content: space-between; padding: 12px 0; font-size: 14px; }
        .calc-row.total { 
            margin-top: 15px; 
            padding-top: 20px; 
            border-top: 2px solid var(--slate-900);
            font-size: 20px;
            font-weight: 900;
        }
        .calc-row label { color: var(--slate-500); font-weight: 600; }
        .calc-row span { color: var(--slate-900); font-weight: 800; }

        /* Footer */
        .footer { margin-top: 100px; padding-top: 40px; border-top: 1px solid var(--slate-100); text-align: center; }
        .footer p { font-size: 10px; font-weight: 600; color: var(--slate-400); letter-spacing: 0.05em; }
        
        .no-print-bar { 
            background: var(--slate-900); 
            padding: 15px; 
            display: flex; 
            justify-content: center; 
            gap: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .btn { 
            padding: 10px 25px; 
            font-size: 12px; 
            font-weight: 800; 
            text-transform: uppercase; 
            border: none; 
            border-radius: 30px; 
            cursor: pointer;
            transition: 0.2s;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-secondary { background: #334155; color: white; }

        @media print {
            .no-print-bar { display: none; }
            body { background: white; }
            .print-container { padding: 0; max-width: 100%; }
        }
    </style>
</head>
<body>
    <div class="no-print-bar">
        <button class="btn btn-primary" onclick="window.print()">Print Document 🖨️</button>
        <button class="btn btn-secondary" onclick="window.history.back()">Go Back</button>
    </div>

    <div class="print-container">
        <div class="header">
            <div>
                <div class="brand-logo">POULTRYPRO</div>
                <div class="brand-sub">Industrial Farm Management</div>
            </div>
            <div class="invoice-badge">
                <h1>INVOICE</h1>
                <p>#PUR{{ str_pad($purchase->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>

        <div class="meta-grid">
            <div class="meta-item">
                <label>Supplier / Vendor</label>
                <span>{{ $purchase->vendor_name }}</span>
            </div>
            <div class="meta-item">
                <label>Billing Date</label>
                <span>{{ $purchase->date->format('d F, Y') }}</span>
            </div>
            <div class="meta-item">
                <label>Settlement Mode</label>
                <span>{{ $purchase->payment_mode }}</span>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Qty / Unit</th>
                    <th style="text-align: right;">Unit Rate</th>
                    <th style="text-align: right;">Taxable Value</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="td-main">{{ $purchase->item }}</div>
                        <div class="td-sub">Farm Procurement Entry</div>
                    </td>
                    <td style="text-align: right;">
                        <div class="td-main">{{ number_format($purchase->quantity, 2) }}</div>
                        <div class="td-sub">{{ $purchase->unit }}</div>
                    </td>
                    <td style="text-align: right;">
                        <div class="td-main">₹{{ number_format($purchase->rate, 2) }}</div>
                    </td>
                    <td style="text-align: right;">
                        <div class="td-main">₹{{ number_format($purchase->quantity * $purchase->rate, 2) }}</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="calc-wrapper">
            <div class="calc-table">
                <div class="calc-row">
                    <label>Subtotal</label>
                    <span>₹{{ number_format($purchase->quantity * $purchase->rate, 2) }}</span>
                </div>
                <div class="calc-row">
                    <label>Tax Basis ({{ $purchase->gst_percentage }}%)</label>
                    <span>₹{{ number_format($purchase->gst_amount, 2) }}</span>
                </div>
                <div class="calc-row total">
                    <label>Grand Total</label>
                    <span>₹{{ number_format($purchase->total_amount, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>VALIDATED DIGITAL RECORD • POULTRYPRO ENTERPRISE SOLUTIONS</p>
            <p style="margin-top: 8px; font-weight: 400; opacity: 0.6;">This is a computer-generated document. No signature required.</p>
        </div>
    </div>
</body>
</html>
