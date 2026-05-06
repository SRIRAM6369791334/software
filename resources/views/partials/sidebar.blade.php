@php
    $currentPath = request()->path();
    $user = auth()->user();
    $userLevel = $user?->getRoleLevel() ?? 0;
    $roleHierarchy = ['viewer'=>1,'staff'=>2,'manager'=>3,'admin'=>4];
    $canAccess = fn(string $minRole) => $userLevel >= ($roleHierarchy[$minRole] ?? 0);

    $navItems = [
        ['label' => 'Dashboard',         'icon' => 'grid',          'route' => 'dashboard',             'min' => 'viewer'],
        
        // Masters
        ['header' => 'Master Records'],
        ['label' => 'Customers',         'icon' => 'users',         'route' => 'masters.customers.index', 'min' => 'staff'],
        ['label' => 'Dealers',           'icon' => 'truck',         'route' => 'masters.dealers.index',   'min' => 'staff'],
        ['label' => 'Vendors',           'icon' => 'package',       'route' => 'masters.vendors.index',   'min' => 'staff'],
        ['label' => 'Warehouse Master',  'icon' => 'home',          'route' => 'inventory.warehouses.index', 'min' => 'staff'],
        
        // Operations
        ['header' => 'Operations'],
        ['label' => 'Stock Dashboard',    'icon' => 'pie-chart',     'route' => 'inventory.stock.index',   'min' => 'staff'],
        ['label' => 'Daily Usage (FCR)',  'icon' => 'pie-chart',     'route' => 'inventory.consumptions.index', 'min' => 'staff'],
        ['label' => 'Mortality Tracking', 'icon' => 'activity',      'route' => 'inventory.mortalities.index', 'min' => 'staff'],
        ['label' => 'Item Master',        'icon' => 'package',       'route' => 'inventory.items.index',   'min' => 'staff'],
        ['label' => 'Batch Management',   'icon' => 'folder',        'route' => 'inventory.batches.index', 'min' => 'staff'],
        ['label' => 'Batch Performance',  'icon' => 'trending-up',   'route' => 'inventory.analytics',     'min' => 'staff'],
        ['label' => 'Purchase List',     'icon' => 'shopping-cart', 'route' => 'purchases.entry',         'min' => 'staff'],
        ['label' => 'Purchase Invoices', 'icon' => 'receipt',       'route' => 'purchases.invoices',      'min' => 'staff'],
        ['label' => 'Weekly Billing',    'icon' => 'file-text',     'route' => 'billing.weekly.index',    'min' => 'manager'],
        ['label' => 'Daily Billing',     'icon' => 'calendar',      'route' => 'billing.daily.index',     'min' => 'manager'],
        
        // Finance
        ['header' => 'Finance & Payments'],
        ['label' => 'Customer Payments', 'icon' => 'credit-card',   'route' => 'payments.customers.index', 'min' => 'manager'],
        ['label' => 'Dealer Payments',   'icon' => 'banknote',      'route' => 'payments.dealers.index',   'min' => 'manager'],
        ['label' => 'Expenses',          'icon' => 'wallet',        'route' => 'expenses.index',          'min' => 'manager'],
        ['label' => 'Expense Categories','icon' => 'tag',           'route' => 'expenses.categories',     'min' => 'manager'],
        ['label' => 'EMI Alerts',        'icon' => 'bell',          'route' => 'expenses.emis.alerts',    'min' => 'manager'],
        
        // Reports
        ['header' => 'Sales Reports'],
        ['label' => 'Daily Sales',       'icon' => 'calendar',      'route' => 'reports.sales.daily',     'min' => 'viewer'],
        ['label' => 'Weekly Sales',      'icon' => 'file-text',     'route' => 'reports.sales.weekly',    'min' => 'viewer'],
        ['label' => 'Monthly Sales',     'icon' => 'bar-chart',     'route' => 'reports.sales.monthly',   'min' => 'viewer'],
        
        ['header' => 'Purchase Reports'],
        ['label' => 'Daily Purchase',    'icon' => 'calendar',      'route' => 'reports.purchases.daily', 'min' => 'viewer'],
        ['label' => 'Weekly Purchase',   'icon' => 'file-text',     'route' => 'reports.purchases.weekly','min' => 'viewer'],
        ['label' => 'Monthly Purchase',  'icon' => 'bar-chart',     'route' => 'reports.purchases.monthly','min' => 'viewer'],
        ['label' => 'Vendor Analytics',  'icon' => 'trending-up',   'route' => 'reports.purchases.vendor-analytics','min' => 'viewer'],

        // Analytics
        ['header' => 'Performance'],
        ['label' => 'Profit & Loss',     'icon' => 'trending-up',   'route' => 'profit.index',            'min' => 'manager'],

        // Admin
        ['header' => 'Administration', 'min' => 'admin'],
        ['label' => 'User Management',   'icon' => 'settings',      'route' => 'admin.users.index',       'min' => 'admin'],
    ];

    $isActive = fn(string $routeName) => request()->routeIs($routeName);
