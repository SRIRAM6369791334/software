<?php $__env->startSection('title', 'EMI Tracking'); ?>

<?php $__env->startSection('content'); ?>
<div class="animate-fade-in" x-data="{ 
    activeTab: '<?php echo e(request('tab', 'receive')); ?>', 
    activeEntity: null, 
    activeInvoice: null, 
    searchQuery: '', 
    timeframe: 'all',
    editId: null,
    editCategory: '',
    editDate: '',
    editDescription: '',
    editAmount: '',
    editAction: ''
}">
    <?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'EMI & Loan Installments','subtitle' => 'Manage fixed monthly business repayments']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'EMI & Loan Installments','subtitle' => 'Manage fixed monthly business repayments']); ?>
         <?php $__env->slot('actions', null, []); ?> 
            <?php
                $totalAlertCount = count($overdueToReceive) + count($upcomingToReceive) + count($overdueToPay) + count($upcomingToPay);
            ?>
            <?php if($totalAlertCount > 0): ?>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 dark:bg-amber-950/30 text-amber-700 dark:text-amber-400 text-xs font-bold font-outfit border border-amber-200/50 dark:border-amber-900/50">
                <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-ping"></span>
                <?php echo e($totalAlertCount); ?> Alerts Active
            </span>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create emis')): ?>
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','href' => ''.e(route('expenses.emis.create')).'','icon' => 'add']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','href' => ''.e(route('expenses.emis.create')).'','icon' => 'add']); ?>
                Setup New EMI
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

    <!-- Search and Filter Bar -->
    <div class="flex flex-col sm:flex-row gap-4 mb-6 items-center justify-between bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-2xl p-4 shadow-sm">
        <div class="relative w-full sm:w-80">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-400">
                <span class="material-symbols-rounded text-lg">search</span>
            </span>
            <input type="text" x-model="searchQuery" placeholder="Search by name, loan, reference..." class="w-full pl-10 pr-4 py-2 bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-outfit" />
        </div>
        
        <div class="flex items-center gap-3 w-full sm:w-auto">
            <label class="text-xs font-bold text-zinc-400 uppercase tracking-wider font-outfit">Filter Range:</label>
            <select x-model="timeframe" class="bg-zinc-50 dark:bg-zinc-950 border border-zinc-200 dark:border-zinc-800 rounded-xl text-sm px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 font-outfit">
                <option value="all">Show All Upcoming</option>
                <option value="7">Next 7 Days</option>
                <option value="30">Next 30 Days</option>
                <option value="90">Next 90 Days</option>
            </select>
        </div>
    </div>

    <!-- Tab Navigation Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <!-- Tab 1: To Receive -->
        <button @click="activeTab = 'receive'; activeEntity = null; activeInvoice = null"
                :class="activeTab === 'receive' ? 'border-emerald-500 ring-2 ring-emerald-500/20 bg-emerald-50/10 dark:bg-emerald-950/10' : 'border-zinc-200 dark:border-zinc-800/50 bg-white dark:bg-zinc-900'"
                class="w-full border rounded-2xl p-5 text-left transition-all duration-200 group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-cabinet">To Receive</span>
                <span class="material-symbols-rounded text-emerald-500" :style="activeTab === 'receive' ? 'font-weight: bold;' : ''">call_received</span>
            </div>
            <?php
                $totalReceivePending = collect($toReceiveEmis)->sum('pending_amount');
            ?>
            <span class="font-jetbrains font-bold text-2xl text-zinc-900 dark:text-zinc-50 block leading-tight">
                <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $totalReceivePending]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($totalReceivePending)]); ?>
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
            </span>
            <span class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 block font-outfit">From Customers & Dealers (Sales EMIs)</span>
        </button>

        <!-- Tab 2: To Pay -->
        <button @click="activeTab = 'pay'; activeEntity = null; activeInvoice = null"
                :class="activeTab === 'pay' ? 'border-amber-500 ring-2 ring-amber-500/20 bg-amber-50/10 dark:bg-amber-950/10' : 'border-zinc-200 dark:border-zinc-800/50 bg-white dark:bg-zinc-900'"
                class="w-full border rounded-2xl p-5 text-left transition-all duration-200 group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-cabinet">To Pay</span>
                <span class="material-symbols-rounded text-amber-500" :style="activeTab === 'pay' ? 'font-weight: bold;' : ''">call_made</span>
            </div>
            <?php
                $totalPayPending = collect($toPayEmis)->sum('pending_amount');
            ?>
            <span class="font-jetbrains font-bold text-2xl text-zinc-900 dark:text-zinc-50 block leading-tight">
                <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $totalPayPending]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($totalPayPending)]); ?>
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
            </span>
            <span class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 block font-outfit">To Vendors & Bank Loans (Purchases / Repayments)</span>
        </button>

        <!-- Tab 3: General Expenses -->
        <button @click="activeTab = 'expenses'; activeEntity = null; activeInvoice = null"
                :class="activeTab === 'expenses' ? 'border-rose-500 ring-2 ring-rose-500/20 bg-rose-50/10 dark:bg-rose-950/10' : 'border-zinc-200 dark:border-zinc-800/50 bg-white dark:bg-zinc-900'"
                class="w-full border rounded-2xl p-5 text-left transition-all duration-200 group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-cabinet">General Expenses</span>
                <span class="material-symbols-rounded text-rose-500" :style="activeTab === 'expenses' ? 'font-weight: bold;' : ''">receipt_long</span>
            </div>
            <?php
                $totalExpenses = $totals['total_expenses'] ?? 0;
            ?>
            <span class="font-jetbrains font-bold text-2xl text-zinc-900 dark:text-zinc-50 block leading-tight">
                <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $totalExpenses]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($totalExpenses)]); ?>
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
            </span>
            <span class="text-xs text-zinc-500 dark:text-zinc-400 mt-1 block font-outfit">Operational Expenditures (Monthly Burn)</span>
        </button>
    </div>

    <!-- PANEL 1: TO RECEIVE -->
    <div x-show="activeTab === 'receive'" x-transition:enter="transition ease-out duration-200" class="space-y-4">
        
        <!-- Alerts for To Receive -->
        <?php if(count($overdueToReceive) > 0 || count($upcomingToReceive) > 0): ?>
        <div class="bg-gradient-to-br from-amber-50/20 via-white to-zinc-50/20 dark:from-amber-950/10 dark:via-zinc-900 dark:to-zinc-950/20 border border-amber-200/60 dark:border-amber-900/50 rounded-2xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-amber-100 dark:border-amber-900/30 pb-3">
                <h3 class="text-sm font-black text-amber-800 dark:text-amber-400 font-cabinet flex items-center gap-2">
                    <span class="material-symbols-rounded text-lg">notifications_active</span>
                    Action Required: Early Warning Alerts (<?php echo e(count($overdueToReceive) + count($upcomingToReceive)); ?>)
                </h3>
            </div>
            
            <div class="grid grid-cols-1 gap-3">
                
                <?php $__currentLoopData = $overdueToReceive; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $entityName = $emi->customer ? $emi->customer->name : ($emi->dealer ? ($emi->dealer->firm_name ?? $emi->dealer->name) : 'Unknown');
                    ?>
                    <div x-show="searchQuery === '' || '<?php echo e(strtolower($entityName)); ?>'.includes(searchQuery.toLowerCase()) || '<?php echo e(strtolower($emi->loan_name)); ?>'.includes(searchQuery.toLowerCase())" class="flex flex-col sm:flex-row sm:items-center justify-between bg-rose-50/30 dark:bg-rose-950/10 border border-rose-100 dark:border-rose-900/30 rounded-xl p-4 gap-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-rose-500 mt-0.5">warning</span>
                            <div>
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-50 font-cabinet"><?php echo e($emi->loan_name); ?></h4>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    <?php echo e($entityName); ?> • <span class="font-semibold text-rose-600 dark:text-rose-400">Overdue (Due: <?php echo e($emi->due_date->format('d M, Y')); ?>)</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-6 font-outfit">
                            <div class="text-right">
                                <span class="text-xs text-zinc-400 block">Amount</span>
                                <span class="font-jetbrains font-bold text-rose-600 dark:text-rose-400"><?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $emi->amount]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($emi->amount)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $attributes = $__attributesOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__attributesOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $component = $__componentOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__componentOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?></span>
                            </div>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit emis')): ?>
                            <form action="<?php echo e(route('expenses.emis.pay', $emi)); ?>" method="POST" class="inline" onsubmit="return confirm('Mark this installment as Paid?')">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                    Mark Paid
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <?php $__currentLoopData = $upcomingToReceive; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $entityName = $emi->customer ? $emi->customer->name : ($emi->dealer ? ($emi->dealer->firm_name ?? $emi->dealer->name) : 'Unknown');
                        $daysDue = now()->startOfDay()->diffInDays($emi->due_date->startOfDay(), false);
                    ?>
                    <div x-show="(searchQuery === '' || '<?php echo e(strtolower($entityName)); ?>'.includes(searchQuery.toLowerCase()) || '<?php echo e(strtolower($emi->loan_name)); ?>'.includes(searchQuery.toLowerCase())) && (timeframe === 'all' || <?php echo e($daysDue); ?> <= parseInt(timeframe))" class="flex flex-col sm:flex-row sm:items-center justify-between bg-amber-50/20 dark:bg-amber-950/5 border border-amber-100/50 dark:border-amber-900/20 rounded-xl p-4 gap-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-amber-500 mt-0.5">schedule</span>
                            <div>
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-50 font-cabinet"><?php echo e($emi->loan_name); ?></h4>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    <?php echo e($entityName); ?> • <span class="font-semibold text-amber-600 dark:text-amber-400">Due in <?php echo e($daysDue); ?> days (<?php echo e($emi->due_date->format('d M')); ?>)</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-6 font-outfit">
                            <div class="text-right">
                                <span class="text-xs text-zinc-400 block">Amount</span>
                                <span class="font-jetbrains font-bold text-zinc-900 dark:text-zinc-50"><?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $emi->amount]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($emi->amount)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $attributes = $__attributesOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__attributesOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $component = $__componentOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__componentOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?></span>
                            </div>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit emis')): ?>
                            <form action="<?php echo e(route('expenses.emis.pay', $emi)); ?>" method="POST" class="inline" onsubmit="return confirm('Mark this installment as Paid?')">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                    Mark Paid
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php $__empty_1 = true; $__currentLoopData = $toReceiveEmis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entityKey => $entity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <!-- LEVEL 1: ENTITY CARD -->
            <div x-show="searchQuery === '' || '<?php echo e(strtolower($entity['name'])); ?>'.includes(searchQuery.toLowerCase())" class="border border-zinc-200/60 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm overflow-hidden transition-all duration-300">
                <button @click="activeEntity = (activeEntity === '<?php echo e($entityKey); ?>' ? null : '<?php echo e($entityKey); ?>'); activeInvoice = null;" 
                        class="w-full flex items-center justify-between p-5 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm tracking-wide bg-emerald-50 text-emerald-600 dark:bg-emerald-950/50 dark:text-emerald-400">
                            <?php echo e(strtoupper(substr($entity['name'], 0, 2))); ?>

                        </div>
                        <div>
                            <h3 class="font-cabinet font-bold text-zinc-900 dark:text-zinc-50 text-lg leading-tight"><?php echo e($entity['name']); ?></h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-100/50 text-emerald-700 dark:bg-emerald-950/50 dark:text-emerald-400">
                                    <?php echo e($entity['type']); ?>

                                </span>
                                <span class="text-zinc-400 text-xs">•</span>
                                <span class="text-zinc-500 text-xs font-outfit"><?php echo e($entity['total_installments']); ?> Total Installments</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-5">
                        <div class="text-right font-outfit">
                            <span class="text-zinc-400 text-xs block uppercase tracking-wider">Pending Balance</span>
                            <span class="font-jetbrains font-bold text-base <?php echo e($entity['pending_amount'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-500'); ?>">
                                <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $entity['pending_amount']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($entity['pending_amount'])]); ?>
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
                            </span>
                        </div>
                        <span class="material-symbols-rounded text-zinc-400 transition-transform duration-300"
                              :style="activeEntity === '<?php echo e($entityKey); ?>' ? 'transform: rotate(180deg);' : ''">
                            keyboard_arrow_down
                        </span>
                    </div>
                </button>
                
                <!-- LEVEL 2: INVOICES -->
                <div x-show="activeEntity === '<?php echo e($entityKey); ?>'" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     class="border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/20 dark:bg-zinc-900/20 p-5 space-y-3"
                     style="display: none;">
                    
                    <?php $__currentLoopData = $entity['invoices']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoiceKey => $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $invoiceHash = md5($entityKey . '_' . $invoiceKey); ?>
                        <div class="border border-zinc-200/50 dark:border-zinc-800/50 rounded-xl bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                            <button @click.stop="activeInvoice === '<?php echo e($invoiceHash); ?>' ? activeInvoice = null : activeInvoice = '<?php echo e($invoiceHash); ?>'"
                                    class="w-full flex items-center justify-between p-4 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors text-left">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-rounded text-zinc-400 text-lg">receipt_long</span>
                                    <div>
                                        <h4 class="font-semibold text-sm text-zinc-800 dark:text-zinc-200 font-cabinet"><?php echo e($invoice['name']); ?></h4>
                                        <div class="flex items-center gap-2 mt-0.5 font-outfit">
                                            <span class="text-zinc-500 text-xs">
                                                <?php echo e(count($invoice['installments'])); ?> Installments
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 font-outfit">
                                    <div class="text-right">
                                        <span class="text-zinc-500 text-xs block">Invoice Total: <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $invoice['total_amount']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoice['total_amount'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $attributes = $__attributesOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__attributesOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $component = $__componentOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__componentOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?></span>
                                        <?php if($invoice['pending_amount'] > 0): ?>
                                            <span class="text-[11px] font-semibold text-rose-500 dark:text-rose-400">
                                                Unpaid: <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $invoice['pending_amount']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoice['pending_amount'])]); ?>
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
                                            </span>
                                        <?php else: ?>
                                            <span class="text-[11px] font-semibold text-emerald-500">Fully Closed</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="material-symbols-rounded text-zinc-400 text-sm transition-transform duration-300"
                                          :style="activeInvoice === '<?php echo e($invoiceHash); ?>' ? 'transform: rotate(180deg);' : ''">
                                        expand_more
                                    </span>
                                </div>
                            </button>
                            
                            <!-- LEVEL 3: INSTALLMENTS TABLE -->
                            <div x-show="activeInvoice === '<?php echo e($invoiceHash); ?>'" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-950/20"
                                 style="display: none;">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm font-outfit">
                                        <thead>
                                            <tr class="bg-zinc-50/50 dark:bg-zinc-800/50 text-[11px] text-zinc-500 uppercase tracking-widest border-b border-zinc-100 dark:border-zinc-800">
                                                <th class="px-6 py-3 font-semibold">Installment ID</th>
                                                <th class="px-6 py-3 font-semibold">Due Date</th>
                                                <th class="px-6 py-3 font-semibold text-right">Amount</th>
                                                <th class="px-6 py-3 font-semibold text-center">Status</th>
                                                <th class="px-6 py-3 font-semibold text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                                            <?php $__currentLoopData = $invoice['installments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                                    <td class="px-6 py-3 font-jetbrains text-xs text-zinc-500">
                                                        REF#<?php echo e(str_pad($emi->id, 4, '0', STR_PAD_LEFT)); ?>

                                                    </td>
                                                    <td class="px-6 py-3">
                                                        <?php $isOverdue = $emi->status != 'Paid' && $emi->due_date < now(); ?>
                                                        <span class="font-medium <?php echo e($isOverdue ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-zinc-700 dark:text-zinc-300'); ?>">
                                                            <?php echo e($emi->due_date->format('d M, Y')); ?>

                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-3 font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 text-right">
                                                        <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $emi->amount]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($emi->amount)]); ?>
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
                                                    </td>
                                                    <td class="px-6 py-3 text-center">
                                                        <?php
                                                            $variant = $emi->status == 'Paid' ? 'success' : ($emi->status == 'Overdue' ? 'danger' : 'warning');
                                                        ?>
                                                        <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['variant' => $variant]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($variant)]); ?><?php echo e($emi->status); ?> <?php echo $__env->renderComponent(); ?>
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
                                                    <td class="px-6 py-3 text-center">
                                                        <div class="flex justify-center items-center gap-3">
                                                            <?php if($emi->status !== 'Paid'): ?>
                                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit emis')): ?>
                                                                <form action="<?php echo e(route('expenses.emis.pay', $emi)); ?>" method="POST" class="inline" onsubmit="return confirm('Mark this EMI installment as Paid?')">
                                                                    <?php echo csrf_field(); ?>
                                                                    <button type="submit" class="text-emerald-500 hover:text-emerald-700 transition-colors" title="Mark as Paid">
                                                                        <span class="material-symbols-rounded text-lg">check_circle</span>
                                                                    </button>
                                                                </form>

                                                                <form action="<?php echo e(route('expenses.emis.close-full', $emi)); ?>" method="POST" class="inline" onsubmit="return confirm('Close the entire loan group (<?php echo e($emi->loan_name); ?>) and mark all remaining installments as Paid?')">
                                                                    <?php echo csrf_field(); ?>
                                                                    <button type="submit" class="text-blue-500 hover:text-blue-700 transition-colors" title="Close Entire Loan (Pay Full)">
                                                                        <span class="material-symbols-rounded text-lg">assignment_turned_in</span>
                                                                    </button>
                                                                </form>
                                                                <?php endif; ?>
                                                            <?php endif; ?>

                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit emis')): ?>
                                                            <a href="<?php echo e(route('expenses.emis.edit', $emi)); ?>" class="text-zinc-400 hover:text-amber-600 transition-colors" title="Edit EMI">
                                                                <span class="material-symbols-rounded text-lg">edit</span>
                                                            </a>
                                                            <?php endif; ?>

                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete emis')): ?>
                                                            <form action="<?php echo e(route('expenses.emis.destroy', $emi)); ?>" method="POST" onsubmit="return confirm('Delete this EMI record?')">
                                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                                <button type="submit" class="text-zinc-400 hover:text-rose-600 transition-colors" title="Delete">
                                                                    <span class="material-symbols-rounded text-lg">delete</span>
                                                                </button>
                                                            </form>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
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
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'call_received','title' => 'No EMIs to receive','description' => 'You don\'t have any pending Customer or Dealer EMI receivables.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'call_received','title' => 'No EMIs to receive','description' => 'You don\'t have any pending Customer or Dealer EMI receivables.']); ?>
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
        <?php endif; ?>
    </div>

    <!-- PANEL 2: TO PAY -->
    <div x-show="activeTab === 'pay'" x-transition:enter="transition ease-out duration-200" class="space-y-4" style="display: none;">
        
        <!-- Alerts for To Pay -->
        <?php if(count($overdueToPay) > 0 || count($upcomingToPay) > 0): ?>
        <div class="bg-gradient-to-br from-amber-50/20 via-white to-zinc-50/20 dark:from-amber-950/10 dark:via-zinc-900 dark:to-zinc-950/20 border border-amber-200/60 dark:border-amber-900/50 rounded-2xl p-5 shadow-sm space-y-4">
            <div class="flex items-center justify-between border-b border-amber-100 dark:border-amber-900/30 pb-3">
                <h3 class="text-sm font-black text-amber-800 dark:text-amber-400 font-cabinet flex items-center gap-2">
                    <span class="material-symbols-rounded text-lg">notifications_active</span>
                    Action Required: Early Warning Alerts (<?php echo e(count($overdueToPay) + count($upcomingToPay)); ?>)
                </h3>
            </div>
            
            <div class="grid grid-cols-1 gap-3">
                
                <?php $__currentLoopData = $overdueToPay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $entityName = $emi->vendor ? ($emi->vendor->firm_name ?? $emi->vendor->name) : ($emi->bank_name ?? 'Bank Loan');
                    ?>
                    <div x-show="searchQuery === '' || '<?php echo e(strtolower($entityName)); ?>'.includes(searchQuery.toLowerCase()) || '<?php echo e(strtolower($emi->loan_name)); ?>'.includes(searchQuery.toLowerCase())" class="flex flex-col sm:flex-row sm:items-center justify-between bg-rose-50/30 dark:bg-rose-950/10 border border-rose-100 dark:border-rose-900/30 rounded-xl p-4 gap-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-rose-500 mt-0.5">warning</span>
                            <div>
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-50 font-cabinet"><?php echo e($emi->loan_name); ?></h4>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    <?php echo e($entityName); ?> • <span class="font-semibold text-rose-600 dark:text-rose-400">Overdue (Due: <?php echo e($emi->due_date->format('d M, Y')); ?>)</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-6 font-outfit">
                            <div class="text-right">
                                <span class="text-xs text-zinc-400 block">Amount</span>
                                <span class="font-jetbrains font-bold text-rose-600 dark:text-rose-400"><?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $emi->amount]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($emi->amount)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $attributes = $__attributesOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__attributesOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $component = $__componentOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__componentOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?></span>
                            </div>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit emis')): ?>
                            <form action="<?php echo e(route('expenses.emis.pay', $emi)); ?>" method="POST" class="inline" onsubmit="return confirm('Mark this installment as Paid?')">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="px-3.5 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                    Mark Paid
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <?php $__currentLoopData = $upcomingToPay; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $entityName = $emi->vendor ? ($emi->vendor->firm_name ?? $emi->vendor->name) : ($emi->bank_name ?? 'Bank Loan');
                        $daysDue = now()->startOfDay()->diffInDays($emi->due_date->startOfDay(), false);
                    ?>
                    <div x-show="(searchQuery === '' || '<?php echo e(strtolower($entityName)); ?>'.includes(searchQuery.toLowerCase()) || '<?php echo e(strtolower($emi->loan_name)); ?>'.includes(searchQuery.toLowerCase())) && (timeframe === 'all' || <?php echo e($daysDue); ?> <= parseInt(timeframe))" class="flex flex-col sm:flex-row sm:items-center justify-between bg-amber-50/20 dark:bg-amber-950/5 border border-amber-100/50 dark:border-amber-900/20 rounded-xl p-4 gap-4">
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-rounded text-amber-500 mt-0.5">schedule</span>
                            <div>
                                <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-50 font-cabinet"><?php echo e($emi->loan_name); ?></h4>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                    <?php echo e($entityName); ?> • <span class="font-semibold text-amber-600 dark:text-amber-400">Due in <?php echo e($daysDue); ?> days (<?php echo e($emi->due_date->format('d M')); ?>)</span>
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center justify-between sm:justify-end gap-6 font-outfit">
                            <div class="text-right">
                                <span class="text-xs text-zinc-400 block">Amount</span>
                                <span class="font-jetbrains font-bold text-zinc-900 dark:text-zinc-50"><?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $emi->amount]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($emi->amount)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $attributes = $__attributesOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__attributesOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $component = $__componentOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__componentOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?></span>
                            </div>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit emis')): ?>
                            <form action="<?php echo e(route('expenses.emis.pay', $emi)); ?>" method="POST" class="inline" onsubmit="return confirm('Mark this installment as Paid?')">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="px-3.5 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                    Mark Paid
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>

        <?php $__empty_1 = true; $__currentLoopData = $toPayEmis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entityKey => $entity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <!-- LEVEL 1: ENTITY CARD -->
            <div x-show="searchQuery === '' || '<?php echo e(strtolower($entity['name'])); ?>'.includes(searchQuery.toLowerCase())" class="border border-zinc-200/60 dark:border-zinc-800 rounded-2xl bg-white dark:bg-zinc-900 shadow-sm overflow-hidden transition-all duration-300">
                <button @click="activeEntity = (activeEntity === '<?php echo e($entityKey); ?>' ? null : '<?php echo e($entityKey); ?>'); activeInvoice = null;" 
                        class="w-full flex items-center justify-between p-5 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm tracking-wide bg-amber-50 text-amber-600 dark:bg-amber-950/50 dark:text-amber-400">
                            <?php echo e(strtoupper(substr($entity['name'], 0, 2))); ?>

                        </div>
                        <div>
                            <h3 class="font-cabinet font-bold text-zinc-900 dark:text-zinc-50 text-lg leading-tight"><?php echo e($entity['name']); ?></h3>
                            <div class="flex items-center gap-2 mt-1">
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-100/50 text-amber-700 dark:bg-amber-950/50 dark:text-amber-400">
                                    <?php echo e($entity['type']); ?>

                                </span>
                                <span class="text-zinc-400 text-xs">•</span>
                                <span class="text-zinc-500 text-xs font-outfit"><?php echo e($entity['total_installments']); ?> Total Installments</span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-5">
                        <div class="text-right font-outfit">
                            <span class="text-zinc-400 text-xs block uppercase tracking-wider">Pending Balance</span>
                            <span class="font-jetbrains font-bold text-base <?php echo e($entity['pending_amount'] > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-zinc-500'); ?>">
                                <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $entity['pending_amount']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($entity['pending_amount'])]); ?>
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
                            </span>
                        </div>
                        <span class="material-symbols-rounded text-zinc-400 transition-transform duration-300"
                              :style="activeEntity === '<?php echo e($entityKey); ?>' ? 'transform: rotate(180deg);' : ''">
                            keyboard_arrow_down
                        </span>
                    </div>
                </button>
                
                <!-- LEVEL 2: INVOICES -->
                <div x-show="activeEntity === '<?php echo e($entityKey); ?>'" 
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 transform scale-95"
                     x-transition:enter-end="opacity-100 transform scale-100"
                     class="border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/20 dark:bg-zinc-900/20 p-5 space-y-3"
                     style="display: none;">
                    
                    <?php $__currentLoopData = $entity['invoices']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $invoiceKey => $invoice): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php $invoiceHash = md5($entityKey . '_' . $invoiceKey); ?>
                        <div class="border border-zinc-200/50 dark:border-zinc-800/50 rounded-xl bg-white dark:bg-zinc-900 overflow-hidden shadow-sm">
                            <button @click.stop="activeInvoice === '<?php echo e($invoiceHash); ?>' ? activeInvoice = null : activeInvoice = '<?php echo e($invoiceHash); ?>'"
                                    class="w-full flex items-center justify-between p-4 hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors text-left">
                                <div class="flex items-center gap-3">
                                    <span class="material-symbols-rounded text-zinc-400 text-lg">receipt_long</span>
                                    <div>
                                        <h4 class="font-semibold text-sm text-zinc-800 dark:text-zinc-200 font-cabinet"><?php echo e($invoice['name']); ?></h4>
                                        <div class="flex items-center gap-2 mt-0.5 font-outfit">
                                            <span class="text-zinc-500 text-xs">
                                                <?php echo e(count($invoice['installments'])); ?> Installments
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 font-outfit">
                                    <div class="text-right">
                                        <span class="text-zinc-500 text-xs block">Invoice Total: <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $invoice['total_amount']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoice['total_amount'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $attributes = $__attributesOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__attributesOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $component = $__componentOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__componentOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?></span>
                                        <?php if($invoice['pending_amount'] > 0): ?>
                                            <span class="text-[11px] font-semibold text-rose-500 dark:text-rose-400">
                                                Unpaid: <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $invoice['pending_amount']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($invoice['pending_amount'])]); ?>
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
                                            </span>
                                        <?php else: ?>
                                            <span class="text-[11px] font-semibold text-emerald-500">Fully Closed</span>
                                        <?php endif; ?>
                                    </div>
                                    <span class="material-symbols-rounded text-zinc-400 text-sm transition-transform duration-300"
                                          :style="activeInvoice === '<?php echo e($invoiceHash); ?>' ? 'transform: rotate(180deg);' : ''">
                                        expand_more
                                    </span>
                                </div>
                            </button>
                            
                            <!-- LEVEL 3: INSTALLMENTS TABLE -->
                            <div x-show="activeInvoice === '<?php echo e($invoiceHash); ?>'" 
                                 x-transition:enter="transition ease-out duration-150"
                                 x-transition:enter-start="opacity-0"
                                 x-transition:enter-end="opacity-100"
                                 class="border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/30 dark:bg-zinc-950/20"
                                 style="display: none;">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-left text-sm font-outfit">
                                        <thead>
                                            <tr class="bg-zinc-50/50 dark:bg-zinc-800/50 text-[11px] text-zinc-500 uppercase tracking-widest border-b border-zinc-100 dark:border-zinc-800">
                                                <th class="px-6 py-3 font-semibold">Installment ID</th>
                                                <th class="px-6 py-3 font-semibold">Due Date</th>
                                                <th class="px-6 py-3 font-semibold text-right">Amount</th>
                                                <th class="px-6 py-3 font-semibold text-center">Status</th>
                                                <th class="px-6 py-3 font-semibold text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-zinc-100 dark:divide-zinc-800/50">
                                            <?php $__currentLoopData = $invoice['installments']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                                    <td class="px-6 py-3 font-jetbrains text-xs text-zinc-500">
                                                        REF#<?php echo e(str_pad($emi->id, 4, '0', STR_PAD_LEFT)); ?>

                                                    </td>
                                                    <td class="px-6 py-3">
                                                        <?php $isOverdue = $emi->status != 'Paid' && $emi->due_date < now(); ?>
                                                        <span class="font-medium <?php echo e($isOverdue ? 'text-rose-600 dark:text-rose-400 font-semibold' : 'text-zinc-700 dark:text-zinc-300'); ?>">
                                                            <?php echo e($emi->due_date->format('d M, Y')); ?>

                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-3 font-jetbrains font-bold text-zinc-900 dark:text-zinc-100 text-right">
                                                        <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $emi->amount]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($emi->amount)]); ?>
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
                                                    </td>
                                                    <td class="px-6 py-3 text-center">
                                                        <?php
                                                            $variant = $emi->status == 'Paid' ? 'success' : ($emi->status == 'Overdue' ? 'danger' : 'warning');
                                                        ?>
                                                        <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['variant' => $variant]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($variant)]); ?><?php echo e($emi->status); ?> <?php echo $__env->renderComponent(); ?>
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
                                                    <td class="px-6 py-3 text-center">
                                                        <div class="flex justify-center items-center gap-3">
                                                            <?php if($emi->status !== 'Paid'): ?>
                                                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit emis')): ?>
                                                                <form action="<?php echo e(route('expenses.emis.pay', $emi)); ?>" method="POST" class="inline" onsubmit="return confirm('Mark this EMI installment as Paid?')">
                                                                    <?php echo csrf_field(); ?>
                                                                    <button type="submit" class="text-emerald-500 hover:text-emerald-700 transition-colors" title="Mark as Paid">
                                                                        <span class="material-symbols-rounded text-lg">check_circle</span>
                                                                    </button>
                                                                </form>

                                                                <form action="<?php echo e(route('expenses.emis.close-full', $emi)); ?>" method="POST" class="inline" onsubmit="return confirm('Close the entire loan group (<?php echo e($emi->loan_name); ?>) and mark all remaining installments as Paid?')">
                                                                    <?php echo csrf_field(); ?>
                                                                    <button type="submit" class="text-blue-500 hover:text-blue-700 transition-colors" title="Close Entire Loan (Pay Full)">
                                                                        <span class="material-symbols-rounded text-lg">assignment_turned_in</span>
                                                                    </button>
                                                                </form>
                                                                <?php endif; ?>
                                                            <?php endif; ?>

                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit emis')): ?>
                                                            <a href="<?php echo e(route('expenses.emis.edit', $emi)); ?>" class="text-zinc-400 hover:text-amber-600 transition-colors" title="Edit EMI">
                                                                <span class="material-symbols-rounded text-lg">edit</span>
                                                            </a>
                                                            <?php endif; ?>

                                                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete emis')): ?>
                                                            <form action="<?php echo e(route('expenses.emis.destroy', $emi)); ?>" method="POST" onsubmit="return confirm('Delete this EMI record?')">
                                                                <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                                <button type="submit" class="text-zinc-400 hover:text-rose-600 transition-colors" title="Delete">
                                                                    <span class="material-symbols-rounded text-lg">delete</span>
                                                                </button>
                                                            </form>
                                                            <?php endif; ?>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
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
                <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'call_made','title' => 'No EMIs to pay','description' => 'You don\'t have any pending Vendor or Bank Loan EMI payables.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'call_made','title' => 'No EMIs to pay','description' => 'You don\'t have any pending Vendor or Bank Loan EMI payables.']); ?>
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
        <?php endif; ?>
    </div>

    <!-- PANEL 3: GENERAL EXPENSES -->
    <div x-show="activeTab === 'expenses'" x-transition:enter="transition ease-out duration-200" class="space-y-4" style="display: none;">
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
            <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50 flex justify-between items-center">
                <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50 font-semibold">General Expense Ledger</h2>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create expenses')): ?>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','xData' => true,'xOn:click' => '$dispatch(\'open-modal\', \'add-expense\')','icon' => 'add']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','x-data' => true,'x-on:click' => '$dispatch(\'open-modal\', \'add-expense\')','icon' => 'add']); ?>
                    Record Expense
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
                <?php endif; ?>
            </div>
            
            <?php if (isset($component)) { $__componentOriginalc8463834ba515134d5c98b88e1a9dc03 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data-table','data' => ['headers' => ['Date', 'Category', 'Description', 'Amount', 'Action']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Date', 'Category', 'Description', 'Amount', 'Action'])]); ?>
                <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                        <td class="px-6 py-4 font-medium text-zinc-900 dark:text-zinc-100">
                            <?php echo e($e->date->format('M d, Y')); ?>

                        </td>
                        <td class="px-6 py-4">
                            <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['variant' => 'zinc']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'zinc']); ?><?php echo e($e->category); ?> <?php echo $__env->renderComponent(); ?>
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
                        <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">
                            <?php echo e($e->description); ?>

                        </td>
                        <td class="px-6 py-4 font-jetbrains font-medium text-rose-600 dark:text-rose-400">
                            <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $e->amount]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($e->amount)]); ?>
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
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit expenses')): ?>
                                <button type="button" @click="
                                    editId = <?php echo e($e->id); ?>;
                                    editCategory = '<?php echo e($e->category); ?>';
                                    editDate = '<?php echo e($e->date->format('Y-m-d')); ?>';
                                    editDescription = '<?php echo e(addslashes($e->description)); ?>';
                                    editAmount = '<?php echo e($e->amount); ?>';
                                    editAction = '<?php echo e(route('expenses.update', $e)); ?>';
                                    $dispatch('open-modal', 'edit-expense');
                                " class="text-zinc-400 hover:text-amber-600 transition-colors" title="Edit">
                                    <span class="material-symbols-rounded text-lg">edit</span>
                                </button>
                                <?php endif; ?>

                                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete expenses')): ?>
                                <form action="<?php echo e(route('expenses.destroy', $e)); ?>" method="POST" onsubmit="return confirm('Delete this expense entry?')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-zinc-400 hover:text-rose-600 transition-colors" title="Delete">
                                        <span class="material-symbols-rounded text-lg">delete</span>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                     <?php $__env->slot('empty', null, []); ?> 
                        <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'receipt_long','title' => 'No expenses recorded','description' => 'No expenses logged in this cycle.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'receipt_long','title' => 'No expenses recorded','description' => 'No expenses logged in this cycle.']); ?>
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

                <?php if($expenses->hasPages()): ?>
                     <?php $__env->slot('pagination', null, []); ?> 
                        <?php echo e($expenses->appends(['tab' => 'expenses'])->links()); ?>

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
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('modals'); ?>
<?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => 'add-expense','title' => 'Record Expense','subtitle' => 'Log operational expenditures','icon' => 'receipt_long','maxWidth' => '720','show' => $errors->any()]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'add-expense','title' => 'Record Expense','subtitle' => 'Log operational expenditures','icon' => 'receipt_long','maxWidth' => '720','show' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->any())]); ?>
    <form id="add-expense-form" action="<?php echo e(route('expenses.store')); ?>" method="POST">
        <?php echo csrf_field(); ?>

        
        <div class="mb-8">
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-3.5">
                Category <span class="text-zinc-400 dark:text-zinc-500 text-xs ml-0.5">*</span>
            </label>
            <div class="grid grid-cols-5 gap-3">
                <?php $catIcons = ['Fuel' => 'local_gas_station', 'Salary' => 'payments', 'Transport' => 'local_shipping', 'Utility' => 'bolt', 'Misc' => 'more_horiz']; ?>
                <?php $catColors = ['Fuel' => 'text-orange-500', 'Salary' => 'text-blue-500', 'Transport' => 'text-amber-500', 'Utility' => 'text-purple-500', 'Misc' => 'text-zinc-400']; ?>
                <?php $__currentLoopData = ['Fuel','Salary','Transport','Utility','Misc']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="group relative flex flex-col items-center gap-2 py-4 px-2 rounded-2xl border-2 cursor-pointer transition-all duration-200 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/80 dark:has-[:checked]:bg-emerald-500/12 has-[:checked]:shadow-[0_0_0_1px_rgba(16,185,129,0.15),0_4px_12px_rgba(16,185,129,0.15)] border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600 bg-white/50 dark:bg-zinc-900/50">
                    <input type="radio" name="category" value="<?php echo e($c); ?>" class="sr-only" <?php echo e($c === 'Fuel' ? 'checked' : ''); ?> required>
                    <div class="relative">
                        <span class="material-symbols-rounded text-[28px] <?php echo e($catColors[$c]); ?>"><?php echo e($catIcons[$c]); ?></span>
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center scale-0 group-has-[:checked]:scale-100 transition-transform duration-200">
                            <span class="material-symbols-rounded text-[12px]">check</span>
                        </span>
                    </div>
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 group-has-[:checked]:text-emerald-700 dark:group-has-[:checked]:text-emerald-300 group-has-[:checked]:font-bold transition-all"><?php echo e($c); ?></span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
            <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['type' => 'date','name' => 'date','label' => 'Date','required' => true,'value' => ''.e(date('Y-m-d')).'','icon' => 'calendar_month']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'date','name' => 'date','label' => 'Date','required' => true,'value' => ''.e(date('Y-m-d')).'','icon' => 'calendar_month']); ?>
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
            <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['type' => 'number','name' => 'amount','label' => 'Amount','required' => true,'step' => '0.01','min' => '0.01','placeholder' => 'Enter amount','icon' => 'currency_rupee']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'number','name' => 'amount','label' => 'Amount','required' => true,'step' => '0.01','min' => '0.01','placeholder' => 'Enter amount','icon' => 'currency_rupee']); ?>
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

        
        <div class="mb-8">
            <?php if (isset($component)) { $__componentOriginalcd97a59301ba78d56b3ed60dd41409ab = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.textarea','data' => ['name' => 'description','label' => 'Description','required' => true,'placeholder' => 'e.g. Purchased poultry feed from ABC Traders','rows' => '4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'description','label' => 'Description','required' => true,'placeholder' => 'e.g. Purchased poultry feed from ABC Traders','rows' => '4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab)): ?>
<?php $attributes = $__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab; ?>
<?php unset($__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcd97a59301ba78d56b3ed60dd41409ab)): ?>
<?php $component = $__componentOriginalcd97a59301ba78d56b3ed60dd41409ab; ?>
<?php unset($__componentOriginalcd97a59301ba78d56b3ed60dd41409ab); ?>
<?php endif; ?>
        </div>

        
        <div class="mb-2">
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-3.5">
                Payment Method <span class="text-zinc-400 dark:text-zinc-500 text-xs ml-0.5">*</span>
            </label>
            <div class="grid grid-cols-4 gap-2.5">
                <?php $pmOptions = [['value' => 'Cash', 'icon' => 'payments', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50'], ['value' => 'Bank Transfer', 'icon' => 'account_balance', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50'], ['value' => 'UPI', 'icon' => 'smartphone', 'color' => 'text-violet-500', 'bg' => 'bg-violet-50'], ['value' => 'Card', 'icon' => 'credit_card', 'color' => 'text-rose-500', 'bg' => 'bg-rose-50']]; ?>
                <?php $__currentLoopData = $pmOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="group relative flex flex-col items-center gap-2 py-4 px-1 rounded-2xl border-2 cursor-pointer transition-all duration-200 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/80 dark:has-[:checked]:bg-emerald-500/12 has-[:checked]:shadow-[0_0_0_1px_rgba(16,185,129,0.15),0_4px_12px_rgba(16,185,129,0.15)] border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600 bg-white/50 dark:bg-zinc-900/50">
                    <input type="radio" name="payment_method" value="<?php echo e($pm['value']); ?>" class="sr-only" <?php echo e($loop->first ? 'checked' : ''); ?> required>
                    <div class="w-9 h-9 rounded-full <?php echo e($pm['bg']); ?> dark:<?php echo e($pm['bg']); ?>/10 flex items-center justify-center <?php echo e($pm['color']); ?>">
                        <span class="material-symbols-rounded text-xl"><?php echo e($pm['icon']); ?></span>
                    </div>
                    <span class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 group-has-[:checked]:text-emerald-700 dark:group-has-[:checked]:text-emerald-300 group-has-[:checked]:font-bold transition-all text-center leading-tight"><?php echo e($pm['value']); ?></span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

         <?php $__env->slot('footer', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'button','variant' => 'outline','xOn:click' => 'show = false']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'outline','x-on:click' => 'show = false']); ?>Cancel <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','form' => 'add-expense-form','variant' => 'primary','icon' => 'check','class' => 'px-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','form' => 'add-expense-form','variant' => 'primary','icon' => 'check','class' => 'px-8']); ?>Log Expense <?php echo $__env->renderComponent(); ?>
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
    </form>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>

