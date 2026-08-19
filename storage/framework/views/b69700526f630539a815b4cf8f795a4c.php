<?php $__env->startSection('title', 'Record Dealer Payout'); ?>

<?php $__env->startSection('content'); ?>
<div class="animate-fade-in max-w-4xl mx-auto">
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Record Payout','subtitle' => 'Enter payment details to clear supplier dues']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Record Payout','subtitle' => 'Enter payment details to clear supplier dues']); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'outline','href' => ''.e(route('payments.dealers.index')).'','icon' => 'arrow_back']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','href' => ''.e(route('payments.dealers.index')).'','icon' => 'arrow_back']); ?>
                Back to Payouts
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
         <?php $__env->endSlot(); ?>
     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $attributes = $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e)): ?>
<?php $component = $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e; ?>
<?php unset($__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e); ?>
<?php endif; ?>

    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/60 dark:border-zinc-800/60 shadow-[0_8px_32px_rgba(0,0,0,0.04)] rounded-3xl overflow-hidden p-6 sm:p-10">
        
        <?php if($errors->any()): ?>
            <div class="p-4 mb-6 rounded-2xl bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-850/30 text-red-600 dark:text-red-400 text-sm">
                <p class="font-bold mb-1">Please fix the following errors:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?php echo e(route('payments.dealers.store')); ?>" method="POST" class="space-y-8" x-data="{ cashAmount: 0, bankAmount: 0, paymentMode: 'Cash', bankTransferType: '' }">
            <?php echo csrf_field(); ?>
            
            
            <section class="space-y-4">
                <div class="flex items-center gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="h-10 w-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <span class="material-symbols-rounded">storefront</span>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 tracking-tight font-cabinet">Supplier Details</h3>
                </div>

                <div class="p-6 bg-zinc-50 dark:bg-zinc-800/40 rounded-2xl border border-zinc-200/60 dark:border-zinc-700/60 transition-all hover:border-blue-500/30">
                    <?php if($selected_dealer_id && $dealers->count() === 1): ?>
                        <?php $d = $dealers->first(); ?>
                        <input type="hidden" name="dealer_id" value="<?php echo e($d->id); ?>">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div>
                                <span class="block text-xs font-bold text-zinc-500 uppercase mb-1">Dealer</span>
                                <div class="text-xl font-black text-zinc-900 dark:text-white">
                                    <?php echo e($d->firm_name); ?>

                                </div>
                            </div>
                            <div class="text-left sm:text-right">
                                <span class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Net Outstanding Balance</span>
                                <div class="text-xl font-cabinet font-black text-rose-600 dark:text-rose-400">
                                    <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $d->displayed_outstanding]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($d->displayed_outstanding)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $attributes = $__attributesOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__attributesOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $component = $__componentOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__componentOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?>
                                </div>
                            </div>
                        </div>

                        
                        <div class="mt-4 pt-3 border-t border-zinc-200/60 dark:border-zinc-700/60 flex flex-wrap items-center justify-between gap-2 text-xs">
                            <div class="flex flex-wrap items-center gap-3 text-zinc-600 dark:text-zinc-300 font-semibold">
                                <span class="inline-flex items-center gap-1 bg-zinc-200/60 dark:bg-zinc-700/60 px-2.5 py-1 rounded-lg">
                                    📋 Day-Load Dues: <strong>Rs <?php echo e(number_format($dayLoadEntriesTotal ?? 0, 2)); ?></strong>
                                </span>
                                <?php if(($d->pending_amount ?? 0) > 0): ?>
                                    <span class="inline-flex items-center gap-1 bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 px-2.5 py-1 rounded-lg">
                                        ➕ Initial Balance: <strong>Rs <?php echo e(number_format($d->pending_amount, 2)); ?></strong>
                                    </span>
                                <?php endif; ?>
                                <?php if(($pendingEmisTotal ?? 0) > 0): ?>
                                    <span class="inline-flex items-center gap-1 bg-purple-100 dark:bg-purple-950/60 text-purple-800 dark:text-purple-300 px-2.5 py-1 rounded-lg">
                                        ⏳ Pending Dealer EMIs (<?php echo e($pendingEmis->count()); ?>): <strong>Rs <?php echo e(number_format($pendingEmisTotal, 2)); ?></strong>
                                    </span>
                                <?php endif; ?>
                                <?php if(($unallocatedPayments ?? 0) > 0): ?>
                                    <span class="inline-flex items-center gap-1 bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 px-2.5 py-1 rounded-lg">
                                        ➖ General Advance Collected: <strong>Rs <?php echo e(number_format($unallocatedPayments, 2)); ?></strong>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php if (isset($component)) { $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.select','data' => ['name' => 'dealer_id','label' => 'Select Dealer','required' => true,'onchange' => 'if(this.value) window.location.href=\''.e(route('payments.dealers.create')).'?dealer_id=\'+this.value']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'dealer_id','label' => 'Select Dealer','required' => true,'onchange' => 'if(this.value) window.location.href=\''.e(route('payments.dealers.create')).'?dealer_id=\'+this.value']); ?>
                            <option value="">Choose dealer…</option>
                            <?php $__currentLoopData = $dealers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($d->id); ?>" <?php echo e($selected_dealer_id == $d->id ? 'selected' : ''); ?>>
                                <?php echo e($d->firm_name); ?> — Pending: Rs <?php echo e(number_format($d->displayed_outstanding, 0)); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36)): ?>
