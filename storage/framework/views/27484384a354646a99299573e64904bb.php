<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'label' => null,
    'title' => null,
    'value',
    'icon' => null,
    'trend' => null,
    'trendUp' => true,
    'color' => 'emerald',
    'prefix' => '',
    'subtitle' => null,
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
    'label' => null,
    'title' => null,
    'value',
    'icon' => null,
    'trend' => null,
    'trendUp' => true,
    'color' => 'emerald',
    'prefix' => '',
    'subtitle' => null,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $iconColors = [
        'emerald' => 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 shadow-lg shadow-emerald-500/20 dark:bg-emerald-500/20 dark:text-emerald-400',
        'blue' => 'bg-blue-500/10 text-blue-600 border border-blue-500/20 shadow-lg shadow-blue-500/20 dark:bg-blue-500/20 dark:text-blue-400',
        'amber' => 'bg-amber-500/10 text-amber-600 border border-amber-500/20 shadow-lg shadow-amber-500/20 dark:bg-amber-500/20 dark:text-amber-400',
        'rose' => 'bg-rose-500/10 text-rose-600 border border-rose-500/20 shadow-lg shadow-rose-500/20 dark:bg-rose-500/20 dark:text-rose-400',
        'zinc' => 'bg-zinc-500/10 text-zinc-600 border border-zinc-500/20 shadow-lg shadow-zinc-500/20 dark:bg-zinc-800/50 dark:text-zinc-400',
    ];
    $iconClass = $iconColors[$color] ?? $iconColors['emerald'];
?>

<div class="group relative rounded-[2rem] border border-white/80 dark:border-zinc-700/50 bg-gradient-to-br from-white/60 to-white/30 dark:from-zinc-800/60 dark:to-zinc-900/40 backdrop-blur-3xl p-5 shadow-xl shadow-zinc-200/40 dark:shadow-none hover:shadow-2xl hover:shadow-zinc-300/50 dark:hover:shadow-[0_0_40px_-10px_rgba(16,185,129,0.15)] transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:-translate-y-2 overflow-hidden">
    <!-- Subtle hover glow background -->
    <div class="absolute inset-0 bg-gradient-to-br from-white/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
    
    <div class="relative z-10 flex items-start justify-between gap-2">
        <div class="min-w-0 flex-1">
            <p class="font-outfit text-xs sm:text-sm font-medium text-zinc-500 dark:text-zinc-400 truncate"><?php echo e($label ?? $title); ?></p>
            <p class="font-outfit mt-1.5 text-base sm:text-lg lg:text-xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50 drop-shadow-sm flex items-center gap-1">
                <?php if($prefix): ?>
                    <span class="text-[10px] sm:text-xs font-semibold text-zinc-500 dark:text-zinc-400"><?php echo e($prefix); ?></span>
                <?php endif; ?>
                <span class="truncate"><?php echo e($value); ?></span>
            </p>
        </div>
        <?php if($icon): ?>
            <div class="flex h-10 w-10 sm:h-12 sm:w-12 shrink-0 items-center justify-center rounded-xl sm:rounded-2xl <?php echo e($iconClass); ?> transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3">
                <?php if(Str::startsWith($icon, 'ph-')): ?>
                    <i class="ph <?php echo e($icon); ?> text-xl sm:text-2xl leading-none flex items-center justify-center"></i>
                <?php else: ?>
                    <span class="material-symbols-rounded text-xl sm:text-2xl leading-none flex items-center justify-center"><?php echo e($icon); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if($trend): ?>
        <div class="mt-4 flex items-center gap-2 text-sm">
            <?php if($trendUp): ?>
                <span class="flex items-center text-emerald-600 dark:text-emerald-400 font-medium">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    <?php echo e($trend); ?>

                </span>
            <?php else: ?>
                <span class="flex items-center text-rose-600 dark:text-rose-400 font-medium">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                    <?php echo e($trend); ?>

                </span>
            <?php endif; ?>
            <span class="text-zinc-500 dark:text-zinc-400 font-outfit">vs last month</span>
        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\components\stat-card.blade.php ENDPATH**/ ?>