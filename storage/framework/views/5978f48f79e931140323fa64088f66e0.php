<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'amount',
    'showSign' => true,
    'colorCode' => false,
    'currency' => '₹',
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
    'amount',
    'showSign' => true,
    'colorCode' => false,
    'currency' => '₹',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $isNegative = $amount < 0;
    $absAmount = abs($amount);
    $formatted = number_format($absAmount, 2);
    
    $colorClass = '';
    if ($colorCode) {
        $colorClass = $isNegative ? 'text-rose-600 dark:text-rose-400' : 'text-emerald-600 dark:text-emerald-400';
    }
?>

<span class="font-jetbrains-mono tracking-tight <?php echo e($colorClass); ?> <?php echo e($attributes->get('class')); ?>">
    <?php if($showSign && $isNegative): ?>-<?php endif; ?><?php echo e($currency); ?><?php echo e($formatted); ?>

</span>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/components/currency.blade.php ENDPATH**/ ?>