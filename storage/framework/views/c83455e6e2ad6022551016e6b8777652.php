<?php $__env->startSection('title', 'Vendor Details - ' . $vendor->firm_name); ?>

<?php $__env->startSection('content'); ?>
<div class="space-y-6">
    <div class="mb-4">
        <a href="<?php echo e(route('masters.vendors.index')); ?>" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition-colors">
            <span class="material-symbols-rounded text-[20px]">arrow_back</span>
            Back to Directory
        </a>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['name' => ''.e($vendor->firm_name).'','size' => 'lg']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($vendor->firm_name).'','size' => 'lg']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $attributes = $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b)): ?>
<?php $component = $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b; ?>
<?php unset($__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b); ?>
<?php endif; ?>
            <div>
                <h1 class="text-2xl font-bold font-cabinet text-zinc-900 dark:text-zinc-100 tracking-tight"><?php echo e($vendor->firm_name); ?></h1>
                <div class="flex items-center gap-2 mt-1">
                    <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['color' => 'teal']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'teal']); ?>Supplier Partner <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
                    <?php if (isset($component)) { $__componentOriginal2ddbc40e602c342e508ac696e52f8719 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal2ddbc40e602c342e508ac696e52f8719 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.badge','data' => ['color' => 'zinc']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['color' => 'zinc']); ?>
                        <span class="material-symbols-rounded text-[14px] mr-1">alt_route</span>
                        <?php echo e($vendor->route ?: 'General Sector'); ?>

                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $attributes = $__attributesOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__attributesOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal2ddbc40e602c342e508ac696e52f8719)): ?>
