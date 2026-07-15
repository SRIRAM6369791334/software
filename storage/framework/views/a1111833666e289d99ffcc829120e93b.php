<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['paginator']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['paginator']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between mt-6">
    
    <div class="flex justify-between flex-1 sm:hidden">
        <?php if($paginator->onFirstPage()): ?>
            <span class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-zinc-500 bg-white/50 backdrop-blur-md border border-white/60 cursor-default rounded-full dark:bg-zinc-900/50 dark:border-zinc-800 dark:text-zinc-400 min-h-[44px] min-w-[44px] justify-center shadow-sm">
                <?php echo __('pagination.previous'); ?>

            </span>
        <?php else: ?>
            <a href="<?php echo e($paginator->previousPageUrl()); ?>" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-zinc-700 bg-white/50 backdrop-blur-md border border-white/60 rounded-full hover:bg-white/80 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 active:bg-zinc-100 transition duration-300 dark:bg-zinc-900/50 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-800 min-h-[44px] min-w-[44px] justify-center shadow-sm">
                <?php echo __('pagination.previous'); ?>

            </a>
        <?php endif; ?>

        <?php if($paginator->hasMorePages()): ?>
            <a href="<?php echo e($paginator->nextPageUrl()); ?>" class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-zinc-700 bg-white/50 backdrop-blur-md border border-white/60 rounded-full hover:bg-white/80 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-emerald-500 active:bg-zinc-100 transition duration-300 dark:bg-zinc-900/50 dark:border-zinc-800 dark:text-zinc-300 dark:hover:bg-zinc-800 min-h-[44px] min-w-[44px] justify-center shadow-sm">
                <?php echo __('pagination.next'); ?>

            </a>
        <?php else: ?>
            <span class="relative inline-flex items-center px-4 py-2 ml-3 text-sm font-medium text-zinc-500 bg-white/50 backdrop-blur-md border border-white/60 cursor-default rounded-full dark:bg-zinc-900/50 dark:border-zinc-800 dark:text-zinc-400 min-h-[44px] min-w-[44px] justify-center shadow-sm">
                <?php echo __('pagination.next'); ?>

            </span>
        <?php endif; ?>
    </div>

    
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-zinc-700 dark:text-zinc-400 leading-5">
                <?php echo __('Showing'); ?>

                <span class="font-medium font-['JetBrains_Mono']"><?php echo e($paginator->firstItem() ?? 0); ?></span>
                <?php echo __('to'); ?>

                <span class="font-medium font-['JetBrains_Mono']"><?php echo e($paginator->lastItem() ?? 0); ?></span>
                <?php echo __('of'); ?>

                <span class="font-medium font-['JetBrains_Mono']"><?php echo e($paginator->total()); ?></span>
                <?php echo __('results'); ?>

            </p>
        </div>

        <div>
            <span class="relative z-0 inline-flex shadow-sm rounded-full bg-white/50 dark:bg-zinc-900/50 backdrop-blur-md border border-white/60 dark:border-zinc-800 p-1">
                
                <?php if($paginator->onFirstPage()): ?>
                    <span aria-disabled="true" aria-label="<?php echo e(__('pagination.previous')); ?>">
                        <span class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-zinc-400 cursor-default rounded-l-full dark:text-zinc-600 min-h-[44px] min-w-[44px] justify-center" aria-hidden="true">
                            <span class="material-symbols-rounded">chevron_left</span>
                        </span>
                    </span>
                <?php else: ?>
                    <a href="<?php echo e($paginator->previousPageUrl()); ?>" rel="prev" class="relative inline-flex items-center px-2 py-2 text-sm font-medium text-zinc-500 hover:text-zinc-700 hover:bg-white/80 rounded-l-full focus:z-10 focus:outline-none transition-all duration-300 dark:text-zinc-400 dark:hover:text-zinc-200 dark:hover:bg-zinc-800/50 min-h-[44px] min-w-[44px] justify-center" aria-label="<?php echo e(__('pagination.previous')); ?>">
                        <span class="material-symbols-rounded">chevron_left</span>
                    </a>
                <?php endif; ?>

                
                <?php $__currentLoopData = $paginator->elements(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $element): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    
                    <?php if(is_string($element)): ?>
                        <span aria-disabled="true">
                            <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-zinc-700 cursor-default dark:text-zinc-400 min-h-[44px] min-w-[44px] justify-center font-['JetBrains_Mono']"><?php echo e($element); ?></span>
                        </span>
                    <?php endif; ?>

                    
                    <?php if(is_array($element)): ?>
                        <?php $__currentLoopData = $element; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page => $url): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($page == $paginator->currentPage()): ?>
                                <span aria-current="page">
                                    <span class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-white bg-emerald-500 rounded-full cursor-default shadow-md shadow-emerald-500/20 min-h-[44px] min-w-[44px] justify-center font-['JetBrains_Mono']"><?php echo e($page); ?></span>
                                </span>
                            <?php else: ?>
                                <a href="<?php echo e($url); ?>" class="relative inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-zinc-500 hover:text-zinc-700 hover:bg-white/80 rounded-full focus:z-10 focus:outline-none transition-all duration-300 dark:text-zinc-400 dark:hover:text-zinc-200 dark:hover:bg-zinc-800/50 min-h-[44px] min-w-[44px] justify-center font-['JetBrains_Mono']"><?php echo e($page); ?></a>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                
                <?php if($paginator->hasMorePages()): ?>
                    <a href="<?php echo e($paginator->nextPageUrl()); ?>" rel="next" class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-zinc-500 hover:text-zinc-700 hover:bg-white/80 rounded-r-full focus:z-10 focus:outline-none transition-all duration-300 dark:text-zinc-400 dark:hover:text-zinc-200 dark:hover:bg-zinc-800/50 min-h-[44px] min-w-[44px] justify-center" aria-label="<?php echo e(__('pagination.next')); ?>">
                        <span class="material-symbols-rounded">chevron_right</span>
                    </a>
                <?php else: ?>
                    <span aria-disabled="true" aria-label="<?php echo e(__('pagination.next')); ?>">
                        <span class="relative inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-zinc-400 cursor-default rounded-r-full dark:text-zinc-600 min-h-[44px] min-w-[44px] justify-center" aria-hidden="true">
                            <span class="material-symbols-rounded">chevron_right</span>
                        </span>
                    </span>
                <?php endif; ?>
            </span>
        </div>
    </div>
</nav>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\components\pagination.blade.php ENDPATH**/ ?>