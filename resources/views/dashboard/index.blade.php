@extends('layouts.app')
@section('title', 'Executive Command Center')

@section('content')
{{-- Premium Cinematic Header --}}
<div class="relative mb-12 p-10 rounded-[3.5rem] bg-gray-900 overflow-hidden group shadow-2xl shadow-gray-900/20">
    <div class="absolute top-0 right-0 w-1/2 h-full bg-gradient-to-l from-emerald-500/10 to-transparent pointer-events-none"></div>
    <div class="absolute -bottom-24 -right-24 w-64 h-64 bg-emerald-500/5 rounded-full blur-3xl"></div>
    
    <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-8">
        <div>
            <div class="flex items-center gap-3 mb-4">
                <span class="px-3 py-1 bg-emerald-500 text-[10px] font-black text-white rounded-full tracking-widest uppercase">Live System Active</span>
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>
            <h1 class="text-4xl md:text-5xl font-black text-white tracking-tighter mb-2">
                Good {{ now()->hour < 12 ? 'Morning' : (now()->hour < 17 ? 'Afternoon' : 'Evening') }}, <span class="text-emerald-400">Admin</span>
            </h1>
            <p class="text-gray-400 font-medium text-lg">Your farm operations are performing within optimal thresholds today.</p>
        </div>
        <div class="flex flex-col items-end">
            <div class="text-right">
                <p class="text-[10px] font-black text-gray-500 uppercase tracking-[0.3em] mb-1">Station Local Time</p>
                <p class="text-3xl font-black text-white tracking-tighter">{{ date('h:i A') }}</p>
                <p class="text-sm font-bold text-emerald-500/80 uppercase tracking-widest mt-1">{{ date('l, d F Y') }}</p>
            </div>
        </div>
    </div>
</div>

