@props([
    'name',
    'label' => false,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'required' => false,
    'error' => null,
])

<div class="space-y-2">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 font-outfit">
            {{ $label }} @if($required) <span class="text-red-500">*</span> @endif
        </label>
    @endif

    <div class="relative">
        <select 
            id="{{ $name }}" 
            name="{{ $name }}" 
            @if($required) required @endif
            {{ $attributes->merge([
                'class' => 'appearance-none block w-full pl-3 pr-10 py-2.5 min-h-[44px] text-base border border-zinc-200 dark:border-zinc-700 shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)] hover:border-zinc-300 dark:hover:border-zinc-600 focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-400 sm:text-sm rounded-xl bg-white/30 dark:bg-zinc-900/30 backdrop-blur-2xl text-zinc-900 dark:text-zinc-100 transition-all duration-300 ease-[cubic-bezier(0.32,0.72,0,1)]' . ($error ? ' border-red-500 focus:ring-red-500 focus:border-red-500' : '')
            ]) }}
        >
            @if($placeholder)
                <option value="" disabled {{ is_null($selected) ? 'selected' : '' }}>{{ $placeholder }}</option>
            @endif

            @foreach($options as $value => $text)
                <option value="{{ $value }}" {{ (string)$selected === (string)$value ? 'selected' : '' }}>
                    {{ $text }}
                </option>
            @endforeach
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-zinc-500 dark:text-zinc-400">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </div>
    </div>

    @if($error)
        <p class="text-sm text-red-600 dark:text-red-400 font-outfit mt-1">{{ $error }}</p>
    @endif
</div>
