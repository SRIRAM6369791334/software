<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['tabs' => [], 'active' => null]));

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

foreach (array_filter((['tabs' => [], 'active' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="border-b border-zinc-200 dark:border-zinc-800 w-full">
    <nav class="-mb-px flex space-x-8 overflow-x-auto no-scrollbar" aria-label="Tabs">
        <?php $__currentLoopData = $tabs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tab): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $isActive = ($active === ($tab['name'] ?? $tab['id']));
            ?>
            <a href="<?php echo e($tab['href'] ?? '#'); ?>"
               class="
                   whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-all duration-300 min-h-[44px] min-w-[44px] flex items-center justify-center
                   <?php echo e($isActive
                       ? 'border-emerald-500 text-emerald-600 dark:border-emerald-400 dark:text-emerald-400'
                       : 'border-transparent text-zinc-500 hover:text-zinc-700 hover:border-zinc-300 dark:text-zinc-400 dark:hover:text-zinc-300 dark:hover:border-zinc-700'); ?>

               "
               <?php echo e($isActive ? 'aria-current="page"' : ''); ?>>
                
                <?php if(isset($tab['icon'])): ?>
                    <span class="material-symbols-rounded mr-2 text-[20px]"><?php echo e($tab['icon']); ?></span>
                <?php endif; ?>
                
                <?php echo e($tab['label']); ?>

                
                <?php if(isset($tab['count'])): ?>
                    <span class="ml-3 rounded-full py-0.5 px-2.5 text-xs font-medium font-['JetBrains_Mono']
                        <?php echo e($isActive 
                            ? 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400' 
                            : 'bg-zinc-100 text-zinc-900 dark:bg-zinc-800 dark:text-zinc-200'); ?>">
                        <?php echo e($tab['count']); ?>

                    </span>
                <?php endif; ?>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>
</div>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\components\tabs.blade.php ENDPATH**/ ?>