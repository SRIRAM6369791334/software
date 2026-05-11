@php
    $user = auth()->user();
    $userLevel = $user?->getRoleLevel() ?? 0;
    $roleHierarchy = [
        'viewer' => 1,
        'staff' => 2,
        'delivery_staff' => 2,
        'data_entry' => 2,
        'accountant' => 3,
        'manager' => 3,
        'admin' => 4,
    ];
    $canAccess = fn(string $minRole) => isset($roleHierarchy[$minRole]) && $userLevel >= $roleHierarchy[$minRole];
    $routeExists = fn(string $routeName) => \Illuminate\Support\Facades\Route::has($routeName);

    $navItems = [
        ['label' => 'Dashboard', 'icon' => 'dashboard', 'route' => 'dashboard', 'min' => 'viewer'],

        ['header' => 'Master Records'],
        ['label' => 'Customers', 'icon' => 'group', 'route' => 'masters.customers.index', 'min' => 'staff'],
        ['label' => 'Dealers', 'icon' => 'local_shipping', 'route' => 'masters.dealers.index', 'min' => 'staff'],
        ['label' => 'Vendors', 'icon' => 'inventory_2', 'route' => 'masters.vendors.index', 'min' => 'staff'],
        ['header' => 'Operations'],
        ['label' => 'Stock Overview', 'icon' => 'inventory', 'route' => 'stock.index', 'min' => 'data_entry'],
        ['label' => 'Bird Batches', 'icon' => 'egg_alt', 'route' => 'stock.batches.index', 'min' => 'data_entry'],
        ['label' => 'Purchase List', 'icon' => 'shopping_cart', 'route' => 'purchases.entry', 'min' => 'staff'],
        ['label' => 'Purchase Invoices', 'icon' => 'receipt_long', 'route' => 'purchases.invoices', 'min' => 'staff'],
        ['label' => 'Weekly Billing', 'icon' => 'description', 'route' => 'billing.weekly.index', 'min' => 'manager'],
        ['label' => 'Daily Billing', 'icon' => 'event_note', 'route' => 'billing.daily.index', 'min' => 'manager'],

        ['header' => 'Logistics'],
        ['label' => 'Route Management', 'icon' => 'map', 'route' => 'routes.index', 'min' => 'delivery_staff'],
        ['label' => 'Vehicle Fleet', 'icon' => 'local_shipping', 'route' => 'routes.index', 'min' => 'delivery_staff'],

        ['header' => 'Finance & Payments'],
        ['label' => 'Customer Payments', 'icon' => 'credit_card', 'route' => 'payments.customers.index', 'min' => 'manager'],
        ['label' => 'Dealer Payments', 'icon' => 'account_balance', 'route' => 'payments.dealers.index', 'min' => 'manager'],
        ['label' => 'Expenses', 'icon' => 'account_balance_wallet', 'route' => 'expenses.index', 'min' => 'manager'],
        ['label' => 'EMI Records', 'icon' => 'notifications_active', 'route' => 'expenses.emis.index', 'min' => 'manager'],

        ['header' => 'Sales Reports'],
        ['label' => 'Reports Home', 'icon' => 'assessment', 'route' => 'reports.index', 'min' => 'viewer'],
        ['label' => 'Daily Sales', 'icon' => 'calendar_today', 'route' => 'reports.sales.daily', 'min' => 'viewer'],
        ['label' => 'Weekly Sales', 'icon' => 'date_range', 'route' => 'reports.sales.weekly', 'min' => 'viewer'],
        ['label' => 'Monthly Sales', 'icon' => 'bar_chart', 'route' => 'reports.sales.monthly', 'min' => 'viewer'],

        ['header' => 'Purchase Reports'],
        ['label' => 'Daily Purchase', 'icon' => 'calendar_today', 'route' => 'reports.purchases.daily', 'min' => 'viewer'],
        ['label' => 'Weekly Purchase', 'icon' => 'date_range', 'route' => 'reports.purchases.weekly', 'min' => 'viewer'],
        ['label' => 'Monthly Purchase', 'icon' => 'bar_chart', 'route' => 'reports.purchases.monthly', 'min' => 'viewer'],
        ['label' => 'Vendor Analytics', 'icon' => 'trending_up', 'route' => 'reports.purchases.vendor-analytics', 'min' => 'viewer'],

        ['header' => 'Performance'],
        ['label' => 'Profit & Loss', 'icon' => 'show_chart', 'route' => 'profit.index', 'min' => 'manager'],

        ['header' => 'Administration', 'min' => 'admin'],
        ['label' => 'User Management', 'icon' => 'manage_accounts', 'route' => 'admin.users.index', 'min' => 'admin'],
    ];

    $isActive = fn(string $routeName) => request()->routeIs($routeName);
