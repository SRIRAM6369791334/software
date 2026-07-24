<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => '',
    'label' => '',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'error' => null,
    'icon' => null,
    'hint' => null,
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
    'name' => '',
    'label' => '',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'error' => null,
    'icon' => null,
    'hint' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="w-full font-outfit">
    <?php if($label): ?>
        <label for="<?php echo e($name); ?>" class="block mb-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit">
            <?php echo e($label); ?>

            <?php if($required): ?>
                <span class="text-emerald-500 font-bold ml-0.5 drop-shadow-[0_0_8px_rgba(16,185,129,0.5)]">*</span>
            <?php endif; ?>
        </label>
    <?php endif; ?>

    <div class="relative">
        <?php if($icon): ?>
            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-600 dark:text-zinc-400 z-10">
                <span class="material-symbols-rounded text-[22px]"><?php echo e($icon); ?></span>
            </div>
        <?php endif; ?>

        <input 
            type="<?php echo e($type); ?>" 
            name="<?php echo e($name); ?>" 
            id="<?php echo e($name); ?>" 
            value="<?php echo e(old($name, $value)); ?>"
            <?php echo e($required ? 'required' : ''); ?>

            <?php echo e($attributes->merge(['class' => 'block w-full bg-white/30 dark:bg-zinc-900/30 backdrop-blur-2xl border border-zinc-200 dark:border-zinc-700 shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)] text-zinc-900 dark:text-zinc-100 text-sm rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 ' . ($icon ? 'pl-11' : 'pl-4') . ' p-3 transition-all duration-300 ' . ($errors->has($name) || $error ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'hover:border-zinc-300 dark:hover:border-zinc-600')])); ?>

            placeholder="<?php echo e($placeholder); ?>"
        >
    </div>

    <?php if($errors->has($name) || $error): ?>
        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
            <span class="material-symbols-rounded text-sm">error</span>
            <?php echo e($error ?? $errors->first($name)); ?>

        </p>
    <?php elseif($hint): ?>
        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400"><?php echo e($hint); ?></p>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\components\form\input.blade.php ENDPATH**/ ?>