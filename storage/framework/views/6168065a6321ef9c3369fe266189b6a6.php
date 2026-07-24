<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - <?php echo e($bill->invoice_no ?? ('INV-W-' . str_pad($bill->id, 4, '0', STR_PAD_LEFT))); ?></title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        outfit: ['Outfit', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Outfit', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .invoice-card {
                border: none !important;
                box-shadow: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-zinc-100 min-h-screen py-12 px-4">
    <div class="max-w-4xl mx-auto">

        
        <div class="flex justify-between items-center mb-6 no-print">
            <a href="<?php echo e(route('billing.weekly.index')); ?>"
               class="inline-flex items-center gap-2 px-4 py-2 border border-zinc-300 bg-white text-zinc-600 text-sm font-bold rounded-xl hover:bg-zinc-50 transition-all shadow-sm">
                <span class="material-symbols-rounded text-[18px]">arrow_back</span> Back to Bills
            </a>
            <div class="flex gap-3">
                <button onclick="window.print()"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-xl transition-all shadow-md">
                    <span class="material-symbols-rounded text-[18px]">print</span> Print
                </button>
                <a href="<?php echo e(route('billing.weekly.pdf', $bill->id)); ?>"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition-all shadow-md">
                    <span class="material-symbols-rounded text-[18px]">download</span> Download PDF
                </a>
                <?php if(!$bill->is_approved ?? true): ?>
                    <a href="<?php echo e(route('billing.weekly.whatsapp', $bill->id)); ?>"
                       class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-500 hover:bg-green-600 text-white text-sm font-bold rounded-xl transition-all shadow-md"
                       target="_blank">
                        <span class="material-symbols-rounded text-[18px]">chat</span> WhatsApp
                    </a>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="bg-white border border-zinc-200 shadow-sm rounded-2xl overflow-hidden invoice-card" id="invoice-print">

            
            <div class="px-8 pt-8 pb-6 border-b-2 border-zinc-900 flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-tight text-zinc-900">FlockWise<span class="text-emerald-600">BizTrack</span></h1>
                    <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest mt-1">Poultry Management Solutions</p>
                    
                    <div class="mt-6 text-xs text-zinc-500 space-y-1">
                        <p class="font-bold text-zinc-700 uppercase tracking-wider text-[10px]">Bill To</p>
                        <p class="text-sm font-bold text-zinc-900"><?php echo e($bill->dealer?->firm_name ?? 'N/A'); ?></p>
                        <p><?php echo e($bill->dealer?->location ?? ''); ?></p>
                        <p>📞 <?php echo e($bill->dealer?->phone ?? 'N/A'); ?></p>
                        <?php if($bill->dealer?->gst_number): ?>
                            <p>GSTIN: <span class="font-mono text-zinc-600"><?php echo e($bill->dealer->gst_number); ?></span></p>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 mb-3">
                        Weekly Invoice
                    </span>
                    <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider">Invoice No</p>
                    <p class="text-xl font-black font-mono text-zinc-900"><?php echo e($bill->invoice_no ?? ('INV-W-' . str_pad($bill->id, 4, '0', STR_PAD_LEFT))); ?></p>
                    <span class="inline-block mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                        <?php echo e($bill->status === 'Paid' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'); ?>">
                        <?php echo e($bill->status); ?>

                    </span>

                    <div class="mt-4 text-xs text-zinc-500 space-y-1">
                        <p><span class="font-bold text-zinc-700">Period:</span> <?php echo e($bill->period_start?->format('d M Y')); ?> — <?php echo e($bill->period_end?->format('d M Y')); ?></p>
                        <p><span class="font-bold text-zinc-700">Generated:</span> <?php echo e(now()->format('d M Y')); ?></p>
                        <p><span class="font-bold text-zinc-700">Payment Mode:</span> <?php echo e($bill->payment_mode ?? 'Credit'); ?></p>
                    </div>
                </div>
            </div>

            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 px-8 py-6 bg-zinc-50/50 border-b border-zinc-200">
                <div class="p-4 rounded-xl border border-blue-200 bg-blue-50/30 text-center">
                    <p class="text-[9px] font-bold text-blue-600 uppercase tracking-wider mb-1">Previous Outstanding</p>
                    <p class="text-lg font-black font-mono text-blue-800">₹<?php echo e(number_format((float)($bill->previous_outstanding ?? 0), 2)); ?></p>
                </div>
                <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50/30 text-center">
                    <p class="text-[9px] font-bold text-emerald-600 uppercase tracking-wider mb-1">This Week's Day-Load</p>
                    <p class="text-lg font-black font-mono text-emerald-800">+ ₹<?php echo e(number_format($dayLoadTotal ?? $bill->amount, 2)); ?></p>
                </div>
                <div class="p-4 rounded-xl border border-amber-200 bg-amber-50/30 text-center">
                    <p class="text-[9px] font-bold text-amber-600 uppercase tracking-wider mb-1">Payments During Week</p>
                    <p class="text-lg font-black font-mono text-amber-800">- ₹<?php echo e(number_format((float)($bill->payments_during_week ?? 0), 2)); ?></p>
                </div>
                <div class="p-4 rounded-xl border border-purple-200 bg-purple-50/30 text-center">
                    <p class="text-[9px] font-bold text-purple-600 uppercase tracking-wider mb-1">Net Invoice Amount</p>
                    <p class="text-lg font-black font-mono text-purple-800">₹<?php echo e(number_format((float)($bill->net_amount ?? $bill->amount), 2)); ?></p>
                </div>
            </div>

            
            <div>
                <div class="px-8 py-3 bg-zinc-50 border-b border-zinc-200 flex items-center gap-2">
                    <span class="material-symbols-rounded text-zinc-500 text-[18px]">local_shipping</span>
                    <h3 class="font-bold text-zinc-700 text-xs uppercase tracking-wider">Day-Load Entries</h3>
                    <?php if(isset($dayLoadEntries) && $dayLoadEntries->isNotEmpty()): ?>
                        <span class="ml-auto text-[10px] font-bold text-zinc-500 bg-zinc-200/50 px-2 py-0.5 rounded-full">
                            <?php echo e($dayLoadEntries->count()); ?> entries | <?php echo e(number_format($dayLoadEntries->sum('bird_weight'), 2)); ?> kg total
                        </span>
                    <?php endif; ?>
                </div>

                <?php if(isset($dayLoadEntries) && $dayLoadEntries->isNotEmpty()): ?>
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest bg-zinc-50/50 border-b border-zinc-200">
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Vendor</th>
                                <th class="px-6 py-3 text-right">Weight (kg)</th>
                                <th class="px-6 py-3 text-right">Customer Rate</th>
                                <th class="px-6 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-150">
                            <?php $__currentLoopData = $dayLoadEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $kg    = (float) $entry->bird_weight;
                                    $rate  = (float) $entry->customer_rate;
                                    $total = round($kg * $rate, 2);
                                ?>
                                <tr class="hover:bg-zinc-50/50 transition-colors">
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
                            <tr class="bg-zinc-50/50 border-t border-zinc-200">
                                <td colspan="2" class="px-6 py-3 text-xs font-bold text-zinc-500 uppercase tracking-wider">
                                    Day-Load Total
                                </td>
                                <td class="px-6 py-3 text-right font-mono font-bold text-zinc-700">
                                    <?php echo e(number_format($dayLoadEntries->sum('bird_weight'), 2)); ?> kg
                                </td>
                                <td></td>
                                <td class="px-6 py-3 text-right font-black font-mono text-emerald-600 text-base">
                                    ₹<?php echo e(number_format($dayLoadTotal ?? 0, 2)); ?>

                                </td>
                            </tr>
                        </tfoot>
                    </table>
                <?php else: ?>
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest bg-zinc-50/50 border-b border-zinc-200">
                                <th class="px-6 py-3">Description</th>
                                <th class="px-6 py-3 text-right">Qty (kg)</th>
                                <th class="px-6 py-3 text-right">Rate</th>
                                <th class="px-6 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-150">
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

            
            <div>
                <div class="px-8 py-3 bg-zinc-50 border-y border-zinc-200 flex items-center gap-2">
                    <span class="material-symbols-rounded text-zinc-500 text-[18px]">calendar_month</span>
                    <h3 class="font-bold text-zinc-700 text-xs uppercase tracking-wider">Payment Schedule (Monday/Friday Split)</h3>
                </div>
                <div class="grid grid-cols-2 gap-4 p-6 bg-white">
                    
                    <div class="p-4 rounded-xl border <?php echo e(($bill->monday_payment_status ?? '') === 'Paid' ? 'border-emerald-200 bg-emerald-50/20' : 'border-zinc-200 bg-zinc-50/20'); ?>">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-base"><?php echo e(($bill->monday_payment_status ?? '') === 'Paid' ? '✅' : '⏳'); ?></span>
                            <span class="text-[10px] font-bold uppercase tracking-wider <?php echo e(($bill->monday_payment_status ?? '') === 'Paid' ? 'text-emerald-700' : 'text-zinc-600'); ?>">
                                Monday Split (50%)
                            </span>
                        </div>
                        <p class="text-lg font-black font-mono <?php echo e(($bill->monday_payment_status ?? '') === 'Paid' ? 'text-emerald-700' : 'text-zinc-800'); ?>">
                            ₹<?php echo e(number_format((float)($bill->monday_payment_amount ?? 0), 2)); ?>

                        </p>
                        <p class="text-[10px] mt-1 <?php echo e(($bill->monday_payment_status ?? '') === 'Paid' ? 'text-emerald-600' : 'text-zinc-500'); ?>">
                            Status: <strong><?php echo e($bill->monday_payment_status ?? 'Unpaid'); ?></strong>
                        </p>
                    </div>
                    
                    <div class="p-4 rounded-xl border <?php echo e(($bill->friday_payment_status ?? '') === 'Paid' ? 'border-emerald-200 bg-emerald-50/20' : 'border-zinc-200 bg-zinc-50/20'); ?>">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-base"><?php echo e(($bill->friday_payment_status ?? '') === 'Paid' ? '✅' : '⏳'); ?></span>
                            <span class="text-[10px] font-bold uppercase tracking-wider <?php echo e(($bill->friday_payment_status ?? '') === 'Paid' ? 'text-emerald-700' : 'text-zinc-600'); ?>">
                                Friday Split (50%)
                            </span>
                        </div>
                        <p class="text-lg font-black font-mono <?php echo e(($bill->friday_payment_status ?? '') === 'Paid' ? 'text-emerald-700' : 'text-zinc-800'); ?>">
                            ₹<?php echo e(number_format((float)($bill->friday_payment_amount ?? 0), 2)); ?>

                        </p>
                        <p class="text-[10px] mt-1 <?php echo e(($bill->friday_payment_status ?? '') === 'Paid' ? 'text-emerald-600' : 'text-zinc-500'); ?>">
                            Status: <strong><?php echo e($bill->friday_payment_status ?? 'Unpaid'); ?></strong>
                        </p>
                    </div>
                </div>
            </div>

            
            <div class="border-t border-zinc-200">
                <div class="px-8 py-3 bg-zinc-50 border-b border-zinc-200 flex items-center gap-2">
                    <span class="material-symbols-rounded text-zinc-500 text-[18px]">receipt_long</span>
                    <h3 class="font-bold text-zinc-700 text-xs uppercase tracking-wider">Payment History</h3>
                    <span class="ml-auto text-[10px] font-bold text-zinc-500 bg-zinc-200/50 px-2 py-0.5 rounded-full">
                        Total Paid: ₹<?php echo e(number_format($totalPaid ?? 0, 2)); ?>

                    </span>
                </div>

                <?php if(isset($allPayments) && $allPayments->isNotEmpty()): ?>
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest bg-zinc-50/50 border-b border-zinc-200">
                                <th class="px-6 py-3">Date</th>
                                <th class="px-6 py-3">Description</th>
                                <th class="px-6 py-3 text-center">Mode</th>
                                <th class="px-6 py-3 text-right">Cash</th>
                                <th class="px-6 py-3 text-right">Bank</th>
                                <th class="px-6 py-3 text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-zinc-150">
                            <?php $__currentLoopData = $allPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $desc = $payment->notes ?? 'Payment';
                                    if (str_contains(strtolower($desc), 'monday') || str_contains(strtolower($desc), 'monday split')) {
                                        $desc = '🗓️ Monday Split Payment';
                                    } elseif (str_contains(strtolower($desc), 'friday') || str_contains(strtolower($desc), 'friday split')) {
                                        $desc = '🗓️ Friday Split Payment';
                                    } elseif (str_contains(strtolower($desc), 'allocated') || str_contains(strtolower($desc), 'auto-allocated')) {
                                        $desc = '💰 Day-Load Payment';
                                    } else {
                                        $desc = '💳 ' . ($payment->notes ?? 'Ledger Payment');
                                    }
                                ?>
                                <tr class="hover:bg-zinc-50/50 transition-colors text-xs">
                                    <td class="px-6 py-3 font-medium text-zinc-800">
                                        <?php echo e($payment->date?->format('d M Y')); ?>

                                        <span class="block text-[9px] text-zinc-400"><?php echo e($payment->date?->format('l')); ?></span>
                                    </td>
                                    <td class="px-6 py-3 text-zinc-600 text-xs"><?php echo e($desc); ?></td>
                                    <td class="px-6 py-3 text-center">
                                        <span class="inline-block px-2.5 py-0.5 rounded-full text-[9px] font-bold <?php echo e($payment->payment_mode === 'Cash' ? 'bg-zinc-100 text-zinc-700 border border-zinc-200' : 'bg-blue-50 text-blue-700 border border-blue-100'); ?>">
                                            <?php echo e($payment->payment_mode); ?>

                                            <?php if($payment->bank_transfer_type): ?>
                                                (<?php echo e($payment->bank_transfer_type); ?>)
                                            <?php endif; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-right font-mono text-zinc-600">
                                        <?php echo e((float)$payment->cash_amount > 0 ? '₹' . number_format((float)$payment->cash_amount, 2) : '—'); ?>

                                    </td>
                                    <td class="px-6 py-3 text-right font-mono text-zinc-600">
                                        <?php echo e((float)$payment->bank_amount > 0 ? '₹' . number_format((float)$payment->bank_amount, 2) : '—'); ?>

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

                
                <div class="px-8 py-4 bg-zinc-50 border-t border-zinc-200">
                    <div class="flex justify-between items-center max-w-sm ml-auto">
                        <div class="grid grid-cols-2 gap-x-12 gap-y-1 w-full text-xs">
                            <span class="text-zinc-500">Total Invoice Amount</span>
                            <span class="text-right font-mono font-bold text-zinc-800">₹<?php echo e(number_format((float)($bill->net_amount ?? 0), 2)); ?></span>

                            <span class="text-zinc-500">Total Paid</span>
                            <span class="text-right font-mono font-bold text-emerald-600">₹<?php echo e(number_format($totalPaid ?? 0, 2)); ?></span>

                            <span class="font-bold <?php echo e(($remainingDue ?? 0) <= 0 ? 'text-emerald-700' : 'text-rose-600'); ?> text-sm pt-2 border-t border-zinc-200">
                                <?php echo e(($remainingDue ?? 0) <= 0 ? '✅ Fully Paid' : '⏳ Remaining Due'); ?>

                            </span>
                            <span class="text-right font-black font-mono text-base pt-2 border-t border-zinc-200 <?php echo e(($remainingDue ?? 0) <= 0 ? 'text-emerald-600' : 'text-rose-600'); ?>">
                                ₹<?php echo e(number_format($remainingDue ?? 0, 2)); ?>

                            </span>
                        </div>
                    </div>
                </div>
            </div>

            
            <div class="px-8 py-6 text-center border-t border-zinc-200">
                <p class="text-sm font-bold text-zinc-700 mb-1">Thank you for your business! 🙏</p>
                <p class="text-xs text-zinc-400">Please settle the payment within the weekly credit cycle.</p>
            </div>

        </div>

    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\billing\invoice.blade.php ENDPATH**/ ?>