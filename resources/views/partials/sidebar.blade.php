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
        ['label' => 'Purchase List', 'icon' => 'shopping_cart', 'route' => 'purchases.entry', 'min' => 'staff'],
        ['label' => 'Purchase Invoices', 'icon' => 'receipt_long', 'route' => 'purchases.invoices', 'min' => 'staff'],
        ['label' => 'Dealer billing', 'icon' => 'description', 'route' => 'billing.weekly.index', 'min' => 'manager'],
        ['label' => 'Customer Billing', 'icon' => 'event_note', 'route' => 'billing.daily.index', 'min' => 'manager'],

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
        
        ['header' => 'Performance'],
        ['label' => 'Profit & Loss', 'icon' => 'show_chart', 'route' => 'profit.index', 'min' => 'manager'],

        ['header' => 'Administration', 'min' => 'admin'],
        ['label' => 'User Management', 'icon' => 'manage_accounts', 'route' => 'admin.users.index', 'min' => 'admin'],
    ];

    $isActive = fn(string $routeName) => request()->routeIs($routeName);
@endphp

<aside id="sidebar" 
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-slate-200/60 bg-white/80 backdrop-blur-2xl transition-transform duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] lg:static lg:translate-x-0 -translate-x-full shadow-[4px_0_24px_rgba(0,0,0,0.02)]">
    {{-- Branding Header --}}
    <div class="flex h-20 items-center gap-3 px-6">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-900 text-white shadow-sm transition-transform hover:scale-105">
            <span class="material-symbols-rounded text-[22px]">egg</span>
        </div>
        <div class="flex min-w-0 flex-col">
            <span class="text-base font-bold tracking-tight text-slate-900 leading-none">PoultryPro</span>
            <span class="mt-1 text-[10px] font-semibold uppercase tracking-widest text-slate-400">Management</span>
        </div>
        <button class="ml-auto rounded-xl p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-900 lg:hidden"
                @click="sidebarOpen = false">
            <span class="material-symbols-rounded text-xl">close</span>
        </button>
    </div>

    {{-- Navigation Links --}}
    <nav class="custom-scrollbar flex-1 space-y-1 overflow-y-auto px-4 py-6">
        @foreach($navItems as $item)
            @if(isset($item['header']))
                @php $headerVisible = !isset($item['min']) || $canAccess($item['min']); @endphp
                @if($headerVisible)
                    <h3 class="mb-3 mt-6 px-3 text-[10px] font-bold uppercase tracking-[0.15em] text-slate-400 first:mt-0">
                        {{ $item['header'] }}
                    </h3>
                @endif
            @elseif($canAccess($item['min']) && $routeExists($item['route']))
                <a href="{{ route($item['route']) }}"
                   class="group relative flex items-center gap-3 rounded-xl px-3 py-2.5 transition-all duration-300 {{ $isActive($item['route']) ? 'bg-white shadow-[0_4px_12px_rgba(0,0,0,0.05)] border border-slate-100 text-slate-900 translate-x-1' : 'text-slate-500 border border-transparent hover:bg-slate-50 hover:text-slate-900 hover:translate-x-1' }}">
                    <span class="material-symbols-rounded text-[20px] {{ $isActive($item['route']) ? 'text-indigo-600' : 'text-slate-400 group-hover:text-slate-600' }} transition-colors">
                        {{ $item['icon'] }}
                    </span>
                    <span class="truncate text-sm font-semibold tracking-tight">{{ $item['label'] }}</span>
                    @if($isActive($item['route']))
                        <span class="absolute right-3 h-1.5 w-1.5 rounded-full bg-indigo-600 shadow-[0_0_8px_rgba(79,70,229,0.5)]"></span>
                    @endif
                </a>
            @endif
        @endforeach
    </nav>

    {{-- User Profile Footer --}}
    <div class="border-t border-slate-100 p-4">
        <div class="group flex items-center gap-3 rounded-2xl bg-slate-50 p-3 transition-colors hover:bg-slate-100">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-white font-bold text-slate-700 shadow-sm transition-transform group-hover:scale-105">
                {{ substr($user?->name ?? 'A', 0, 1) }}
            </div>
            <div class="flex min-w-0 flex-col">
                <span class="truncate text-sm font-bold tracking-tight text-slate-900">{{ $user?->name ?? 'Admin' }}</span>
                <span class="truncate text-[10px] font-medium text-slate-500">{{ $user?->email ?? 'admin@poultry.com' }}</span>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ml-auto shrink-0">
                @csrf
                <button type="submit" class="rounded-lg p-2 text-slate-400 transition-colors hover:bg-red-50 hover:text-red-600" title="Logout">
                    <span class="material-symbols-rounded text-[20px]">logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>
