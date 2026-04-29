@props(['label' => null, 'error' => null, 'icon' => null])

<div class="space-y-2">
    @if($label)
        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-1">{{ $label }}</label>
    @endif
    
    <div class="relative group">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-4 h-4 text-slate-400 group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    {!! $icon !!}
                </svg>
            </div>
        @endif
        
        <input {{ $attributes->merge([
            'class' => "w-full bg-slate-50 border-slate-200 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 rounded-2xl py-3 text-sm font-medium transition-all outline-none placeholder:text-slate-400" . ($icon ? ' pl-11' : ' px-5') . ($error ? ' border-red-500 focus:border-red-500 focus:ring-red-500/10' : '')
        ]) }}>
    </div>

    @if($error)
        <p class="text-[11px] font-bold text-red-500 px-1">{{ $error }}</p>
    @endif
</div>
