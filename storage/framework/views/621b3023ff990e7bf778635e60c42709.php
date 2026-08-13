<?php $__env->startSection('title', 'Dealer Daily Billing System'); ?>

<?php $__env->startSection('content'); ?>
<div class="animate-fade-in" x-data="{ activeTab: 'invoices' }">
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Dealer Daily Billing','subtitle' => 'Generate daily invoices from dealer day-loads, calculate balances, and manage payments']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Dealer Daily Billing','subtitle' => 'Generate daily invoices from dealer day-loads, calculate balances, and manage payments']); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'outline','href' => ''.e(route('billing.daily.export')).'','icon' => 'download']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','href' => ''.e(route('billing.daily.export')).'','icon' => 'download']); ?>
                Export Log
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
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'outline','href' => ''.e(route('billing.day-load.index')).'','icon' => 'local_shipping']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','href' => ''.e(route('billing.day-load.index')).'','icon' => 'local_shipping']); ?>
                Day Load Billing
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

    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Daily Invoices','value' => ''.e($bills->total()).'','icon' => 'receipt','color' => 'violet']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Daily Invoices','value' => ''.e($bills->total()).'','icon' => 'receipt','color' => 'violet']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
        <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => 'Pending Dues','value' => 'Rs '.e(number_format($outstandingDuesTotal, 0)).'','icon' => 'pending_actions','color' => 'amber']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => 'Pending Dues','value' => 'Rs '.e(number_format($outstandingDuesTotal, 0)).'','icon' => 'pending_actions','color' => 'amber']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $attributes = $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682)): ?>
