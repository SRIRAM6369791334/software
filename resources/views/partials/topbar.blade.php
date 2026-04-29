<header class="flex h-14 shrink-0 items-center gap-3 border-b border-gray-200 bg-white/80 backdrop-blur-md px-4 shadow-sm">
    {{-- Mobile menu button --}}
    <button class="lg:hidden text-gray-600 hover:text-gray-900 p-1 rounded"
            onclick="document.getElementById('sidebar').classList.remove('-translate-x-full'); document.getElementById('sidebar-overlay').classList.remove('hidden')">
        ☰
    </button>

    <div class="flex-1"></div>

    <span class="text-xs text-gray-400 hidden sm:block">
        {{ now()->translatedFormat('l, d F Y') }}
    </span>
</header>
