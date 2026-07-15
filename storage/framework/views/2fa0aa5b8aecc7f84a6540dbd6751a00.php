<?php $__env->startSection('title', 'Purchase History Statement'); ?>
<?php $__env->startSection('meta', 'Generated: ' . now()->format('d M Y, h:i A')); ?>

<?php $__env->startSection('content'); ?>

<table class="summary-grid">
    <tr>
        <td style="width: 50%; padding-right: 10px;">
            <div class="summary-card">
                <div class="summary-label">Vendor Details</div>
                <div class="summary-value" style="margin-bottom: 5px;"><?php echo e($vendor->firm_name); ?></div>
                <div style="font-size: 10px; color: #4b5563; line-height: 1.4;">
                    <strong>Contact Person:</strong> <?php echo e($vendor->contact_person ?: 'N/A'); ?><br>
                    <strong>Phone:</strong> <?php echo e($vendor->phone); ?><br>
                    <strong>Location:</strong> <?php echo e($vendor->location ?: 'N/A'); ?>

                </div>
            </div>
        </td>
        <td style="width: 50%; padding-left: 10px;">
            <div class="summary-card" style="border-left: 3px solid #10b981;">
                <div class="summary-label">Account Info</div>
                <div style="font-size: 10px; color: #4b5563; line-height: 1.4; margin-top: 5px;">
                    <strong>GSTIN:</strong> <?php echo e($vendor->gst_number ?: 'UNREGISTERED'); ?><br>
                    <strong>Route:</strong> <?php echo e($vendor->route ?: 'General'); ?>

                </div>
            </div>
        </td>
    </tr>
</table>

<h2>Purchase History</h2>
<table class="data-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Item Details</th>
            <th class="text-right">Quantity</th>
            <th class="text-right">Total Amount (Rs)</th>
        </tr>
    </thead>
    <tbody>
        <?php $totalAmount = 0; ?>
        <?php $__empty_1 = true; $__currentLoopData = $purchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <?php $totalAmount += $purchase->total_amount; ?>
            <tr>
                <td><?php echo e($purchase->date->format('d M Y')); ?></td>
                <td>
                    <?php if($purchase->items->isNotEmpty()): ?>
                        <?php echo e($purchase->items->pluck('item_name')->join(', ')); ?>

                    <?php else: ?>
                        <?php echo e($purchase->item); ?>

                    <?php endif; ?>
                </td>
                <td class="text-right">
                    <?php if($purchase->items->isNotEmpty()): ?>
                        <?php echo e(number_format($purchase->items->sum('quantity'), 2)); ?> <?php echo e($purchase->items->first()->unit); ?>

                    <?php else: ?>
                        <?php echo e(number_format($purchase->quantity, 2)); ?> <?php echo e($purchase->unit); ?>

                    <?php endif; ?>
                </td>
                <td class="text-right font-bold">Rs <?php echo e(number_format($purchase->total_amount, 2)); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="4" class="text-center" style="padding: 20px;">No purchases found.</td>
            </tr>
        <?php endif; ?>
        <?php if($purchases->count() > 0): ?>
            <tr>
                <td colspan="3" class="text-right font-bold" style="padding: 12px; background-color: #f9fafb;">Total Business Volume</td>
                <td class="text-right font-bold text-emerald" style="padding: 12px; background-color: #f9fafb;">
                    Rs <?php echo e(number_format($totalAmount, 2)); ?>

                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.pdf', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\masters\vendors\history_pdf.blade.php ENDPATH**/ ?>