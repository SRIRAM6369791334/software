
<?php $__env->startSection('title', 'GST Billing Overview'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <a href="<?php echo e(route('billing.daily.index')); ?>" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Daily Billing</a>
    <h1 class="text-2xl font-bold text-zinc-950">GST Billing & Tax Logs</h1>
    <p class="text-sm text-zinc-500 mt-0.5">Filterable view specifically for tax evaluation and GST reporting</p>
</div>

<div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-xl border border-zinc-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-zinc-200 bg-gradient-to-r from-emerald-50/80 to-sky-50/80 flex justify-between items-center">
        <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Taxable Invoices</h3>
        <button class="text-xs font-bold text-emerald-600 hover:underline">Download GSTR-1 Helper (Excel)</button>
    </div>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left border-b border-zinc-200 bg-zinc-50/20">
                    <th class="px-5 py-3 text-xs font-semibold text-zinc-400 uppercase tracking-wider">Date</th>
                    <th class="px-5 py-3 text-xs font-semibold text-zinc-400 uppercase tracking-wider">Customer / GSTIN</th>
                    <th class="px-5 py-3 text-xs font-semibold text-zinc-400 uppercase tracking-wider">Description</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-zinc-400 uppercase tracking-wider">Taxable Value</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-zinc-400 uppercase tracking-wider">GST (5%)</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-zinc-400 uppercase tracking-wider">Total Amount</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100">
                <?php $__empty_1 = true; $__currentLoopData = $bills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gradient-to-r from-emerald-50/70 to-sky-50/70 transition-colors">
                        <td class="px-5 py-4 font-semibold text-zinc-950"><?php echo e($bill->date->format('d M Y')); ?></td>
                        <td class="px-5 py-4">
                            <p class="font-bold text-zinc-950"><?php echo e($bill->customer->name); ?></p>
                            <p class="text-xs text-zinc-500 font-semibold font-mono"><?php echo e($bill->invoice_number); ?></p>
                            <p class="text-[10px] text-zinc-400 font-mono"><?php echo e($bill->customer->gst_number ?: 'NO GSTIN'); ?></p>
                        </td>
                        <td class="px-5 py-4 text-zinc-500 italic"><?php echo e($bill->items_description); ?></td>
                        <td class="px-5 py-4 text-right font-mono text-zinc-600">Rs <?php echo e(number_format($bill->amount / 1.05, 2)); ?></td>
                        <td class="px-5 py-4 text-right font-mono text-primary">Rs <?php echo e(number_format($bill->amount - ($bill->amount / 1.05), 2)); ?></td>
                        <td class="px-5 py-4 text-right font-black text-zinc-950">Rs <?php echo e(number_format($bill->amount, 2)); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="6" class="px-5 py-12 text-center text-zinc-400 italic">No GST-enabled invoices found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <div class="px-5 py-4 border-t border-zinc-200 italic text-[10px] text-zinc-400 uppercase font-bold tracking-widest text-center">
        Note: Tax values are calculated based on a default 5% Inclusive GST model for poultry.
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/billing/daily/gst.blade.php ENDPATH**/ ?>