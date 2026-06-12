@props([
    'name',
    'label' => '',
    'type' => 'text',
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'error' => null,
    'icon' => null,
    'hint' => null,
])

<div class="w-full font-outfit">
    @if($label)
        <label for="{{ $name }}" class="block mb-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit">
            {{ $label }}
            @if($required)
                <span class="text-emerald-500 font-bold ml-0.5 drop-shadow-[0_0_8px_rgba(16,185,129,0.5)]">*</span>
            @endif
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-zinc-600 dark:text-zinc-400">
                <span class="material-symbols-rounded text-[22px]">{{ $icon }}</span>
            </div>
        @endif

        <input 
            type="{{ $type }}" 
            name="{{ $name }}" 
            id="{{ $name }}" 
            value="{{ old($name, $value) }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes->merge(['class' => 'block w-full bg-white/30 dark:bg-zinc-900/30 backdrop-blur-2xl border border-zinc-200 dark:border-zinc-700 shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)] text-zinc-900 dark:text-zinc-100 text-sm rounded-xl focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 ' . ($icon ? 'pl-11' : 'pl-4') . ' p-3 transition-all duration-300 ' . ($errors->has($name) || $error ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : 'hover:border-zinc-300 dark:hover:border-zinc-600')]) }}
            placeholder="{{ $placeholder }}"
        >
    </div>

    @if($errors->has($name) || $error)
        <p class="mt-2 text-sm text-red-600 dark:text-red-400 flex items-center gap-1">
            <span class="material-symbols-rounded text-sm">error</span>
            {{ $error ?? $errors->first($name) }}
        </p>
    @elseif($hint)
        <p class="mt-2 text-xs text-zinc-500 dark:text-zinc-400">{{ $hint }}</p>
    @endif
</div>
