<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'title' => '',
    'subtitle' => '',
    'maxWidth' => '2xl',
    'icon' => null,
    'iconColor' => 'emerald', // Deprecated: Always uses emerald for consistency
    'show' => false,
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
    'title' => '',
    'subtitle' => '',
    'maxWidth' => '2xl',
    'icon' => null,
    'iconColor' => 'emerald', // Deprecated: Always uses emerald for consistency
    'show' => false,
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$maxWidthClass = match($maxWidth) {
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    '3xl' => 'sm:max-w-3xl',
    default => 'sm:max-w-2xl',
};
?>

<div
    x-data="{
        show: <?php echo e($show ? 'true' : 'false'); ?>,
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)]
                .filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length || 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) -1 },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            setTimeout(() => firstFocusable()?.focus(), 100);
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '<?php echo e($name); ?>' ? show = true : null"
    x-on:close-modal.window="$event.detail == '<?php echo e($name); ?>' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center font-outfit"
    style="display: none;"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        class="fixed inset-0 transform transition-all"
        x-on:click="show = false"
        x-transition:enter="ease-out duration-300 cubic-bezier(0.32, 0.72, 0, 1)"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-zinc-900/60 backdrop-blur-sm"></div>
    </div>

    <!-- Modal Card -->
    <div
        x-show="show"
        class="mb-6 bg-white dark:bg-zinc-900 rounded-3xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:mx-auto <?php echo e($maxWidthClass); ?> border border-zinc-200/50 dark:border-zinc-800/50"
        x-transition:enter="ease-out duration-300 cubic-bezier(0.32, 0.72, 0, 1)"
        x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
    >
        <div class="p-6 sm:p-8">
            <!-- Header -->
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-4">
                    <?php if($icon): ?>
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                        <span class="material-symbols-rounded text-2xl"><?php echo e($icon); ?></span>
                    </div>
                    <?php endif; ?>
                    <div>
                        <?php if($title): ?>
                            <h3 class="text-xl font-bold text-zinc-900 dark:text-white font-cabinet"><?php echo e($title); ?></h3>
                        <?php endif; ?>
                        <?php if($subtitle): ?>
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400"><?php echo e($subtitle); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <button x-on:click="show = false" class="text-zinc-400 hover:text-zinc-500 dark:hover:text-zinc-300 transition-colors p-2 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>

            <!-- Body -->
            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                <?php echo e($slot); ?>

            </div>
        </div>

        <!-- Footer -->
        <?php if(isset($footer)): ?>
        <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200/50 dark:border-zinc-800/50 flex justify-end gap-3 rounded-b-3xl">
            <?php echo e($footer); ?>

        </div>
        <?php endif; ?>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\components\modal.blade.php ENDPATH**/ ?>