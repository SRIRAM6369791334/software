<?php
    $user = auth()->user();
    $canAccess = function(array $item) use ($user) {
        if (!isset($item['permission'])) {
            return true;
        }
        if ($user && $user->hasRole('admin')) {
            return true;
        }
        return $user && $user->can($item['permission']);
    };
    $routeExists = fn(string $routeName) => \Illuminate\Support\Facades\Route::has($routeName);

    $navItems = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'dashboard'],

        ['header' => 'Master Records'],
        ['label' => 'Customers', 'icon' => 'group', 'route' => 'masters.customers.index', 'permission' => 'view customers'],
        ['label' => 'Dealers', 'icon' => 'local_shipping', 'route' => 'masters.dealers.index', 'permission' => 'view dealers'],
        ['label' => 'Vendors', 'icon' => 'inventory_2', 'route' => 'masters.vendors.index', 'permission' => 'view vendors'],
        
        ['header' => 'Operations'],
        ['label' => 'Billing', 'icon' => 'description', 'route' => 'billing.day-load.index', 'permission' => 'view bills'],
        ['label' => 'Weekly Billing', 'icon' => 'receipt_long', 'route' => 'billing.weekly.index', 'permission' => 'view bills'],
        ['label' => 'Purchases', 'icon' => 'shopping_cart', 'route' => 'purchases.entry', 'permission' => 'view purchases'],
        ['label' => 'Purchase Invoices', 'icon' => 'receipt', 'route' => 'purchases.invoices', 'permission' => 'view purchases'],
        ['label' => 'Sales', 'icon' => 'event_note', 'route' => 'billing.daily.index', 'permission' => 'view bills'],

        ['header' => 'Finance & Payments'],
        ['label' => 'Customer Payments', 'icon' => 'credit_card', 'route' => 'payments.customers.index', 'permission' => 'view payments'],
        ['label' => 'Dealer Payments', 'icon' => 'payments', 'route' => 'payments.dealers.index', 'permission' => 'view payments'],
        ['label' => 'Cash & Bank Ledger', 'icon' => 'account_balance', 'route' => 'billing.cash-bank-ledger.index', 'permission' => 'create bills'],
        ['label' => 'Expenses', 'icon' => 'account_balance_wallet', 'route' => 'expenses.index', 'permission' => 'view expenses'],
        ['label' => 'EMI Records', 'icon' => 'notifications_active', 'route' => 'expenses.emis.index', 'permission' => 'view emis'],
        
        ['header' => 'Performance'],
        ['label' => 'Profit & Loss', 'icon' => 'show_chart', 'route' => 'profit.index', 'permission' => 'view profit dashboard'],

        ['header' => 'Administration', 'permission' => 'manage users'],
        ['label' => 'User Management', 'icon' => 'manage_accounts', 'route' => 'admin.users.index', 'permission' => 'manage users'],
        ['label' => 'Role Management', 'icon' => 'admin_panel_settings', 'route' => 'admin.roles.index', 'permission' => 'manage roles'],
        ['label' => 'Permissions', 'icon' => 'vpn_key', 'route' => 'admin.permissions.index', 'permission' => 'manage roles'],
    ];

    $isActive = fn(string $routeName) => request()->routeIs($routeName);

    $visibleItems = [];
    $currentHeader = null;
    $hasLinksUnderHeader = false;

    foreach ($navItems as $item) {
        if (isset($item['header'])) {
            // Check if header itself has explicit permission restrictions
            if (isset($item['permission']) && !$canAccess($item)) {
                $currentHeader = null;
                continue;
            }
            $currentHeader = $item;
            $hasLinksUnderHeader = false;
        } else {
            if ($canAccess($item) && $routeExists($item['route'])) {
                if ($currentHeader && !$hasLinksUnderHeader) {
                    $visibleItems[] = $currentHeader;
                    $hasLinksUnderHeader = true;
                }
                $visibleItems[] = $item;
            }
        }
    }
?>