<?php if (isset($component)) { $__componentOriginal9f64f32e90b9102968f2bc548315018c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9f64f32e90b9102968f2bc548315018c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.modal','data' => ['name' => 'edit-expense','title' => 'Edit Expense','subtitle' => 'Update operational expenditure details','icon' => 'edit','maxWidth' => '720']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('modal'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'edit-expense','title' => 'Edit Expense','subtitle' => 'Update operational expenditure details','icon' => 'edit','maxWidth' => '720']); ?>
    <form :action="editAction" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        
        <div class="mb-8">
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-3.5">
                Category <span class="text-zinc-400 dark:text-zinc-500 text-xs ml-0.5">*</span>
            </label>
            <div class="grid grid-cols-6 gap-3">
                <?php $catIcons = ['Fuel' => 'local_gas_station', 'Salary' => 'payments', 'Transport' => 'local_shipping', 'Utility' => 'bolt', 'Purchase' => 'shopping_cart', 'Misc' => 'more_horiz']; ?>
                <?php $catColors = ['Fuel' => 'text-orange-500', 'Salary' => 'text-blue-500', 'Transport' => 'text-amber-500', 'Utility' => 'text-purple-500', 'Purchase' => 'text-teal-500', 'Misc' => 'text-zinc-400']; ?>
                <?php $__currentLoopData = ['Fuel','Salary','Transport','Utility','Purchase','Misc']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="group relative flex flex-col items-center gap-2 py-4 px-2 rounded-2xl border-2 cursor-pointer transition-all duration-200 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/80 dark:has-[:checked]:bg-emerald-500/12 has-[:checked]:shadow-[0_0_0_1px_rgba(16,185,129,0.15),0_4px_12px_rgba(16,185,129,0.15)] border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600 bg-white/50 dark:bg-zinc-900/50">
                    <input type="radio" name="category" value="<?php echo e($c); ?>" class="sr-only" x-model="editCategory" required>
                    <div class="relative">
                        <span class="material-symbols-rounded text-[28px] <?php echo e($catColors[$c]); ?>"><?php echo e($catIcons[$c]); ?></span>
                        <span class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 text-white flex items-center justify-center scale-0 group-has-[:checked]:scale-100 transition-transform duration-200">
                            <span class="material-symbols-rounded text-[12px]">check</span>
                        </span>
                    </div>
                    <span class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 group-has-[:checked]:text-emerald-700 dark:group-has-[:checked]:text-emerald-300 group-has-[:checked]:font-bold transition-all"><?php echo e($c); ?></span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-8">
            <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['type' => 'date','name' => 'date','label' => 'Date','required' => true,'xModel' => 'editDate','icon' => 'calendar_month']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'date','name' => 'date','label' => 'Date','required' => true,'x-model' => 'editDate','icon' => 'calendar_month']); ?>
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
            <?php if (isset($component)) { $__componentOriginal5c2a97ab476b69c1189ee85d1a95204b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal5c2a97ab476b69c1189ee85d1a95204b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.input','data' => ['type' => 'number','name' => 'amount','label' => 'Amount','required' => true,'step' => '0.01','min' => '0.01','placeholder' => 'Enter amount','icon' => 'currency_rupee','xModel' => 'editAmount']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.input'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'number','name' => 'amount','label' => 'Amount','required' => true,'step' => '0.01','min' => '0.01','placeholder' => 'Enter amount','icon' => 'currency_rupee','x-model' => 'editAmount']); ?>
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

        
        <div class="mb-8">
            <?php if (isset($component)) { $__componentOriginalcd97a59301ba78d56b3ed60dd41409ab = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.form.textarea','data' => ['name' => 'description','label' => 'Description','required' => true,'placeholder' => 'e.g. Purchased poultry feed from ABC Traders','rows' => '4','xModel' => 'editDescription']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('form.textarea'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'description','label' => 'Description','required' => true,'placeholder' => 'e.g. Purchased poultry feed from ABC Traders','rows' => '4','x-model' => 'editDescription']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab)): ?>
<?php $attributes = $__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab; ?>
<?php unset($__attributesOriginalcd97a59301ba78d56b3ed60dd41409ab); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalcd97a59301ba78d56b3ed60dd41409ab)): ?>
<?php $component = $__componentOriginalcd97a59301ba78d56b3ed60dd41409ab; ?>
<?php unset($__componentOriginalcd97a59301ba78d56b3ed60dd41409ab); ?>
<?php endif; ?>
        </div>

        
        <div class="mb-2">
            <label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit mb-3.5">
                Payment Method <span class="text-zinc-400 dark:text-zinc-500 text-xs ml-0.5">*</span>
            </label>
            <div class="grid grid-cols-4 gap-2.5">
                <?php $pmOptions = [['value' => 'Cash', 'icon' => 'payments', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50'], ['value' => 'Bank Transfer', 'icon' => 'account_balance', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50'], ['value' => 'UPI', 'icon' => 'smartphone', 'color' => 'text-violet-500', 'bg' => 'bg-violet-50'], ['value' => 'Card', 'icon' => 'credit_card', 'color' => 'text-rose-500', 'bg' => 'bg-rose-50']]; ?>
                <?php $__currentLoopData = $pmOptions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pm): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="group relative flex flex-col items-center gap-2 py-4 px-1 rounded-2xl border-2 cursor-pointer transition-all duration-200 has-[:checked]:border-emerald-500 has-[:checked]:bg-emerald-50/80 dark:has-[:checked]:bg-emerald-500/12 has-[:checked]:shadow-[0_0_0_1px_rgba(16,185,129,0.15),0_4px_12px_rgba(16,185,129,0.15)] border-zinc-200 dark:border-zinc-700 hover:border-zinc-300 dark:hover:border-zinc-600 bg-white/50 dark:bg-zinc-900/50">
                    <input type="radio" name="payment_method" value="<?php echo e($pm['value']); ?>" class="sr-only" x-model="editPaymentMethod">
                    <div class="w-9 h-9 rounded-full <?php echo e($pm['bg']); ?> dark:<?php echo e($pm['bg']); ?>/10 flex items-center justify-center <?php echo e($pm['color']); ?>">
                        <span class="material-symbols-rounded text-xl"><?php echo e($pm['icon']); ?></span>
                    </div>
                    <span class="text-[11px] font-semibold text-zinc-500 dark:text-zinc-400 group-has-[:checked]:text-emerald-700 dark:group-has-[:checked]:text-emerald-300 group-has-[:checked]:font-bold transition-all text-center leading-tight"><?php echo e($pm['value']); ?></span>
                </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

         <?php $__env->slot('footer', null, []); ?> 
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'button','variant' => 'outline','xOn:click' => 'show = false']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'button','variant' => 'outline','x-on:click' => 'show = false']); ?>Cancel <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','variant' => 'primary','icon' => 'check','class' => 'px-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','icon' => 'check','class' => 'px-8']); ?>Save Changes <?php echo $__env->renderComponent(); ?>
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
    </form>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $attributes = $__attributesOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__attributesOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9f64f32e90b9102968f2bc548315018c)): ?>
<?php $component = $__componentOriginal9f64f32e90b9102968f2bc548315018c; ?>
<?php unset($__componentOriginal9f64f32e90b9102968f2bc548315018c); ?>
<?php endif; ?>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\expenses\emis\index.blade.php ENDPATH**/ ?>