<?php $attributes = $__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36; ?>
<?php unset($__attributesOriginal8cee41e4af1fe2df52d1d5acd06eed36); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36)): ?>
<?php $component = $__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36; ?>
<?php unset($__componentOriginal8cee41e4af1fe2df52d1d5acd06eed36); ?>
<?php endif; ?>
                    <?php endif; ?>
                </div>
            </section>

            <?php if($selected_dealer_id): ?>
                
                <section class="space-y-4">
                    <div class="flex items-center gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                        <div class="h-10 w-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                            <span class="material-symbols-rounded">receipt_long</span>
                        </div>
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 tracking-tight font-cabinet">
                            Day-Load Outstanding Dues
                        </h3>
                    </div>

                    <div class="p-6 bg-zinc-50 dark:bg-zinc-800/40 rounded-2xl border border-zinc-200/60 dark:border-zinc-700/60 overflow-x-auto">
                            <table class="w-full text-left text-sm font-outfit">
                                <thead>
                                    <tr class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest bg-zinc-100/50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
                                        <th class="px-4 py-3 w-12 text-center">Select</th>
                                        <th class="px-4 py-3">Date</th>
                                        <th class="px-4 py-3">Vendor</th>
                                        <th class="px-4 py-3 text-right">Weight (kg)</th>
                                        <th class="px-4 py-3 text-right">Customer Rate</th>
                                        <th class="px-4 py-3 text-right">Total Dues</th>
                                        <th class="px-4 py-3 text-right">Collected</th>
                                        <th class="px-4 py-3 text-right text-blue-600 dark:text-blue-400 font-bold">Dues Remaining</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900/50">
                                    <?php $__empty_1 = true; $__currentLoopData = $dayLoadEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php
                                            $dueRemaining = round((float)$entry->amount - (float)$entry->dealer_collected, 2);
                                        ?>
                                        <tr class="hover:bg-zinc-50/50 transition-colors">
                                            <td class="px-4 py-4 text-center">
                                                <input type="checkbox" name="selected_entry_ids[]" value="<?php echo e($entry->id); ?>" data-remaining="<?php echo e($dueRemaining); ?>" onchange="updateSelectedDuesTotal()" class="day-load-checkbox rounded border-zinc-300 dark:border-zinc-700 text-blue-600 focus:ring-blue-500 bg-white dark:bg-zinc-900">
                                            </td>
                                            <td class="px-4 py-4 font-medium text-zinc-900 dark:text-zinc-100">
                                                <?php echo e($entry->batch?->billing_date?->format('d M Y') ?? '—'); ?>

                                                <span class="block text-[10px] text-zinc-400"><?php echo e($entry->batch?->billing_date?->format('l')); ?></span>
                                            </td>
                                            <td class="px-4 py-4 text-zinc-600 dark:text-zinc-400">
                                                <?php echo e($entry->vendor?->firm_name ?? '—'); ?>

                                            </td>
                                            <td class="px-4 py-4 text-right font-mono text-zinc-700 dark:text-zinc-300">
                                                <?php echo e(number_format($entry->bird_weight, 2)); ?> kg
                                            </td>
                                            <td class="px-4 py-4 text-right font-mono text-zinc-700 dark:text-zinc-300">
                                                ₹<?php echo e(number_format($entry->customer_rate, 2)); ?>

                                            </td>
                                            <td class="px-4 py-4 text-right font-bold font-mono text-zinc-900 dark:text-zinc-100">
                                                ₹<?php echo e(number_format($entry->amount, 2)); ?>

                                            </td>
                                            <td class="px-4 py-4 text-right font-mono text-zinc-500">
                                                ₹<?php echo e(number_format($entry->dealer_collected, 2)); ?>

                                            </td>
                                            <td class="px-4 py-4 text-right font-bold font-mono text-blue-600 dark:text-blue-400">
                                                ₹<?php echo e(number_format($dueRemaining, 2)); ?>

                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr>
                                            <td colspan="8" class="px-6 py-10 text-center text-zinc-500 dark:text-zinc-400">
                                                No unbilled day-load entries found for this dealer.
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                            <?php if($dayLoadEntries->isNotEmpty()): ?>
                                <div class="flex justify-between items-center mt-4 pt-4 border-t border-zinc-200 dark:border-zinc-700">
                                    <span class="text-xs font-bold text-zinc-500 uppercase">Selected Entries Dues:</span>
                                    <span id="selected-dues-display" class="font-jetbrains font-black text-lg text-blue-600 dark:text-blue-400">₹0.00</span>
                                </div>
                            <?php endif; ?>
                        </div>
                </section>

                
                <?php if($pendingEmis->isNotEmpty()): ?>
                    <section class="space-y-4">
                        <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-3">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                                    <span class="material-symbols-rounded">schedule</span>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 tracking-tight font-cabinet">
                                        Pending Dealer EMI Installments
                                    </h3>
                                    <p class="text-xs text-zinc-500">Upcoming and overdue EMI schedule for this dealer</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold font-mono px-3 py-1 bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 rounded-full">
                                Total EMI Dues: ₹<?php echo e(number_format($pendingEmisTotal, 2)); ?>

                            </span>
                        </div>

                        <div class="p-6 bg-zinc-50 dark:bg-zinc-800/40 rounded-2xl border border-zinc-200/60 dark:border-zinc-700/60 overflow-x-auto">
                            <table class="w-full text-left text-sm font-outfit">
                                <thead>
                                    <tr class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest bg-zinc-100/50 dark:bg-zinc-800 border-b border-zinc-200 dark:border-zinc-700">
                                        <th class="px-4 py-3 w-12 text-center">Select</th>
                                        <th class="px-4 py-3">EMI / Loan Name</th>
                                        <th class="px-4 py-3">Due Date</th>
                                        <th class="px-4 py-3">Status</th>
                                        <th class="px-4 py-3 text-right">Installment Amount</th>
                                        <th class="px-4 py-3 text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700 bg-white dark:bg-zinc-900/50">
                                    <?php $__currentLoopData = $pendingEmis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr class="hover:bg-zinc-50/50 transition-colors">
                                            <td class="px-4 py-4 text-center">
                                                <input type="checkbox" name="selected_emi_ids[]" value="<?php echo e($emi->id); ?>" data-amount="<?php echo e($emi->remaining_amount); ?>" onchange="updateSelectedEmisTotal()" class="emi-checkbox rounded border-purple-300 dark:border-purple-700 text-purple-600 focus:ring-purple-500 bg-white dark:bg-zinc-900 w-4 h-4">
                                            </td>
                                            <td class="px-4 py-4 font-semibold text-zinc-900 dark:text-zinc-100">
                                                <?php echo e($emi->loan_name ?: 'Dealer EMI'); ?>

                                            </td>
                                            <td class="px-4 py-4 text-zinc-600 dark:text-zinc-400">
                                                <?php echo e($emi->due_date?->format('d M Y')); ?>

                                                <span class="block text-[10px] text-zinc-400"><?php echo e($emi->due_date?->diffForHumans()); ?></span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <?php if($emi->status === 'Overdue' || ($emi->due_date && $emi->due_date->isPast())): ?>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-rose-100 text-rose-700 dark:bg-rose-950/60 dark:text-rose-400">
                                                        Overdue
                                                    </span>
                                                <?php elseif($emi->status === 'Partial'): ?>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-blue-100 text-blue-700 dark:bg-blue-950/60 dark:text-blue-400">
                                                        Partial
                                                    </span>
                                                <?php else: ?>
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-700 dark:bg-amber-950/60 dark:text-amber-400">
                                                        Upcoming
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-4 text-right">
                                                <div class="font-bold font-mono text-purple-600 dark:text-purple-400">
                                                    ₹<?php echo e(number_format($emi->remaining_amount, 2)); ?>

                                                </div>
                                                <?php if($emi->paid_amount > 0): ?>
                                                    <div class="text-[10px] text-zinc-500 font-medium mt-0.5">
                                                        of ₹<?php echo e(number_format($emi->amount, 2)); ?> (₹<?php echo e(number_format($emi->paid_amount, 2)); ?> paid)
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="px-4 py-4 text-center">
                                                <button type="button" 
                                                        onclick="selectEmiAndFill('<?php echo e($emi->id); ?>')"
                                                        class="px-3 py-1.5 text-xs font-bold text-purple-600 hover:text-white bg-purple-50 hover:bg-purple-600 rounded-xl transition-all border border-purple-200 shadow-2xs">
                                                    Select & Pay
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endif; ?>

            
            <?php if($selected_dealer_id && $pendingDayLoadCount > 0): ?>
                <div class="p-4 rounded-2xl border border-amber-200 bg-amber-50 dark:border-amber-800/50 dark:bg-amber-900/20 flex items-start gap-3">
                    <span class="material-symbols-rounded text-amber-600 dark:text-amber-400 text-[20px] mt-0.5">warning</span>
                    <div>
                        <p class="text-sm font-bold text-amber-800 dark:text-amber-300">
                            This dealer has <?php echo e($pendingDayLoadCount); ?> unpaid day-load <?php echo e(Str::plural('entry', $pendingDayLoadCount)); ?>

                        </p>
                        <p class="text-xs text-amber-700 dark:text-amber-400 mt-1">
                            Consider recording payment from the day-load billing page for proper allocation across entries.
                        </p>
                    </div>
                </div>
            <?php endif; ?>

            
            <section class="space-y-4">
                <div class="flex items-center gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="h-10 w-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <span class="material-symbols-rounded">account_balance_wallet</span>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 tracking-tight font-cabinet">Payout Information</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-blue-50/50 dark:bg-blue-900/10 rounded-2xl border border-blue-100 dark:border-blue-800/30 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Cash Amount (Rs) <span class="text-rose-500">*</span></label>
                        <input type="number" name="cash_amount" required step="0.01" min="0" x-model.number="cashAmount" onwheel="this.blur()" class="block w-full rounded-xl border-blue-200 dark:border-blue-800 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-zinc-900 text-2xl font-black text-zinc-800 dark:text-white shadow-sm py-3 px-4 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Bank Amount (Rs) <span class="text-rose-500">*</span></label>
                        <input type="number" name="bank_amount" required step="0.01" min="0" x-model.number="bankAmount" onwheel="this.blur()" class="block w-full rounded-xl border-blue-200 dark:border-blue-800 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-zinc-900 text-2xl font-black text-zinc-800 dark:text-white shadow-sm py-3 px-4 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Total Amount (Rs)</label>
                        <div class="rounded-xl border border-blue-200 dark:border-blue-800 bg-white dark:bg-zinc-900 text-2xl font-black text-blue-600 dark:text-blue-400 py-3 px-4 shadow-sm" x-text="'Rs ' + (cashAmount + bankAmount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['type' => 'date','name' => 'date','label' => 'Payment Date','required' => true,'value' => ''.e(date('Y-m-d')).'','class' => '!bg-white dark:!bg-zinc-900 shadow-sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'date','name' => 'date','label' => 'Payment Date','required' => true,'value' => ''.e(date('Y-m-d')).'','class' => '!bg-white dark:!bg-zinc-900 shadow-sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b)): ?>
