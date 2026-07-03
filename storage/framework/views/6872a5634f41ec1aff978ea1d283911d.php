<?php $__env->startSection('title', 'TAX INVOICE (WEEKLY)'); ?>
<?php $__env->startSection('meta', "Invoice No: {$bill->invoice_no}"); ?>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<table class="invoice-details">
    <tr>
        <td>
            <div class="section-label">Bill To Dealer</div>
            <div style="font-size: 14px; font-weight: bold; margin-bottom: 5px; color: #111827;"><?php echo e($bill->dealer->firm_name ?? 'N/A'); ?></div>
            <div style="color: #4b5563; line-height: 1.4;">
                <?php echo e($bill->dealer->location ?? 'No location provided'); ?><br>
                <strong>Phone: <?php echo e($bill->dealer->phone ?? 'N/A'); ?></strong>
                <?php if($bill->dealer->gst_number): ?>
                    <br><span style="font-size: 10px; color: #9ca3af;">GSTIN: <?php echo e($bill->dealer->gst_number); ?></span>
                <?php endif; ?>
            </div>
        </td>
        <td style="padding-left: 40px;">
            <div class="section-label">Billing Period</div>
            <div style="font-size: 12px; font-weight: bold; color: #111827;">
                <?php echo e($bill->period_start->format('d M')); ?> - <?php echo e($bill->period_end->format('d M, Y')); ?>

            </div>
            <table style="font-size: 10px; width: 100%; margin-top: 10px;">
                <tr>
                    <td style="color: #6b7280; padding: 4px 0; border: none;">Status:</td>
                    <td class="text-right font-bold" style="border: none;"><?php echo e(strtoupper($bill->status)); ?></td>
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
        <?php $__empty_1 = true; $__currentLoopData = $bill->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td>
                <div class="font-bold text-zinc-900"><?php echo e($item->item_name); ?></div>
                <div style="font-size: 8px; color: #9ca3af; margin-top: 3px;">WEEKLY SUPPLY ITEM</div>
            </td>
            <td class="text-center"><?php echo e(number_format($item->quantity_kg, 2)); ?> KG</td>
            <td class="text-right">Rs <?php echo e(number_format($item->rate_per_kg, 2)); ?></td>
            <td class="text-right font-bold text-indigo-600">Rs <?php echo e(number_format($item->quantity_kg * $item->rate_per_kg, 2)); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td>
                <div class="font-bold text-zinc-900"><?php echo e($bill->items_description ?? 'Poultry Sales'); ?></div>
                <div style="font-size: 8px; color: #9ca3af; margin-top: 3px;">CONSOLIDATED WEEKLY PROCUREMENT</div>
            </td>
            <td class="text-center"><?php echo e(number_format($bill->quantity_kg, 2)); ?> KG</td>
            <td class="text-right">Rs <?php echo e(number_format($bill->amount / max(1, $bill->quantity_kg), 2)); ?></td>
            <td class="text-right font-bold text-indigo-600">Rs <?php echo e(number_format($bill->amount, 2)); ?></td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="total-box">
    <div class="total-row">
        Subtotal
        <span>Rs <?php echo e(number_format($bill->amount, 2)); ?></span>
    </div>
    <div class="total-row" style="margin-bottom: 15px;">
        GST (<?php echo e($bill->gst_percentage); ?>%)
        <span>Rs <?php echo e(number_format($bill->gst_amount, 2)); ?></span>
    </div>
    <div class="grand-total">
        Total Net
        <span>Rs <?php echo e(number_format($bill->net_amount, 2)); ?></span>
    </div>
</div>

<div style="clear: both; margin-top: 80px; text-align: center;">
    <div style="font-size: 9px; font-weight: bold; color: #9ca3af; text-transform: uppercase; margin-bottom: 10px;">
        NO SIGNATURE REQUIRED &bull; COMPUTER GENERATED &bull; AUTH VERIFIED
    </div>
    <p style="font-weight: bold; color: #374151; margin: 5px 0;">Thank you for your continued partnership!</p>
    <p style="font-size: 10px; color: #6b7280; margin: 0;">Please ensure payment is cleared within the weekly credit cycle.</p>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.pdf', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\billing\weekly\pdf.blade.php ENDPATH**/ ?>