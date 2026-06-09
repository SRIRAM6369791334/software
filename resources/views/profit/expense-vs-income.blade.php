@extends('layouts.app')
@section('title', 'Expense vs Income')

@section('content')
<div class="mb-2">
    <a href="{{ route('profit.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider inline-block">← Back to Overview</a>
</div>
<x-page-header 
    title="Expense vs Income Matrix" 
    subtitle="Comparative study of business efficiency" />

<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <x-card>
        <x-slot name="header">
            <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">Income Breakdown (This Month)</h3>
        </x-slot>
        <div class="flex justify-center mb-10">
            <div class="relative w-48 h-48 rounded-full border-[16px] border-emerald-500 flex items-center justify-center">
                <div class="text-center">
                    <p class="text-[10px] font-bold text-zinc-400 uppercase">Revenue</p>
                    <p class="text-xl font-black text-zinc-950">Rs {{ number_format($summary['revenue'], 0) }}</p>
                </div>
            </div>
        </div>
        <div class="space-y-4">
            <div class="flex justify-between items-center text-sm">
                <span class="text-zinc-500">Scheduled Invoicing</span>
                <span class="font-bold text-zinc-950">65%</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-zinc-500">Counter Sales</span>
                <span class="font-bold text-zinc-950">35%</span>
            </div>
        </div>
    </x-card>

    <x-card>
        <x-slot name="header">
            <h3 class="text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">Expense Matrix (Current)</h3>
        </x-slot>
        <div class="flex justify-center mb-10">
            <div class="relative w-48 h-48 rounded-full border-[16px] border-rose-500 flex items-center justify-center">
                <div class="text-center">
                    <p class="text-[10px] font-bold text-zinc-400 uppercase">Outflow</p>
                    <p class="text-xl font-black text-zinc-950">Rs {{ number_format($summary['purchase'] + $summary['expenses'], 0) }}</p>
                </div>
            </div>
        </div>
        <div class="space-y-4">
            <div class="flex justify-between items-center text-sm">
                <span class="text-zinc-500">Procurement (Stock)</span>
                <span class="font-bold text-zinc-950">Rs {{ number_format($summary['purchase'], 0) }}</span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-zinc-500">Operationals & EMIs</span>
                <span class="font-bold text-zinc-950">Rs {{ number_format($summary['expenses'], 0) }}</span>
            </div>
        </div>
    </x-card>
</div>

<x-card class="mt-8 !bg-emerald-50 !border-emerald-100">
    <p class="text-emerald-800 text-sm text-center">
        <strong>Efficiency Ratio:</strong> Your business is currently retaining 
        <span class="font-black text-lg text-emerald-900">{{ number_format($summary['profit'] / ($summary['revenue'] ?: 1) * 100, 1) }}%</span> 
        of every Rupee generated after all procurement and operational expenses.
    </p>
</x-card>
@endsection
