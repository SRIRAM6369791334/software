@props([
    'name',
    'title' => '',
    'subtitle' => '',
    'maxWidth' => '2xl',
    'icon' => null,
    'iconColor' => 'emerald', // Deprecated: Always uses emerald for consistency
    'show' => false,
])

@php
$maxWidthClass = match($maxWidth) {
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
    '3xl' => 'sm:max-w-3xl',
    default => 'sm:max-w-2xl',
};
@endphp

<div
    x-data="{
        show: {{ $show ? 'true' : 'false' }},
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'
            return [...$el.querySelectorAll(selector)]
                .filter(el => ! el.hasAttribute('disabled'))
        },
        firstFocusable() { return this.focusables()[0] },
        lastFocusable() { return this.focusables().slice(-1)[0] },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable() },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable() },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length || 1) },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) -1 },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            setTimeout(() => firstFocusable()?.focus(), 100);
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50 flex items-center justify-center font-outfit"
    style="display: none;"
>
    <!-- Backdrop -->
    <div
        x-show="show"
        class="fixed inset-0 transform transition-all"
        x-on:click="show = false"
        x-transition:enter="ease-out duration-300 cubic-bezier(0.32, 0.72, 0, 1)"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-zinc-900/60 backdrop-blur-sm"></div>
    </div>

    <!-- Modal Card -->
    <div
        x-show="show"
        class="mb-6 bg-white dark:bg-zinc-900 rounded-3xl overflow-hidden shadow-2xl transform transition-all sm:w-full sm:mx-auto {{ $maxWidthClass }} border border-zinc-200/50 dark:border-zinc-800/50"
        x-transition:enter="ease-out duration-300 cubic-bezier(0.32, 0.72, 0, 1)"
        x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-95"
    >
        <div class="p-6 sm:p-8">
            <!-- Header -->
            <div class="flex items-start justify-between mb-6">
                <div class="flex items-center gap-4">
                    @if($icon)
                    <div class="flex-shrink-0 w-12 h-12 rounded-full bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-500">
                        <span class="material-symbols-rounded text-2xl">{{ $icon }}</span>
                    </div>
                    @endif
                    <div>
                        @if($title)
                            <h3 class="text-xl font-bold text-zinc-900 dark:text-white font-cabinet">{{ $title }}</h3>
                        @endif
                        @if($subtitle)
                            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">{{ $subtitle }}</p>
                        @endif
                    </div>
                </div>
                <button x-on:click="show = false" class="text-zinc-400 hover:text-zinc-500 dark:hover:text-zinc-300 transition-colors p-2 rounded-full hover:bg-zinc-100 dark:hover:bg-zinc-800">
                    <span class="material-symbols-rounded">close</span>
                </button>
            </div>

            <!-- Body -->
            <div class="text-sm text-zinc-600 dark:text-zinc-400">
                {{ $slot }}
            </div>
        </div>

        <!-- Footer -->
        @if(isset($footer))
        <div class="px-6 py-4 bg-zinc-50 dark:bg-zinc-800/50 border-t border-zinc-200/50 dark:border-zinc-800/50 flex justify-end gap-3 rounded-b-3xl">
            {{ $footer }}
        </div>
        @endif
    </div>
</div>
