<?php $__env->startSection('title', 'Expense Categories'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-8 animate-fade-in">
    <a href="<?php echo e(route('expenses.index')); ?>" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 uppercase tracking-wider mb-2">
        <span class="material-symbols-rounded text-sm">arrow_back</span>
        Back to Expenses
    </a>
    <h1 class="font-cabinet text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Expense Analysis by Category</h1>
    <p class="mt-1 font-outfit text-sm text-zinc-500 dark:text-zinc-400">Classification of business expenditures</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 animate-fade-in">
    <?php $__empty_1 = true; $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['class' => 'group hover:border-emerald-500/30 transition-all duration-300 hover:-translate-y-1']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'group hover:border-emerald-500/30 transition-all duration-300 hover:-translate-y-1']); ?>
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center justify-center w-12 h-12 bg-emerald-100/80 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-xl font-cabinet font-bold text-xl group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                        <?php echo e(strtoupper(substr($cat->category, 0, 1))); ?>

                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-bold text-zinc-400 uppercase tracking-widest font-outfit">Total Spent</p>
                        <p class="text-xl font-bold font-jetbrains text-zinc-900 dark:text-white">
                            <?php if (isset($component)) { $__componentOriginal6ad77814db6844366c1e7089b9401721 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad77814db6844366c1e7089b9401721 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.currency','data' => ['amount' => $cat->total]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('currency'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['amount' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($cat->total)]); ?>
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
                        </p>
                    </div>
                </div>
                <h3 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-white mb-1 capitalize"><?php echo e($cat->category); ?></h3>
                <p class="font-outfit text-sm text-zinc-500 dark:text-zinc-400 mb-6"><?php echo e($cat->count); ?> transactions recorded</p>
                
                <div class="flex gap-2">
                    <a href="<?php echo e(route('expenses.index', ['category' => $cat->category])); ?>" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest hover:underline">
                        View Logs
                        <span class="material-symbols-rounded text-sm">arrow_forward</span>
                    </a>
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
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-span-full">
            <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'category','title' => 'No categories','description' => 'No expense categories detected yet.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'category','title' => 'No categories','description' => 'No expense categories detected yet.']); ?>
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
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\expenses\categories\index.blade.php ENDPATH**/ ?>