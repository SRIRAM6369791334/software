<div class="flex items-center gap-4 lg:gap-8 flex-1">
    <!-- Mobile Menu Toggle -->
    <button onclick="toggleSidebar()" class="lg:hidden p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition-colors">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
        </svg>
    </button>

    <!-- Page Title (Desktop) -->
    <div class="hidden lg:block">
        <h2 class="text-xl font-bold text-slate-900 tracking-tight">@yield('title', 'Dashboard')</h2>
        <p class="text-xs text-slate-500 font-medium">Welcome back, {{ auth()->user()->name ?? 'Admin' }}</p>
    </div>

    <!-- Search Bar -->
    <div class="flex-1 max-w-md hidden md:block">
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400 group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
            <input type="text" placeholder="Search anything..." 
                   class="w-full bg-slate-100 border-transparent focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 rounded-2xl py-2.5 pl-11 pr-4 text-sm font-medium transition-all outline-none">
        </div>
    </div>
</div>

<div class="flex items-center gap-2 lg:gap-4">
    <!-- Notifications -->
    <button class="p-2 text-slate-500 hover:bg-slate-100 rounded-xl transition-colors relative group">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
        </svg>
        <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-primary-500 rounded-full border-2 border-white"></span>
    </button>

    <!-- Divider -->
    <div class="h-8 w-px bg-slate-200 mx-2 hidden sm:block"></div>

    <!-- Quick Stats (Desktop) -->
    <div class="hidden xl:flex items-center gap-6 mr-4">
        <div class="text-right">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Today's Revenue</p>
            <p class="text-sm font-bold text-slate-900 mt-1">₹45,280</p>
        </div>
        <div class="text-right">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest leading-none">Active Orders</p>
            <p class="text-sm font-bold text-slate-900 mt-1">12</p>
        </div>
    </div>
</div>

