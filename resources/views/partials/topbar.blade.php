<header class="sticky top-0 z-30 flex h-20 shrink-0 items-center justify-between border-b border-zinc-200/60 dark:border-zinc-800/60 bg-white/80 dark:bg-zinc-950/80 px-6 backdrop-blur-xl transition-all duration-300 ease-[cubic-bezier(0.32,0.72,0,1)]">
    <div class="flex min-w-0 items-center gap-4 w-full md:w-auto">
        <button class="rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center text-zinc-500 dark:text-zinc-400 transition-colors hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-zinc-900 dark:hover:text-white lg:hidden focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                @click="sidebarOpen = true"
                aria-label="Open Sidebar">
            <span class="material-symbols-rounded">menu</span>
        </button>

        {{-- Global Search --}}
        <div class="hidden md:block w-72 lg:w-96">
            <x-search placeholder="Search records, invoices..." />
        </div>
    </div>

    <div class="flex items-center gap-2 sm:gap-4 shrink-0">
        <div class="flex items-center gap-1">
            {{-- Dark Mode Toggle --}}
            <button 
                x-data="{ isDark: document.documentElement.classList.contains('dark') }"
                @click="isDark = !isDark; document.documentElement.classList.toggle('dark', isDark); localStorage.setItem('theme', isDark ? 'dark' : 'light')"
                class="rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center text-zinc-400 transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20"
                title="Toggle Dark Mode"
                aria-label="Toggle Dark Mode">
                <span class="material-symbols-rounded text-[22px]" x-text="isDark ? 'light_mode' : 'dark_mode'">dark_mode</span>
            </button>

            {{-- Notification Bell --}}
            <button class="relative rounded-xl p-2 min-h-[44px] min-w-[44px] flex items-center justify-center text-zinc-400 transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20" title="Notifications">
                <span class="material-symbols-rounded text-[22px]">notifications</span>
                <span class="absolute right-2.5 top-2.5 h-2 w-2 rounded-full border-2 border-white dark:border-zinc-950 bg-rose-500"></span>
            </button>
            
            {{-- Settings --}}
            <button class="hidden sm:flex rounded-xl p-2 min-h-[44px] min-w-[44px] items-center justify-center text-zinc-400 transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/20" title="Settings">
                <span class="material-symbols-rounded text-[22px]">settings</span>
            </button>
        </div>

        <div class="hidden h-6 w-px bg-zinc-200 dark:bg-zinc-800 sm:block"></div>

        {{-- User Profile --}}
        <div class="group flex cursor-pointer items-center gap-3 pl-2 rounded-2xl p-1 pr-3 transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/50">
            <div class="hidden text-right sm:block font-outfit">
                <p class="text-sm font-bold leading-none tracking-tight text-zinc-900 dark:text-white">{{ Auth::user()->name ?? 'Admin' }}</p>
                <p class="mt-1 text-[10px] font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">Administrator</p>
            </div>
            <x-avatar name="{{ Auth::user()->name ?? 'Admin' }}" size="md" class="group-hover:scale-105 transition-transform duration-300 ease-[cubic-bezier(0.32,0.72,0,1)]" />
        </div>
    </div>
</header>