<?php $component = $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682; ?>
<?php unset($__componentOriginal527fae77f4db36afc8c8b7e9f5f81682); ?>
<?php endif; ?>
        <div class="rounded-2xl bg-gradient-to-br from-violet-500 to-violet-600 dark:from-violet-600 dark:to-violet-800 p-6 shadow-sm text-white flex items-center justify-between transition-all duration-300 hover:-translate-y-1 hover:shadow-lg hover:shadow-violet-500/20">
            <div>
                <p class="font-outfit text-sm font-medium text-violet-100">Total Revenue</p>
                <p class="font-jetbrains mt-2 text-3xl font-bold tracking-tight">Rs <?php echo e(number_format($paidRevenueTotal, 0)); ?></p>
            </div>
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-white/20 backdrop-blur-sm">
                <span class="material-symbols-rounded text-2xl">account_balance_wallet</span>
            </div>
        </div>
    </div>

    
    <div class="border-b border-zinc-200 dark:border-zinc-800 mb-8 flex flex-wrap gap-2">
        <button @click="activeTab = 'invoices'" :class="activeTab === 'invoices' ? 'border-violet-600 text-violet-600 dark:border-violet-400 dark:text-violet-400' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-white'" class="px-5 py-3 text-sm font-bold border-b-2 transition-colors duration-200 focus:outline-none flex items-center gap-2">
            <span class="material-symbols-rounded text-lg">receipt</span>
            Daily Invoices
        </button>
        <button @click="activeTab = 'purchase_log'" :class="activeTab === 'purchase_log' ? 'border-violet-600 text-violet-600 dark:border-violet-400 dark:text-violet-400' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-white'" class="px-5 py-3 text-sm font-bold border-b-2 transition-colors duration-200 focus:outline-none flex items-center gap-2">
            <span class="material-symbols-rounded text-lg">history</span>
            Day Load Log
        </button>
        <button @click="activeTab = 'generate_invoice'" :class="activeTab === 'generate_invoice' ? 'border-violet-600 text-violet-600 dark:border-violet-400 dark:text-violet-400' : 'border-transparent text-zinc-500 hover:text-zinc-900 dark:hover:text-white'" class="px-5 py-3 text-sm font-bold border-b-2 transition-colors duration-200 focus:outline-none flex items-center gap-2">
            <span class="material-symbols-rounded text-lg">calculate</span>
            Generate Daily Bill
        </button>
    </div>

    
    <div x-show="activeTab === 'invoices'" class="space-y-6">
        <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
            <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">Daily Invoice Log</h2>
                <form method="GET" class="relative max-w-sm w-full sm:w-auto">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-400">
                        <span class="material-symbols-rounded text-xl">search</span>
                    </div>
                    <input type="text" name="search" value="<?php echo e($search); ?>" class="bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-xl focus:ring-violet-500 focus:border-violet-500 block w-full pl-10 p-2.5 transition-colors font-outfit" placeholder="Search invoice or dealer...">
                </form>
            </div>

            <?php if (isset($component)) { $__componentOriginalc8463834ba515134d5c98b88e1a9dc03 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data-table','data' => ['headers' => ['Inv No', 'Client / Dealer', 'Date', 'Outstanding & Day Payments', 'Total Amount', 'Payment Summary', 'Status', 'Actions']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Inv No', 'Client / Dealer', 'Date', 'Outstanding & Day Payments', 'Total Amount', 'Payment Summary', 'Status', 'Actions'])]); ?>
                <?php $__empty_1 = true; $__currentLoopData = $bills; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bill): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                        <td class="px-6 py-4">
                            <span class="font-jetbrains text-xs font-bold text-zinc-500">
                                #<?php echo e($bill->invoice_no ?? $bill->invoice_number); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['name' => $bill->dealer->firm_name ?? $bill->customer->name ?? 'D','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($bill->dealer->firm_name ?? $bill->customer->name ?? 'D'),'size' => 'sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $attributes = $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $component = $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
                                <div>
                                    <p class="font-cabinet font-bold text-zinc-900 dark:text-zinc-100"><?php echo e($bill->dealer->firm_name ?? $bill->customer->name ?? '-'); ?></p>
                                    <p class="font-outfit text-xs text-zinc-500"><?php echo e($bill->dealer->route ?? 'Daily Client'); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-zinc-900 dark:text-zinc-100"><?php echo e($bill->date->format('d M Y')); ?></p>
                            <p class="text-[10px] text-zinc-500 font-medium uppercase tracking-wider"><?php echo e($bill->date->format('l')); ?></p>
                        </td>
                        <td class="px-6 py-4 text-xs font-medium text-zinc-600 dark:text-zinc-400">
                            <div class="space-y-1">
                                <p>Prev Bal: <span class="font-jetbrains font-bold">₹<?php echo e(number_format($bill->previous_outstanding, 2)); ?></span></p>
                                <p>Payments: <span class="font-jetbrains font-bold">₹<?php echo e(number_format($bill->payments_during_day, 2)); ?></span></p>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-jetbrains font-medium text-violet-600 dark:text-violet-400">
                            <div class="flex flex-col">
                                <span class="font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 text-sm">Rs <?php echo e(number_format($bill->net_amount, 2)); ?></span>
                                <span class="text-[9px] text-violet-600 font-bold uppercase tracking-tighter">Incl. GST</span>
                            </div>
                        </td>
                        <?php
                            if ($bill->dealer_id) {
                                $entryIds = $bill->dayLoadEntries->pluck('id')->toArray();
                                $periodPaid = (float) \App\Models\DealerPayment::where('dealer_id', $bill->dealer_id)
                                    ->where(function($q) use ($bill, $entryIds) {
                                        $q->whereDate('date', $bill->date->format('Y-m-d'))
                                          ->orWhereIn('day_load_entry_id', $entryIds)
                                          ->orWhere('invoice_id', $bill->id);
                                    })->sum('amount');
                            } else {
                                $periodPaid = (float) $bill->net_amount;
                            }
                            $periodRemaining = max(0, (float)$bill->net_amount - $periodPaid);
                        ?>
                        <td class="px-6 py-4">
                            <div class="space-y-1 text-xs">
                                <div class="flex items-center gap-1.5">
                                    <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                    <span class="text-zinc-500">Paid:</span>
                                    <span class="font-jetbrains font-bold text-emerald-600">₹<?php echo e(number_format($periodPaid, 0)); ?></span>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <?php if($periodRemaining <= 0): ?>
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></span>
                                        <span class="font-bold text-emerald-600 text-[10px] uppercase tracking-wider">✅ Fully Paid</span>
                                    <?php else: ?>
                                        <span class="w-2 h-2 rounded-full bg-rose-500 flex-shrink-0"></span>
                                        <span class="text-zinc-500">Due:</span>
                                        <span class="font-jetbrains font-bold text-rose-600">₹<?php echo e(number_format($periodRemaining, 0)); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php
                                $statusMap = [
                                    'Generated' => 'info',
                                    'Pending'   => 'warning',
                                    'Paid'      => 'success',
                                ];
                                $st = $statusMap[$bill->status] ?? 'warning';
                            ?>
                            <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['variant' => $st]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($st)]); ?><?php echo e(strtoupper($bill->status)); ?> <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="<?php echo e(route('billing.daily.invoice', $bill)); ?>" target="_blank" class="text-zinc-400 hover:text-violet-600 transition-colors" title="Print Invoice">
                                    <span class="material-symbols-rounded text-lg">print</span>
                                </a>
                                <a href="<?php echo e(route('billing.daily.pdf', $bill)); ?>" class="text-zinc-400 hover:text-rose-600 transition-colors" title="Download PDF">
                                    <span class="material-symbols-rounded text-lg">picture_as_pdf</span>
                                </a>
                                <a href="<?php echo e(route('billing.daily.whatsapp', $bill)); ?>" target="_blank" class="text-emerald-500 hover:text-emerald-600 transition-colors" title="WhatsApp Message">
                                    <span class="material-symbols-rounded text-lg">chat</span>
                                </a>
                                <button type="button" onclick="confirmDeleteDailyBill('<?php echo e(route('billing.daily.destroy', $bill)); ?>')" class="text-zinc-400 hover:text-rose-600 transition-colors" title="Delete Bill">
                                    <span class="material-symbols-rounded text-lg">delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                     <?php $__env->slot('empty', null, []); ?> 
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'receipt','title' => 'No Daily Bills Found','description' => 'Start generating daily invoices for your dealers.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'receipt','title' => 'No Daily Bills Found','description' => 'Start generating daily invoices for your dealers.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
                     <?php $__env->endSlot(); ?>
                <?php endif; ?>

                <?php if($bills->hasPages()): ?>
                     <?php $__env->slot('pagination', null, []); ?> 
                        <?php echo e($bills->appends(request()->except('bills_page'))->links()); ?>

                     <?php $__env->endSlot(); ?>
                <?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8463834ba515134d5c98b88e1a9dc03)): ?>
