@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="relative min-h-screen">
    {{-- Decorative Background Elements --}}
    <div class="absolute -top-40 -left-40 w-[600px] h-[600px] bg-emerald-400/10 blur-[120px] rounded-full pointer-events-none animate-pulse"></div>
    <div class="absolute top-1/2 -right-40 w-[600px] h-[600px] bg-sky-400/10 blur-[120px] rounded-full pointer-events-none animate-pulse" style="animation-delay: 1s"></div>

    {{-- Hero Section: Neural Telemetry v4 --}}
    <section class="relative mb-10 overflow-hidden rounded-[3rem] border border-white/40 bg-white/60 backdrop-blur-3xl p-8 shadow-2xl shadow-slate-200/40 lg:p-12 z-10">
        <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-emerald-50/50 to-transparent pointer-events-none"></div>
        
        <div class="relative z-20 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-12">
            <div class="max-w-2xl">
                <div class="mb-6 inline-flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50/50 px-4 py-2.5 text-[10px] font-black uppercase tracking-[0.2em] text-emerald-600">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Neural Stream Operational
                </div>
                <h1 class="text-4xl font-black tracking-tight text-slate-950 md:text-6xl mb-6">
                    Elite <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-600 to-sky-500">Telemetry</span>
                </h1>
                <p class="text-lg font-medium leading-relaxed text-slate-500">
                    Real-time orchestration of poultry operations, financial flows, and supply chain health through our autonomous neural framework.
                </p>
                <div class="mt-10 flex flex-wrap gap-4">
                    <div class="px-6 py-4 rounded-2xl bg-slate-950 text-white shadow-xl shadow-slate-950/20 flex items-center gap-3 group cursor-pointer hover:scale-[1.02] transition-all">
                        <span class="material-symbols-rounded text-emerald-400">rocket_launch</span>
                        <span class="text-sm font-black uppercase tracking-widest">Active Batches: {{ $stats['activeBatches'] }}</span>
                    </div>
                    <div class="px-6 py-4 rounded-2xl bg-white border border-slate-200 text-slate-950 shadow-sm flex items-center gap-3 group cursor-pointer hover:border-emerald-200 transition-all">
                        <span class="material-symbols-rounded text-sky-500">waves</span>
                        <span class="text-sm font-black uppercase tracking-widest">System Load: 12%</span>
                    </div>
                </div>
            </div>

            {{-- 3D Heartbeat Visualization Simulation --}}
            <div class="relative w-full max-w-sm aspect-square lg:max-w-md">
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="w-64 h-64 bg-gradient-to-br from-emerald-400/20 to-sky-400/20 rounded-full blur-3xl animate-pulse"></div>
                    <div class="absolute w-48 h-48 border-[16px] border-emerald-500/10 rounded-full animate-ping"></div>
                    <div class="absolute w-48 h-48 border border-white shadow-2xl rounded-full bg-white/40 backdrop-blur-3xl flex items-center justify-center overflow-hidden">
                        <div class="flex flex-col items-center">
                            <span class="material-symbols-rounded text-6xl text-emerald-600 animate-bounce">favorite</span>
                            <span class="mt-4 text-[10px] font-black uppercase tracking-[0.3em] text-slate-400">Heartbeat</span>
                        </div>
                        {{-- Random data lines --}}
                        <div class="absolute inset-0 pointer-events-none opacity-20">
                            <div class="absolute top-1/4 left-0 w-full h-px bg-emerald-500 translate-y-2"></div>
                            <div class="absolute top-1/2 left-0 w-full h-px bg-sky-500 -translate-y-4"></div>
                            <div class="absolute top-3/4 left-0 w-full h-px bg-violet-500 translate-y-6"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Stats Grid --}}
    <section class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4 mb-10 z-10 relative">
        @foreach([
            ['label' => 'Total Birds', 'value' => number_format($stats['totalBirds'], 0), 'meta' => 'MTD Inventory', 'icon' => 'egg_alt', 'tone' => 'emerald', 'trend' => '+4.2%'],
            ['label' => 'Mortality', 'value' => number_format($stats['mortalityMTD'], 0), 'meta' => 'Loss Analytics', 'icon' => 'trending_down', 'tone' => 'rose', 'trend' => '-1.5%'],
            ['label' => 'Revenue', 'value' => '₹' . number_format($stats['todayRevenue'], 0), 'meta' => 'Daily Inflow', 'icon' => 'payments', 'tone' => 'sky', 'trend' => '+12%'],
            ['label' => 'Purchase', 'value' => '₹' . number_format($stats['monthlyPurchase'], 0), 'meta' => 'Supply Cost', 'icon' => 'shopping_cart', 'tone' => 'amber', 'trend' => '+2.1%'],
        ] as $card)
            <article class="group relative overflow-hidden rounded-[2rem] border border-white/40 bg-white/80 p-8 shadow-xl shadow-slate-200/30 transition-all duration-500 hover:-translate-y-2 hover:shadow-2xl hover:border-{{ $card['tone'] }}-200">
                <div class="absolute -right-4 -top-4 w-24 h-24 bg-{{ $card['tone'] }}-500/5 blur-2xl rounded-full group-hover:bg-{{ $card['tone'] }}-500/10 transition-colors"></div>
                
                <div class="flex items-center justify-between mb-8">
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-{{ $card['tone'] }}-50 text-{{ $card['tone'] }}-600 transition-all group-hover:scale-110 group-hover:rotate-6 shadow-sm border border-{{ $card['tone'] }}-100">
                        <span class="material-symbols-rounded text-2xl">{{ $card['icon'] }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase tracking-widest {{ str_contains($card['trend'], '+') ? 'text-emerald-600' : 'text-rose-600' }}">
                            {{ $card['trend'] }}
                        </span>
                        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">vs last week</p>
                    </div>
                </div>
                
                <h3 class="text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 mb-2">{{ $card['label'] }}</h3>
                <p class="text-3xl font-black tracking-tight text-slate-950 mb-2">{{ $card['value'] }}</p>
                <div class="flex items-center gap-2">
                    <div class="h-1.5 flex-1 bg-slate-100 rounded-full overflow-hidden">
                        <div class="h-full bg-{{ $card['tone'] }}-500 rounded-full w-2/3 opacity-40"></div>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 italic">{{ $card['meta'] }}</span>
                </div>
            </article>
        @endforeach
    </section>

    {{-- Main Activity Area --}}
    <section class="grid grid-cols-1 gap-8 xl:grid-cols-12 relative z-10">
        {{-- Recent Activity Table --}}
        <div class="xl:col-span-8 bg-white/60 backdrop-blur-xl rounded-[2.5rem] border border-white/40 shadow-xl shadow-slate-200/40 overflow-hidden">
            <div class="p-8 border-b border-slate-100 bg-gradient-to-r from-emerald-50/50 to-sky-50/50 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h3 class="font-black text-slate-950 flex items-center gap-2 uppercase tracking-widest text-xs">
                        <span class="material-symbols-rounded text-emerald-600">history</span>
                        Recent Transactions
                    </h3>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Daily billing flow</p>
                </div>
                <a href="{{ route('billing.daily.index') }}" class="group inline-flex items-center gap-2 rounded-xl bg-slate-950 px-5 py-2.5 text-[10px] font-black uppercase tracking-widest text-white shadow-lg transition-all hover:scale-[1.02] active:scale-95">
                    Full Ledger
                    <span class="material-symbols-rounded text-lg group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-slate-50/50 border-b border-slate-100">
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Identiy / Date</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest">Payload</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-right">Value</th>
                            <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Protocol</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentSales as $sale)
                            <tr class="group hover:bg-emerald-50/30 transition-all cursor-default">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-emerald-50 to-sky-50 flex items-center justify-center font-black text-emerald-700 group-hover:scale-110 transition-transform shadow-sm">
                                            {{ substr($sale->customer->name ?? '?', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-950 tracking-tight text-base">{{ $sale->customer->name ?? 'System User' }}</p>
                                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $sale->date->format('d M, h:i A') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <p class="text-xs font-bold text-slate-500 italic max-w-xs truncate">
                                        {{ $sale->items->pluck('item_name')->join(' · ') }}
                                    </p>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <p class="font-black text-slate-950 text-lg">₹{{ number_format($sale->net_amount, 0) }}</p>
                                </td>
                                <td class="px-8 py-6 text-center">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-emerald-100 bg-emerald-50 text-[9px] font-black uppercase tracking-[0.2em] text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        {{ $sale->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <div class="w-20 h-20 bg-slate-50 rounded-3xl flex items-center justify-center text-slate-200 mx-auto mb-6">
                                        <span class="material-symbols-rounded text-4xl">inventory_2</span>
                                    </div>
                                    <h4 class="font-black text-slate-950 uppercase tracking-widest">Zero Operations</h4>
                                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mt-2">No recent billing activity detected</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Sidebar Widgets --}}
        <div class="xl:col-span-4 space-y-8">
            {{-- Alerts Widget --}}
            <div class="bg-gradient-to-br from-rose-500 to-rose-600 rounded-[2.5rem] p-8 shadow-2xl shadow-rose-200/50 text-white relative overflow-hidden group">
                <div class="absolute -right-12 -top-12 w-48 h-48 bg-white/10 blur-3xl rounded-full group-hover:scale-125 transition-transform duration-700"></div>
                
                <div class="flex items-center justify-between mb-8 relative z-10">
                    <h3 class="font-black flex items-center gap-3 uppercase tracking-widest text-xs">
                        <span class="material-symbols-rounded animate-pulse">warning</span>
                        Varlock Critical
                    </h3>
                    <span class="px-2.5 py-1 rounded-lg bg-white/20 text-[10px] font-black uppercase tracking-widest">Active</span>
                </div>

                <div class="space-y-4 relative z-10">
                    @forelse($upcomingEmis as $emi)
                        <div class="bg-white/10 backdrop-blur-md rounded-2xl p-5 border border-white/10 hover:bg-white/20 transition-all cursor-pointer">
                            <div class="flex items-start justify-between mb-2">
                                <p class="text-xs font-black uppercase tracking-widest opacity-80">{{ $emi->item }}</p>
                                <span class="material-symbols-rounded text-lg opacity-60">notifications</span>
                            </div>
                            <div class="flex items-end justify-between">
                                <div>
                                    <p class="text-2xl font-black">₹{{ number_format($emi->amount, 0) }}</p>
                                    <p class="text-[10px] font-bold uppercase tracking-widest opacity-60 mt-1">Due: {{ $emi->due_date->format('d M') }}</p>
                                </div>
                                <span class="text-[10px] font-black bg-white text-rose-600 px-2 py-1 rounded-md uppercase">{{ now()->diffInDays($emi->due_date) }}d</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-4">
                                <span class="material-symbols-rounded text-2xl">verified_user</span>
                            </div>
                            <p class="text-[10px] font-black uppercase tracking-widest opacity-60">No Security Threats</p>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Financial Orchestration --}}
            <div class="bg-slate-950 rounded-[2.5rem] p-8 shadow-2xl shadow-slate-900/20 text-white relative overflow-hidden">
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 via-transparent to-sky-500/10 pointer-events-none"></div>
                
                <h3 class="font-black flex items-center gap-3 uppercase tracking-widest text-xs text-emerald-400 mb-8">
                    <span class="material-symbols-rounded">account_balance</span>
                    Financial Engine
                </h3>

                <div class="space-y-6">
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all">
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">MTD Net Revenue</p>
                            <p class="text-xl font-black tabular-nums">₹{{ number_format($stats['monthlyRevenue'], 0) }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 flex items-center justify-center text-emerald-400">
                            <span class="material-symbols-rounded">trending_up</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all">
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Exposure / Dues</p>
                            <p class="text-xl font-black tabular-nums text-rose-400">₹{{ number_format($stats['pendingPayments'], 0) }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-rose-500/20 flex items-center justify-center text-rose-400">
                            <span class="material-symbols-rounded">error_outline</span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all">
                        <div>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mb-1">Active Partners</p>
                            <p class="text-xl font-black tabular-nums text-sky-400">{{ $stats['activeDealers'] }}</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-sky-500/20 flex items-center justify-center text-sky-400">
                            <span class="material-symbols-rounded">handshake</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 pt-8 border-t border-white/10">
                    <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">
                        <span>Liquidity Score</span>
                        <span class="text-emerald-400 font-bold">8.4 / 10</span>
                    </div>
                    <div class="h-1.5 w-full bg-white/5 rounded-full mt-3 overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-emerald-500 to-sky-500 w-[84%] rounded-full shadow-[0_0_12px_rgba(16,185,129,0.3)]"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection
