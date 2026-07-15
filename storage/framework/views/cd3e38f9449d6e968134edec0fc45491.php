<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'title' => null,
    'subtitle' => null,
    'padding' => 'p-6',
    'hover' => false,
    'glass' => true,
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
    'title' => null,
    'subtitle' => null,
    'padding' => 'p-6',
    'hover' => false,
    'glass' => true,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div <?php echo e($attributes->merge(['class' => 'rounded-[2.5rem] border border-white/80 dark:border-zinc-700/50 transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] z-10 relative overflow-hidden ' . ($glass ? 'bg-gradient-to-br from-white/60 to-white/30 dark:from-zinc-800/60 dark:to-zinc-900/40 backdrop-blur-3xl' : 'bg-white dark:bg-zinc-900') . ($hover ? ' hover:-translate-y-2 hover:shadow-2xl dark:hover:shadow-[0_0_40px_-10px_rgba(16,185,129,0.1)]' : ' shadow-[0_8px_40px_-12px_rgba(0,0,0,0.1)] dark:shadow-none')])); ?>>
    <?php if($title || isset($actions)): ?>
        <div class="flex items-center justify-between border-b border-zinc-100/80 dark:border-zinc-800/50 px-6 py-5">
            <div>
                <?php if($title): ?>
                    <h3 class="font-cabinet font-semibold text-lg text-zinc-900 dark:text-zinc-50"><?php echo e($title); ?></h3>
                <?php endif; ?>
                <?php if($subtitle): ?>
                    <p class="font-outfit text-sm text-zinc-500 dark:text-zinc-400 mt-1"><?php echo e($subtitle); ?></p>
                <?php endif; ?>
            </div>
            <?php if(isset($actions)): ?>
                <div class="flex items-center gap-3">
                    <?php echo e($actions); ?>

                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="<?php echo e($padding); ?>">
        <?php echo e($slot); ?>

    </div>
</div>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\components\card.blade.php ENDPATH**/ ?>