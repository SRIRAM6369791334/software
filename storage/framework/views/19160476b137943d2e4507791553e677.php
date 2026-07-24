<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'icon' => 'inbox',
    'title' => 'No Data Available',
    'description' => 'There is no data to display at this time.',
    'actionText' => null,
    'actionUrl' => '#'
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
    'icon' => 'inbox',
    'title' => 'No Data Available',
    'description' => 'There is no data to display at this time.',
    'actionText' => null,
    'actionUrl' => '#'
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="flex flex-col items-center justify-center py-16 px-4 text-center font-outfit">
    <div class="w-20 h-20 bg-zinc-50 dark:bg-zinc-800/50 rounded-full flex items-center justify-center mb-6 border border-zinc-100 dark:border-zinc-800">
        <span class="material-symbols-rounded text-4xl text-zinc-400 dark:text-zinc-500"><?php echo e($icon); ?></span>
    </div>
    
    <h3 class="text-lg font-bold text-zinc-900 dark:text-white font-cabinet mb-2"><?php echo e($title); ?></h3>
    
    <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mb-8 leading-relaxed">
        <?php echo e($description); ?>

    </p>
    
    <?php if($actionText): ?>
    <a href="<?php echo e($actionUrl); ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-semibold rounded-xl transition-all shadow-sm hover:shadow shadow-emerald-500/20 focus:ring-2 focus:ring-emerald-500/50 focus:outline-none">
        <span class="material-symbols-rounded text-sm">add</span>
        <?php echo e($actionText); ?>

    </a>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\components\empty-state.blade.php ENDPATH**/ ?>