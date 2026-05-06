<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Ledger Statement - {{ $customer->name }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }
        .header-accent { height: 8px; background: #059669; }
        .container { padding: 30px; }
        .company-header { margin-bottom: 30px; }
        .company-name { font-size: 20px; font-weight: bold; color: #059669; margin: 0; }
        .report-title { font-size: 12px; font-weight: bold; text-transform: uppercase; color: #111; margin-top: 5px; }
        
        .customer-info { margin-bottom: 30px; width: 100%; }
        .info-box { padding: 15px; background: #f9fafb; border-radius: 10px; border: 1px solid #f3f4f6; }
        .label { font-size: 8px; font-weight: bold; color: #6b7280; text-transform: uppercase; margin-bottom: 3px; }
        .value { font-size: 12px; font-weight: bold; color: #111; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #111; color: #fff; padding: 10px; text-align: left; font-size: 8px; text-transform: uppercase; }
        td { padding: 10px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .balance-row { background-color: #f9fafb; }
        .debit { color: #dc2626; } /* red-600 */
        .credit { color: #059669; } /* emerald-600 */
        
        .footer { position: fixed; bottom: 30px; left: 30px; right: 30px; border-top: 1px solid #f3f4f6; padding-top: 15px; text-align: center; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <div class="header-accent"></div>
    <div class="container">
        <div class="company-header">
            <h1 class="company-name">Flockwise BizTrack</h1>
            <div class="report-title">Customer Ledger Statement</div>
            <div style="font-size: 8px; color: #999; margin-top: 5px;">
                Statement Period: Up to {{ now()->format('d M Y') }}
            </div>
        </div>

        <table class="customer-info">
            <tr>
                <td style="width: 60%; padding: 0;">
                    <div class="info-box">
                        <div class="label">Customer Details</div>
                        <div class="value">{{ $customer->name }}</div>
                        <div style="margin-top: 5px; color: #666;">
                            {{ $customer->address }}<br>
                            Phone: {{ $customer->phone }}
                            @if($customer->gst_number)
                                <br>GSTIN: {{ $customer->gst_number }}
                            @endif
                        </div>
                    </div>
                </td>
                <td style="width: 40%; padding: 0 0 0 15px;">
                    <div class="info-box" style="border-left: 4px solid #059669;">
                        <div class="label">Current Balance</div>
                        <div class="value" style="font-size: 20px; color: #059669;">₹{{ number_format($customer->balance, 2) }}</div>
                        <div style="font-size: 8px; color: #999; margin-top: 5px;">OUTSTANDING RECEIVABLE</div>
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
                    <th style="width: 16%; border-radius: 0 8px 0 0;" class="text-right">Running Balance</th>
                </tr>
            </thead>
            <tbody>
                @php $runningBalance = 0; @endphp
                @foreach($ledger as $row)
                    @php 
                        $runningBalance += $row['debit'];
                        $runningBalance -= $row['credit'];
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
                        <td class="text-right font-bold">₹{{ number_format($runningBalance, 2) }}</td>
                    </tr>
                @endforeach
                <tr class="balance-row">
                    <td colspan="4" class="text-right font-bold" style="padding: 15px;">Final Outstanding Balance</td>
                    <td class="text-right font-bold" style="padding: 15px; font-size: 12px; color: #059669;">
                        ₹{{ number_format($runningBalance, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="footer">
            FLOCKWISE BIZTRACK ERP &bull; COMPUTER GENERATED STATEMENT &bull; NO SIGNATURE REQUIRED
        </div>
    </div>
</body>
</html>
