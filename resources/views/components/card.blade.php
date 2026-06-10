@props([
    'title' => null,
    'subtitle' => null,
    'padding' => 'p-6',
    'hover' => false,
    'glass' => true,
])

<div {{ $attributes->merge(['class' => 'rounded-[2.5rem] border border-white/80 dark:border-zinc-700/50 transition-all duration-500 ease-[cubic-bezier(0.32,0.72,0,1)] z-10 relative overflow-hidden ' . ($glass ? 'bg-gradient-to-br from-white/60 to-white/30 dark:from-zinc-800/60 dark:to-zinc-900/40 backdrop-blur-3xl' : 'bg-white dark:bg-zinc-900') . ($hover ? ' hover:-translate-y-2 hover:shadow-2xl dark:hover:shadow-[0_0_40px_-10px_rgba(16,185,129,0.1)]' : ' shadow-[0_8px_40px_-12px_rgba(0,0,0,0.1)] dark:shadow-none')]) }}>
    @if($title || isset($actions))
        <div class="flex items-center justify-between border-b border-zinc-100/80 dark:border-zinc-800/50 px-6 py-5">
            <div>
                @if($title)
                    <h3 class="font-cabinet font-semibold text-lg text-zinc-900 dark:text-zinc-50">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="font-outfit text-sm text-zinc-500 dark:text-zinc-400 mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            @if(isset($actions))
                <div class="flex items-center gap-3">
                    {{ $actions }}
                </div>
            @endif
        </div>
    @endif

    <div class="{{ $padding }}">
        {{ $slot }}
    </div>
</div>