<?php $component = $__componentOriginal2ddbc40e602c342e508ac696e52f8719; ?>
<?php unset($__componentOriginal2ddbc40e602c342e508ac696e52f8719); ?>
<?php endif; ?>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('edit vendors')): ?>
                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('masters.vendors.edit', $vendor)).'','variant' => 'secondary','icon' => 'edit']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('masters.vendors.edit', $vendor)).'','variant' => 'secondary','icon' => 'edit']); ?>Edit Profile <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
            <?php endif; ?>
            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete vendors')): ?>
                <form action="<?php echo e(route('masters.vendors.destroy', $vendor)); ?>" method="POST" onsubmit="return confirm('Delete <?php echo e($vendor->firm_name); ?>? This will keep their transaction history intact.')">
                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                    <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['type' => 'submit','variant' => 'danger','icon' => 'delete']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['type' => 'submit','variant' => 'danger','icon' => 'delete']); ?>Delete <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-3xl p-6 bg-teal-500/40 dark:bg-teal-900/40 backdrop-blur-2xl text-teal-900 dark:text-teal-100 shadow-[0_8px_32px_rgba(20,184,166,0.15)] border border-teal-300/50 dark:border-teal-700/50 relative overflow-hidden transition-all duration-300 hover:shadow-[0_8px_32px_rgba(20,184,166,0.25)] hover:-translate-y-1">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/20 dark:bg-teal-400/10 rounded-full blur-2xl"></div>
                <div class="absolute -left-10 -bottom-10 w-32 h-32 bg-teal-400/20 dark:bg-teal-600/20 rounded-full blur-2xl"></div>
                    <div class="relative z-10 text-center">
                    <div class="text-xs font-bold uppercase tracking-widest text-teal-800/80 dark:text-teal-200 mb-2">Total Business Volume</div>
                    <div class="text-3xl font-extrabold tracking-tight font-jetbrains mb-6 text-teal-950 dark:text-white drop-shadow-sm">
                        Rs <?php echo e(number_format($totalPurchaseAmount, 2)); ?>

                    </div>
                    <div class="flex flex-col gap-3">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create purchases')): ?>
                            <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('purchases.create', ['vendor_name' => $vendor->firm_name])).'','variant' => 'secondary','icon' => 'add_shopping_cart','class' => 'w-full justify-center !text-teal-700 !bg-white/80 hover:!bg-white !border-white backdrop-blur-md shadow-sm']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('purchases.create', ['vendor_name' => $vendor->firm_name])).'','variant' => 'secondary','icon' => 'add_shopping_cart','class' => 'w-full justify-center !text-teal-700 !bg-white/80 hover:!bg-white !border-white backdrop-blur-md shadow-sm']); ?>
                                New Purchase Entry
                             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                        <?php endif; ?>
                        <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('masters.vendors.purchase-history', $vendor)).'','variant' => 'secondary','icon' => 'history','class' => 'w-full justify-center !bg-teal-600/20 !text-teal-900 dark:!text-teal-100 !border-teal-400/30 hover:!bg-teal-600/30 backdrop-blur-md']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('masters.vendors.purchase-history', $vendor)).'','variant' => 'secondary','icon' => 'history','class' => 'w-full justify-center !bg-teal-600/20 !text-teal-900 dark:!text-teal-100 !border-teal-400/30 hover:!bg-teal-600/30 backdrop-blur-md']); ?>
                            View Full History
                         <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>
            <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'Profile Credentials','icon' => 'contact_page']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Profile Credentials','icon' => 'contact_page']); ?>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-zinc-400">person</span>
                        <div>
                            <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Contact Person</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100"><?php echo e($vendor->contact_person ?: 'Not specified'); ?></div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-zinc-400">call</span>
                        <div>
                            <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Contact Phone</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100"><?php echo e($vendor->phone); ?></div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-zinc-400">location_on</span>
                        <div>
                            <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Firm Location</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100"><?php echo e($vendor->location ?: 'Not set'); ?></div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-zinc-400">badge</span>
                        <div>
                            <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">GSTIN / Registration</div>
                            <div class="font-mono text-sm text-zinc-900 dark:text-zinc-100"><?php echo e($vendor->gst_number ?: 'Unregistered'); ?></div>
                        </div>
                    </div>
                </div>
             <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>

            <?php if($vendor->notes): ?>
                <?php if (isset($component)) { $__componentOriginal53747ceb358d30c0105769f8471417f6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal53747ceb358d30c0105769f8471417f6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.card','data' => ['title' => 'Vendor Notes','icon' => 'description','class' => 'border-l-4 border-l-teal-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['title' => 'Vendor Notes','icon' => 'description','class' => 'border-l-4 border-l-teal-500']); ?>
                    <div class="text-sm text-zinc-600 dark:text-zinc-400 whitespace-pre-line leading-relaxed">
                        <?php echo e($vendor->notes); ?>

                    </div>
                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $attributes = $__attributesOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__attributesOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal53747ceb358d30c0105769f8471417f6)): ?>
