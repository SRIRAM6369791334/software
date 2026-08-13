
<?php $__env->startSection('title', 'Profit & Loss Overview'); ?>

<?php $__env->startSection('content'); ?>
<?php if (isset($component)) { $__componentOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalf8d4ea307ab1e58d4e472a43c8548d8e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.page-header','data' => ['title' => 'Profit & Loss Dashboard','subtitle' => 'Real-time financial performance overview']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('page-header'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Profit & Loss Dashboard','subtitle' => 'Real-time financial performance overview']); ?>
    <div class="flex gap-3">
        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'secondary','href' => ''.e(route('profit.monthly')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'secondary','href' => ''.e(route('profit.monthly')).'']); ?>Monthly Breakdown <?php echo $__env->renderComponent(); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','href' => ''.e(route('profit.expense-vs-income')).'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','href' => ''.e(route('profit.expense-vs-income')).'']); ?>Expense vs Income <?php echo $__env->renderComponent(); ?>
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


<!-- <div class="mb-6 p-5 rounded-2xl bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-950/40 dark:to-teal-950/40 border border-emerald-200/80 dark:border-emerald-800/40 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
    <div class="flex items-center gap-3">
        <div class="h-10 w-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-bold text-lg shadow-md">
            <span class="material-symbols-rounded">calculate</span>
        </div>
        <div>
            <h3 class="font-cabinet text-base font-extrabold text-emerald-950 dark:text-emerald-100">Profit & Loss Calculation Formula</h3>
            <p class="text-xs font-mono text-emerald-800 dark:text-emerald-300 font-semibold mt-0.5">
                Net Profit = Total Bills (Dealer & Customer Payments) − Total Vendor Pay − Total Expenses
            </p>
        </div>
    </div>
    <div class="text-xs font-bold text-emerald-700 dark:text-emerald-400 bg-white/80 dark:bg-zinc-900/80 px-4 py-2 rounded-xl border border-emerald-200 dark:border-emerald-800 shadow-xs">
        Includes Weight Loss Expenses & Operational Burn
    </div>
</div> -->

