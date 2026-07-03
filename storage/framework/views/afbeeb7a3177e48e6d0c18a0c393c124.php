<?php $__env->startSection('title', 'Customer Ledger Statement'); ?>
<?php $__env->startSection('meta', 'Statement Period: Up to ' . now()->format('d M Y')); ?>

<?php $__env->startSection('content'); ?>

<table class="summary-grid">
    <tr>
        <td style="width: 60%; padding-right: 10px;">
            <div class="summary-card">
                <div class="summary-label">Customer Details</div>
                <div class="summary-value" style="margin-bottom: 5px;"><?php echo e($customer->name); ?></div>
                <div style="font-size: 10px; color: #4b5563; line-height: 1.4;">
                    <?php echo e($customer->address); ?><br>
                    <strong>Phone: <?php echo e($customer->phone); ?></strong>
                    <?php if($customer->gst_number): ?>
                        <br>GSTIN: <?php echo e($customer->gst_number); ?>

                    <?php endif; ?>
                </div>
            </div>
        </td>
        <td style="width: 40%; padding-left: 10px;">
            <div class="summary-card" style="border-left: 3px solid #10b981;">
                <div class="summary-label">Current Balance</div>
                <div class="summary-value text-emerald" style="font-size: 18px;">Rs <?php echo e(number_format($customer->balance, 2)); ?></div>
                <div style="font-size: 8px; color: #9ca3af; margin-top: 5px; font-weight: bold;">OUTSTANDING RECEIVABLE</div>
            </div>
        </td>
    </tr>
</table>

<h2>Transaction History</h2>
<table class="data-table">
    <thead>
        <tr>
            <th style="width: 15%;">Date</th>
            <th style="width: 45%;">Transaction Description</th>
            <th style="width: 12%;" class="text-right">Debit (+)</th>
            <th style="width: 12%;" class="text-right">Credit (-)</th>
            <th style="width: 16%;" class="text-right">Running Balance</th>
        </tr>
    </thead>
    <tbody>
        <?php $runningBalance = 0; ?>
        <?php $__currentLoopData = $ledger; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php 
                $runningBalance += $row['debit'];
                $runningBalance -= $row['credit'];
            ?>
            <tr>
                <td><?php echo e(\Carbon\Carbon::parse($row['date'])->format('d M Y')); ?></td>
                <td><?php echo e($row['desc']); ?></td>
                <td class="text-right text-rose">
                    <?php echo e($row['debit'] > 0 ? 'Rs ' . number_format($row['debit'], 2) : '-'); ?>

                </td>
                <td class="text-right text-emerald">
                    <?php echo e($row['credit'] > 0 ? 'Rs ' . number_format($row['credit'], 2) : '-'); ?>

                </td>
                <td class="text-right font-bold">Rs <?php echo e(number_format($runningBalance, 2)); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td colspan="4" class="text-right font-bold" style="padding: 12px; background-color: #f9fafb;">Final Outstanding Balance</td>
            <td class="text-right font-bold text-emerald" style="padding: 12px; font-size: 12px; background-color: #f9fafb;">
                Rs <?php echo e(number_format($runningBalance, 2)); ?>

            </td>
        </tr>
    </tbody>
</table>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.pdf', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\masters\customers\ledger_pdf.blade.php ENDPATH**/ ?>