<?php $attributes = $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b; ?>
<?php unset($__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5c2a97ab476b69c1189ee85d1a95204b)): ?>
<?php $component = $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b; ?>
<?php unset($__componentOriginal5c2a97ab476b69c1189ee85d1a95204b); ?>
<?php endif; ?>
                </div>
            </section>

            
            <section class="space-y-4">
                <div class="flex items-center gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="h-10 w-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <span class="material-symbols-rounded">receipt_long</span>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 tracking-tight font-cabinet">Transaction Details</h3>
                </div>

                <div class="p-6 bg-zinc-50 dark:bg-zinc-800/40 rounded-2xl border border-zinc-200/60 dark:border-zinc-700/60">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <input type="hidden" name="payment_mode" :value="bankAmount > 0 ? 'Bank Transfer' : 'Cash'">
                        <div x-show="bankAmount > 0" x-transition class="md:col-span-2">
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2 font-cabinet tracking-wide uppercase">Bank Transfer Type <span class="text-rose-500">*</span></label>
                            <select name="bank_transfer_type" x-model="bankTransferType" :required="bankAmount > 0" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm shadow-sm">
                                <option value="">Select type...</option>
                                <option value="UPI">UPI</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="NEFT">NEFT</option>
                                <option value="RTGS">RTGS</option>
                                <option value="IMPS">IMPS</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['name' => 'notes','label' => 'Remarks / Reference','placeholder' => 'e.g. UPI Transaction ID or Cheque Number...','class' => '!bg-white dark:!bg-zinc-900 shadow-sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'notes','label' => 'Remarks / Reference','placeholder' => 'e.g. UPI Transaction ID or Cheque Number...','class' => '!bg-white dark:!bg-zinc-900 shadow-sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b)): ?>
