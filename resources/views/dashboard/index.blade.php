@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">
    <section class="relative overflow-hidden rounded-3xl border border-emerald-100 bg-gradient-to-br from-emerald-600 via-sky-600 to-violet-600 p-6 text-white shadow-xl shadow-emerald-100 md:p-8">
        <div class="absolute -right-16 -top-20 h-64 w-64 rounded-full bg-white/10 blur-3xl"></div>
        <div class="absolute -bottom-20 left-20 h-56 w-56 rounded-full bg-amber-300/20 blur-3xl"></div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <div class="max-w-3xl">
                <div class="mb-4 inline-flex items-center gap-2 rounded-full border border-white/20 bg-white/15 px-4 py-2 text-xs font-black uppercase tracking-widest backdrop-blur">
                    <span class="material-symbols-rounded text-lg">monitoring</span>
                    Live Operations
                </div>
                <h1 class="text-3xl font-black tracking-tight text-white md:text-5xl">Business Dashboard</h1>
                <p class="mt-3 max-w-2xl text-sm font-medium leading-6 text-white/80 md:text-base">
                    Track sales, payments, purchases, flock health, and alerts from one colorful control panel.
                </p>
            </div>

            <div class="rounded-2xl border border-white/20 bg-white/15 p-5 text-right backdrop-blur">
                <p class="text-[10px] font-black uppercase tracking-[0.28em] text-white/70">Today</p>
                <p class="mt-2 text-3xl font-black tabular-nums text-white">{{ now()->format('h:i A') }}</p>
                <p class="mt-1 text-xs font-bold uppercase tracking-widest text-white/70">{{ now()->format('l, d M Y') }}</p>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-4">
        @foreach([
            ['label' => 'Total Birds', 'value' => number_format($stats['totalBirds'], 0), 'meta' => $stats['activeBatches'] . ' active batches', 'icon' => 'egg_alt', 'tone' => 'emerald'],
            ['label' => 'Mortality MTD', 'value' => number_format($stats['mortalityMTD'], 0), 'meta' => 'Monthly loss tracking', 'icon' => 'trending_down', 'tone' => 'rose'],
            ['label' => 'Today Revenue', 'value' => 'Rs ' . number_format($stats['todayRevenue'], 0), 'meta' => 'Daily billing inflow', 'icon' => 'payments', 'tone' => 'sky'],
            ['label' => 'MTD Purchase', 'value' => 'Rs ' . number_format($stats['monthlyPurchase'], 0), 'meta' => 'Monthly purchase cost', 'icon' => 'shopping_cart', 'tone' => 'amber'],
        ] as $card)
            <article class="group rounded-2xl border border-{{ $card['tone'] }}-100 bg-gradient-to-br from-white via-{{ $card['tone'] }}-50/70 to-sky-50/60 p-6 shadow-md shadow-{{ $card['tone'] }}-100/60 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl">
                <div class="mb-6 flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-{{ $card['tone'] }}-100 text-{{ $card['tone'] }}-700 transition-transform group-hover:scale-110 group-hover:rotate-3">
                        <span class="material-symbols-rounded">{{ $card['icon'] }}</span>
                    </div>
                    <span class="rounded-full bg-white/80 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-{{ $card['tone'] }}-700 shadow-sm">Live</span>
                </div>
                <p class="text-[11px] font-black uppercase tracking-widest text-slate-500">{{ $card['label'] }}</p>
                <p class="mt-2 text-3xl font-black tracking-tight text-slate-950">{{ $card['value'] }}</p>
                <p class="mt-2 text-xs font-bold text-slate-500">{{ $card['meta'] }}</p>
            </article>
        @endforeach
    </section>

    <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="xl:col-span-2 overflow-hidden rounded-3xl border border-sky-100 bg-gradient-to-br from-white via-sky-50/60 to-emerald-50/50 shadow-lg shadow-sky-100/70">
            <div class="flex flex-col gap-3 border-b border-sky-100 bg-gradient-to-r from-sky-50 to-emerald-50 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-xl font-black text-slate-950">Recent Transactions</h2>
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-500">Latest daily sales activity</p>
                </div>
                <a href="{{ route('billing.daily.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-sky-500 px-4 py-2 text-xs font-black uppercase tracking-widest text-white shadow-md">
                    View All
                    <span class="material-symbols-rounded text-base">arrow_forward</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr>
                            <th class="px-6 py-4">Customer</th>
                            <th class="px-6 py-4">Items</th>
                            <th class="px-6 py-4 text-right">Net Value</th>
                            <th class="px-6 py-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white/50">
                        @forelse($recentSales as $sale)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 font-black text-emerald-700">
                                            {{ substr($sale->customer->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-900">{{ $sale->customer->name ?? 'Unknown customer' }}</p>
                                            <p class="text-[11px] font-bold uppercase tracking-wider text-slate-400">{{ $sale->date->format('d M, h:i A') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-xs font-bold text-slate-500">{{ Str::limit($sale->items->pluck('item_name')->join(', '), 32) }}</td>
                                <td class="px-6 py-4 text-right font-black text-slate-950">Rs {{ number_format($sale->net_amount, 0) }}</td>
                                <td class="px-6 py-4 text-center">
                                    <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-emerald-700">{{ $sale->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-16 text-center">
                                    <div class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
                                        <span class="material-symbols-rounded text-3xl">receipt_long</span>
                                    </div>
                                    <p class="font-black text-slate-900">No sales logged recently</p>
                                    <p class="mt-1 text-sm font-medium text-slate-500">New billing activity will appear here.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="overflow-hidden rounded-3xl border border-amber-100 bg-gradient-to-br from-white via-amber-50/80 to-rose-50/50 shadow-lg shadow-amber-100/70">
                <div class="border-b border-amber-100 bg-amber-50 px-6 py-5">
                    <h2 class="flex items-center gap-2 text-lg font-black text-amber-700">
                        <span class="material-symbols-rounded">notifications_active</span>
                        Critical Alerts
                    </h2>
                    <p class="text-xs font-bold uppercase tracking-widest text-amber-700/70">Immediate attention</p>
                </div>
                <div class="space-y-3 p-5">
                    @forelse($upcomingEmis as $emi)
                        <div class="rounded-2xl border border-amber-100 bg-white/80 p-4">
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <p class="text-sm font-black text-slate-950">{{ $emi->item }}</p>
                                    <p class="mt-1 text-xs font-bold text-amber-700">Due {{ $emi->due_date->format('d M') }} · {{ now()->diffInDays($emi->due_date) }} days</p>
                                </div>
                                <p class="font-black text-slate-950">Rs {{ number_format($emi->amount, 0) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="py-10 text-center">
                            <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                                <span class="material-symbols-rounded text-3xl">verified</span>
                            </div>
                            <p class="text-sm font-black text-slate-900">No pending alerts</p>
                            <p class="mt-1 text-xs font-bold text-slate-500">Everything looks clear.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="rounded-3xl bg-gradient-to-br from-emerald-600 via-sky-600 to-violet-600 p-6 text-white shadow-xl shadow-emerald-100">
                <h3 class="mb-5 text-[11px] font-black uppercase tracking-[0.3em] text-white/70">Financial Summary</h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between border-b border-white/15 pb-4">
                        <span class="text-xs font-bold uppercase tracking-widest text-white/70">MTD Revenue</span>
                        <span class="text-xl font-black">Rs {{ number_format($stats['monthlyRevenue'], 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between border-b border-white/15 pb-4">
                        <span class="text-xs font-bold uppercase tracking-widest text-white/70">Customer Dues</span>
                        <span class="text-xl font-black">Rs {{ number_format($stats['pendingPayments'], 0) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-widest text-white/70">Active Dealers</span>
                        <span class="text-xl font-black">{{ $stats['activeDealers'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
