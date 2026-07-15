<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Day-Load Invoice - <?php echo e($dateObj->format('d M Y')); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, sans-serif;
            color: #0f172a;
            background: #fff;
            line-height: 1.6;
            padding: 48px;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .container { max-width: 900px; margin: 0 auto; }

        .toolbar { display: flex; gap: 10px; margin-bottom: 32px; align-items: center; }
        .toolbar a, .toolbar button {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600;
            text-decoration: none; cursor: pointer; border: none; transition: all .2s;
        }
        .toolbar .btn-print { background: #059669; color: #fff; }
        .toolbar .btn-print:hover { background: #047857; }
        .toolbar .btn-pdf { background: #2563eb; color: #fff; }
        .toolbar .btn-pdf:hover { background: #1d4ed8; }

        .invoice-wrap {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 4px 24px rgba(0,0,0,.04);
        }

        .header {
            display: flex; justify-content: space-between; align-items: flex-start;
            border-bottom: 3px solid #0f172a; padding-bottom: 24px; margin-bottom: 28px;
        }
        .brand h1 { font-size: 26px; font-weight: 900; letter-spacing: -.03em; text-transform: uppercase; color: #0f172a; }
        .brand h1 span { color: #059669; }
        .brand .tag { font-size: 10px; font-weight: 600; color: #64748b; text-transform: uppercase; letter-spacing: .12em; margin-top: 2px; }
        .brand .address { margin-top: 14px; font-size: 12px; color: #64748b; line-height: 1.6; }
        .brand .address strong { color: #0f172a; }
        .meta { text-align: right; }
        .meta .badge {
            display: inline-block; background: #ecfdf5; color: #059669;
            padding: 6px 18px; border-radius: 10px; margin-bottom: 10px;
        }
        .meta .badge h2 { font-size: 16px; font-weight: 800; text-transform: uppercase; letter-spacing: -.01em; }
        .meta .label { font-size: 10px; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: .06em; }
        .meta .value { font-size: 16px; font-weight: 700; color: #0f172a; font-family: 'JetBrains Mono', monospace; margin-top: 2px; }
        .meta .day { font-size: 13px; color: #64748b; margin-top: 2px; }
        .meta .batch-ref { margin-top: 12px; padding-top: 12px; border-top: 1px solid #e2e8f0; }
        .meta .batch-ref .value { font-size: 13px; }

        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 32px; }
        .stat-card {
            padding: 18px 12px; border-radius: 12px; text-align: center;
            border: 1px solid; transition: none;
        }
        .stat-card .s-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px; }
        .stat-card .s-value { font-size: 22px; font-weight: 900; font-family: 'JetBrains Mono', monospace; }
        .stat-card .s-unit { font-size: 12px; font-weight: 600; }
        .sc-blue { background: #eff6ff; border-color: #bfdbfe; }
        .sc-blue .s-label { color: #2563eb; }
        .sc-blue .s-value { color: #1d4ed8; }
        .sc-green { background: #ecfdf5; border-color: #a7f3d0; }
        .sc-green .s-label { color: #059669; }
        .sc-green .s-value { color: #047857; }
        .sc-amber { background: #fffbeb; border-color: #fde68a; }
        .sc-amber .s-label { color: #d97706; }
        .sc-amber .s-value { color: #b45309; }
        .sc-violet { background: #f5f3ff; border-color: #ddd6fe; }
        .sc-violet .s-label { color: #7c3aed; }
        .sc-violet .s-value { color: #6d28d9; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead th {
            background: #f8fafc; font-size: 10px; font-weight: 700; text-transform: uppercase;
            letter-spacing: .06em; color: #64748b; padding: 12px 16px;
            text-align: left; border-bottom: 2px solid #e2e8f0;
        }
        tbody td { padding: 12px 16px; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
        tbody tr:nth-child(even) { background: #fafbfc; }
        tfoot td {
            padding: 14px 16px; border-top: 2px solid #e2e8f0;
            font-weight: 700; background: #f8fafc; font-size: 13px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        .text-muted { color: #94a3b8; }
        .text-emerald { color: #059669; }
        .text-rose { color: #e11d48; }

        .footer {
            text-align: center; font-size: 10px; color: #94a3b8;
            padding-top: 20px; border-top: 1px solid #e2e8f0; margin-top: 24px;
        }

        .no-print { display: flex; }
        @media print {
            body { padding: 0; background: #fff; }
            .toolbar { display: none !important; }
            .invoice-wrap { border: none; box-shadow: none; padding: 20px 0; }
            .stat-card { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            thead th { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            tbody tr:nth-child(even) { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            tfoot td { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .sc-blue, .sc-green, .sc-amber, .sc-violet { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
<div class="container">

<div class="toolbar no-print">
    <button onclick="window.print()" class="btn-print">&#128424; Print</button>
    <a href="<?php echo e(route('billing.day-load.pdf', $dateObj->format('Y-m-d'))); ?>" class="btn-pdf">&#128196; Download PDF</a>
</div>

<div class="invoice-wrap">

<div class="header">
    <div class="brand">
        <h1>Poultry<span>Pro</span></h1>
        <div class="tag">Poultry Management Solutions</div>
        <div class="address">
            <strong>Poultry Farm Unit #1</strong><br>
            Tamil Nadu, India &bull; +91 98765 43210
        </div>
    </div>
    <div class="meta">
        <div class="badge"><h2>Day-Load Invoice</h2></div>
        <div class="label">Billing Date</div>
        <div class="value"><?php echo e($dateObj->format('d M Y')); ?></div>
        <div class="day"><?php echo e($dateObj->format('l')); ?></div>
        <?php if($batch): ?>
        <div class="batch-ref">
            <div class="label">Batch Reference</div>
            <div class="value">#BAT<?php echo e(str_pad($batch->id, 4, '0', STR_PAD_LEFT)); ?></div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php if($batch): ?>
<div class="stats">
    <div class="stat-card sc-blue">
        <div class="s-label">Total Boxes</div>
        <div class="s-value"><?php echo e($batch->total_boxes); ?></div>
    </div>
    <div class="stat-card sc-green">
        <div class="s-label">Bird Weight</div>
        <div class="s-value"><?php echo e(number_format((float) $batch->total_bird_weight, 2)); ?> <span class="s-unit">kg</span></div>
    </div>
    <div class="stat-card sc-amber">
        <div class="s-label">Farm Weight</div>
        <div class="s-value"><?php echo e(number_format((float) ($batch->total_farm_weight ?? 0), 2)); ?> <span class="s-unit">kg</span></div>
    </div>
    <div class="stat-card sc-violet">
        <div class="s-label">Total Weight</div>
        <div class="s-value"><?php echo e(number_format((float) ($batch->total_weight ?? 0), 2)); ?> <span class="s-unit">kg</span></div>
    </div>
</div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th style="width:36px;">#</th>
            <th>Vendor</th>
            <th>Dealer</th>
            <th class="text-center" style="width:70px;">Boxes</th>
            <th class="text-right" style="width:100px;">Bird Wt</th>
            <th class="text-right" style="width:100px;">Farm Wt</th>
            <th class="text-right" style="width:90px;">Loss</th>
            <th class="text-right" style="width:100px;">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td class="text-center font-mono text-muted"><?php echo e($idx + 1); ?></td>
            <td><strong><?php echo e($entry->vendor->firm_name ?? '-'); ?></strong></td>
            <td><?php echo e($entry->dealer->firm_name ?? '-'); ?></td>
            <td class="text-center font-bold font-mono"><?php echo e($entry->no_of_boxes); ?></td>
            <td class="text-right font-mono"><?php echo e(number_format((float) $entry->bird_weight, 2)); ?></td>
            <td class="text-right font-mono"><?php echo e($entry->farm_weight ? number_format((float) $entry->farm_weight, 2) : '—'); ?></td>
            <td class="text-right font-mono" style="<?php echo e(($entry->loss_weight ?? 0) > 0 ? 'color:#e11d48;font-weight:700;' : 'color:#94a3b8;'); ?>"><?php echo e($entry->loss_weight ? number_format((float) $entry->loss_weight, 2) : '—'); ?></td>
            <td class="text-right font-mono font-bold" style="<?php echo e($entry->total_weight ? 'color:#059669;' : 'color:#94a3b8;'); ?>"><?php echo e($entry->total_weight ? number_format((float) $entry->total_weight, 2) : '—'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="8" class="text-center" style="padding:30px;color:#94a3b8;">No entries found for this date.</td>
        </tr>
        <?php endif; ?>
    </tbody>
    <?php if($entries->count() > 0): ?>
    <tfoot>
        <tr>
            <td colspan="3" style="text-transform:uppercase;font-size:11px;">Totals</td>
            <td class="text-center font-bold font-mono" style="color:#2563eb;"><?php echo e($entries->sum('no_of_boxes')); ?></td>
            <td class="text-right font-bold font-mono"><?php echo e(number_format((float) $entries->sum('bird_weight'), 2)); ?></td>
            <td class="text-right font-bold font-mono"><?php echo e(number_format((float) $entries->sum('farm_weight'), 2)); ?></td>
            <td class="text-right font-bold font-mono text-rose"><?php echo e(number_format((float) $entries->sum('loss_weight'), 2)); ?></td>
            <td class="text-right font-bold font-mono text-emerald"><?php echo e(number_format((float) $entries->sum('total_weight'), 2)); ?></td>
        </tr>
    </tfoot>
    <?php endif; ?>
</table>

<div class="footer">
    This is a computer-generated document. No signature is required.<br>
    Generated on <?php echo e(now()->format('d M Y, h:i A')); ?>

</div>

</div>
</div>
</body>
</html><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\billing\day-load\invoice.blade.php ENDPATH**/ ?>