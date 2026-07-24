<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames(([
    'placeholder' => 'Search customers, dealers, vendors...',
]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter(([
    'placeholder' => 'Search customers, dealers, vendors...',
]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div
    x-data="{
        query: '',
        results: [],
        loading: false,
        open: false,
        searchUrl: '<?php echo e(route('global.search')); ?>',
        debounceTimer: null,

        onInput() {
            clearTimeout(this.debounceTimer);
            if (this.query.trim().length < 2) {
                this.results = [];
                this.open = false;
                return;
            }
            this.loading = true;
            this.debounceTimer = setTimeout(() => this.fetch(), 300);
        },

        async fetch() {
            try {
                const res = await fetch(this.searchUrl + '?q=' + encodeURIComponent(this.query));
                this.results = await res.json();
                this.open = this.results.length > 0;
            } catch(e) {
                this.results = [];
            } finally {
                this.loading = false;
            }
        },

        go(url) {
            this.open = false;
            this.query = '';
            window.location.href = url;
        },

        clear() {
            this.query = '';
            this.results = [];
            this.open = false;
            this.$refs.searchInput.focus();
        }
    }"
    @keydown.escape="open = false"
    @click.outside="open = false"
    class="relative flex items-center w-full max-w-md"
>
    
    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-zinc-500 dark:text-zinc-400 z-10">
        <span x-show="!loading" class="material-symbols-rounded text-[20px]">search</span>
        <svg x-show="loading" class="animate-spin w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    </span>

    
    <input
        type="text"
        x-model="query"
        x-ref="searchInput"
        @input="onInput()"
        @keydown.window.meta.k.prevent="$refs.searchInput.focus()"
        @keydown.window.ctrl.k.prevent="$refs.searchInput.focus()"
        @keydown.arrow-down.prevent="$refs.resultsList?.querySelector('a')?.focus()"
        placeholder="<?php echo e($placeholder); ?>"
        autocomplete="off"
        class="block w-full pl-10 pr-20 py-2.5 min-h-[44px] bg-white/60 dark:bg-zinc-900/60 backdrop-blur-2xl border border-white/60 shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)] dark:border-zinc-800 rounded-2xl text-sm placeholder-zinc-500 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:text-zinc-100 transition-all duration-300"
    >

    
    <div class="absolute inset-y-0 right-0 flex items-center pr-3 gap-1">
        <button
            type="button"
            x-show="query.length > 0"
            @click="clear()"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-90"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            style="display:none;"
            class="p-1 text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-300 transition-colors min-h-[32px] min-w-[32px] flex items-center justify-center rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800 focus:outline-none"
            aria-label="Clear search"
        >
            <span class="material-symbols-rounded text-[18px]">close</span>
        </button>
        <div x-show="query.length === 0" class="hidden sm:flex items-center justify-center px-2 py-1 text-xs text-zinc-400 bg-zinc-100/50 dark:bg-zinc-800/50 rounded-lg border border-zinc-200 dark:border-zinc-700 pointer-events-none">
            <span class="text-[10px] mr-0.5">⌘</span>K
        </div>
    </div>

    
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
        x-ref="resultsList"
        class="absolute top-full left-0 right-0 mt-2 bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 rounded-2xl shadow-2xl shadow-zinc-200/50 dark:shadow-zinc-900/50 overflow-hidden z-50"
    >
        
        <template x-if="results.length > 0">
            <div>
                
                <template x-for="type in ['Customer', 'Dealer', 'Vendor', 'Purchase Invoice', 'Daily Invoice', 'Weekly Invoice']" :key="type">
                    <template x-if="results.filter(r => r.type === type).length > 0">
                        <div>
                            
                            <div class="px-4 py-2 text-[10px] font-semibold uppercase tracking-widest text-zinc-400 dark:text-zinc-500 bg-zinc-50 dark:bg-zinc-800/50 border-b border-zinc-100 dark:border-zinc-800">
                                <span x-text="type + 's'"></span>
                            </div>
                            
                            <template x-for="item in results.filter(r => r.type === type)" :key="item.id + item.type">
                                <a
                                    :href="item.url"
                                    @click.prevent="go(item.url)"
                                    class="flex items-center gap-3 px-4 py-3 hover:bg-emerald-50 dark:hover:bg-zinc-800 transition-colors cursor-pointer focus:outline-none focus:bg-emerald-50 dark:focus:bg-zinc-800 group"
                                >
                                    
                                    <span
                                        class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 text-white text-[18px] material-symbols-rounded"
                                        :class="{
                                            'bg-emerald-500': item.color === 'emerald',
                                            'bg-blue-500': item.color === 'blue',
                                            'bg-amber-500': item.color === 'amber',
                                            'bg-orange-500': item.color === 'orange',
                                            'bg-violet-500': item.color === 'violet',
                                            'bg-purple-500': item.color === 'purple'
                                        }"
                                        x-text="item.icon"
                                    ></span>
                                    
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-zinc-800 dark:text-zinc-100 truncate" x-text="item.label"></p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate" x-text="item.sub"></p>
                                    </div>
                                    
                                    <span class="material-symbols-rounded text-[16px] text-zinc-300 group-hover:text-emerald-500 transition-colors">arrow_forward</span>
                                </a>
                            </template>
                        </div>
                    </template>
                </template>
            </div>
        </template>

        
        <template x-if="results.length === 0 && !loading && query.length >= 2">
            <div class="flex flex-col items-center justify-center py-8 px-4 text-center">
                <span class="material-symbols-rounded text-4xl text-zinc-300 mb-2">search_off</span>
                <p class="text-sm text-zinc-500 dark:text-zinc-400">No results for "<span x-text="query" class="font-medium text-zinc-700 dark:text-zinc-200"></span>"</p>
                <p class="text-xs text-zinc-400 mt-1">Try searching by phone number or name</p>
            </div>
        </template>
    </div>
</div>

<?php /**PATH C:\xampp\htdocs\Poultry Management System\flockwise-biztrack-main\flockwise-biztrack-laravel\resources\views\components\search.blade.php ENDPATH**/ ?>