@endphp

<aside id="sidebar" class="fixed inset-y-0 left-0 z-50 flex w-60 flex-col bg-white border-r border-gray-200 shadow-lg
    transition-transform duration-300 ease-out lg:static lg:translate-x-0 -translate-x-full">

    {{-- Logo --}}
    <div class="flex h-14 items-center gap-2.5 border-b border-gray-200 px-4">
        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-600 text-white text-sm font-bold shrink-0">🥚</div>
        <div>
            <p class="text-sm font-bold text-gray-900 leading-none">PoultryPro</p>
            <p class="text-[10px] text-gray-400 mt-0.5">Management System</p>
        </div>
        <button class="ml-auto lg:hidden text-gray-500 hover:text-gray-700"
                onclick="document.getElementById('sidebar').classList.add('-translate-x-full'); document.getElementById('sidebar-overlay').classList.add('hidden')">
            ✕
        </button>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-0.5">
        @foreach($navItems as $item)
            @if(isset($item['header']))
                @php $headerVisible = !isset($item['min']) || $canAccess($item['min']); @endphp
                @if($headerVisible)
                    <div class="pt-5 pb-1 mb-1 first:pt-0">
                        <span class="px-3 text-[10px] font-bold text-gray-400 uppercase tracking-[0.2em]">{{ $item['header'] }}</span>
                    </div>
                @endif
            @elseif($canAccess($item['min']))
                <a href="{{ route($item['route']) }}"
                   class="flex items-center gap-2.5 px-3 py-2 text-sm rounded-lg font-medium transition-all duration-150
                          {{ $isActive($item['route'])
                             ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm shadow-emerald-600/5'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <span class="text-base w-5 text-center opacity-70">
                        @switch($item['icon'])
                            @case('grid') 📊 @break
                            @case('pie-chart') 🥧 @break
                            @case('activity') 📉 @break
                            @case('users') 👥 @break
                            @case('truck') 🚛 @break
                            @case('package') 📦 @break
                            @case('file-text') 📄 @break
                            @case('calendar') 📅 @break
                            @case('shopping-cart') 🛒 @break
                            @case('receipt') 🧾 @break
                            @case('credit-card') 💳 @break
                            @case('banknote') � @break
                            @case('wallet') 💼 @break
                            @case('tag') 🏷️ @break
                            @case('bell') � @break
                            @case('trending-up') 📈 @break
                            @case('bar-chart') 📊 @break
                            @case('settings') ⚙️ @break
                            @default ◦
                        @endswitch
                    </span>
                    {{ $item['label'] }}
                    @if($isActive($item['route']))
                        <div class="ml-auto w-1 h-1 rounded-full bg-emerald-500"></div>
                    @endif
                </a>
            @endif
        @endforeach
    </nav>

    {{-- User footer --}}
    <div class="border-t border-gray-200 p-3">
        <div class="flex items-center gap-2 px-1">
            <div class="flex-1 min-w-0">
                <p class="text-xs font-semibold text-gray-800 truncate">{{ $user?->name }}</p>
                <p class="text-[10px] text-gray-400 truncate">{{ $user?->email }}</p>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" title="Sign out"
                        class="text-gray-400 hover:text-red-500 transition-colors p-1 rounded">
                    ↩
                </button>
            </form>
        </div>
    </div>
</aside>