<?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'mb-6 !p-4 border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'mb-6 !p-4 border border-zinc-200/80 dark:border-zinc-800/80 shadow-xs']); ?>
    <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
        <form method="GET" class="flex flex-wrap lg:flex-nowrap items-center gap-3">
            <div class="flex items-center gap-2 bg-zinc-100/80 dark:bg-zinc-800/80 px-3 py-1.5 rounded-xl border border-zinc-200/50 dark:border-zinc-700/50">
                <span class="material-symbols-rounded text-emerald-500 text-sm">calendar_today</span>
                <span class="text-xs font-black text-zinc-600 dark:text-zinc-300 uppercase tracking-wider">Year:</span>
                <select name="year" onchange="this.form.submit()" class="text-xs font-extrabold rounded-lg border-0 bg-transparent text-zinc-900 dark:text-zinc-100 focus:ring-0 py-0 pl-1 pr-6 cursor-pointer">
                    <option value="" class="dark:bg-zinc-900">-- Custom Range --</option>
                    <?php $__currentLoopData = $availableYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($yr); ?>" <?php echo e((string)$selectedYear === (string)$yr ? 'selected' : ''); ?> class="dark:bg-zinc-900">Year <?php echo e($yr); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>

            <div class="h-6 w-px bg-zinc-200 dark:bg-zinc-800 hidden sm:block"></div>

            <div class="flex items-center gap-2">
                <input type="date" name="start_date" value="<?php echo e($startDate); ?>" class="text-xs font-bold rounded-xl border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-2 px-3">
                <span class="text-zinc-400 font-bold">→</span>
                <input type="date" name="end_date" value="<?php echo e($endDate); ?>" class="text-xs font-bold rounded-xl border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 text-zinc-900 dark:text-zinc-100 shadow-2xs focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 py-2 px-3">
            </div>

            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','variant' => 'primary','class' => '!py-2 !px-4 !text-xs font-bold shadow-xs']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'primary','class' => '!py-2 !px-4 !text-xs font-bold shadow-xs']); ?>
                <span class="material-symbols-rounded text-sm mr-1">filter_alt</span> Filter
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

            <?php if(request()->filled('start_date') || request()->filled('end_date') || request()->filled('year')): ?>
                <a href="<?php echo e(route('profit.index')); ?>" class="inline-flex items-center gap-1 px-3 py-2 text-xs font-bold rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 dark:border-rose-900/50 dark:text-rose-400 dark:hover:bg-rose-950/50 transition-all shadow-2xs">
                    <span class="material-symbols-rounded text-sm">restart_alt</span> Reset
                </a>
            <?php endif; ?>
        </form>

        <div class="flex items-center gap-2 pt-3 lg:pt-0 border-t lg:border-t-0 border-zinc-200/50 dark:border-zinc-800/50">
            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'outline','href' => ''.e(route('profit.export', ['start_date' => $startDate, 'end_date' => $endDate])).'','class' => '!py-2 !px-3.5 !text-xs font-bold']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'outline','href' => ''.e(route('profit.export', ['start_date' => $startDate, 'end_date' => $endDate])).'','class' => '!py-2 !px-3.5 !text-xs font-bold']); ?>
                <span class="material-symbols-rounded text-sm mr-1 text-emerald-600">csv</span> CSV Export
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['variant' => 'primary','href' => ''.e(route('profit.export-pdf', ['start_date' => $startDate, 'end_date' => $endDate])).'','class' => '!py-2 !px-3.5 !text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['variant' => 'primary','href' => ''.e(route('profit.export-pdf', ['start_date' => $startDate, 'end_date' => $endDate])).'','class' => '!py-2 !px-3.5 !text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white']); ?>
                <span class="material-symbols-rounded text-sm mr-1">picture_as_pdf</span> PDF Report
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
        </div>
    </div>
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


<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <?php if (isset($component)) { $__componentOriginal527fae77f4db36afc8c8b7e9f5f81682 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal527fae77f4db36afc8c8b7e9f5f81682 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => '1. Total Billed Amount','value' => 'Rs '.e(number_format($breakdown['total_billed'], 2)).'','icon' => 'receipt_long','color' => 'emerald']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => '1. Total Billed Amount','value' => 'Rs '.e(number_format($breakdown['total_billed'], 2)).'','icon' => 'receipt_long','color' => 'emerald']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => '2. Dealer Paid Amount','value' => 'Rs '.e(number_format($breakdown['dealer_paid'], 2)).'','icon' => 'payments','color' => 'teal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => '2. Dealer Paid Amount','value' => 'Rs '.e(number_format($breakdown['dealer_paid'], 2)).'','icon' => 'payments','color' => 'teal']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => '3. Dealer Pending Balance','value' => 'Rs '.e(number_format($breakdown['dealer_pending'], 2)).'','icon' => 'pending_actions','color' => 'amber']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => '3. Dealer Pending Balance','value' => 'Rs '.e(number_format($breakdown['dealer_pending'], 2)).'','icon' => 'pending_actions','color' => 'amber']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => '4. Total Vendor Cost','value' => 'Rs '.e(number_format($breakdown['vendor_cost'], 2)).'','icon' => 'inventory_2','color' => 'purple']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => '4. Total Vendor Cost','value' => 'Rs '.e(number_format($breakdown['vendor_cost'], 2)).'','icon' => 'inventory_2','color' => 'purple']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => '5. Vendor Paid Amount','value' => 'Rs '.e(number_format($breakdown['vendor_paid'], 2)).'','icon' => 'outbound','color' => 'blue']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => '5. Vendor Paid Amount','value' => 'Rs '.e(number_format($breakdown['vendor_paid'], 2)).'','icon' => 'outbound','color' => 'blue']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => '6. Vendor Pending Payable','value' => 'Rs '.e(number_format($breakdown['vendor_pending'], 2)).'','icon' => 'account_balance','color' => 'indigo']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => '6. Vendor Pending Payable','value' => 'Rs '.e(number_format($breakdown['vendor_pending'], 2)).'','icon' => 'account_balance','color' => 'indigo']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => '7. Total Expenses','value' => 'Rs '.e(number_format($breakdown['total_expenses'], 2)).'','icon' => 'trending_up','color' => 'rose']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => '7. Total Expenses','value' => 'Rs '.e(number_format($breakdown['total_expenses'], 2)).'','icon' => 'trending_up','color' => 'rose']); ?>
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
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.stat-card','data' => ['label' => '8. Net Profit / Loss','value' => 'Rs '.e(number_format($breakdown['net_profit'], 2)).'','icon' => 'account_balance_wallet','color' => ''.e($breakdown['net_profit'] >= 0 ? 'emerald' : 'rose').'']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('stat-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['label' => '8. Net Profit / Loss','value' => 'Rs '.e(number_format($breakdown['net_profit'], 2)).'','icon' => 'account_balance_wallet','color' => ''.e($breakdown['net_profit'] >= 0 ? 'emerald' : 'rose').'']); ?>
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
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'lg:col-span-2']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:col-span-2']); ?>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 mb-4 pb-3 border-b border-zinc-200/50 dark:border-zinc-800/50">
            <div class="flex flex-wrap items-center gap-2.5">
                <h2 class="font-cabinet text-lg font-extrabold text-zinc-900 dark:text-zinc-50 flex items-center gap-2">
                    <span class="material-symbols-rounded text-emerald-500 text-[20px]">analytics</span>
                    Revenue vs Expenses & Vendor Pay Trend
                </h2>
                <?php if($breakdown['net_profit'] >= 0): ?>
                    <span class="hidden sm:inline-flex items-center gap-1 text-xs font-black text-emerald-700 bg-emerald-100 dark:bg-emerald-950 dark:text-emerald-300 px-2.5 py-1 rounded-lg shadow-2xs">
                        <span class="material-symbols-rounded text-sm">trending_up</span> Profit: <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $breakdown['net_profit']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($breakdown['net_profit'])]); ?>
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
                <?php endif; ?>
            </div>
            <div class="flex items-center gap-1.5 bg-zinc-100 dark:bg-zinc-800/80 p-1 rounded-xl shadow-inner">
                <button type="button" onclick="setChartHorizon(1)" id="btn-horizon-1" class="px-2.5 py-1 text-xs font-extrabold rounded-lg transition-all bg-emerald-600 text-white shadow-xs">This Week</button>
                <button type="button" onclick="setChartHorizon(4)" id="btn-horizon-4" class="px-2.5 py-1 text-xs font-bold rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 transition-all">4 Wks</button>
                <button type="button" onclick="setChartHorizon(12)" id="btn-horizon-12" class="px-2.5 py-1 text-xs font-bold rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 transition-all">12 Wks</button>
                <button type="button" onclick="setChartHorizon(26)" id="btn-horizon-26" class="px-2.5 py-1 text-xs font-bold rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 transition-all">26 Wks</button>
                <button type="button" onclick="setChartHorizon(52)" id="btn-horizon-52" class="px-2.5 py-1 text-xs font-bold rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 transition-all">1 Year</button>
            </div>
        </div>
        <div class="relative h-72">
            <canvas id="weeklyChart" class="w-full h-full"></canvas>
        </div>
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

    <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'lg:col-span-1','title' => 'Financial Allocation Breakdown']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'lg:col-span-1','title' => 'Financial Allocation Breakdown']); ?>
        <div class="relative h-72 flex items-center justify-center">
            <canvas id="collectionChart" class="w-full h-full"></canvas>
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none pb-8">
                <span class="text-[11px] font-extrabold uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Total Billed</span>
                <span class="font-cabinet font-black text-base sm:text-lg text-zinc-950 dark:text-zinc-50 mt-0.5"><?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $breakdown['total_billed']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($breakdown['total_billed'])]); ?>
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
                <?php if($breakdown['total_billed'] > 0): ?>
                    <span class="inline-flex items-center gap-1 text-[11px] font-extrabold text-emerald-600 dark:text-emerald-400 mt-1 px-2.5 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200/50 dark:border-emerald-800/50 shadow-2xs">
                        <span class="material-symbols-rounded text-xs">percent</span>
                        <?php echo e(number_format(($breakdown['net_profit'] / $breakdown['total_billed']) * 100, 1)); ?>% Margin
                    </span>
                <?php endif; ?>
            </div>
        </div>
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