{{-- Operational Pulse Grid --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-12">
    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 flex flex-col justify-between group hover:border-emerald-200 transition-all">
        <div class="flex items-center justify-between mb-6">
            <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">🐥</div>
            <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest bg-emerald-50 px-2 py-1 rounded-lg">Live Count</span>
        </div>
        <div>
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Current Birds</h3>
            <p class="text-4xl font-black text-gray-900 tracking-tighter">{{ number_format($stats['totalBirds'], 0) }}</p>
            <p class="text-xs text-gray-400 font-bold mt-2 uppercase tracking-tight">Across {{ $stats['activeBatches'] }} Active Batches</p>
        </div>
    </div>

    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 flex flex-col justify-between group hover:border-red-200 transition-all">
        <div class="flex items-center justify-between mb-6">
            <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">📉</div>
            <span class="text-[10px] font-black text-red-600 uppercase tracking-widest bg-red-50 px-2 py-1 rounded-lg">Survival</span>
        </div>
        <div>
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Mortality (MTD)</h3>
            <p class="text-4xl font-black text-gray-900 tracking-tighter">{{ number_format($stats['mortalityMTD'], 0) }}</p>
            <p class="text-xs text-gray-400 font-bold mt-2 uppercase tracking-tight">Total Losses This Month</p>
        </div>
    </div>

    <div class="bg-white p-8 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 flex flex-col justify-between group hover:border-blue-200 transition-all">
        <div class="flex items-center justify-between mb-6">
            <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">💰</div>
            <span class="text-[10px] font-black text-blue-600 uppercase tracking-widest bg-blue-50 px-2 py-1 rounded-lg">Collections</span>
        </div>
        <div>
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Today's Revenue</h3>
            <p class="text-4xl font-black text-gray-900 tracking-tighter">₹{{ number_format($stats['todayRevenue'], 0) }}</p>
            <p class="text-xs text-gray-400 font-bold mt-2 uppercase tracking-tight">From Retail & Wholesale</p>
        </div>
    </div>

    <div class="bg-gray-900 p-8 rounded-[2.5rem] shadow-2xl shadow-gray-900/20 flex flex-col justify-between relative overflow-hidden group">
        <div class="absolute -right-6 -top-6 opacity-10 text-8xl pointer-events-none group-hover:scale-110 transition-transform">🏗️</div>
        <div class="flex items-center justify-between mb-6 relative z-10">
            <div class="w-14 h-14 bg-white/10 text-white rounded-2xl flex items-center justify-center text-2xl">🚨</div>
        </div>
        <div class="relative z-10">
            <h3 class="text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1">Dealer Dues</h3>
            <p class="text-4xl font-black text-white tracking-tighter">₹{{ number_format($stats['monthlyPurchase'] / 1000, 1) }}k</p>
            <p class="text-xs text-emerald-400 font-bold mt-2 uppercase tracking-tight">MTD Liability Flow</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
    {{-- Left Column: Recent Activity --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-[3rem] border border-gray-200 shadow-2xl overflow-hidden h-full">
            <div class="px-10 py-8 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                <div>
                    <h2 class="text-xl font-black text-gray-900 tracking-tight">Recent Transactions</h2>
                    <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mt-1">Live Sales Feed</p>
                </div>
                <a href="{{ route('billing.daily.index') }}" class="px-5 py-2 bg-white border border-gray-200 rounded-xl text-[10px] font-black text-gray-400 uppercase tracking-widest hover:text-emerald-600 hover:border-emerald-200 transition-all">View All →</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead>
                        <tr class="text-[10px] font-black text-gray-400 uppercase tracking-widest border-b border-gray-50">
                            <th class="px-10 py-5">Customer</th>
                            <th class="px-10 py-5">Items</th>
                            <th class="px-10 py-5 text-right">Net Value</th>
                            <th class="px-10 py-5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentSales as $sale)
                            <tr class="hover:bg-gray-50/50 transition-all group">
                                <td class="px-10 py-6">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center font-black text-gray-500 group-hover:bg-emerald-100 group-hover:text-emerald-600 transition-all">
                                            {{ substr($sale->customer->name ?? '?', 0, 1) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="font-black text-gray-900 tracking-tight">{{ $sale->customer->name ?? '—' }}</span>
                                            <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $sale->date->format('d M, h:i A') }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-10 py-6">
                                    <span class="text-xs font-bold text-gray-500 italic">{{ Str::limit($sale->items->pluck('item_name')->join(', '), 25) }}</span>
                                </td>
                                <td class="px-10 py-6 text-right font-black text-gray-900">
                                    ₹{{ number_format($sale->net_amount, 0) }}
                                </td>
                                <td class="px-10 py-6 text-center">
                                    <span class="px-3 py-1.5 bg-emerald-50 text-emerald-600 text-[9px] font-black rounded-lg tracking-tighter shadow-sm">
                                        {{ strtoupper($sale->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-10 py-20 text-center text-gray-400 font-medium">No sales logged recently.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Right Column: Critical Alerts --}}
    <div class="space-y-8">
        {{-- EMI/Payment Alerts --}}
        <div class="bg-white rounded-[3rem] border border-gray-200 shadow-2xl overflow-hidden">
            <div class="px-10 py-8 border-b border-gray-50 bg-amber-50/50">
                <h2 class="text-lg font-black text-amber-900 tracking-tight flex items-center gap-2">
                    <span>🔔</span> Critical Alerts
                </h2>
                <p class="text-[10px] text-amber-700 font-bold uppercase tracking-widest mt-1">Immediate Action Required</p>
            </div>
            <div class="p-8 space-y-4">
                @forelse($upcomingEmis as $emi)
                    <div class="p-5 bg-white border border-amber-100 rounded-2xl shadow-sm flex items-center justify-between group hover:shadow-lg transition-all">
                        <div>
                            <p class="text-xs font-black text-gray-900 uppercase tracking-tight">{{ $emi->item }}</p>
                            <p class="text-[10px] text-amber-600 font-bold uppercase mt-1">Due: {{ $emi->due_date->format('d M') }} ({{ now()->diffInDays($emi->due_date) }} days)</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-black text-gray-900">₹{{ number_format($emi->amount, 0) }}</p>
                            <a href="#" class="text-[9px] font-black text-amber-600 uppercase tracking-tighter hover:underline">Pay Now →</a>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center">
                        <div class="text-4xl mb-4">✅</div>
                        <p class="text-sm font-bold text-gray-400">All clear! No pending alerts.</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Financial Insights --}}
        <div class="bg-indigo-600 rounded-[3rem] p-10 text-white relative overflow-hidden shadow-2xl shadow-indigo-600/30">
            <div class="absolute -right-10 -bottom-10 text-[10rem] font-black opacity-5 pointer-events-none select-none">$$</div>
            <div class="relative z-10">
                <h3 class="text-[10px] font-black text-indigo-200 uppercase tracking-[0.3em] mb-6">Financial Summary</h3>
                <div class="space-y-6">
                    <div class="flex justify-between items-center border-b border-indigo-500/30 pb-4">
                        <span class="text-xs font-bold text-indigo-100 uppercase tracking-widest">MTD Revenue</span>
                        <span class="text-xl font-black tracking-tighter">₹{{ number_format($stats['monthlyRevenue'], 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center border-b border-indigo-500/30 pb-4">
                        <span class="text-xs font-bold text-indigo-100 uppercase tracking-widest">Customer Dues</span>
                        <span class="text-xl font-black tracking-tighter">₹{{ number_format($stats['pendingPayments'], 0) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-xs font-bold text-indigo-100 uppercase tracking-widest">Active Dealers</span>
                        <span class="text-xl font-black tracking-tighter">{{ $stats['activeDealers'] }} Accounts</span>
                    </div>
                </div>
                <button class="w-full mt-8 py-4 bg-white text-indigo-600 font-black rounded-2xl text-[10px] uppercase tracking-widest shadow-xl active:scale-95 transition-all">Download P&L Report 📄</button>
            </div>
        </div>
    </div>
</div>
@endsection
