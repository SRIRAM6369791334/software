@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="cm-page">
    <div class="cm-topbar">
        <div>
            <h1 class="cm-page-title">Executive Dashboard</h1>
            <p class="cm-page-sub">Real-time overview of poultry operations, financials, and inventory.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('reports.index') }}" class="cm-btn-ghost group">
                <span class="material-symbols-rounded text-[18px]">download</span>
                Export Report
            </a>
            <a href="{{ route('billing.daily.index') }}" class="cm-btn-primary group">
                <span class="material-symbols-rounded text-[18px]">add</span>
                New Entry
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <section class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4 mb-8"
             x-data="{ showCards: false }" x-init="setTimeout(() => showCards = true, 150)">
        @foreach([
            ['label' => 'Total Birds', 'value' => number_format($stats['totalBirds'], 0), 'meta' => 'MTD Inventory', 'icon' => 'egg_alt', 'color' => 'cm-icon-teal', 'trend' => '+4.2%'],
            ['label' => 'Mortality', 'value' => number_format($stats['mortalityMTD'], 0), 'meta' => 'Loss Analytics', 'icon' => 'trending_down', 'color' => 'cm-icon-red', 'trend' => '-1.5%'],
            ['label' => 'Today\'s Revenue', 'value' => '₹' . number_format($stats['todayRevenue'], 0), 'meta' => 'Daily Inflow', 'icon' => 'payments', 'color' => 'cm-icon-blue', 'trend' => '+12%'],
            ['label' => 'Purchase Cost', 'value' => '₹' . number_format($stats['monthlyPurchase'], 0), 'meta' => 'Supply Cost', 'icon' => 'shopping_cart', 'color' => 'cm-icon-amber', 'trend' => '+2.1%'],
        ] as $card)
            <div class="cm-stat-card group hover:-translate-y-1 hover:shadow-lg transition-all duration-300 justify-between items-start flex-col !items-stretch"
                 x-show="showCards"
                 x-transition:enter="transition ease-out duration-700"
                 x-transition:enter-start="opacity-0 translate-y-8"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 style="transition-delay: {{ $loop->index * 100 }}ms;">
                <div class="flex items-center gap-3 w-full">
                    <div class="cm-stat-icon {{ $card['color'] }}">
                        <span class="material-symbols-rounded text-[20px]">{{ $card['icon'] }}</span>
                    </div>
                    <div>
                        <div class="cm-stat-label">{{ $card['label'] }}</div>
                        <div class="cm-stat-value">{{ $card['value'] }}</div>
                    </div>
                </div>
                <div class="mt-2 flex items-center justify-between border-t border-slate-100 pt-3">
                    <span class="text-[11px] font-semibold text-slate-400">{{ $card['meta'] }}</span>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold {{ str_contains($card['trend'], '+') ? 'text-emerald-600' : 'text-rose-600' }}">
                        <span class="material-symbols-rounded text-[14px]">{{ str_contains($card['trend'], '+') ? 'arrow_upward' : 'arrow_downward' }}</span>
                        {{ trim($card['trend'], '+-') }}
                    </span>
                </div>
            </div>
        @endforeach
    </section>

    <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
        {{-- Recent Activity Table --}}
        <div class="xl:col-span-2 cm-table-card">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5">
                <div>
                    <h2 class="text-sm font-bold text-slate-900">Recent Transactions</h2>
                    <p class="mt-0.5 text-[11px] font-medium text-slate-500">Latest sales and billing flow</p>
                </div>
                <a href="{{ route('billing.daily.index') }}" class="text-xs font-semibold text-indigo-600 hover:text-indigo-700">View All</a>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/50">
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">Customer</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500">Items</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500 text-right">Amount</th>
                            <th class="px-6 py-4 text-[10px] font-bold uppercase tracking-wider text-slate-500 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentSales as $sale)
                            <tr class="transition-colors hover:bg-slate-50/80">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-sm font-bold text-slate-600">
                                            {{ substr($sale->customer->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">{{ $sale->customer->name ?? 'System User' }}</p>
                                            <p class="text-[11px] font-medium text-slate-500">{{ $sale->date->format('d M, h:i A') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-xs font-medium text-slate-600 max-w-[150px] truncate">
                                        {{ $sale->items->pluck('item_name')->join(', ') }}
                                    </p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <p class="text-sm font-bold text-slate-900">₹{{ number_format($sale->net_amount, 0) }}</p>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center gap-1 rounded-full bg-emerald-50 px-2 py-1 text-[10px] font-bold text-emerald-700">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $sale->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-sm text-slate-500">
                                    No recent transactions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Financial Alerts & EMI --}}
        <div class="space-y-6">
            {{-- Upcoming Dues --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="mb-6 flex items-center justify-between">
                    <h2 class="text-sm font-bold text-slate-900">Pending Dues</h2>
                    <span class="rounded-full bg-rose-50 px-2 py-1 text-[10px] font-bold text-rose-600">Requires Action</span>
                </div>

                <div class="space-y-4">
                    @forelse($upcomingEmis as $emi)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-slate-50 p-4 transition-colors hover:border-slate-200">
                            <div>
                                <p class="text-xs font-bold text-slate-900">{{ $emi->item }}</p>
                                <p class="mt-1 text-[10px] font-medium text-slate-500">Due: {{ $emi->due_date->format('d M') }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-slate-900">₹{{ number_format($emi->amount, 0) }}</p>
                                <p class="mt-1 text-[10px] font-bold text-rose-600">{{ now()->diffInDays($emi->due_date) }}d left</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-slate-200 p-6 text-center">
                            <span class="material-symbols-rounded text-3xl text-slate-300">verified</span>
                            <p class="mt-2 text-xs font-medium text-slate-500">No pending dues right now.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Summary Card --}}
            <div class="rounded-2xl bg-slate-900 p-6 text-white shadow-lg">
                <h3 class="mb-6 text-sm font-bold flex items-center gap-2">
                    <span class="material-symbols-rounded text-[18px] text-indigo-400">account_balance</span>
                    Financial Health
                </h3>
                
                <div class="space-y-5">
                    <div class="flex justify-between items-end border-b border-white/10 pb-4">
                        <div>
                            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">MTD Net Revenue</p>
                            <p class="text-xl font-bold">₹{{ number_format($stats['monthlyRevenue'], 0) }}</p>
                        </div>
                        <span class="material-symbols-rounded text-emerald-400">trending_up</span>
                    </div>
                    <div class="flex justify-between items-end border-b border-white/10 pb-4">
                        <div>
                            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Exposure</p>
                            <p class="text-xl font-bold text-rose-400">₹{{ number_format($stats['pendingPayments'], 0) }}</p>
                        </div>
                        <span class="material-symbols-rounded text-rose-400">error_outline</span>
                    </div>
                    <div class="flex justify-between items-end">
                        <div>
                            <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Active Partners</p>
                            <p class="text-xl font-bold text-indigo-400">{{ $stats['activeDealers'] }}</p>
                        </div>
                        <span class="material-symbols-rounded text-indigo-400">handshake</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