<?php $attributes = $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03; ?>
<?php unset($__attributesOriginalc8463834ba515134d5c98b88e1a9dc03); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8463834ba515134d5c98b88e1a9dc03)): ?>
<?php $component = $__componentOriginalc8463834ba515134d5c98b88e1a9dc03; ?>
<?php unset($__componentOriginalc8463834ba515134d5c98b88e1a9dc03); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
    </div>

    
    <div x-show="activeTab === 'purchase_log'">
        <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
            <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50 flex items-center justify-between">
                <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">Day Load Entries Log</h2>
            </div>

            <?php if (isset($component)) { $__componentOriginalc8463834ba515134d5c98b88e1a9dc03 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data-table','data' => ['headers' => ['Date', 'Batch', 'Dealer', 'Vendor', 'Bird Weight', 'Customer Rate', 'Total Amount', 'Daily Bill Status']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Date', 'Batch', 'Dealer', 'Vendor', 'Bird Weight', 'Customer Rate', 'Total Amount', 'Daily Bill Status'])]); ?>
                <?php $__empty_1 = true; $__currentLoopData = $dealerDayLoads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                        <td class="px-6 py-4">
                            <p class="font-medium text-zinc-900 dark:text-zinc-100"><?php echo e($entry->batch->billing_date->format('d M Y')); ?></p>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-jetbrains text-xs font-bold text-zinc-500">
                                #Batch <?php echo e($entry->batch_id); ?>

                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['name' => $entry->dealer->firm_name ?? 'D','size' => 'sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($entry->dealer->firm_name ?? 'D'),'size' => 'sm']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $attributes = $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $component = $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
                                <div>
                                    <p class="font-cabinet font-bold text-zinc-900 dark:text-zinc-100"><?php echo e($entry->dealer->firm_name ?? '-'); ?></p>
                                    <p class="font-outfit text-xs text-zinc-500"><?php echo e($entry->dealer->route ?? 'General Route'); ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="font-medium text-zinc-800 dark:text-zinc-200"><?php echo e($entry->vendor->firm_name ?? '-'); ?></span>
                        </td>
                        <td class="px-6 py-4 text-center font-jetbrains font-bold text-zinc-900 dark:text-zinc-100">
                            <?php echo e(number_format($entry->bird_weight, 2)); ?> <span class="text-[10px] text-zinc-500 uppercase">kg</span>
                        </td>
                        <td class="px-6 py-4 text-right font-jetbrains font-medium text-zinc-700 dark:text-zinc-300">
                            ₹<?php echo e(number_format($entry->customer_rate, 2)); ?>

                        </td>
                        <td class="px-6 py-4 font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 text-right">
                            ₹<?php echo e(number_format($entry->amount, 2)); ?>

                        </td>
                        <td class="px-6 py-4 text-center">
                            <?php if($entry->daily_bill_id): ?>
                                <a href="<?php echo e(route('billing.daily.invoice', $entry->daily_bill_id)); ?>" target="_blank" class="text-violet-600 hover:text-violet-700 dark:text-violet-400 dark:hover:text-violet-300 font-bold hover:underline flex items-center justify-center gap-1">
                                    <span class="material-symbols-rounded text-sm">link</span>
                                    #<?php echo e($entry->dailyBill->invoice_no ?? 'Daily Bill'); ?>

                                </a>
                            <?php else: ?>
                                <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['variant' => 'warning']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'warning']); ?>Not Invoiced <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                     <?php $__env->slot('empty', null, []); ?> 
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'history','title' => 'No Day Load Logs Found','description' => 'Dealer day load entries will appear here.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'history','title' => 'No Day Load Logs Found','description' => 'Dealer day load entries will appear here.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
                     <?php $__env->endSlot(); ?>
                <?php endif; ?>

                <?php if($dealerDayLoads->hasPages()): ?>
                     <?php $__env->slot('pagination', null, []); ?> 
                        <?php echo e($dealerDayLoads->appends(request()->except('dealer_dayload_page'))->links()); ?>

                     <?php $__env->endSlot(); ?>
                <?php endif; ?>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8463834ba515134d5c98b88e1a9dc03)): ?>
