<header class="sticky top-0 z-40 flex h-20 shrink-0 items-center justify-between border-b border-emerald-100 bg-gradient-to-r from-white/90 via-emerald-50/85 to-sky-50/85 px-4 shadow-sm shadow-emerald-100/60 backdrop-blur-md sm:px-6 lg:px-8">
    <div class="flex min-w-0 items-center gap-4">
        <button class="rounded-xl p-2 text-slate-500 transition-colors hover:bg-sky-50 hover:text-slate-900 lg:hidden"
                onclick="document.getElementById('sidebar').classList.remove('-translate-x-full'); document.getElementById('sidebar-overlay').classList.remove('hidden')">
            <span class="material-symbols-rounded">menu</span>
        </button>

        <div class="hidden items-center gap-2 rounded-2xl border border-emerald-100 bg-emerald-50/70 px-4 py-2.5 text-emerald-700 shadow-sm md:flex">
            <span class="material-symbols-rounded text-lg">search</span>
            <input type="text" placeholder="Search records..." class="w-64 border-none bg-transparent text-sm font-semibold placeholder:text-slate-400 focus:ring-0">
            <!-- <span class="rounded-md border border-sky-100 bg-white px-1.5 py-0.5 text-[10px] font-black text-sky-600 shadow-sm">Ctrl K</span> -->
        </div>
    </div>

    <div class="flex items-center gap-3">
        <div class="flex items-center gap-1">
            <button class="relative rounded-xl p-2 text-amber-500 transition-all hover:bg-amber-50 hover:text-amber-600" title="Notifications">
                <span class="material-symbols-rounded">notifications</span>
                <span class="absolute right-2 top-2 h-2 w-2 rounded-full border-2 border-white bg-amber-500"></span>
            </button>
            <button class="rounded-xl p-2 text-sky-500 transition-all hover:bg-sky-50 hover:text-sky-600" title="Settings">
                <span class="material-symbols-rounded">settings</span>
            </button>
            <button id="theme-toggle" type="button" class="rounded-xl p-2 text-violet-500 transition-all hover:bg-violet-50 hover:text-violet-600" title="Toggle theme">
                <span class="material-symbols-rounded">dark_mode</span>
            </button>
        </div>

        <div class="mx-1 h-8 w-px bg-slate-200"></div>

        <div class="group flex cursor-pointer items-center gap-3 pl-1">
            <div class="hidden text-right sm:block">
                <p class="text-xs font-black leading-none tracking-tight text-slate-950">{{ Auth::user()->name ?? 'Admin' }}</p>
                <p class="mt-1 text-[10px] font-bold uppercase tracking-widest text-emerald-600">Online</p>
            </div>
            <div class="flex h-10 w-10 items-center justify-center rounded-xl border border-emerald-100 bg-gradient-to-br from-emerald-500 to-sky-500 font-bold text-white shadow-sm transition-all group-hover:shadow-md">
                {{ substr(Auth::user()->name ?? 'A', 0, 1) }}
            </div>
        </div>
    </div>
</header>
