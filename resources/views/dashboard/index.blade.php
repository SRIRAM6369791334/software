@extends('layouts.app')
@section('title', 'Dashboard Overview')

@section('content')
<div class="space-y-8">
    <!-- Welcome Section -->
    <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-900 p-8 lg:p-12 text-white shadow-2xl">
        <div class="relative z-10 max-w-2xl">
            <h1 class="text-3xl lg:text-5xl font-extrabold tracking-tight mb-4">
                Good morning, <span class="text-primary-500">{{ auth()->user()->name ?? 'Manager' }}</span>! 🐔
            </h1>
            <p class="text-slate-400 text-lg font-medium mb-8 leading-relaxed">
                Your poultry empire is thriving. Today you have <span class="text-white">12 pending deliveries</span> and <span class="text-white">3 new wholesale inquiries</span>.
            </p>
            <div class="flex flex-wrap gap-4">
                <x-button variant="primary" size="md">
                    <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></x-slot>
                    Generate New Bill
                </x-button>
                <x-button variant="ghost" class="text-white hover:bg-white/10" size="md">
                    View Sales Reports
                </x-button>
            </div>
        </div>
        
        <!-- Abstract Decoration -->
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-96 h-96 bg-primary-500/20 rounded-full blur-[120px]"></div>
        <div class="absolute bottom-0 right-0 translate-y-1/2 -translate-x-1/4 w-64 h-64 bg-blue-500/10 rounded-full blur-[80px]"></div>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
        <!-- Revenue Card -->
        <x-card padding="false" class="group">
            <div class="p-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <x-badge variant="success">+12.5%</x-badge>
                </div>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Daily Revenue</p>
                <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">₹{{ number_format($stats['todayRevenue'], 0) }}</h3>
                <div class="mt-6 flex items-end gap-1 h-8">
                    @foreach([40,70,50,90,60,80,100] as $h)
                        <div class="flex-1 bg-emerald-100 rounded-t-sm transition-all group-hover:bg-emerald-500" style="height: {{ $h }}%"></div>
                    @endforeach
                </div>
            </div>
        </x-card>

        <!-- Customers Card -->
        <x-card padding="false" class="group">
            <div class="p-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                    <x-badge variant="info">Stable</x-badge>
                </div>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Total Customers</p>
                <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $stats['totalCustomers'] }}</h3>
                <div class="mt-6 flex items-center -space-x-3">
                    @foreach(range(1,5) as $i)
                        <div class="w-8 h-8 rounded-full border-2 border-white bg-slate-200 flex items-center justify-center text-[10px] font-bold text-slate-600">
                            {{ chr(64 + $i) }}
                        </div>
                    @endforeach
                    <div class="w-8 h-8 rounded-full border-2 border-white bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-400">
                        +{{ $stats['totalCustomers'] - 5 }}
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Outstandings Card -->
        <x-card padding="false" class="group">
            <div class="p-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                    </div>
                    <x-badge variant="warning">{{ $stats['pendingCount'] }} Due</x-badge>
                </div>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Outstanding</p>
                <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">₹{{ number_format($stats['pendingPayments'] / 1000, 1) }}k</h3>
                <div class="mt-6 bg-slate-100 rounded-full h-2 overflow-hidden">
                    <div class="bg-amber-500 h-full transition-all group-hover:scale-x-110 origin-left" style="width: 65%"></div>
                </div>
            </div>
        </x-card>

        <!-- Sales Trend Card -->
        <x-card padding="false" class="group">
            <div class="p-8">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-primary-50 rounded-2xl flex items-center justify-center text-primary-500">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                    </div>
                    <x-badge variant="primary">High</x-badge>
                </div>
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-widest">Monthly Sale</p>
                <h3 class="text-3xl font-extrabold text-slate-900 tracking-tight mt-1">₹{{ number_format($stats['monthlyRevenue'] / 100000, 1) }}L</h3>
                <div class="mt-6 flex items-center justify-between">
                    <div class="flex gap-1">
                        @foreach([1,2,3,4,5] as $i)
                            <div class="w-1.5 h-1.5 rounded-full {{ $i < 4 ? 'bg-primary-500' : 'bg-slate-200' }}"></div>
                        @endforeach
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Target Met</span>
                </div>
            </div>
        </x-card>
    </div>

    <!-- Main Content Area (2 Columns) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Recent Sales Table -->
        <div class="lg:col-span-2">
            <x-card padding="false" title="Recent Sales Activity" subtitle="Real-time transaction stream">
                <x-slot name="header">
                    <a href="{{ route('billing.daily.index') }}" class="text-sm font-bold text-primary-500 hover:text-primary-600 transition-colors">View All</a>
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left border-b border-slate-100">
                                <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Customer</th>
                                <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Items</th>
                                <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Amount</th>
                                <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($recentSales as $sale)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-[10px] font-bold text-slate-500">
                                                {{ substr($sale->customer->name ?? '?', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-slate-900">{{ $sale->customer->name ?? '—' }}</p>
                                                <p class="text-[10px] text-slate-500 font-medium mt-0.5">{{ $sale->date->format('d M, H:i') }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <p class="text-slate-600 font-medium truncate max-w-[150px]">{{ $sale->items_description }}</p>
                                    </td>
                                    <td class="px-8 py-5 text-right font-extrabold text-slate-900">
                                        ₹{{ number_format($sale->amount, 0) }}
                                    </td>
                                    <td class="px-8 py-5 text-center">
                                        @php
                                            $variants = ['Generated'=>'info','Pending'=>'warning','Paid'=>'success'];
                                        @endphp
                                        <x-badge :variant="$variants[$sale->status] ?? 'slate'">{{ $sale->status }}</x-badge>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center grayscale opacity-50">
                                            <svg class="w-12 h-12 text-slate-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                            <p class="text-sm font-bold text-slate-400">No recent sales records found</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        <!-- Right Column: EMI Alerts & Insights -->
        <div class="space-y-6">
            <!-- EMI Alerts -->
            @if($upcomingEmis->isNotEmpty())
                <div class="bg-amber-50/50 rounded-[2rem] border border-amber-200 p-8 shadow-sm">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-amber-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-amber-500/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-amber-900 tracking-tight leading-none">EMI Reminders</h3>
                            <p class="text-[11px] text-amber-700 font-bold uppercase tracking-wider mt-1.5">Action Required</p>
                        </div>
                    </div>
                    <div class="space-y-3">
                        @foreach($upcomingEmis as $emi)
                            <div class="bg-white p-4 rounded-2xl border border-amber-100 flex justify-between items-center group hover:border-amber-400 transition-colors">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">{{ $emi->item }}</p>
                                    <p class="text-[10px] text-amber-600 font-bold mt-1">DUE {{ $emi->due_date->format('d M') }}</p>
                                </div>
                                <p class="text-sm font-black text-slate-900">₹{{ number_format($emi->amount, 0) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Quick Stats -->
            <x-card title="System Health" subtitle="Live monitoring status">
                <div class="space-y-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="text-sm font-bold text-slate-700">Stock Inventory</span>
                        </div>
                        <span class="text-xs font-black text-slate-900">92%</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                            <span class="text-sm font-bold text-slate-700">Billing Service</span>
                        </div>
                        <span class="text-xs font-black text-slate-900">Active</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-2 h-2 rounded-full bg-primary-500"></div>
                            <span class="text-sm font-bold text-slate-700">Database Sync</span>
                        </div>
                        <span class="text-xs font-black text-slate-400">2m ago</span>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection

