<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name' => '',
    'label' => false,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'required' => false,
    'error' => null,
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
    'label' => false,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'required' => false,
    'error' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="space-y-2">
    <?php if($label): ?>
        <label for="<?php echo e($name); ?>" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit">
            <?php echo e($label); ?> <?php if($required): ?> <span class="text-red-500">*</span> <?php endif; ?>
        </label>
    <?php endif; ?>

    <div class="relative">
        <select 
            id="<?php echo e($name); ?>" 
            name="<?php echo e($name); ?>" 
            <?php if($required): ?> required <?php endif; ?>
            <?php echo e($attributes->merge([
                'class' => 'appearance-none block w-full pl-3 pr-10 py-2.5 min-h-[44px] text-base border border-zinc-200 dark:border-zinc-700 shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)] hover:border-zinc-300 dark:hover:border-zinc-600 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 sm:text-sm rounded-xl bg-white/30 dark:bg-zinc-900/30 backdrop-blur-2xl text-zinc-900 dark:text-zinc-100 transition-all duration-300 ease-[cubic-bezier(0.32,0.72,0,1)]' . ($error ? ' border-red-500 focus:ring-red-500 focus:border-red-500' : '')
            ])); ?>

        >
            <?php if($placeholder): ?>
                <option value="" disabled <?php echo e(is_null($selected) ? 'selected' : ''); ?>><?php echo e($placeholder); ?></option>
            <?php endif; ?>

            <?php $__currentLoopData = $options; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $text): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($value); ?>" <?php echo e((string)$selected === (string)$value ? 'selected' : ''); ?>>
                    <?php echo e($text); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            <?php echo e($slot); ?>

        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-zinc-500 dark:text-zinc-400">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>

    <?php if($error): ?>
        <p class="text-sm text-red-600 dark:text-red-400 font-outfit mt-1"><?php echo e($error); ?></p>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/components/form/select.blade.php ENDPATH**/ ?>