<?php $attributes = $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b; ?>
<?php unset($__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal5c2a97ab476b69c1189ee85d1a95204b)): ?>
<?php $component = $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b; ?>
<?php unset($__componentOriginal5c2a97ab476b69c1189ee85d1a95204b); ?>
<?php endif; ?>
                </div>
            </section>

            <div class="flex items-center justify-end gap-4 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'button','variant' => 'outline','href' => ''.e(route('payments.dealers.index')).'','class' => 'hover:bg-zinc-100']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'outline','href' => ''.e(route('payments.dealers.index')).'','class' => 'hover:bg-zinc-100']); ?>Cancel <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','variant' => 'primary','icon' => 'check_circle','size' => 'lg','class' => 'shadow-xl shadow-blue-500/20 px-8 !bg-blue-600 hover:!bg-blue-700']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','icon' => 'check_circle','size' => 'lg','class' => 'shadow-xl shadow-blue-500/20 px-8 !bg-blue-600 hover:!bg-blue-700']); ?>Confirm & Record <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            </div>
            
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Enforce exclusive selection: Only ONE checkbox can be checked at a time
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('day-load-checkbox') || e.target.classList.contains('emi-checkbox')) {
            if (e.target.checked) {
                document.querySelectorAll('.day-load-checkbox, .emi-checkbox').forEach(cb => {
                    if (cb !== e.target) {
                        cb.checked = false;
                    }
                });
            }
            recalculateTotalPayment();
        }
    });

    function selectEmiAndFill(emiId) {
        const cb = document.querySelector(`.emi-checkbox[value="${emiId}"]`);
        if (cb) {
            cb.checked = true;
            // Manually trigger the change event to enforce exclusivity
            cb.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }

    // Keep aliases for inline onchange handlers in the HTML (if any remain)
    function updateSelectedDuesTotal() { recalculateTotalPayment(); }
    function updateSelectedEmisTotal() { recalculateTotalPayment(); }

    function recalculateTotalPayment() {
        let total = 0;
        
        // Sum day-load (will be max 1)
        const checkedDayLoad = document.querySelector('.day-load-checkbox:checked');
        if (checkedDayLoad) {
            total += parseFloat(checkedDayLoad.getAttribute('data-remaining')) || 0;
        }

        // Update "Selected Entries Dues" display if it exists
        const duesDisplay = document.getElementById('selected-dues-display');
        if (duesDisplay) {
            const dayLoadAmount = checkedDayLoad ? parseFloat(checkedDayLoad.getAttribute('data-remaining')) || 0 : 0;
            duesDisplay.textContent = '₹' + dayLoadAmount.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }

        // Sum EMI (will be max 1)
        const checkedEmi = document.querySelector('.emi-checkbox:checked');
        if (checkedEmi) {
            total += parseFloat(checkedEmi.getAttribute('data-amount')) || 0;
        }

        // Fill into Alpine cash amount
        const formEl = document.querySelector('form');
        if (formEl && window.Alpine) {
            const alpineData = window.Alpine.$data(formEl);
            if (alpineData) {
                alpineData.cashAmount = total;
                alpineData.bankAmount = 0;
            }
        }
    }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/payments/dealers/create.blade.php ENDPATH**/ ?>