@props([
    'label' => null,
    'title' => null,
    'value',
    'icon' => null,
    'trend' => null,
    'trendUp' => true,
    'color' => 'emerald',
    'prefix' => '',
    'subtitle' => null,
])

@php
    $iconColors = [
        'emerald' => 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20 shadow-lg shadow-emerald-500/20 dark:bg-emerald-500/20 dark:text-emerald-400',
        'blue' => 'bg-blue-500/10 text-blue-600 border border-blue-500/20 shadow-lg shadow-blue-500/20 dark:bg-blue-500/20 dark:text-blue-400',
        'amber' => 'bg-amber-500/10 text-amber-600 border border-amber-500/20 shadow-lg shadow-amber-500/20 dark:bg-amber-500/20 dark:text-amber-400',
        'rose' => 'bg-rose-500/10 text-rose-600 border border-rose-500/20 shadow-lg shadow-rose-500/20 dark:bg-rose-500/20 dark:text-rose-400',
        'zinc' => 'bg-zinc-500/10 text-zinc-600 border border-zinc-500/20 shadow-lg shadow-zinc-500/20 dark:bg-zinc-800/50 dark:text-zinc-400',
    ];
    $iconClass = $iconColors[$color] ?? $iconColors['emerald'];
@endphp

<div class="group relative rounded-[2.5rem] border border-white/80 dark:border-zinc-700/50 bg-gradient-to-br from-white/60 to-white/30 dark:from-zinc-800/60 dark:to-zinc-900/40 backdrop-blur-3xl p-6 shadow-xl shadow-zinc-200/40 dark:shadow-none hover:shadow-2xl hover:shadow-zinc-300/50 dark:hover:shadow-[0_0_40px_-10px_rgba(16,185,129,0.15)] transition-all duration-500 ease-[cubic-bezier(0.34,1.56,0.64,1)] hover:-translate-y-2 overflow-hidden">
    <!-- Subtle hover glow background -->
    <div class="absolute inset-0 bg-gradient-to-br from-white/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
    
    <div class="relative z-10 flex items-center justify-between">
        <div>
            <p class="font-outfit text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $label ?? $title }}</p>
            <p class="font-cabinet mt-2 text-2xl sm:text-3xl font-extrabold tracking-tight text-zinc-900 dark:text-zinc-50 drop-shadow-sm">{{ $prefix }}{{ $value }}</p>
        </div>
        @if($icon)
            <div class="flex h-14 w-14 items-center justify-center rounded-2xl {{ $iconClass }} transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3">
                @if(Str::startsWith($icon, 'ph-'))
                    <i class="ph {{ $icon }} text-3xl"></i>
                @else
                    <span class="material-symbols-rounded text-3xl">{{ $icon }}</span>
                @endif
            </div>
        @endif
    </div>
    
    @if($trend)
        <div class="mt-4 flex items-center gap-2 text-sm">
            @if($trendUp)
                <span class="flex items-center text-emerald-600 dark:text-emerald-400 font-medium">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                    {{ $trend }}
                </span>
            @else
                <span class="flex items-center text-rose-600 dark:text-rose-400 font-medium">
                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                    {{ $trend }}
                </span>
            @endif
            <span class="text-zinc-500 dark:text-zinc-400 font-outfit">vs last month</span>
        </div>
    @endif
</div>