<?php $component = $__componentOriginal53747ceb358d30c0105769f8471417f6; ?>
<?php unset($__componentOriginal53747ceb358d30c0105769f8471417f6); ?>
<?php endif; ?>
            <?php endif; ?>
        </div>

        <div class="lg:col-span-2">
            <div id="cm-tabs-container" class="bg-white/30 dark:bg-zinc-900/40 backdrop-blur-2xl border border-white/60 dark:border-zinc-800/80 rounded-[2rem] overflow-hidden shadow-[0_8px_32px_rgba(31,38,135,0.07)] z-10 relative">
                <div class="flex flex-wrap p-2 m-4 bg-white/40 dark:bg-zinc-900/40 backdrop-blur-md rounded-2xl border border-white/50 dark:border-zinc-700/50 gap-2">
                    <a href="<?php echo e(route('masters.vendors.show', $vendor)); ?>" class="flex-1 text-center py-3 text-sm font-bold text-teal-700 dark:text-teal-400 bg-white/70 dark:bg-zinc-800/80 shadow-sm rounded-xl transition-all duration-300">
                        Quick Look
                    </a>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('view vendor purchases')): ?>
                    <a href="<?php echo e(route('masters.vendors.purchase-history', $vendor)); ?>" class="flex-1 text-center py-3 text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-white/50 dark:hover:bg-zinc-800/50 rounded-xl transition-all duration-300">
                        Full Purchase History
                    </a>
                    <?php endif; ?>
                </div>

                <div class="p-6">
                    <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider mb-6">Recent Activity Insights</h4>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                        <div class="p-4 rounded-2xl border border-white/60 dark:border-zinc-700 shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)] bg-white/40 dark:bg-zinc-900/40 backdrop-blur-xl flex items-center gap-4 transition-all duration-300 hover:bg-white/60">
                            <div class="w-10 h-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center">
                                <span class="material-symbols-rounded text-xl">shopping_cart</span>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Total Purchases</div>
                                <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                                    <?php echo e($totalPurchaseCount); ?>

                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl border border-white/60 dark:border-zinc-700 shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)] bg-white/40 dark:bg-zinc-900/40 backdrop-blur-xl flex items-center gap-4 transition-all duration-300 hover:bg-white/60">
                            <div class="w-10 h-10 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400 flex items-center justify-center">
                                <span class="material-symbols-rounded text-xl">payments</span>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Total Volume</div>
                                <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100 font-jetbrains">
                                    Rs <?php echo e(number_format($totalPurchaseAmount, 0)); ?>

                                </div>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl border border-white/60 dark:border-zinc-700 shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)] bg-white/40 dark:bg-zinc-900/40 backdrop-blur-xl flex items-center gap-4 transition-all duration-300 hover:bg-white/60">
                            <div class="w-10 h-10 rounded-lg bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center">
                                <span class="material-symbols-rounded text-xl">calendar_today</span>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Last Purchase</div>
                                <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100">
                                    <?php echo e($lastPurchaseDate?->format('d M y') ?? 'N/A'); ?>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-zinc-200 dark:border-zinc-800">
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">Recent Supply Activity</h4>
                            <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('create purchases')): ?>
                                <?php if (isset($component)) { $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.button','data' => ['href' => ''.e(route('purchases.create', ['vendor_name' => $vendor->firm_name])).'','variant' => 'primary','size' => 'sm','icon' => 'add']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['href' => ''.e(route('purchases.create', ['vendor_name' => $vendor->firm_name])).'','variant' => 'primary','size' => 'sm','icon' => 'add']); ?>
                                    Record Entry
                                 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $attributes = $__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__attributesOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561)): ?>
