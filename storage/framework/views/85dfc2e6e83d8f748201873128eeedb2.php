<?php $__env->startSection('title', 'Supplier Ledger Statement'); ?>
<?php $__env->startSection('meta', 'Statement Period: Up to ' . now()->format('d M Y')); ?>

<?php $__env->startSection('content'); ?>

<table class="summary-grid">
    <tr>
        <td style="width: 60%; padding-right: 10px;">
            <div class="summary-card">
                <div class="summary-label">Supplier Details</div>
                <div class="summary-value" style="margin-bottom: 5px;"><?php echo e($dealer->firm_name); ?></div>
                <div style="font-size: 10px; color: #4b5563; line-height: 1.4;">
                    <?php echo e($dealer->contact_person); ?><br>
                    <strong>Phone: <?php echo e($dealer->phone); ?></strong>
                    <?php if($dealer->gst_number): ?>
                        <br>GSTIN: <?php echo e($dealer->gst_number); ?>

                    <?php endif; ?>
                </div>
            </div>
        </td>
        <td style="width: 40%; padding-left: 10px;">
            <div class="summary-card" style="border-left: 3px solid #f43f5e;">
                <div class="summary-label">Pending Payable</div>
                <div class="summary-value text-rose" style="font-size: 18px;">Rs <?php echo e(number_format($dealer->displayed_outstanding, 2)); ?></div>
                <?php if($dealer->dayload_outstanding > 0): ?>
                    <div style="font-size: 8px; color: #9ca3af; margin-top: 2px;">Old: Rs <?php echo e(number_format($dealer->pending_amount, 0)); ?> + Day-Load: Rs <?php echo e(number_format($dealer->dayload_outstanding, 0)); ?></div>
                <?php endif; ?>
                <div style="font-size: 8px; color: #9ca3af; margin-top: 5px; font-weight: bold;">ACCOUNT PAYABLE LIABILITY</div>
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
            <th style="width: 16%;" class="text-right">Liability Balance</th>
        </tr>
    </thead>
    <tbody>
        <?php $runningLiability = 0; ?>
        <?php $__currentLoopData = $ledger; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php 
                $runningLiability += $row['debit'];
                $runningLiability -= $row['credit'];
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
                <td class="text-right font-bold">Rs <?php echo e(number_format($runningLiability, 2)); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td colspan="4" class="text-right font-bold" style="padding: 12px; background-color: #f9fafb;">Final Outstanding Liability</td>
            <td class="text-right font-bold text-rose" style="padding: 12px; font-size: 12px; background-color: #f9fafb;">
                Rs <?php echo e(number_format($runningLiability, 2)); ?>

            </td>
        </tr>
    </tbody>
</table>

<?php if($dayLoadLedger->isNotEmpty()): ?>
    <h2>Day-Load Billing</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Date</th>
                <th style="width: 45%;">Description</th>
                <th style="width: 14%;" class="text-right">Bird Value (Dr)</th>
                <th style="width: 14%;" class="text-right">Payment (Cr)</th>
                <th style="width: 12%;" class="text-right">D/L Balance</th>
            </tr>
        </thead>
        <tbody>
            <?php $dlRunning = 0; ?>
            <?php $__currentLoopData = $dayLoadLedger; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $dlRunning += $row['debit'] - $row['credit']; ?>
                <tr>
                    <td><?php echo e(\Carbon\Carbon::parse($row['date'])->format('d M Y')); ?></td>
                    <td><?php echo e($row['desc']); ?></td>
                    <td class="text-right text-rose">
                        <?php echo e($row['debit'] > 0 ? 'Rs ' . number_format($row['debit'], 2) : '-'); ?>

                    </td>
                    <td class="text-right text-emerald">
                        <?php echo e($row['credit'] > 0 ? 'Rs ' . number_format($row['credit'], 2) : '-'); ?>

                    </td>
                    <td class="text-right font-bold">Rs <?php echo e(number_format($dlRunning, 2)); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php
                // Sanity check: running balance must equal the accessor's computation.
                // Both derive from the same underlying data (non-cancelled entries minus
                // payments), but via two independent code paths — any mismatch signals a bug.
                $expectedFinal = max(0, $dlRunning);
                $accessorFinal = $dealer->dayload_outstanding;
            ?>
            <?php if(abs($expectedFinal - $accessorFinal) > 0.01): ?>
                <tr>
                    <td colspan="5" class="text-center" style="padding: 8px; background-color: #fef2f2; color: #dc2626; font-size: 10px;">
                        ⚠ DAY-LOAD BALANCE MISMATCH — Running total (Rs <?php echo e(number_format($expectedFinal, 2)); ?>)
                        differs from system outstanding (Rs <?php echo e(number_format($accessorFinal, 2)); ?>).
                        Please verify data integrity.
                    </td>
                </tr>
            <?php endif; ?>
            <tr>
                <td colspan="4" class="text-right font-bold" style="padding: 8px; background-color: #f9fafb;">Final Day-Load Outstanding</td>
                <td class="text-right font-bold text-rose" style="padding: 8px; font-size: 11px; background-color: #f9fafb;">
                    Rs <?php echo e(number_format($expectedFinal, 2)); ?>

                </td>
            </tr>
        </tbody>
    </table>
<?php endif; ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.pdf', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\masters\dealers\ledger_pdf.blade.php ENDPATH**/ ?>