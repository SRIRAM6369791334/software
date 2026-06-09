@props([
    'placeholder' => 'Search...',
    'name' => 'search',
    'value' => request('search', '')
])

<div x-data="{ query: '{{ $value }}' }" class="relative flex items-center w-full max-w-md">
    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-500 dark:text-zinc-400">
        <span class="material-symbols-rounded text-[20px]">search</span>
    </span>
    
    <input
        type="text"
        name="{{ $name }}"
        x-model="query"
        x-ref="searchInput"
        placeholder="{{ $placeholder }}"
        class="block w-full pl-10 pr-20 py-2.5 min-h-[44px] bg-white/60 dark:bg-zinc-900/60 backdrop-blur-2xl border border-white/60 shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)] dark:border-zinc-800 rounded-2xl text-sm placeholder-zinc-500 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:text-zinc-100 transition-all duration-300"
        @keydown.window.prevent.cmd.k="$refs.searchInput.focus()"
        @keydown.window.prevent.ctrl.k="$refs.searchInput.focus()"
    >
    
    <div class="absolute inset-y-0 right-0 flex items-center pr-3">
        <button
            type="button"
            x-show="query.length > 0"
            @click="query = ''; $refs.searchInput.focus()"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="p-1 mr-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors min-h-[32px] min-w-[32px] flex items-center justify-center rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 focus:outline-none"
            style="display: none;"
            aria-label="Clear search"
        >
            <span class="material-symbols-rounded text-[18px]">close</span>
        </button>
        
        <div class="hidden sm:flex items-center justify-center px-2 py-1 text-xs font-['JetBrains_Mono'] text-zinc-400 bg-zinc-100/50 dark:bg-zinc-800/50 dark:text-zinc-500 rounded-lg border border-zinc-200 dark:border-zinc-700 pointer-events-none">
            <span class="text-[10px] mr-0.5">⌘</span>K
        </div>
    </div>
</div>
