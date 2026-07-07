<?php $__env->startSection('title', 'TAX INVOICE'); ?>
<?php $__env->startSection('meta', "Invoice No: {$bill->invoice_no}"); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .invoice-details { width: 100%; margin-bottom: 30px; }
    .invoice-details td { vertical-align: top; width: 50%; }
    .section-label { font-size: 10px; font-weight: bold; color: #10b981; text-transform: uppercase; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; margin-bottom: 10px; }
    .total-box { float: right; width: 200px; background-color: #111827; color: #fff; padding: 20px; border-radius: 8px; margin-top: 20px; }
    .total-row { margin-bottom: 8px; font-size: 10px; text-transform: uppercase; opacity: 0.8; }
    .total-row span { float: right; }
    .grand-total { margin-top: 12px; padding-top: 12px; border-top: 1px solid rgba(255,255,255,0.2); font-size: 16px; font-weight: bold; color: #34d399; }
    .grand-total span { float: right; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<table class="invoice-details">
    <tr>
        <td>
            <div class="section-label">Bill To Customer</div>
            <div style="font-size: 14px; font-weight: bold; margin-bottom: 5px; color: #111827;"><?php echo e($bill->customer->name ?? 'N/A'); ?></div>
            <div style="color: #4b5563; line-height: 1.4;">
                <?php echo e($bill->customer->address ?? 'No address provided'); ?><br>
                <strong>Phone: <?php echo e($bill->customer->phone ?? 'N/A'); ?></strong>
                <?php if($bill->customer->gst_number): ?>
                    <br><span style="font-size: 10px; color: #9ca3af;">GSTIN: <?php echo e($bill->customer->gst_number); ?></span>
                <?php endif; ?>
            </div>
        </td>
        <td style="padding-left: 40px;">
            <div class="section-label">Payment Summary</div>
            <table style="font-size: 10px; width: 100%;">
                <tr>
                    <td style="color: #6b7280; padding: 4px 0; border: none;">Status:</td>
                    <td class="text-right font-bold" style="border: none;"><?php echo e(strtoupper($bill->status)); ?></td>
                </tr>
                <tr>
                    <td style="color: #6b7280; padding: 4px 0; border: none;">Billing Type:</td>
                    <td class="text-right font-bold" style="border: none;">Daily Retail Sale</td>
                </tr>
                <tr>
                    <td style="color: #6b7280; padding: 4px 0; border: none;">Currency:</td>
                    <td class="text-right font-bold" style="border: none;">INR</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<table class="data-table">
    <thead>
        <tr>
            <th>Item Description</th>
            <th class="text-center">Quantity</th>
            <th class="text-right">Unit Price</th>
            <th class="text-right">Taxable Amt</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $bill->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td>
                <div class="font-bold text-zinc-900"><?php echo e($item->item_name); ?></div>
                <div style="font-size: 8px; color: #9ca3af; margin-top: 3px;">POULTRY PRODUCT</div>
            </td>
            <td class="text-center"><?php echo e(number_format($item->quantity_kg, 2)); ?> <?php echo e($item->unit); ?></td>
            <td class="text-right">Rs <?php echo e(number_format($item->rate_per_kg, 2)); ?></td>
            <td class="text-right font-bold text-emerald">Rs <?php echo e(number_format($item->quantity_kg * $item->rate_per_kg, 2)); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
    <p style="font-weight: bold; color: #374151; margin: 5px 0;">Thank you for choosing <?php echo e(config('app.name', 'Poultry ')); ?>!</p>
    <p style="font-size: 10px; color: #6b7280; margin: 0;">Please settle the payment according to the agreed credit terms.</p>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.pdf', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/billing/daily/pdf.blade.php ENDPATH**/ ?>