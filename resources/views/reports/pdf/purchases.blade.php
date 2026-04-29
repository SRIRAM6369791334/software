<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; color: #1a56db; }
        .summary { margin-bottom: 20px; border: 1px solid #ddd; padding: 10px; border-radius: 5px; background: #f9f9f9; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; color: #777; border-top: 1px solid #ddd; padding-top: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>PoultryPro Management System</h2>
        <h3>{{ $title }}</h3>
        <p>Generated on: {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <div class="summary">
        <strong>Total Records:</strong> {{ $data->count() }}<br>
        <strong>Total Purchase:</strong> ₹{{ number_format($data->sum('total_amount'), 2) }}<br>
        <strong>Total GST Paid:</strong> ₹{{ number_format($data->sum('gst_amount'), 2) }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Vendor</th>
                <th>Item</th>
                <th>Date</th>
                <th class="text-right">Rate</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $row)
            <tr>
                <td>{{ $row->vendor->name ?? $row->vendor_name }}</td>
                <td>{{ $row->item }}</td>
                <td>{{ $row->date->format('d M Y') }}</td>
                <td class="text-right">₹{{ number_format($row->rate, 2) }}</td>
                <td class="text-right">{{ $row->quantity }}</td>
                <td class="text-right font-bold">₹{{ number_format($row->total_amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        &copy; {{ date('Y') }} PoultryPro. All Rights Reserved.
    </div>
</body>
</html>