<?php $attributes = $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03; ?>
<?php unset($__attributesOriginalc8463834ba515134d5c98b88e1a9dc03); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8463834ba515134d5c98b88e1a9dc03)): ?>
<?php $component = $__componentOriginalc8463834ba515134d5c98b88e1a9dc03; ?>
<?php unset($__componentOriginalc8463834ba515134d5c98b88e1a9dc03); ?>
<?php endif; ?>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
    </div>

    
    <div x-show="activeTab === 'generate_invoice'">
        <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'max-w-2xl mx-auto','xData' => '{ previewLoaded: false, prevOutstanding: 0, totalPurchases: 0, totalPayments: 0, netInvoice: 0, balanceDue: 0, purchasesCount: 0, discountAmount: 0, billExists: false }','@previewUpdateDaily.window' => '
                previewLoaded = true;
                prevOutstanding = $event.detail.prevOutstanding;
                totalPurchases = $event.detail.totalPurchases;
                totalPayments = $event.detail.totalPayments;
                netInvoice = $event.detail.netInvoice;
                balanceDue = $event.detail.balanceDue;
                purchasesCount = $event.detail.purchasesCount;
                discountAmount = $event.detail.discountAmount;
                billExists = $event.detail.billExists;
            ']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'max-w-2xl mx-auto','x-data' => '{ previewLoaded: false, prevOutstanding: 0, totalPurchases: 0, totalPayments: 0, netInvoice: 0, balanceDue: 0, purchasesCount: 0, discountAmount: 0, billExists: false }','@preview-update-daily.window' => '
                previewLoaded = true;
                prevOutstanding = $event.detail.prevOutstanding;
                totalPurchases = $event.detail.totalPurchases;
                totalPayments = $event.detail.totalPayments;
                netInvoice = $event.detail.netInvoice;
                balanceDue = $event.detail.balanceDue;
                purchasesCount = $event.detail.purchasesCount;
                discountAmount = $event.detail.discountAmount;
                billExists = $event.detail.billExists;
            ']); ?>
            <div class="border-b border-zinc-200 dark:border-zinc-800 pb-4 mb-6">
                <h2 class="text-lg font-extrabold text-zinc-900 dark:text-zinc-50 font-cabinet">Generate Daily Invoice</h2>
                <p class="text-xs text-zinc-500 mt-1">Select a dealer and date to generate a daily invoice.</p>
            </div>

            <form action="<?php echo e(route('billing.daily.generate')); ?>" method="POST" id="generate-daily-bill-form" @submit.prevent="submitDailyBill($event)">
                <?php echo csrf_field(); ?>
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-2">Dealer <span class="text-red-500">*</span></label>
                        <select name="dealer_id" id="gen-daily-dealer-id" required class="appearance-none block w-full pl-3 pr-10 py-2.5 min-h-[44px] text-base border border-zinc-200 dark:border-zinc-700 focus:outline-none focus:ring-4 focus:ring-violet-500/10 focus:border-violet-400 sm:text-sm rounded-xl bg-white/30 dark:bg-zinc-900/30 text-zinc-900 dark:text-zinc-100 transition-all">
                            <option value="">Select dealer...</option>
                            <?php $__currentLoopData = $dealers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $d): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($d->id); ?>" data-balance="<?php echo e($d->displayed_outstanding); ?>"><?php echo e($d->firm_name); ?> — Balance: Rs <?php echo e(number_format($d->displayed_outstanding, 2)); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-2">Billing Date From <span class="text-red-500">*</span></label>
                            <input type="date" name="date_from" id="gen-daily-date-from" required value="<?php echo e(today()->format('Y-m-d')); ?>" class="block w-full bg-white/30 dark:bg-zinc-900/30 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-xl focus:outline-none focus:ring-4 focus:ring-violet-500/10 focus:border-violet-400 p-3 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-2">Billing Date To <span class="text-red-500">*</span></label>
                            <input type="date" name="date_to" id="gen-daily-date-to" required value="<?php echo e(today()->format('Y-m-d')); ?>" class="block w-full bg-white/30 dark:bg-zinc-900/30 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-xl focus:outline-none focus:ring-4 focus:ring-violet-500/10 focus:border-violet-400 p-3 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-2">Discount Amount (Rs)</label>
                        <input type="number" step="0.01" min="0" name="discount_amount" id="gen-daily-discount-amount" value="0.00" class="block w-full bg-white/30 dark:bg-zinc-900/30 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-xl focus:outline-none focus:ring-4 focus:ring-violet-500/10 focus:border-violet-400 p-3 transition-all">
                    </div>
                </div>

                <div class="mb-6 flex gap-4">
                    <button type="button" onclick="previewDailyBilling(this)" class="w-full bg-zinc-800 hover:bg-zinc-700 text-white font-bold py-3 px-6 rounded-lg transition-colors flex items-center justify-center gap-2">
                        <span class="material-symbols-rounded">analytics</span>
                        Calculate & Preview Bill
                    </button>
                </div>

                
                <div x-show="previewLoaded" x-transition class="bg-zinc-50 dark:bg-zinc-800/40 border border-zinc-200 dark:border-zinc-800 rounded-xl p-5 mb-6 space-y-4">
                    <h3 class="text-sm font-bold text-zinc-700 dark:text-zinc-300 font-cabinet uppercase tracking-wider">Calculation Details</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm font-outfit text-zinc-600 dark:text-zinc-400">
                        <div>Previous Outstanding Balance:</div>
                        <div class="text-right font-jetbrains font-bold text-zinc-900 dark:text-white" x-text="'₹' + prevOutstanding.toLocaleString('en-IN', { minimumFractionDigits: 2 })"></div>
                        
                        <div>Purchases in Period (<span x-text="purchasesCount"></span> logs):</div>
                        <div class="text-right font-jetbrains font-bold text-zinc-900 dark:text-white text-emerald-600 dark:text-emerald-400" x-text="'+ ₹' + totalPurchases.toLocaleString('en-IN', { minimumFractionDigits: 2 })"></div>
                        
                        <div>Payments Received in Period:</div>
                        <div class="text-right font-jetbrains font-bold text-zinc-900 dark:text-white text-rose-600 dark:text-rose-400" x-text="'- ₹' + totalPayments.toLocaleString('en-IN', { minimumFractionDigits: 2 })"></div>

                        <div x-show="discountAmount > 0">Discount Applied:</div>
                        <div class="text-right font-jetbrains font-bold text-zinc-900 dark:text-white text-rose-600 dark:text-rose-400" x-show="discountAmount > 0" x-text="'- ₹' + discountAmount.toLocaleString('en-IN', { minimumFractionDigits: 2 })"></div>
                        
                        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2 font-bold text-zinc-800 dark:text-zinc-200">Gross Billed Amount:</div>
                        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2 text-right font-jetbrains font-black text-violet-600 dark:text-violet-400 text-lg" x-text="'₹' + netInvoice.toLocaleString('en-IN', { minimumFractionDigits: 2 })"></div>

                        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2 font-bold text-zinc-800 dark:text-zinc-200">Balance Due:</div>
                        <div class="border-t border-zinc-200 dark:border-zinc-700 pt-2 text-right font-jetbrains font-black text-emerald-600 dark:text-emerald-400 text-xl" x-text="'₹' + balanceDue.toLocaleString('en-IN', { minimumFractionDigits: 2 })"></div>
                    </div>

                    <button type="submit" class="w-full bg-gradient-to-r from-violet-600 to-purple-600 hover:from-violet-700 hover:to-purple-700 text-white font-bold py-3 px-6 rounded-lg shadow-lg shadow-violet-500/20 transition-transform active:scale-95 flex items-center justify-center gap-2">
                        <span class="material-symbols-rounded">verified</span>
                        Confirm & Generate Daily Bill
                    </button>
                </div>
            </form>
         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function previewDailyBilling(btn) {
    const dealerEl = document.getElementById('gen-daily-dealer-id');
    const dateFromEl = document.getElementById('gen-daily-date-from');
    const dateToEl = document.getElementById('gen-daily-date-to');
    const discountEl = document.getElementById('gen-daily-discount-amount');

    const dealerId = dealerEl ? dealerEl.value : '';
    const dateFrom = dateFromEl ? dateFromEl.value : '';
    const dateTo = dateToEl ? dateToEl.value : dateFrom;
    const discount = discountEl ? parseFloat(discountEl.value) || 0 : 0;

    if (!dealerId || !dateFrom || !dateTo) {
        Swal.fire({
            title: 'Missing Fields',
            text: 'Please select dealer, Date From, and Date To.',
            icon: 'warning',
            confirmButtonColor: '#7c3aed'
        });
        return;
    }

    if (dateFrom > dateTo) {
        Swal.fire({
            title: 'Invalid Date Range',
            text: 'Date From cannot be after Date To.',
            icon: 'warning',
            confirmButtonColor: '#7c3aed'
        });
        return;
    }

    btn.disabled = true;
    btn.innerHTML = `<span class="material-symbols-rounded animate-spin">sync</span> Loading...`;

    fetch(`<?php echo e(route('billing.daily.calculate-preview')); ?>?dealer_id=${dealerId}&date_from=${dateFrom}&date_to=${dateTo}&discount_amount=${discount}`)
        .then(res => res.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-rounded">analytics</span> Calculate & Preview Bill`;
            if (data.success) {
                window.dispatchEvent(new CustomEvent('preview-update-daily', {
                    detail: {
                        prevOutstanding: data.previous_outstanding,
                        totalPurchases: data.total_purchases,
                        totalPayments: data.total_payments,
                        netInvoice: data.net_invoice_amount,
                        balanceDue: data.balance_due,
                        purchasesCount: data.purchases_count,
                        discountAmount: data.discount_amount,
                        billExists: data.exists
                    }
                }));
            } else {
                Swal.fire({
                    title: 'Calculation Error',
                    text: data.message || "Failed to calculate preview.",
                    icon: 'error',
                    confirmButtonColor: '#ef4444'
                });
            }
        })
        .catch(err => {
            btn.disabled = false;
            btn.innerHTML = `<span class="material-symbols-rounded">analytics</span> Calculate & Preview Bill`;
            Swal.fire({
                title: 'Network Error',
                text: "Error fetching preview.",
                icon: 'error',
                confirmButtonColor: '#ef4444'
            });
        });
}