@endphp

<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-emerald-100 bg-gradient-to-br from-white/95 via-emerald-50/80 to-sky-50/80 shadow-md shadow-emerald-100/70 backdrop-blur-xl transition-transform duration-300 ease-out lg:static lg:translate-x-0 -translate-x-full">
    <div class="flex h-20 items-center gap-3 border-b border-emerald-100 bg-gradient-to-r from-emerald-50 to-sky-50 px-5">
        <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-emerald-600 to-sky-500 text-white shadow-lg shadow-emerald-100">
            <span class="material-symbols-rounded text-[25px]">egg</span>
        </div>
        <div class="flex min-w-0 flex-col">
            <span class="text-base font-black leading-none tracking-tight text-slate-950">PoultryPro</span>
            <span class="mt-1 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-400">Management System</span>
        </div>
        <button class="ml-auto rounded-xl p-2 text-slate-400 transition hover:bg-sky-50 hover:text-slate-900 lg:hidden"
                onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); document.getElementById('sidebar-overlay').classList.add('hidden')">
            <span class="material-symbols-rounded">close</span>
        </button>
    </div>

    <nav class="custom-scrollbar flex-1 space-y-1 overflow-y-auto px-4 py-5">
        @foreach($navItems as $item)
            @if(isset($item['header']))
                @php $headerVisible = !isset($item['min']) || $canAccess($item['min']); @endphp
                @if($headerVisible)
                    <h3 class="mb-2 mt-5 px-3 text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 first:mt-0">{{ $item['header'] }}</h3>
                @endif
            @elseif($canAccess($item['min']) && $routeExists($item['route']))
                <a href="{{ route($item['route']) }}"
                   class="group flex items-center gap-3 rounded-xl px-3.5 py-2.5 transition-all duration-200 {{ $isActive($item['route']) ? 'bg-gradient-to-r from-emerald-600 to-sky-500 text-white shadow-lg shadow-emerald-100' : 'text-slate-600 hover:bg-emerald-50 hover:text-emerald-800' }}">
                    <span class="material-symbols-rounded text-[22px] {{ $isActive($item['route']) ? 'text-white' : 'text-slate-400 group-hover:text-primary' }} transition-colors">
                        {{ $item['icon'] }}
                    </span>
                    <span class="truncate text-sm font-bold tracking-tight">{{ $item['label'] }}</span>
                    @if($isActive($item['route']))
                        <span class="ml-auto h-1.5 w-1.5 rounded-full bg-white shadow-[0_0_8px_white]"></span>
                    @endif
                </a>
            @endif
        @endforeach
    </nav>

    <div class="border-t border-slate-100 p-4">
        <div class="group flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50/80 p-3 transition-all hover:border-emerald-200 hover:bg-emerald-50/40">
            <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-slate-200 bg-white font-bold text-slate-700 shadow-sm transition-transform group-hover:scale-105">
                {{ substr($user?->name ?? 'A', 0, 1) }}
            </div>
            <div class="flex min-w-0 flex-col">
                <span class="truncate text-xs font-black tracking-tight text-slate-950">{{ $user?->name ?? 'Admin' }}</span>
                <span class="truncate text-[10px] font-bold text-slate-400">{{ $user?->email ?? 'admin@poultry.com' }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ml-auto">
                @csrf
                <button type="submit" class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-500" title="Logout">
                    <span class="material-symbols-rounded text-lg">logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
