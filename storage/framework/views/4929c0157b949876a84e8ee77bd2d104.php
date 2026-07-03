<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'size' => 'md',
    'color' => null,
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
    'name',
    'size' => 'md',
    'color' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $sizes = [
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-11 h-11 text-sm',
        'lg' => 'w-14 h-14 text-base',
        'xl' => 'w-20 h-20 text-xl',
    ];
    
    $sizeClass = $sizes[$size] ?? $sizes['md'];
    
    $initial = strtoupper(substr($name, 0, 1));
    
    // Generate dynamic color if not provided
    if (!$color) {
        $colors = [
            'bg-emerald-500', 'bg-blue-500', 'bg-indigo-500', 
            'bg-purple-500', 'bg-pink-500', 'bg-rose-500', 
            'bg-orange-500', 'bg-amber-500', 'bg-teal-500'
        ];
        $colorIndex = ord($initial) % count($colors);
        $color = $colors[$colorIndex] ?? 'bg-zinc-500';
    }
?>

<div <?php echo e($attributes->merge(['class' => "relative inline-flex items-center justify-center rounded-full font-cabinet text-white shadow-sm shrink-0 overflow-hidden ring-2 ring-white/20 dark:ring-zinc-900/50 transition-all duration-300 ease-[cubic-bezier(0.32,0.72,0,1)] {$sizeClass} {$color}"])); ?>>
    <span class="font-bold tracking-wider leading-none select-none"><?php echo e($initial); ?></span>
</div>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/components/avatar.blade.php ENDPATH**/ ?>