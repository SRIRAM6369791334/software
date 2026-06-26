@props([
    'name' => '',
    'label' => false,
    'value' => '',
    'placeholder' => '',
    'required' => false,
    'rows' => 4,
    'error' => null,
])

<div class="space-y-2">
    @if($label)
        <label for="{{ $name }}" class="block mb-2 text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit">
            {{ $label }} @if($required) <span class="text-emerald-500 font-bold ml-0.5 drop-shadow-[0_0_8px_rgba(16,185,129,0.5)]">*</span> @endif
        </label>
    @endif

    <div class="relative">
        <textarea
            id="{{ $name }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            {{ $attributes->merge([
                'class' => 'block w-full px-4 py-3 text-base border border-zinc-200 dark:border-zinc-700 shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)] hover:border-zinc-300 dark:hover:border-zinc-600 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 sm:text-sm rounded-xl bg-white/30 dark:bg-zinc-900/30 backdrop-blur-2xl text-zinc-900 dark:text-zinc-100 transition-all duration-300 ease-[cubic-bezier(0.32,0.72,0,1)] resize-y font-outfit' . ($error ? ' border-red-500 focus:ring-red-500 focus:border-red-500' : '')
            ]) }}
        >{{ old($name, $value) }}</textarea>
    </div>

    @if($error)
        <p class="text-sm text-red-600 dark:text-red-400 font-outfit mt-1">{{ $error }}</p>
    @endif
</div>
