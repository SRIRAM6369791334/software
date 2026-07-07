<header class="sticky top-0 z-30 flex h-20 shrink-0 items-center justify-between border-b border-zinc-200/60 dark:border-zinc-800/60 bg-white/80 dark:bg-zinc-950/80 px-6 backdrop-blur-xl transition-all duration-300 ease-[cubic-bezier(0.32,0.72,0,1)]">
    <div class="flex min-w-0 items-center gap-4 w-full md:w-auto">
        <button class="rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center text-zinc-500 dark:text-zinc-400 transition-colors hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white lg:hidden focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                @click="sidebarOpen = true"
                aria-label="Open Sidebar">
            <span class="material-symbols-rounded">menu</span>
        </button>

        
        <div class="hidden md:block w-72 lg:w-96">
            <?php if (isset($component)) { $__componentOriginal9b33c063a2222f59546ad2a2a9a94bc6 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9b33c063a2222f59546ad2a2a9a94bc6 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.search','data' => ['placeholder' => 'Search records, invoices...']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['placeholder' => 'Search records, invoices...']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9b33c063a2222f59546ad2a2a9a94bc6)): ?>
<?php $attributes = $__attributesOriginal9b33c063a2222f59546ad2a2a9a94bc6; ?>
<?php unset($__attributesOriginal9b33c063a2222f59546ad2a2a9a94bc6); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9b33c063a2222f59546ad2a2a9a94bc6)): ?>
<?php $component = $__componentOriginal9b33c063a2222f59546ad2a2a9a94bc6; ?>
<?php unset($__componentOriginal9b33c063a2222f59546ad2a2a9a94bc6); ?>
<?php endif; ?>
        </div>
    </div>

    <div class="flex items-center gap-2 sm:gap-4 shrink-0">
        <div class="flex items-center gap-1">
            
            <button 
                x-data="{ isDark: document.documentElement.classList.contains('dark') }"
                @click="isDark = !isDark; document.documentElement.classList.toggle('dark', isDark); localStorage.setItem('theme', isDark ? 'dark' : 'light')"
                class="rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center text-zinc-400 transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                title="Toggle Dark Mode"
                aria-label="Toggle Dark Mode">
                <span class="material-symbols-rounded text-[22px]" x-text="isDark ? 'light_mode' : 'dark_mode'">dark_mode</span>
            </button>

            
            <div 
                x-data="{ 
                    open: false,
                    count: 0,
                    notifications: [],
                    loading: false,
                    fetchNotifications() {
                        this.loading = true;
                        fetch('<?php echo e(route('notifications.index')); ?>')
                            .then(res => res.json())
                            .then(data => {
                                this.count = data.count || 0;
                                this.notifications = data.notifications || [];
                                this.loading = false;
                            });
                    },
                    markAsRead(id, url) {
                        fetch(`/notifications/${id}/read`, {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
                        }).then(() => {
                            window.location.href = url;
                        });
                    },
                    markAllAsRead() {
                        fetch('<?php echo e(route('notifications.readAll')); ?>', {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
                        }).then(() => {
                            this.count = 0;
                            this.notifications = [];
                            this.open = false;
                        });
                    }
                }"
                x-init="fetchNotifications(); setInterval(() => fetchNotifications(), 60000)"
                class="relative"
            >
                <button @click="open = !open" @click.away="open = false" class="relative rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center text-zinc-400 transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20" title="Notifications">
                    <span class="material-symbols-rounded text-[22px]">notifications</span>
                    <span x-show="count > 0" class="absolute right-2 top-2 h-4 w-4 rounded-full border-2 border-white dark:border-zinc-950 bg-rose-500 text-[9px] font-bold text-white flex items-center justify-center" x-text="count > 9 ? '9+' : count" x-cloak></span>
                </button>

                
                <div x-show="open"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-150"
                     x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                     x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
                     class="absolute right-0 mt-2 w-80 rounded-2xl bg-white/90 dark:bg-zinc-900/90 backdrop-blur-xl border border-zinc-200/60 dark:border-zinc-800/60 shadow-[0_8px_32px_rgba(0,0,0,0.08)] overflow-hidden z-50"
                     x-cloak>
                    
                    <div class="flex items-center justify-between p-4 border-b border-zinc-100 dark:border-zinc-800">
                        <h3 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 tracking-tight">Recent Activity</h3>
                        <button x-show="count > 0" @click="markAllAsRead" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-500 transition-colors">Mark all read</button>
                    </div>

                    <div class="max-h-80 overflow-y-auto">
                        <div x-show="loading" class="p-8 text-center text-zinc-400">
                            <span class="material-symbols-rounded animate-spin">refresh</span>
                        </div>
                        
                        <div x-show="!loading && notifications.length === 0" class="p-8 text-center flex flex-col items-center justify-center">
                            <div class="h-12 w-12 rounded-full bg-zinc-50 dark:bg-zinc-800/50 flex items-center justify-center text-zinc-400 mb-3">
                                <span class="material-symbols-rounded">notifications_paused</span>
                            </div>
                            <p class="text-sm text-zinc-500 font-medium tracking-tight">You're all caught up!</p>
                            <p class="text-xs text-zinc-400 mt-1">No new activities to show.</p>
                        </div>

                        <template x-for="notif in notifications" :key="notif.id">
                            <button @click="markAsRead(notif.id, notif.data.url)" class="w-full text-left p-4 hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors border-b border-zinc-50 dark:border-zinc-800/50 last:border-0 flex items-start gap-3">
                                <div class="mt-0.5 shrink-0 h-8 w-8 rounded-full flex items-center justify-center text-white shadow-sm"
                                     :class="{
                                         'bg-emerald-500': notif.data.color === 'emerald',
                                         'bg-blue-500': notif.data.color === 'blue',
                                         'bg-amber-500': notif.data.color === 'amber',
                                         'bg-orange-500': notif.data.color === 'orange',
                                         'bg-violet-500': notif.data.color === 'violet',
                                         'bg-purple-500': notif.data.color === 'purple'
                                     }">
                                    <span class="material-symbols-rounded text-[16px]" x-text="notif.data.icon"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-bold text-zinc-900 dark:text-zinc-100 truncate" x-text="notif.data.title"></p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 line-clamp-2" x-text="notif.data.message"></p>
                                    <p class="text-[10px] text-zinc-400 font-medium tracking-wide uppercase mt-1.5" x-text="notif.created_at"></p>
                                </div>
                            </button>
                        </template>
                    </div>
                    
                    <div class="p-2 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-900/50 text-center">
                        <a href="<?php echo e(route('dashboard.alerts')); ?>" class="text-xs font-semibold text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-300 transition-colors">View Alert Center</a>
                    </div>
                </div>
            </div>
            
         
        </div>

        <div class="hidden h-6 w-px bg-zinc-200 dark:bg-zinc-800 sm:block"></div>

        
        <div class="group flex cursor-pointer items-center gap-3 pl-2 rounded-2xl p-1 pr-3 transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
            <div class="hidden text-right sm:block font-outfit">
                <p class="text-sm font-bold leading-none tracking-tight text-zinc-900 dark:text-white"><?php echo e(Auth::user()->name ?? 'Admin'); ?></p>
                <p class="mt-1 text-[10px] font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">Administrator</p>
            </div>
            <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['name' => ''.e(Auth::user()->name ?? 'Admin').'','size' => 'md','class' => 'group-hover:scale-105 transition-transform duration-300 ease-[cubic-bezier(0.32,0.72,0,1)]']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e(Auth::user()->name ?? 'Admin').'','size' => 'md','class' => 'group-hover:scale-105 transition-transform duration-300 ease-[cubic-bezier(0.32,0.72,0,1)]']); ?>
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
        </div>
    </div>
</header>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/partials/topbar.blade.php ENDPATH**/ ?>