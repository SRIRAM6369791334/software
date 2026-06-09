@props([
    'variant' => 'primary',
    'size' => 'md',
    'icon' => null,
    'type' => 'button',
    'href' => null,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-outfit font-medium transition-all duration-300 ease-[cubic-bezier(0.32,0.72,0,1)] focus:outline-none focus:ring-2 focus:ring-offset-2 dark:focus:ring-offset-zinc-900 disabled:opacity-50 disabled:cursor-not-allowed group relative overflow-hidden';
    
    $variants = [
        'primary' => 'bg-gradient-to-b from-emerald-500 to-emerald-600 text-white shadow-[0_4px_14px_0_rgba(16,185,129,0.39)] border border-emerald-400/50 hover:shadow-[0_6px_20px_rgba(16,185,129,0.5)] focus:ring-emerald-500 hover:-translate-y-0.5',
        'secondary' => 'bg-white/80 dark:bg-zinc-800/80 backdrop-blur-sm text-zinc-700 dark:text-zinc-200 border border-zinc-200/80 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700 focus:ring-zinc-500 shadow-sm hover:-translate-y-0.5',
        'ghost' => 'text-zinc-600 dark:text-zinc-400 hover:bg-zinc-100/80 dark:hover:bg-zinc-800/50 hover:text-zinc-900 dark:hover:text-zinc-100 focus:ring-zinc-500 border border-transparent',
        'danger' => 'bg-rose-600 text-white hover:bg-rose-700 shadow-md shadow-rose-500/20 focus:ring-rose-500 border border-transparent hover:-translate-y-0.5',
        'outline' => 'bg-transparent text-emerald-600 dark:text-emerald-400 border border-emerald-600 dark:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 focus:ring-emerald-500 shadow-sm hover:-translate-y-0.5',
    ];
    
    $sizes = [
        'sm' => 'px-3 py-1.5 text-xs rounded-xl min-h-[36px]',
        'md' => 'px-4 py-2 text-sm rounded-xl min-h-[44px]',
        'lg' => 'px-6 py-3 text-base rounded-xl min-h-[52px]',
    ];
    
    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            @if(Str::startsWith($icon, 'ph-'))
                <i class="ph {{ $icon }} mr-2 -ml-1 text-[1.1em] flex items-center justify-center"></i>
            @else
                <span class="material-symbols-rounded mr-2 -ml-1 flex items-center justify-center" style="font-size: 1.1em">{{ $icon }}</span>
            @endif
        @endif
        <span class="relative z-10">{{ $slot }}</span>
        @if($variant === 'primary' || $variant === 'danger')
            <div class="absolute inset-0 h-full w-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite] z-0"></div>
        @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($icon)
            @if(Str::startsWith($icon, 'ph-'))
                <i class="ph {{ $icon }} mr-2 -ml-1 text-[1.1em] flex items-center justify-center"></i>
            @else
                <span class="material-symbols-rounded mr-2 -ml-1 flex items-center justify-center" style="font-size: 1.1em">{{ $icon }}</span>
            @endif
        @endif
        <span class="relative z-10">{{ $slot }}</span>
        @if($variant === 'primary' || $variant === 'danger')
            <div class="absolute inset-0 h-full w-full bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full group-hover:animate-[shimmer_1.5s_infinite] z-0"></div>
        @endif
    </button>
@endif
