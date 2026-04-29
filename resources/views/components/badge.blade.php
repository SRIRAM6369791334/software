@props(['variant' => 'info'])

@php
    $variants = [
        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
        'danger' => 'bg-red-50 text-red-700 border-red-100',
        'warning' => 'bg-amber-50 text-amber-700 border-amber-100',
        'info' => 'bg-blue-50 text-blue-700 border-blue-100',
        'slate' => 'bg-slate-50 text-slate-700 border-slate-100',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold uppercase tracking-wider border ' . $variants[$variant]]) }}>
    {{ $slot }}
</span>
