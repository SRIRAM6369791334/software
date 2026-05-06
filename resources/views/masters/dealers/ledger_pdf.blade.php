<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Supplier Ledger - {{ $dealer->firm_name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }
        .header-accent { height: 8px; background: #4f46e5; }
        .container { padding: 30px; }
        .company-header { margin-bottom: 30px; }
        .company-name { font-size: 20px; font-weight: bold; color: #4f46e5; margin: 0; }
        .report-title { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #111; margin-top: 5px; }
        
        .dealer-info { margin-bottom: 30px; width: 100%; }
        .info-box { padding: 15px; background: #f9fafb; border-radius: 10px; border: 1px solid #f3f4f6; }
        .label { font-size: 8px; font-weight: bold; color: #6b7280; text-transform: uppercase; margin-bottom: 3px; }
        .value { font-size: 12px; font-weight: bold; color: #111; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #111; color: #fff; padding: 10px; text-align: left; font-size: 8px; text-transform: uppercase; }
        td { padding: 10px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .balance-row { background-color: #f9fafb; }
        .debit { color: #dc2626; } /* red-600 (Purchases increase liability) */
        .credit { color: #4f46e5; } /* indigo-600 (Payments reduce liability) */
        
        .footer { position: fixed; bottom: 30px; left: 30px; right: 30px; border-top: 1px solid #f3f4f6; padding-top: 15px; text-align: center; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <div class="header-accent"></div>
    <div class="container">
        <div class="company-header">
            <h1 class="company-name">Flockwise BizTrack</h1>
            <div class="report-title">Supplier Ledger Statement</div>
            <div style="font-size: 8px; color: #999; margin-top: 5px;">
                Statement Period: Up to {{ now()->format('d M Y') }}
            </div>
        </div>

        <table class="dealer-info">
            <tr>
                <td style="width: 60%; padding: 0;">
                    <div class="info-box">
                        <div class="label">Supplier Details</div>
                        <div class="value">{{ $dealer->firm_name }}</div>
                        <div style="margin-top: 5px; color: #666;">
                            {{ $dealer->contact_person }}<br>
                            Phone: {{ $dealer->phone }}
                            @if($dealer->gst_number)
                                <br>GSTIN: {{ $dealer->gst_number }}
                            @endif
                        </div>
                    </div>
                </td>
                <td style="width: 40%; padding: 0 0 0 15px;">
                    <div class="info-box" style="border-left: 4px solid #4f46e5;">
                        <div class="label">Pending Payable</div>
                        <div class="value" style="font-size: 20px; color: #4f46e5;">₹{{ number_format($dealer->pending_amount, 2) }}</div>
                        <div style="font-size: 8px; color: #999; margin-top: 5px;">ACCOUNT PAYABLE LIABILITY</div>
                    </div>
                </td>
            </tr>
        </table>

        <table>
            <thead>
                <tr>
                    <th style="width: 15%; border-radius: 8px 0 0 0;">Date</th>
                    <th style="width: 45%;">Transaction Description</th>
                    <th style="width: 12%;" class="text-right">Debit (+)</th>
                    <th style="width: 12%;" class="text-right">Credit (-)</th>
                    <th style="width: 16%; border-radius: 0 8px 0 0;" class="text-right">Liability Balance</th>
                </tr>
            </thead>
            <tbody>
                @php $runningLiability = 0; @endphp
                @foreach($ledger as $row)
                    @php 
                        $runningLiability += $row['debit'];
                        $runningLiability -= $row['credit'];
                    @endphp
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($row['date'])->format('d M Y') }}</td>
                        <td>{{ $row['desc'] }}</td>
                        <td class="text-right debit">
                            {{ $row['debit'] > 0 ? '₹' . number_format($row['debit'], 2) : '—' }}
                        </td>
                        <td class="text-right credit">
                            {{ $row['credit'] > 0 ? '₹' . number_format($row['credit'], 2) : '—' }}
                        </td>
                        <td class="text-right font-bold">₹{{ number_format($runningLiability, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="balance-row">
                    <td colspan="4" class="text-right font-bold" style="padding: 15px;">Final Outstanding Liability</td>
                    <td class="text-right font-bold" style="padding: 15px; font-size: 12px; color: #4f46e5;">
                        ₹{{ number_format($runningLiability, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            FLOCKWISE BIZTRACK ERP &bull; COMPUTER GENERATED STATEMENT &bull; INTERNAL AUDIT DOCUMENT
        </div>
    </div>
</body>
</html>
