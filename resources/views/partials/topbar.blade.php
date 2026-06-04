<header class="sticky top-0 z-30 flex h-20 shrink-0 items-center justify-between border-b border-slate-200 bg-white/80 px-6 backdrop-blur-xl transition-all">
    <div class="flex min-w-0 items-center gap-4">
        <button class="rounded-xl p-2 text-slate-500 transition-colors hover:bg-slate-100 hover:text-slate-900 lg:hidden"
                onclick="document.getElementById('sidebar').classList.remove('-translate-x-full'); document.getElementById('sidebar-overlay').classList.remove('hidden')">
            <span class="material-symbols-rounded">menu</span>
        </button>

        {{-- Global Search --}}
        <div class="hidden items-center gap-2 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-2 text-slate-500 transition-colors focus-within:border-slate-300 focus-within:bg-white md:flex">
            <span class="material-symbols-rounded text-[20px]">search</span>
            <input type="text" placeholder="Search records, invoices..." class="w-72 border-none bg-transparent text-sm font-medium placeholder:text-slate-400 focus:ring-0">
            <div class="flex items-center gap-1 text-[10px] font-bold text-slate-400">
                <kbd class="rounded border border-slate-200 bg-white px-1.5 py-0.5">⌘</kbd>
                <kbd class="rounded border border-slate-200 bg-white px-1.5 py-0.5">K</kbd>
            </div>
        </div>
    </div>

    <div class="flex items-center gap-2 sm:gap-4">
        <div class="flex items-center gap-1">
            {{-- Notification Bell --}}
            <button class="relative rounded-xl p-2 text-slate-400 transition-colors hover:bg-slate-50 hover:text-slate-900" title="Notifications">
                <span class="material-symbols-rounded text-[22px]">notifications</span>
                <span class="absolute right-2 top-2 h-2 w-2 rounded-full border-2 border-white bg-red-500"></span>
            </button>
            {{-- Settings --}}
            <button class="hidden sm:block rounded-xl p-2 text-slate-400 transition-colors hover:bg-slate-50 hover:text-slate-900" title="Settings">
                <span class="material-symbols-rounded text-[22px]">settings</span>
            </button>
        </div>

        <div class="hidden h-6 w-px bg-slate-200 sm:block"></div>

        {{-- User Profile --}}
        <div class="group flex cursor-pointer items-center gap-3 pl-2">
            <div class="hidden text-right sm:block">
                <p class="text-sm font-bold leading-none tracking-tight text-slate-900">{{ Auth::user()->name ?? 'Admin' }}</p>
                <p class="mt-1 text-[10px] font-semibold uppercase tracking-widest text-slate-500">Administrator</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-900 font-bold text-white shadow-sm transition-transform group-hover:scale-105">
                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
            </div>
        </div>
    </div>
</header>
