<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'name',
    'title' => 'Are you sure?',
    'description' => 'This action cannot be undone.',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'variant' => 'danger' // danger, primary
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
    'title' => 'Are you sure?',
    'description' => 'This action cannot be undone.',
    'confirmText' => 'Confirm',
    'cancelText' => 'Cancel',
    'variant' => 'danger' // danger, primary
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div
    x-data="{
        show: false,
        init() {
            window.addEventListener('open-confirm-dialog', (e) => {
                if (e.detail === '<?php echo e($name); ?>') {
                    this.show = true;
                }
            });
        },
        close() {
            this.show = false;
        }
    }"
    @keydown.escape.window="close()"
    x-show="show"
    class="relative z-50"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true"
    style="display: none;"
>
    
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-zinc-900/40 backdrop-blur-sm transition-opacity"
    ></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            
            <div
                x-show="show"
                @click.outside="close()"
                x-transition:enter="transition ease-[cubic-bezier(0.32,0.72,0,1)] duration-500"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-hidden rounded-3xl bg-white/90 dark:bg-zinc-900/90 backdrop-blur-xl border border-zinc-200 dark:border-zinc-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
            >
                <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full min-h-[44px] min-w-[44px] sm:mx-0 sm:h-10 sm:w-10
                            <?php echo e($variant === 'danger' ? 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' : 'bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400'); ?>">
                            <span class="material-symbols-rounded">
                                <?php echo e($variant === 'danger' ? 'warning' : 'help'); ?>

                            </span>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left">
                            <h3 class="text-lg font-['Cabinet_Grotesk'] font-bold leading-6 text-zinc-900 dark:text-zinc-100" id="modal-title">
                                <?php echo e($title); ?>

                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                    <?php echo e($description); ?>

                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-zinc-50/50 dark:bg-zinc-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 rounded-b-3xl">
                    <?php if($slot->isEmpty()): ?>
                        <button
                            type="button"
                            @click="$dispatch('confirm-' + '<?php echo e($name); ?>'); close()"
                            class="inline-flex w-full justify-center rounded-xl px-3 py-2 text-sm font-medium text-white shadow-sm sm:ml-3 sm:w-auto min-h-[44px] items-center transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-zinc-900
                            <?php echo e($variant === 'danger' ? 'bg-red-600 hover:bg-red-700 focus:ring-red-500' : 'bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500'); ?>"
                        >
                            <?php echo e($confirmText); ?>

                        </button>
                    <?php else: ?>
                        <?php echo e($slot); ?>

                    <?php endif; ?>
                    
                    <button
                        type="button"
                        @click="close()"
                        class="mt-3 inline-flex w-full justify-center rounded-xl bg-white dark:bg-zinc-800 px-3 py-2 text-sm font-medium text-zinc-900 dark:text-zinc-100 shadow-sm ring-1 ring-inset ring-zinc-300 dark:ring-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 sm:mt-0 sm:w-auto min-h-[44px] items-center transition-all duration-300 focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 dark:focus:ring-offset-zinc-900"
                    >
                        <?php echo e($cancelText); ?>

                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\components\confirm-dialog.blade.php ENDPATH**/ ?>