function submitDailyBill(e) {
    const form = document.getElementById('generate-daily-bill-form');
    const existingInput = form.querySelector('input[name="replace_existing"]');
    if (existingInput) existingInput.remove();

    const dealerEl = document.getElementById('gen-daily-dealer-id');
    const dateFromEl = document.getElementById('gen-daily-date-from');
    const dateToEl = document.getElementById('gen-daily-date-to');
    const discountEl = document.getElementById('gen-daily-discount-amount');

    const dealerId = dealerEl ? dealerEl.value : '';
    const dateFrom = dateFromEl ? dateFromEl.value : '';
    const dateTo = dateToEl ? dateToEl.value : dateFrom;
    const discount = discountEl ? parseFloat(discountEl.value) || 0 : 0;

    if (!dealerId || !dateFrom || !dateTo) return;

    fetch(`<?php echo e(route('billing.daily.calculate-preview')); ?>?dealer_id=${dealerId}&date_from=${dateFrom}&date_to=${dateTo}&discount_amount=${discount}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.exists) {
                Swal.fire({
                    title: 'Existing Bills Found!',
                    text: `One or more daily bills already exist in the selected range (${dateFrom} to ${dateTo}). Regenerating will consolidate and overwrite existing bills. Do you want to proceed?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#7c3aed',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Yes, Overwrite & Regenerate',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        const hidden = document.createElement('input');
                        hidden.type = 'hidden';
                        hidden.name = 'replace_existing';
                        hidden.value = '1';
                        form.appendChild(hidden);
                        form.submit();
                    }
                });
            } else {
                form.submit();
            }
        })
        .catch(() => form.submit());
}

function confirmDeleteDailyBill(deleteUrl) {
    Swal.fire({
        title: 'Delete Daily Bill?',
        text: "Are you sure you want to delete this bill? Unbilled day-load entries will be restored.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteUrl;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken;
                form.appendChild(csrfInput);
            }

            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            document.body.appendChild(form);
            form.submit();
        }
    });
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/billing/daily/index.blade.php ENDPATH**/ ?>