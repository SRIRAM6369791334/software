@props(['title' => null, 'subtitle' => null, 'padding' => true])

<div {{ $attributes->merge(['class' => 'bg-white rounded-[2rem] border border-slate-200/60 shadow-[0_20px_40px_-15px_rgba(0,0,0,0.05)] transition-all duration-300 hover:shadow-[0_30px_60px_-15px_rgba(0,0,0,0.08)] overflow-hidden']) }}>
    @if($title || $subtitle || isset($header))
        <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
            <div>
                @if($title)
                    <h3 class="text-lg font-bold text-slate-900 tracking-tight">{{ $title }}</h3>
                @endif
                @if($subtitle)
                    <p class="text-xs text-slate-500 font-medium mt-1">{{ $subtitle }}</p>
                @endif
            </div>
            @if(isset($header))
                <div>{{ $header }}</div>
            @endif
        </div>
    @endif

    <div class="{{ $padding ? 'p-8' : '' }}">
        {{ $slot }}
    </div>

    @if(isset($footer))
        <div class="px-8 py-5 bg-slate-50/50 border-t border-slate-100">
            {{ $footer }}
        </div>
    @endif
</div>
