<!DOCTYPE html>
<html lang="en" class="h-full scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | PoultryPro</title>
    
    <!-- Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        [x-cloak] { display: none !important; }
        .sidebar-scroll::-webkit-scrollbar { width: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
    </style>
</head>
<body class="h-full bg-[#F8FAFC] font-sans antialiased text-slate-900 selection:bg-primary-100 selection:text-primary-700">

    <div class="flex h-full overflow-hidden">
        
        <!-- Sidebar (Desktop) -->
        <aside id="sidebar" class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0F172A] transform -translate-x-full lg:translate-x-0 lg:static lg:inset-0 transition-transform duration-300 ease-in-out flex flex-col shadow-2xl">
            @include('partials.sidebar')
        </aside>

        <!-- Sidebar Overlay (Mobile) -->
        <div id="sidebar-overlay" class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden hidden transition-opacity duration-300 opacity-0"></div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden relative">
            
            <!-- Topbar -->
            <header class="h-16 lg:h-20 flex items-center justify-between px-4 lg:px-8 bg-white/80 backdrop-blur-md border-b border-slate-200 sticky top-0 z-30">
                @include('partials.topbar')
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto overflow-x-hidden p-4 lg:p-8 pb-24 lg:pb-8">
                <div class="max-w-[1400px] mx-auto">
                    @include('partials.flash-messages')
                    @yield('content')
                </div>
            </main>

            <!-- Mobile Bottom Navigation -->
            <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-lg border-t border-slate-200 px-6 py-3 flex items-center justify-between z-40">
                <a href="{{ route('dashboard') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('dashboard') ? 'text-primary-500' : 'text-slate-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span class="text-[10px] font-medium uppercase tracking-wider">Home</span>
                </a>
                <a href="{{ route('billing.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('billing.*') ? 'text-primary-500' : 'text-slate-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    <span class="text-[10px] font-medium uppercase tracking-wider">Bills</span>
                </a>
                <div class="relative -top-8">
                    <button onclick="toggleFAB()" class="w-14 h-14 bg-primary-500 text-white rounded-full shadow-lg shadow-primary-500/40 flex items-center justify-center transform active:scale-95 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    </button>
                </div>
                <a href="{{ route('customers.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('customers.*') ? 'text-primary-500' : 'text-slate-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span class="text-[10px] font-medium uppercase tracking-wider">CRM</span>
                </a>
                <a href="{{ route('reports.index') }}" class="flex flex-col items-center gap-1 {{ request()->routeIs('reports.*') ? 'text-primary-500' : 'text-slate-400' }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2h2a2 2 0 002-2zm6 0v-2a2 2 0 00-2-2h-2a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2zm0 0V5a2 2 0 00-2-2h-2a2 2 0 00-2 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path></svg>
                    <span class="text-[10px] font-medium uppercase tracking-wider">Stats</span>
                </a>
            </nav>

            <!-- FAB Overlay (Mobile) -->
            <div id="fab-menu" class="fixed bottom-24 right-6 flex flex-col items-end gap-3 z-50 pointer-events-none opacity-0 translate-y-4 transition-all duration-300">
                <a href="{{ route('billing.create') }}" class="flex items-center gap-3 pointer-events-auto group">
                    <span class="bg-slate-900 text-white text-xs font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity">New Bill</span>
                    <div class="w-12 h-12 bg-white text-primary-500 rounded-full shadow-lg flex items-center justify-center border border-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                    </div>
                </a>
                <a href="{{ route('customers.create') }}" class="flex items-center gap-3 pointer-events-auto group">
                    <span class="bg-slate-900 text-white text-xs font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity">New Customer</span>
                    <div class="w-12 h-12 bg-white text-primary-500 rounded-full shadow-lg flex items-center justify-center border border-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                </a>
                <a href="{{ route('payments.create') }}" class="flex items-center gap-3 pointer-events-auto group">
                    <span class="bg-slate-900 text-white text-xs font-bold px-3 py-1.5 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity">Record Payment</span>
                    <div class="w-12 h-12 bg-white text-primary-500 rounded-full shadow-lg flex items-center justify-center border border-slate-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                </a>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Sidebar Toggle
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const fabMenu = document.getElementById('fab-menu');
        
        function toggleSidebar() {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
            setTimeout(() => overlay.classList.toggle('opacity-0'), 10);
        }

        overlay.onclick = toggleSidebar;

        function toggleFAB() {
            const isHidden = fabMenu.classList.contains('opacity-0');
            if (isHidden) {
                fabMenu.classList.remove('pointer-events-none', 'opacity-0', 'translate-y-4');
                fabMenu.classList.add('opacity-100', 'translate-y-0');
            } else {
                fabMenu.classList.add('pointer-events-none', 'opacity-0', 'translate-y-4');
                fabMenu.classList.remove('opacity-100', 'translate-y-0');
            }
        }

        // Close FAB on scroll
        document.querySelector('main').onscroll = () => {
            if (!fabMenu.classList.contains('opacity-0')) toggleFAB();
        };
    </script>
    @stack('scripts')
</body>
</html>