<?php $component = $__componentOriginald0f1fd2689e4bb7060122a5b91fe8561; ?>
<?php unset($__componentOriginald0f1fd2689e4bb7060122a5b91fe8561); ?>
<?php endif; ?>
                            <?php endif; ?>
                        </div>

                    <?php if (isset($component)) { $__componentOriginalc8463834ba515134d5c98b88e1a9dc03 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.data-table','data' => ['headers' => ['Date', 'Item Details', ['label' => 'Quantity', 'align' => 'right'], ['label' => 'Total Bill', 'align' => 'right']]]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('data-table'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['headers' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(['Date', 'Item Details', ['label' => 'Quantity', 'align' => 'right'], ['label' => 'Total Bill', 'align' => 'right']])]); ?>
                        <?php $__empty_1 = true; $__currentLoopData = $recentPurchases; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $purchase): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr class="hover:bg-white/80 dark:hover:bg-zinc-800/50 transition-all duration-300">
                                <td class="px-6 py-4 font-semibold text-sm text-zinc-700 dark:text-zinc-300">
                                    <?php echo e($purchase->date->format('d M Y')); ?>

                                </td>
                                <td class="px-6 py-4 font-bold text-sm text-zinc-900 dark:text-zinc-100">
                                    <?php if($purchase->items->isNotEmpty()): ?>
                                        <?php echo e($purchase->items->pluck('item_name')->join(', ')); ?>

                                    <?php else: ?>
                                        <?php echo e($purchase->item); ?>

                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right font-mono text-sm text-zinc-600 dark:text-zinc-400">
                                    <?php if($purchase->items->isNotEmpty()): ?>
                                        <?php echo e(number_format($purchase->items->sum('quantity'), 2)); ?> <?php echo e($purchase->items->first()->unit); ?>

                                    <?php else: ?>
                                        <?php echo e(number_format($purchase->quantity, 2)); ?> <?php echo e($purchase->unit); ?>

                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-sm text-zinc-900 dark:text-zinc-100 font-jetbrains">
                                    Rs <?php echo e(number_format($purchase->total_amount, 0)); ?>

                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <?php if (isset($component)) { $__componentOriginal074a021b9d42f490272b5eefda63257c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal074a021b9d42f490272b5eefda63257c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.empty-state','data' => ['icon' => 'inventory_2','title' => 'No supplies logged','subtitle' => 'No recent transaction entries found for this supplier.']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('empty-state'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['icon' => 'inventory_2','title' => 'No supplies logged','subtitle' => 'No recent transaction entries found for this supplier.']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $attributes = $__attributesOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__attributesOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal074a021b9d42f490272b5eefda63257c)): ?>
<?php $component = $__componentOriginal074a021b9d42f490272b5eefda63257c; ?>
<?php unset($__componentOriginal074a021b9d42f490272b5eefda63257c); ?>
<?php endif; ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                     <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginalc8463834ba515134d5c98b88e1a9dc03)): ?>
<?php $attributes = $__attributesOriginalc8463834ba515134d5c98b88e1a9dc03; ?>
<?php unset($__attributesOriginalc8463834ba515134d5c98b88e1a9dc03); ?>
<?php endif; ?>
<?php if (isset($__componentOriginalc8463834ba515134d5c98b88e1a9dc03)): ?>
<?php $component = $__componentOriginalc8463834ba515134d5c98b88e1a9dc03; ?>
<?php unset($__componentOriginalc8463834ba515134d5c98b88e1a9dc03); ?>
<?php endif; ?>

                    <?php if($loadCount > 0): ?>
                    <div class="pt-8 border-t border-zinc-200 dark:border-zinc-800 mt-8">
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider mb-6">Day-Load Activity</h4>

                        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-6">
                            <div class="p-4 rounded-2xl border border-white/60 dark:border-zinc-700 bg-white/40 dark:bg-zinc-900/40 backdrop-blur-xl">
                                <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Total Loads</div>
                                <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100 font-jetbrains"><?php echo e($loadCount); ?></div>
                            </div>
                            <div class="p-4 rounded-2xl border border-white/60 dark:border-zinc-700 bg-white/40 dark:bg-zinc-900/40 backdrop-blur-xl">
                                <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Total Boxes</div>
                                <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100 font-jetbrains"><?php echo e(number_format($totalBoxesLoaded)); ?></div>
                            </div>
                            <div class="p-4 rounded-2xl border border-white/60 dark:border-zinc-700 bg-white/40 dark:bg-zinc-900/40 backdrop-blur-xl">
                                <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Bird Weight</div>
                                <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100 font-jetbrains"><?php echo e(number_format($totalBirdWeight, 1)); ?> kg</div>
                            </div>
                            <div class="p-4 rounded-2xl border border-white/60 dark:border-zinc-700 bg-white/40 dark:bg-zinc-900/40 backdrop-blur-xl">
                                <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Farm Weight</div>
                                <div class="text-lg font-bold text-zinc-900 dark:text-zinc-100 font-jetbrains"><?php echo e(number_format($totalFarmWeight, 1)); ?> kg</div>
                            </div>
                            <div class="p-4 rounded-2xl border border-white/60 dark:border-zinc-700 bg-white/40 dark:bg-zinc-900/40 backdrop-blur-xl">
                                <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Loss Weight</div>
                                <div class="text-lg font-bold text-rose-600 dark:text-rose-400 font-jetbrains"><?php echo e(number_format($totalLossWeight, 1)); ?> kg</div>
                            </div>
                            <div class="p-4 rounded-2xl border border-white/60 dark:border-zinc-700 bg-white/40 dark:bg-zinc-900/40 backdrop-blur-xl">
                                <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider mb-1">Avg Rate Variance</div>
                                <div class="text-lg font-bold <?php echo e($avgRateVariance >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'); ?> font-jetbrains">
                                    <?php echo e($avgRateVariance !== null ? number_format($avgRateVariance, 2) : 'N/A'); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/masters/vendors/show.blade.php ENDPATH**/ ?>