<?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'Weekly Performance Report (Select Any Week to View Itemized Details)','class' => 'mb-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Weekly Performance Report (Select Any Week to View Itemized Details)','class' => 'mb-8']); ?>
    <?php if (isset($component)) { $__componentOriginalc8463834ba515134d5c98b88e1a9dc03 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data-table','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
         <?php $__env->slot('head', null, []); ?> 
            <tr>
                <th class="px-6 py-4 font-semibold tracking-wider">Week Period</th>
                <th class="px-6 py-4 font-semibold tracking-wider text-right">Total Bills / Inflow</th>
                <th class="px-6 py-4 font-semibold tracking-wider text-right">Total Vendor Pay</th>
                <th class="px-6 py-4 font-semibold tracking-wider text-right">Total Expenses</th>
                <th class="px-6 py-4 font-semibold tracking-wider text-right">Net Profit / Loss</th>
                <th class="px-6 py-4 font-semibold tracking-wider text-center">Action</th>
            </tr>
         <?php $__env->endSlot(); ?>
        <?php $__currentLoopData = $weeklyData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="hover:bg-emerald-50/50 dark:hover:bg-zinc-800/40 transition-colors group cursor-pointer" onclick="window.location='<?php echo e(route('profit.weekly-detail', ['start_date' => $row['start_date'], 'end_date' => $row['end_date']])); ?>'">
            <td class="px-6 py-4">
                <div class="font-black text-zinc-950 dark:text-zinc-50 text-base"><?php echo e($row['week']); ?></div>
                <div class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 mt-0.5"><?php echo e($row['week_label'] ?? ''); ?></div>
            </td>
            <td class="px-6 py-4 text-right font-mono text-emerald-600 dark:text-emerald-400 font-extrabold text-base"><?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $row['revenue']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['revenue'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $attributes = $__attributesOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__attributesOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $component = $__componentOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__componentOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?></td>
            <td class="px-6 py-4 text-right font-mono text-purple-600 dark:text-purple-400 font-extrabold text-base"><?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $row['purchase']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['purchase'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $attributes = $__attributesOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__attributesOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $component = $__componentOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__componentOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?></td>
            <td class="px-6 py-4 text-right font-mono text-rose-600 dark:text-rose-400 font-extrabold text-base"><?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $row['expenses']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['expenses'])]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $attributes = $__attributesOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__attributesOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ad77814db6844366c1e7089b9401721)): ?>
