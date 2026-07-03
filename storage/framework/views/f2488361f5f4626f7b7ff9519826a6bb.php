<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'headers' => [],
    'searchable' => false,
    'searchPlaceholder' => 'Search...',
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
    'headers' => [],
    'searchable' => false,
    'searchPlaceholder' => 'Search...',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-2xl rounded-[2rem] border border-white/60 dark:border-zinc-800/50 shadow-xl shadow-zinc-200/50 overflow-hidden transition-all duration-300" x-data="{ searchQuery: '' }">
    <?php if($searchable): ?>
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-400">
                    <span class="material-symbols-rounded text-xl">search</span>
                </div>
                <input type="text" x-model="searchQuery" class="bg-zinc-50 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-zinc-900 dark:text-zinc-100 text-sm rounded-xl focus:ring-emerald-500 focus:border-emerald-500 block w-full pl-10 p-2.5 transition-colors font-outfit" placeholder="<?php echo e($searchPlaceholder); ?>">
            </div>
        </div>
    <?php endif; ?>

    <div class="overflow-x-auto [&::-webkit-scrollbar]:hidden [-ms-overflow-style:none] [scrollbar-width:none]">
        <table class="w-full text-sm text-left text-zinc-600 dark:text-zinc-400 font-outfit">
            <thead class="text-xs text-zinc-500 dark:text-zinc-400 uppercase bg-transparent border-b border-zinc-200/50 dark:border-zinc-700/50 font-cabinet [&_th]:px-6 [&_th]:py-4 [&_th]:font-semibold [&_th]:tracking-wider [&_th]:whitespace-nowrap">
                <?php if(isset($head)): ?>
                    <?php echo e($head); ?>

                <?php else: ?>
                    <tr>
                        <?php $__currentLoopData = $headers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $header): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <th scope="col">
                                <?php if(is_array($header)): ?>
                                    <?php echo e($header['label'] ?? ''); ?>

                                <?php else: ?>
                                    <?php echo e($header); ?>

                                <?php endif; ?>
                            </th>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tr>
                <?php endif; ?>
            </thead>
            <tbody class="[&_td]:px-6 [&_td]:py-4 [&_td]:whitespace-nowrap">
                <?php echo e($slot); ?>

            </tbody>
        </table>
    </div>

    <?php if(isset($empty)): ?>
        <div class="p-8 text-center" x-show="!$el.previousElementSibling.querySelector('tbody tr')">
            <?php echo e($empty); ?>

        </div>
    <?php endif; ?>

    <?php if(isset($pagination)): ?>
        <div class="p-4 border-t border-zinc-200/50 dark:border-zinc-800/50">
            <?php echo e($pagination); ?>

        </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\components\data-table.blade.php ENDPATH**/ ?>