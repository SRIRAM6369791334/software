<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'items' => []
]));

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

foreach (array_filter(([
    'items' => []
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<nav class="flex text-sm text-zinc-500 dark:text-zinc-400 font-outfit mb-6" aria-label="Breadcrumb">
    <ol class="inline-flex items-center space-x-1 md:space-x-2">
        <?php $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="inline-flex items-center">
                <?php if($index > 0): ?>
                    <span class="material-symbols-rounded text-sm mx-1 text-zinc-400">chevron_right</span>
                <?php endif; ?>
                
                <?php if(isset($item['url']) && !$loop->last): ?>
                    <a href="<?php echo e($item['url']); ?>" class="inline-flex items-center hover:text-emerald-500 dark:hover:text-emerald-400 transition-colors font-medium">
                        <?php echo e($item['label']); ?>

                    </a>
                <?php else: ?>
                    <span class="text-zinc-900 dark:text-zinc-100 font-semibold" aria-current="page">
                        <?php echo e($item['label']); ?>

                    </span>
                <?php endif; ?>
            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ol>
</nav>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\components\breadcrumb.blade.php ENDPATH**/ ?>