@props(['variant' => 'primary', 'size' => 'md', 'icon' => null])

@php
    $variants = [
        'primary' => 'bg-primary-500 text-white hover:bg-primary-600 shadow-lg shadow-primary-500/25 active:scale-95',
        'secondary' => 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50 shadow-sm active:scale-95',
        'danger' => 'bg-red-500 text-white hover:bg-red-600 shadow-lg shadow-red-500/25 active:scale-95',
        'ghost' => 'bg-transparent text-slate-600 hover:bg-slate-100 active:scale-95',
    ];

    $sizes = [
        'sm' => 'px-4 py-2 text-xs font-bold rounded-xl',
        'md' => 'px-6 py-3 text-sm font-bold rounded-2xl',
        'lg' => 'px-8 py-4 text-base font-bold rounded-[1.25rem]',
    ];

    $classes = "inline-flex items-center justify-center gap-2 transition-all duration-200 " . $variants[$variant] . " " . $sizes[$size];
@endphp

<button {{ $attributes->merge(['class' => $classes]) }}>
    @if($icon)
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $icon !!}
        </svg>
    @endif
    {{ $slot }}
</button>
