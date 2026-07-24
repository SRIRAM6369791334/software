<?php $__env->startSection('title', 'DAILY INVOICE SUMMARY'); ?>
<?php $__env->startSection('meta', "Date: {$dateObj->format('d M Y')}"); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .summary-grid { width: 100%; margin-bottom: 25px; border-collapse: collapse; }
    .summary-grid td { padding: 10px; text-align: center; border: 1px solid #e5e7eb; width: 25%; }
    .summary-label { font-size: 9px; text-transform: uppercase; color: #6b7280; margin-bottom: 4px; }
    .summary-value { font-size: 16px; font-weight: bold; }
    .section-title { font-size: 13px; font-weight: bold; color: #064e3b; margin-top: 25px; margin-bottom: 10px; border-bottom: 1px solid #e5e7eb; padding-bottom: 5px; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>

<table class="summary-grid">
    <tr>
        <td style="background: #eff6ff;">
            <div class="summary-label">Purchases</div>
            <div class="summary-value" style="color: #3b82f6;"><?php echo e($purchaseCount); ?></div>
        </td>
        <td style="background: #ecfdf5;">
            <div class="summary-label">Purchase Total</div>
            <div class="summary-value" style="color: #10b981;">Rs <?php echo e(number_format($purchaseTotal, 2)); ?></div>
        </td>
        <td style="background: #fffbeb;">
            <div class="summary-label">Day-Load Batches</div>
            <div class="summary-value" style="color: #f59e0b;"><?php echo e($dayLoadBatch ? 1 : 0); ?></div>
        </td>
        <td style="background: #f5f3ff;">
            <div class="summary-label">Birds Loaded</div>
            <div class="summary-value" style="color: #8b5cf6;"><?php echo e($dayLoadBatch?->total_boxes ?? 0); ?> boxes</div>
        </td>
    </tr>
</table>

<h2>Date: <?php echo e($dateObj->format('d F, Y')); ?> (<?php echo e($dateObj->format('l')); ?>)</h2>

<?php if($purchases->count() > 0): ?>
<div class="section-title">Purchase Invoices</div>
<table class="data-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Vendor</th>
            <th>Invoice No</th>
            <th>Items</th>
            <th class="text-right">Amount</th>
            <th class="text-right">Mode</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="text-center"><?php echo e($idx + 1); ?></td>
            <td><?php echo e($p->vendor_name); ?></td>
            <td><?php echo e($p->invoice_no ?: '—'); ?></td>
            <td>
                <?php $firstItem = $p->items->first(); ?>
                <?php if($firstItem): ?>
                    <?php echo e($firstItem->item_name); ?>

                    <?php if($p->items->count() > 1): ?> +<?php echo e($p->items->count() - 1); ?> more <?php endif; ?>
                <?php endif; ?>
            </td>
            <td class="text-right">Rs <?php echo e(number_format((float) $p->total_amount, 2)); ?></td>
            <td class="text-right"><?php echo e($p->payment_mode); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot>
        <tr style="font-weight: bold; background: #f3f4f6;">
            <td colspan="4" style="text-transform: uppercase; font-size: 10px;">Total</td>
            <td class="text-right">Rs <?php echo e(number_format($purchases->sum('total_amount'), 2)); ?></td>
            <td></td>
        </tr>
    </tfoot>
</table>
<?php endif; ?>

<?php if($dayLoadBatch && $dayLoadEntries->count() > 0): ?>
<div class="section-title">Day-Load Entries</div>
<table class="data-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Vendor</th>
            <th>Dealer</th>
            <th class="text-center">Boxes</th>
            <th class="text-right">Bird Wt</th>
            <th class="text-right">Farm Wt</th>
            <th class="text-right">Loss</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $dayLoadEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idx => $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="text-center"><?php echo e($idx + 1); ?></td>
            <td><?php echo e($entry->vendor->firm_name ?? '-'); ?></td>
            <td><?php echo e($entry->dealer->firm_name ?? '-'); ?></td>
            <td class="text-center font-bold"><?php echo e($entry->no_of_boxes); ?></td>
            <td class="text-right"><?php echo e(number_format((float) $entry->bird_weight, 2)); ?></td>
            <td class="text-right"><?php echo e($entry->farm_weight ? number_format((float) $entry->farm_weight, 2) : '—'); ?></td>
            <td class="text-right <?php echo e(($entry->loss_weight ?? 0) > 0 ? 'text-rose font-bold' : ''); ?>"><?php echo e($entry->loss_weight ? number_format((float) $entry->loss_weight, 2) : '—'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot>
        <tr style="font-weight: bold; background: #f3f4f6;">
            <td colspan="3" style="text-transform: uppercase; font-size: 10px;">Total</td>
            <td class="text-center"><?php echo e($dayLoadEntries->sum('no_of_boxes')); ?></td>
            <td class="text-right"><?php echo e(number_format((float) $dayLoadEntries->sum('bird_weight'), 2)); ?></td>
            <td class="text-right"><?php echo e(number_format((float) ($dayLoadBatch?->total_farm_weight ?? $dayLoadEntries->sum('farm_weight')), 2)); ?></td>
            <td class="text-right text-rose"><?php echo e(number_format((float) ($dayLoadBatch?->total_loss_weight ?? $dayLoadEntries->sum('loss_weight')), 2)); ?></td>
        </tr>
    </tfoot>
</table>
<?php endif; ?>

<p style="text-align: center; font-size: 10px; color: #9ca3af; margin-top: 30px;">
    This is a computer-generated document. No signature is required.
</p>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.pdf', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\purchases\invoices-pdf.blade.php ENDPATH**/ ?>