<?php $component = $__componentOriginal6ad77814db6844366c1e7089b9401721; ?>
<?php unset($__componentOriginal6ad77814db6844366c1e7089b9401721); ?>
<?php endif; ?></td>
            <td class="px-6 py-4 text-right font-black text-base <?php echo e($row['profit'] >= 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300'); ?>">
                <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $row['profit']]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($row['profit'])]); ?>
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
            <td class="px-6 py-4 text-center" onclick="event.stopPropagation()">
                <a href="<?php echo e(route('profit.weekly-detail', ['start_date' => $row['start_date'], 'end_date' => $row['end_date']])); ?>" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-extrabold rounded-xl bg-emerald-100 text-emerald-800 hover:bg-emerald-200 dark:bg-emerald-950 dark:text-emerald-300 transition-all shadow-2xs">
                    View Details <span class="material-symbols-rounded text-sm">arrow_forward</span>
                </a>
            </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
    <?php if($weeklyData->hasPages()): ?>
        <div class="p-4 border-t border-zinc-200/50 dark:border-zinc-800/50">
            <?php echo e($weeklyData->links()); ?>

        </div>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const allWeeksData = <?php echo json_encode($allWeeks ?? [], 15, 512) ?>;
    let currentHorizon = 1;
    let weeklyChartInstance = null;

    function buildChartData(weeksCount) {
        const sliced = allWeeksData.slice(-weeksCount);
        return {
            labels: sliced.map(d => d.week_label || d.week),
            revenue: sliced.map(d => d.revenue),
            expenses: sliced.map(d => d.expenses + d.purchase),
            profit: sliced.map(d => d.profit)
        };
    }

    const ctxWeekly = document.getElementById('weeklyChart').getContext('2d');
    
    const revGrad = ctxWeekly.createLinearGradient(0, 0, 0, 300);
    revGrad.addColorStop(0, 'rgba(16, 185, 129, 0.9)');
    revGrad.addColorStop(1, 'rgba(16, 185, 129, 0.08)');

    const expGrad = ctxWeekly.createLinearGradient(0, 0, 0, 300);
    expGrad.addColorStop(0, 'rgba(244, 63, 94, 0.9)');
    expGrad.addColorStop(1, 'rgba(244, 63, 94, 0.08)');

    const initialData = buildChartData(currentHorizon);

    weeklyChartInstance = new Chart(ctxWeekly, {
        type: 'bar',
        data: {
            labels: initialData.labels,
            datasets: [
                {
                    label: 'Revenue / Bills',
                    data: initialData.revenue,
                    backgroundColor: revGrad,
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1.5,
                    borderRadius: { topLeft: 8, topRight: 8 },
                    borderSkipped: 'bottom',
                    barPercentage: 0.55,
                    maxBarThickness: 44,
                },
                {
                    label: 'Vendor Pay & Expenses',
                    data: initialData.expenses,
                    backgroundColor: expGrad,
                    borderColor: 'rgba(244, 63, 94, 1)',
                    borderWidth: 1.5,
                    borderRadius: { topLeft: 8, topRight: 8 },
                    borderSkipped: 'bottom',
                    barPercentage: 0.55,
                    maxBarThickness: 44,
                },
                {
                    label: 'Net Profit',
                    data: initialData.profit,
                    type: 'line',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.06)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: 'rgba(59, 130, 246, 1)',
                    pointBorderWidth: 2.5,
                    pointRadius: currentHorizon > 12 ? 0 : 5,
                    pointHoverRadius: 7
                }
            ]
        },
        plugins: [{
            id: 'topValuesPlugin',
            afterDatasetsDraw(chart) {
                if (currentHorizon > 12) return;
                const { ctx } = chart;
                chart.data.datasets.forEach((dataset, datasetIndex) => {
                    const meta = chart.getDatasetMeta(datasetIndex);
                    meta.data.forEach((element, index) => {
                        const val = dataset.data[index];
                        if (val === undefined || val === null || val === 0) return;
                        
                        let text = '';
                        if (val >= 10000000) text = 'Rs ' + (val / 10000000).toFixed(1) + 'Cr';
                        else if (val >= 100000) text = 'Rs ' + (val / 100000).toFixed(2) + 'L';
                        else if (val >= 1000) text = 'Rs ' + (val / 1000).toFixed(1) + 'k';
                        else text = 'Rs ' + val;

                        ctx.save();
                        ctx.font = 'bold 11px Outfit, sans-serif';
                        ctx.fillStyle = datasetIndex === 0 ? '#047857' : (datasetIndex === 1 ? '#be123c' : '#1d4ed8');
                        ctx.textAlign = 'center';
                        ctx.textBaseline = 'bottom';
                        ctx.fillText(text, element.x, element.y - 5);
                        ctx.restore();
                    });
                });
            }
        }],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: 'index', intersect: false },
            plugins: {
                legend: { 
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 18, font: { family: "'Outfit', sans-serif", size: 12, weight: '600' } }
                },
                tooltip: {
                    backgroundColor: 'rgba(24, 24, 27, 0.95)',
                    titleFont: { family: "'Cabinet Grotesk', sans-serif", size: 14 },
                    bodyFont: { family: "'Outfit', sans-serif", size: 13 },
                    padding: 12,
                    cornerRadius: 12,
                    displayColors: true,
                    usePointStyle: true,
                    callbacks: {
                        label: function(ctx) {
                            const val = ctx.raw || 0;
                            return ' ' + ctx.dataset.label + ': Rs ' + val.toLocaleString('en-IN', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            scales: {
                x: { 
                    grid: { display: false }, 
                    ticks: { 
                        font: { family: "'Outfit', sans-serif", size: 11, weight: '600' },
                        maxRotation: 0,
                        minRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 7
                    } 
                },
                y: { 
                    beginAtZero: true, 
                    grace: '10%',
                    grid: { color: 'rgba(161, 161, 170, 0.12)', borderDash: [4, 4] }, 
                    ticks: { 
                        font: { family: "'Outfit', sans-serif", size: 11 },
                        callback: function(val) {
                            if (val >= 10000000) return 'Rs ' + (val / 10000000).toFixed(1) + 'Cr';
                            if (val >= 100000) return 'Rs ' + (val / 100000).toFixed(1) + 'L';
                            if (val >= 1000) return 'Rs ' + (val / 1000).toFixed(0) + 'k';
                            return 'Rs ' + val;
                        }
                    } 
                }
            }
        }
    });

    window.setChartHorizon = function(weeksCount) {
        currentHorizon = weeksCount;
        [1, 4, 12, 26, 52].forEach(w => {
            const btn = document.getElementById('btn-horizon-' + w);
            if (btn) {
                if (w === weeksCount) {
                    btn.className = "px-2.5 py-1 text-xs font-extrabold rounded-lg transition-all bg-emerald-600 text-white shadow-xs";
                } else {
                    btn.className = "px-2.5 py-1 text-xs font-bold rounded-lg text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 transition-all";
                }
            }
        });

        const newData = buildChartData(weeksCount);
        weeklyChartInstance.data.labels = newData.labels;
        weeklyChartInstance.data.datasets[0].data = newData.revenue;
        weeklyChartInstance.data.datasets[1].data = newData.expenses;
        weeklyChartInstance.data.datasets[2].data = newData.profit;
        
        // Hide point dots when showing long horizons (> 12 weeks) to eliminate blue circle clutter
        weeklyChartInstance.data.datasets[2].pointRadius = weeksCount > 12 ? 0 : 5;
        weeklyChartInstance.data.datasets[2].pointHoverRadius = 7;

        weeklyChartInstance.update();
    };

    // Financial Allocation Doughnut Chart
    const breakdown = <?php echo json_encode($breakdown, 15, 512) ?>;
    const vCost    = parseFloat(breakdown.vendor_cost || 0);
    const exps     = parseFloat(breakdown.total_expenses || 0);
    const netProf  = Math.max(0, parseFloat(breakdown.net_profit || 0));
    const dPending = parseFloat(breakdown.dealer_pending || 0);

    const hasData = (vCost + exps + netProf + dPending) > 0;

    const ctxColl = document.getElementById('collectionChart').getContext('2d');

    new Chart(ctxColl, {
        type: 'doughnut',
        data: {
            labels: hasData ? ['Vendor Cost', 'Expenses', 'Net Profit', 'Dealer Pending'] : ['No Data'],
            datasets: [{
                data: hasData ? [vCost, exps, netProf, dPending] : [1],
                backgroundColor: hasData 
                    ? ['#a855f7', '#f43f5e', '#10b981', '#f59e0b']
                    : ['#e5e7eb'],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    display: hasData,
                    labels: { usePointStyle: true, padding: 14, font: { family: "'Outfit', sans-serif", size: 11, weight: '600' } }
                },
                tooltip: {
                    enabled: hasData,
                    backgroundColor: 'rgba(24, 24, 27, 0.95)',
                    titleFont: { family: "'Cabinet Grotesk', sans-serif", size: 14 },
                    bodyFont: { family: "'Outfit', sans-serif", size: 12 },
                    padding: 12,
                    cornerRadius: 12,
                    callbacks: {
                        label: function(ctx) {
                            const val = ctx.raw || 0;
                            return ' ' + ctx.label + ': Rs ' + val.toLocaleString('en-IN', {minimumFractionDigits: 2});
                        }
                    }
                }
            },
            cutout: '72%',
            layout: { padding: 10 }
        }
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/profit/index.blade.php ENDPATH**/ ?>