<aside id="sidebar" 
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-zinc-200/60 dark:border-zinc-800/60 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-2xl transition-transform duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] lg:static lg:translate-x-0 shadow-[4px_0_24px_rgba(0,0,0,0.02)] dark:shadow-[4px_0_24px_rgba(0,0,0,0.2)]">
    
    
    <div class="flex h-20 items-center gap-3 px-6 shrink-0">
        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-sm transition-transform hover:scale-105 duration-300 ease-[cubic-bezier(0.32,0.72,0,1)] shrink-0">
            <span class="material-symbols-rounded text-[24px]">egg</span>
        </div>
        <div class="flex min-w-0 flex-col justify-center">
            <span class="text-xl font-cabinet font-bold tracking-tight text-zinc-900 dark:text-white leading-none">PoultryPro</span>
            <span class="mt-0.5 text-[10px] font-bold uppercase tracking-widest text-emerald-600 dark:text-emerald-400">Management</span>
        </div>
        <button class="ml-auto rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center text-zinc-400 transition-colors hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white lg:hidden focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                @click="sidebarOpen = false"
                aria-label="Close Sidebar">
            <span class="material-symbols-rounded text-xl">close</span>
        </button>
    </div>

    
    <nav class="custom-scrollbar flex-1 space-y-1 overflow-y-auto px-4 py-6 font-outfit">
        <?php $__currentLoopData = $visibleItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(isset($item['header'])): ?>
                <h3 class="mb-2 mt-6 px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-zinc-400 dark:text-zinc-500 first:mt-0 font-cabinet">
                    <?php echo e($item['header']); ?>

                </h3>
            <?php else: ?>
                <a href="<?php echo e(route($item['route'])); ?>"
                   class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 min-h-[44px] transition-all duration-300 ease-[cubic-bezier(0.32,0.72,0,1)] <?php echo e($isActive($item['route']) ? 'bg-white dark:bg-zinc-900 shadow-[0_4px_12px_rgba(0,0,0,0.05)] dark:shadow-[0_4px_12px_rgba(0,0,0,0.2)] border border-zinc-100 dark:border-zinc-800 text-zinc-900 dark:text-white translate-x-1' : 'text-zinc-500 dark:text-zinc-400 border border-transparent hover:bg-zinc-50 dark:hover:bg-zinc-900/50 hover:text-zinc-900 dark:hover:text-white hover:translate-x-1'); ?> focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                    <span class="material-symbols-rounded text-[20px] transition-colors duration-300 <?php echo e($isActive($item['route']) ? 'text-emerald-500' : 'text-zinc-400 dark:text-zinc-500 group-hover:text-emerald-500'); ?>">
                        <?php echo e($item['icon']); ?>

                    </span>
                    <span class="truncate text-sm font-semibold tracking-tight"><?php echo e($item['label']); ?></span>
                    <?php if($isActive($item['route'])): ?>
                        <span class="absolute left-0 top-1/2 -translate-y-1/2 h-6 w-1 rounded-r-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>

    
    <div class="border-t border-zinc-200/60 dark:border-zinc-800/60 p-4 shrink-0 bg-zinc-50/50 dark:bg-zinc-900/30">
        <div class="group flex items-center gap-3 rounded-2xl p-2 transition-colors hover:bg-white dark:hover:bg-zinc-800 border border-transparent hover:border-zinc-200 dark:hover:border-zinc-700 hover:shadow-sm">
            <?php if (isset($component)) { $__componentOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8ca5b43b8fff8bb34ab2ba4eb4bdd67b = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.avatar','data' => ['name' => ''.e($user?->name ?? 'Admin').'','size' => 'md','color' => 'bg-emerald-500']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('avatar'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => ''.e($user?->name ?? 'Admin').'','size' => 'md','color' => 'bg-emerald-500']); ?>
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
            
            <div class="flex min-w-0 flex-col font-outfit">
                <span class="truncate text-sm font-bold tracking-tight text-zinc-900 dark:text-white"><?php echo e($user?->name ?? 'Admin'); ?></span>
                <span class="truncate text-[10px] font-medium text-zinc-500 dark:text-zinc-400"><?php echo e($user?->email ?? 'admin@poultry.com'); ?></span>
            </div>
            
            <form method="POST" action="<?php echo e(route('logout')); ?>" class="ml-auto shrink-0">
                <?php echo csrf_field(); ?>
                <button type="submit" class="rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center text-zinc-400 dark:text-zinc-500 transition-colors hover:bg-rose-50 dark:hover:bg-rose-500/10 hover:text-rose-600 dark:hover:text-rose-400 focus:outline-none focus:ring-2 focus:ring-rose-500/20" title="Logout" aria-label="Logout">
                    <span class="material-symbols-rounded text-[20px]">logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views/partials/sidebar.blade.php ENDPATH**/ ?>