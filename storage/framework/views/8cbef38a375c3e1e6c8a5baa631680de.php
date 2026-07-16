<?php $__env->startSection('title', 'Invoice #' . ($bill->invoice_no ?? $bill->id)); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-4xl mx-auto my-6 space-y-0" id="invoice-print">

    
    <div class="bg-gradient-to-br from-emerald-700 to-emerald-900 text-white px-8 py-6 rounded-t-2xl flex justify-between items-start">
        <div>
            <h1 class="text-2xl font-black tracking-tight">🐔 FlockWise BizTrack</h1>
            <p class="text-emerald-200 text-xs mt-1 uppercase tracking-widest">Poultry Management Solutions</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-emerald-300 uppercase tracking-widest mb-1">Invoice</p>
            <p class="text-2xl font-black font-mono"><?php echo e($bill->invoice_no ?? ('INV-W-' . str_pad($bill->id, 4, '0', STR_PAD_LEFT))); ?></p>
            <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider
                <?php echo e($bill->status === 'Paid' ? 'bg-emerald-400 text-emerald-900' : 'bg-amber-400 text-amber-900'); ?>">
                <?php echo e($bill->status); ?>

            </span>
        </div>
    </div>

    
    <div class="bg-white border-x border-zinc-200 px-8 py-5 grid grid-cols-2 gap-8">
        <div>
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-2">Bill To</p>
            <h2 class="text-lg font-black text-zinc-900"><?php echo e($bill->dealer?->firm_name ?? 'N/A'); ?></h2>
            <p class="text-sm text-zinc-500 mt-0.5"><?php echo e($bill->dealer?->location ?? ''); ?></p>
            <p class="text-sm text-zinc-500">📞 <?php echo e($bill->dealer?->phone ?? 'N/A'); ?></p>
            <?php if($bill->dealer?->gst_number): ?>
                <p class="text-xs text-zinc-400 mt-1">GSTIN: <span class="font-mono text-zinc-600"><?php echo e($bill->dealer->gst_number); ?></span></p>
            <?php endif; ?>
        </div>
        <div class="text-right">
            <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-2">Period & Details</p>
            <p class="text-base font-bold text-zinc-800">
                <?php echo e($bill->period_start?->format('d M Y')); ?> — <?php echo e($bill->period_end?->format('d M Y')); ?>

            </p>
            <p class="text-xs text-zinc-500 mt-1">Generated: <?php echo e(now()->format('d M Y')); ?></p>
            <p class="text-xs text-zinc-500">Payment Mode: <?php echo e($bill->payment_mode ?? 'Credit'); ?></p>
        </div>
    </div>

    
    <div class="bg-white border-x border-t border-zinc-200">
        <div class="px-8 py-3 bg-emerald-50 border-b border-zinc-200 flex items-center gap-2">
            <span class="material-symbols-rounded text-emerald-600 text-[18px]">local_shipping</span>
            <h3 class="font-bold text-emerald-800 text-sm uppercase tracking-wider">Day-Load Entries</h3>
            <?php if(isset($dayLoadEntries) && $dayLoadEntries->isNotEmpty()): ?>
                <span class="ml-auto text-xs text-emerald-700 font-semibold">
                    <?php echo e($dayLoadEntries->count()); ?> entries |
                    <?php echo e(number_format($dayLoadEntries->sum('bird_weight'), 2)); ?> kg total
                </span>
            <?php endif; ?>
        </div>

        <?php if(isset($dayLoadEntries) && $dayLoadEntries->isNotEmpty()): ?>
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest bg-zinc-50 border-b border-zinc-200">
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Vendor</th>
                        <th class="px-6 py-3 text-right">Weight (kg)</th>
                        <th class="px-6 py-3 text-right">Customer Rate</th>
                        <th class="px-6 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    <?php $__currentLoopData = $dayLoadEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $kg    = (float) $entry->bird_weight;
                            $rate  = (float) $entry->customer_rate;
                            $total = round($kg * $rate, 2);
                        ?>
                        <tr class="hover:bg-emerald-50/30">
                            <td class="px-6 py-3 font-medium text-zinc-800">
                                <?php echo e($entry->batch?->billing_date?->format('d M Y') ?? '—'); ?>

                                <span class="block text-[10px] text-zinc-400"><?php echo e($entry->batch?->billing_date?->format('l')); ?></span>
                            </td>
                            <td class="px-6 py-3 text-zinc-600"><?php echo e($entry->vendor?->firm_name ?? '—'); ?></td>
                            <td class="px-6 py-3 text-right font-mono text-zinc-700"><?php echo e(number_format($kg, 2)); ?> kg</td>
                            <td class="px-6 py-3 text-right font-mono text-zinc-700">₹<?php echo e(number_format($rate, 2)); ?></td>
                            <td class="px-6 py-3 text-right font-bold font-mono text-zinc-900">₹<?php echo e(number_format($total, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr class="bg-emerald-50 border-t-2 border-emerald-200">
                        <td colspan="2" class="px-6 py-3 text-xs font-bold text-emerald-700 uppercase tracking-wider">
                            Day-Load Total
                        </td>
                        <td class="px-6 py-3 text-right font-mono font-bold text-emerald-700">
                            <?php echo e(number_format($dayLoadEntries->sum('bird_weight'), 2)); ?> kg
                        </td>
                        <td></td>
                        <td class="px-6 py-3 text-right font-black font-mono text-emerald-700 text-base">
                            ₹<?php echo e(number_format($dayLoadTotal ?? 0, 2)); ?>

                        </td>
                    </tr>
                </tfoot>
            </table>
        <?php else: ?>
            
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest bg-zinc-50 border-b border-zinc-200">
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3 text-right">Qty (kg)</th>
                        <th class="px-6 py-3 text-right">Rate</th>
                        <th class="px-6 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    <?php $__empty_1 = true; $__currentLoopData = $bill->items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td class="px-6 py-3 font-medium text-zinc-800"><?php echo e($item->item_name); ?></td>
                            <td class="px-6 py-3 text-right font-mono text-zinc-600"><?php echo e(number_format($item->quantity_kg, 2)); ?></td>
                            <td class="px-6 py-3 text-right font-mono text-zinc-600">₹<?php echo e(number_format($item->rate_per_kg, 2)); ?></td>
                            <td class="px-6 py-3 text-right font-bold font-mono text-zinc-900">₹<?php echo e(number_format($item->quantity_kg * $item->rate_per_kg, 2)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-6 text-center text-zinc-400 text-sm">No line items found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    
    <div class="bg-white border-x border-t border-zinc-200 px-8 py-5">
        <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-4 flex items-center gap-2">
            <span class="material-symbols-rounded text-[16px]">calculate</span>
            Financial Summary
        </h3>
        <div class="max-w-sm ml-auto space-y-2">
            <div class="flex justify-between text-sm">
                <span class="text-zinc-500">Previous Outstanding</span>
                <span class="font-mono font-semibold text-rose-600">₹<?php echo e(number_format((float)($bill->previous_outstanding ?? 0), 2)); ?></span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-zinc-500">This Week's Day-Load</span>
                <span class="font-mono font-semibold text-zinc-800">+ ₹<?php echo e(number_format($dayLoadTotal ?? $bill->amount, 2)); ?></span>
            </div>
            <?php if((float)($bill->payments_during_week ?? 0) > 0): ?>
            <div class="flex justify-between text-sm">
                <span class="text-zinc-500">Payments During Week</span>
                <span class="font-mono font-semibold text-emerald-600">- ₹<?php echo e(number_format((float)($bill->payments_during_week ?? 0), 2)); ?></span>
            </div>
            <?php endif; ?>
            <?php if(($bill->gst_amount ?? 0) > 0): ?>
            <div class="flex justify-between text-sm">
                <span class="text-zinc-500">GST (<?php echo e($bill->gst_percentage ?? 18); ?>%)</span>
                <span class="font-mono font-semibold text-zinc-600">+ ₹<?php echo e(number_format((float)($bill->gst_amount ?? 0), 2)); ?></span>
            </div>
            <?php endif; ?>
            <div class="flex justify-between items-center pt-3 border-t-2 border-zinc-900">
                <span class="font-bold text-zinc-900 uppercase tracking-wider text-sm">Net Invoice Amount</span>
                <span class="font-black font-mono text-xl text-emerald-600">₹<?php echo e(number_format((float)($bill->net_amount ?? $bill->amount), 2)); ?></span>
            </div>
        </div>
    </div>

    
    <div class="bg-white border-x border-t border-zinc-200 px-8 py-5">
        <h3 class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest mb-4 flex items-center gap-2">
            <span class="material-symbols-rounded text-[16px]">calendar_month</span>
            Payment Schedule (Monday/Friday Split)
        </h3>
        <div class="grid grid-cols-2 gap-4">
            
            <div class="p-4 rounded-xl border-2 <?php echo e(($bill->monday_payment_status ?? '') === 'Paid' ? 'border-emerald-300 bg-emerald-50' : 'border-amber-200 bg-amber-50'); ?>">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-lg"><?php echo e(($bill->monday_payment_status ?? '') === 'Paid' ? '✅' : '⏳'); ?></span>
                    <span class="text-xs font-bold uppercase tracking-wider <?php echo e(($bill->monday_payment_status ?? '') === 'Paid' ? 'text-emerald-700' : 'text-amber-700'); ?>">
                        Monday Split (50%)
                    </span>
                </div>
                <p class="text-xl font-black font-mono <?php echo e(($bill->monday_payment_status ?? '') === 'Paid' ? 'text-emerald-700' : 'text-amber-800'); ?>">
                    ₹<?php echo e(number_format((float)($bill->monday_payment_amount ?? 0), 2)); ?>

                </p>
                <p class="text-xs mt-1 <?php echo e(($bill->monday_payment_status ?? '') === 'Paid' ? 'text-emerald-600' : 'text-amber-600'); ?>">
                    Status: <strong><?php echo e($bill->monday_payment_status ?? 'Unpaid'); ?></strong>
                </p>
            </div>
            
            <div class="p-4 rounded-xl border-2 <?php echo e(($bill->friday_payment_status ?? '') === 'Paid' ? 'border-emerald-300 bg-emerald-50' : 'border-amber-200 bg-amber-50'); ?>">
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-lg"><?php echo e(($bill->friday_payment_status ?? '') === 'Paid' ? '✅' : '⏳'); ?></span>
                    <span class="text-xs font-bold uppercase tracking-wider <?php echo e(($bill->friday_payment_status ?? '') === 'Paid' ? 'text-emerald-700' : 'text-amber-700'); ?>">
                        Friday Split (50%)
                    </span>
                </div>
                <p class="text-xl font-black font-mono <?php echo e(($bill->friday_payment_status ?? '') === 'Paid' ? 'text-emerald-700' : 'text-amber-800'); ?>">
                    ₹<?php echo e(number_format((float)($bill->friday_payment_amount ?? 0), 2)); ?>

                </p>
                <p class="text-xs mt-1 <?php echo e(($bill->friday_payment_status ?? '') === 'Paid' ? 'text-emerald-600' : 'text-amber-600'); ?>">
                    Status: <strong><?php echo e($bill->friday_payment_status ?? 'Unpaid'); ?></strong>
                </p>
            </div>
        </div>
    </div>

    
    <div class="bg-white border-x border-t border-zinc-200">
        <div class="px-8 py-3 bg-indigo-50 border-b border-zinc-200 flex items-center gap-2">
            <span class="material-symbols-rounded text-indigo-600 text-[18px]">receipt_long</span>
            <h3 class="font-bold text-indigo-800 text-sm uppercase tracking-wider">Payment History</h3>
            <span class="ml-auto text-xs text-indigo-600 font-semibold">
                Total Paid: ₹<?php echo e(number_format($totalPaid ?? 0, 2)); ?>

            </span>
        </div>

        <?php if(isset($allPayments) && $allPayments->isNotEmpty()): ?>
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest bg-zinc-50 border-b border-zinc-200">
                        <th class="px-6 py-3">Date</th>
                        <th class="px-6 py-3">Description</th>
                        <th class="px-6 py-3 text-center">Mode</th>
                        <th class="px-6 py-3 text-right">Cash</th>
                        <th class="px-6 py-3 text-right">Bank</th>
                        <th class="px-6 py-3 text-right">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100">
                    <?php $__currentLoopData = $allPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php
                            $desc = $payment->notes ?? 'Payment';
                            if (str_contains(strtolower($desc), 'monday') || str_contains(strtolower($desc), 'monday split')) {
                                $desc = '🗓️ Monday Split Payment';
                                $badgeColor = 'bg-blue-100 text-blue-700';
                            } elseif (str_contains(strtolower($desc), 'friday') || str_contains(strtolower($desc), 'friday split')) {
                                $desc = '🗓️ Friday Split Payment';
                                $badgeColor = 'bg-purple-100 text-purple-700';
                            } elseif (str_contains(strtolower($desc), 'allocated') || str_contains(strtolower($desc), 'auto-allocated')) {
                                $desc = '💰 Day-Load Payment';
                                $badgeColor = 'bg-emerald-100 text-emerald-700';
                            } else {
                                $desc = '💳 ' . ($payment->notes ?? 'Ledger Payment');
                                $badgeColor = 'bg-zinc-100 text-zinc-600';
                            }
                        ?>
                        <tr class="hover:bg-indigo-50/20">
                            <td class="px-6 py-3 font-medium text-zinc-800">
                                <?php echo e($payment->date?->format('d M Y')); ?>

                                <span class="block text-[10px] text-zinc-400"><?php echo e($payment->date?->format('l')); ?></span>
                            </td>
                            <td class="px-6 py-3 text-zinc-600 text-xs"><?php echo e($desc); ?></td>
                            <td class="px-6 py-3 text-center">
                                <span class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold <?php echo e($payment->payment_mode === 'Cash' ? 'bg-emerald-100 text-emerald-700' : 'bg-blue-100 text-blue-700'); ?>">
                                    <?php echo e($payment->payment_mode); ?>

                                    <?php if($payment->bank_transfer_type): ?>
                                        (<?php echo e($payment->bank_transfer_type); ?>)
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td class="px-6 py-3 text-right font-mono text-emerald-600">
                                <?php if((float)$payment->cash_amount > 0): ?>
                                    ₹<?php echo e(number_format((float)$payment->cash_amount, 2)); ?>

                                <?php else: ?>
                                    <span class="text-zinc-300">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-3 text-right font-mono text-blue-600">
                                <?php if((float)$payment->bank_amount > 0): ?>
                                    ₹<?php echo e(number_format((float)$payment->bank_amount, 2)); ?>

                                <?php else: ?>
                                    <span class="text-zinc-300">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-3 text-right font-bold font-mono text-zinc-900">
                                ₹<?php echo e(number_format((float)$payment->amount, 2)); ?>

                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="px-8 py-6 text-center text-zinc-400 text-sm">
                <span class="material-symbols-rounded text-3xl block mb-2">payments</span>
                No payments recorded yet.
            </div>
        <?php endif; ?>

        
        <div class="px-8 py-4 border-t border-zinc-200 <?php echo e(($remainingDue ?? 0) <= 0 ? 'bg-emerald-50' : 'bg-amber-50'); ?>">
            <div class="flex justify-between items-center max-w-sm ml-auto space-y-0">
                <div class="grid grid-cols-2 gap-x-12 gap-y-1 w-full text-sm">
                    <span class="text-zinc-500">Total Invoice Amount</span>
                    <span class="text-right font-mono font-bold text-zinc-800">₹<?php echo e(number_format((float)($bill->net_amount ?? 0), 2)); ?></span>

                    <span class="text-zinc-500">Total Paid</span>
                    <span class="text-right font-mono font-bold text-emerald-600">₹<?php echo e(number_format($totalPaid ?? 0, 2)); ?></span>

                    <span class="font-bold <?php echo e(($remainingDue ?? 0) <= 0 ? 'text-emerald-700' : 'text-rose-600'); ?> text-base pt-2 border-t border-zinc-300">
                        <?php echo e(($remainingDue ?? 0) <= 0 ? '✅ Fully Paid' : '⏳ Remaining Due'); ?>

                    </span>
                    <span class="text-right font-black font-mono text-lg pt-2 border-t border-zinc-300 <?php echo e(($remainingDue ?? 0) <= 0 ? 'text-emerald-600' : 'text-rose-600'); ?>">
                        ₹<?php echo e(number_format($remainingDue ?? 0, 2)); ?>

                    </span>
                </div>
            </div>
        </div>
    </div>

    
    <div class="bg-white border border-zinc-200 rounded-b-2xl px-8 py-6 text-center">
        <p class="text-sm font-bold text-zinc-700 mb-1">Thank you for your business! 🙏</p>
        <p class="text-xs text-zinc-400">Please settle the payment within the weekly credit cycle.</p>

        
        <div class="mt-5 flex justify-center gap-3 no-print">
            <a href="<?php echo e(route('billing.weekly.pdf', $bill->id)); ?>"
               class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-md">
                <span class="material-symbols-rounded text-[18px]">download</span> Download PDF
            </a>
            <?php if(!$bill->is_approved ?? true): ?>
                <a href="<?php echo e(route('billing.weekly.whatsapp', $bill->id)); ?>"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white text-sm font-bold rounded-xl transition-all shadow-md"
                   target="_blank">
                    <span class="material-symbols-rounded text-[18px]">chat</span> WhatsApp
                </a>
            <?php endif; ?>
            <button onclick="window.print()"
                    class="inline-flex items-center gap-2 px-5 py-2.5 border border-zinc-300 bg-white text-zinc-700 text-sm font-bold rounded-xl hover:bg-zinc-50 transition-all">
                <span class="material-symbols-rounded text-[18px]">print</span> Print
            </button>
            <a href="<?php echo e(route('billing.weekly.index')); ?>"
               class="inline-flex items-center gap-2 px-5 py-2.5 border border-zinc-300 bg-white text-zinc-600 text-sm font-bold rounded-xl hover:bg-zinc-50 transition-all">
                <span class="material-symbols-rounded text-[18px]">arrow_back</span> Back
            </a>
        </div>
    </div>
</div>

<style>
@media print {
    .no-print, nav, aside, header, footer { display: none !important; }
    body { background-color: white !important; font-size: 12px; }
    #invoice-print {
        margin: 0 !important;
        max-width: none !important;
        box-shadow: none !important;
    }
    .rounded-t-2xl, .rounded-b-2xl { border-radius: 0 !important; }
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/billing/invoice.blade.php ENDPATH**/ ?>