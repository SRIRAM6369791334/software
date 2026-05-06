<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Profit & Loss Statement</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; color: #333; margin: 0; padding: 0; }
        .header-accent { height: 8px; background: #059669; }
        .container { padding: 30px; }
        .company-header { margin-bottom: 30px; }
        .company-name { font-size: 20px; font-weight: bold; color: #059669; margin: 0; }
        .report-title { font-size: 14px; font-weight: bold; text-transform: uppercase; color: #111; margin-top: 5px; }
        
        .summary-grid { width: 100%; margin-bottom: 30px; border-collapse: separate; border-spacing: 10px 0; margin-left: -10px; }
        .summary-card { background: #f9fafb; padding: 15px; border-radius: 10px; border: 1px solid #f3f4f6; }
        .label { font-size: 8px; font-weight: bold; color: #6b7280; text-transform: uppercase; margin-bottom: 5px; }
        .value { font-size: 14px; font-weight: bold; color: #111; }
        
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #111; color: #fff; padding: 10px; text-align: left; font-size: 8px; text-transform: uppercase; }
        td { padding: 12px 10px; border-bottom: 1px solid #f3f4f6; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        
        .revenue { color: #059669; }
        .expense { color: #dc2626; }
        .profit { background-color: #ecfdf5; border-radius: 8px; padding: 5px 10px; display: inline-block; }
        
        .footer { position: fixed; bottom: 30px; left: 30px; right: 30px; border-top: 1px solid #f3f4f6; padding-top: 15px; text-align: center; font-size: 8px; color: #999; }
    </style>
</head>
<body>
    <div class="header-accent"></div>
    <div class="container">
        <div class="company-header">
            <h1 class="company-name">Flockwise BizTrack</h1>
            <div class="report-title">Profit & Loss Performance Statement</div>
            <div style="font-size: 8px; color: #999; margin-top: 5px;">
                Period: {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} — {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
            </div>
        </div>

        <table class="summary-grid">
            <tr>
                <td style="width: 25%;">
                    <div class="summary-card">
                        <div class="label">Total Revenue</div>
                        <div class="value revenue">₹{{ number_format($summary['total_revenue'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td style="width: 25%;">
                    <div class="summary-card">
                        <div class="label">Procurement Cost</div>
                        <div class="value expense">₹{{ number_format($summary['total_purchases'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td style="width: 25%;">
                    <div class="summary-card">
                        <div class="label">Operating Expenses</div>
                        <div class="value expense">₹{{ number_format($summary['total_expenses'] ?? 0, 2) }}</div>
                    </div>
                </td>
                <td style="width: 25%;">
                    <div class="summary-card" style="border-left: 4px solid #059669;">
                        <div class="label">Net Profit</div>
                        <div class="value" style="color: #059669; font-size: 18px;">₹{{ number_format($summary['net_profit'] ?? 0, 2) }}</div>
                    </div>
                </td>
            </tr>
        </table>

        <h3 style="text-transform: uppercase; font-size: 10px; color: #111; border-bottom: 1px solid #111; padding-bottom: 5px;">Weekly Performance Breakdown</h3>
        <table>
            <thead>
                <tr>
                    <th style="border-radius: 8px 0 0 0;">Week Period</th>
                    <th class="text-right">Revenue</th>
                    <th class="text-right">Procurement</th>
                    <th class="text-right">Expenses</th>
                    <th class="text-right" style="border-radius: 0 8px 0 0;">Weekly Profit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($weeklyData as $week)
                <tr>
                    <td class="font-bold">{{ $week['week'] }}</td>
                    <td class="text-right revenue">₹{{ number_format($week['revenue'], 2) }}</td>
                    <td class="text-right expense">₹{{ number_format($week['purchase'], 2) }}</td>
                    <td class="text-right expense">₹{{ number_format($week['expenses'], 2) }}</td>
                    <td class="text-right font-bold">
                        <span class="{{ $week['profit'] >= 0 ? 'revenue' : 'expense' }}">
                            ₹{{ number_format($week['profit'], 2) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div style="margin-top: 50px;">
            <h3 style="text-transform: uppercase; font-size: 10px; color: #111; border-bottom: 1px solid #111; padding-bottom: 5px;">Category Wise Distribution</h3>
            <table style="width: 50%;">
                <tr>
                    <td class="label">Total Sales Volume</td>
                    <td class="text-right font-bold">{{ number_format($breakdown['sales_qty'] ?? 0, 2) }} KG</td>
                </tr>
                <tr>
                    <td class="label">Avg Rate Realized</td>
                    <td class="text-right font-bold">₹{{ number_format($breakdown['avg_rate'] ?? 0, 2) }} / KG</td>
                </tr>
                <tr>
                    <td class="label">Mortality Loss Valuation</td>
                    <td class="text-right expense font-bold">₹{{ number_format($breakdown['mortality_valuation'] ?? 0, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            FLOCKWISE BIZTRACK ERP &bull; EXECUTIVE FINANCIAL SUMMARY &bull; CONFIDENTIAL
        </div>
    </div>
</body>
</html>
