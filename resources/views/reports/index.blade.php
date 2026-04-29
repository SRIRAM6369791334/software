@extends('layouts.app')
@section('title', 'Reports & Analytics')

@section('content')
<div class="space-y-10">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Reports & Analytics</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Real-time business summary and performance metrics</p>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="secondary" size="md">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></x-slot>
                Download PDF
            </x-button>
        </div>
    </div>

    <!-- Analytics Bento Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $tiles = [
                ['label' => 'Total Customers', 'value' => $summary['total_customers'], 'icon' => 'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z', 'color' => 'blue', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
                ['label' => 'Total Dealers', 'value' => $summary['total_dealers'], 'icon' => 'M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h8a1 1 0 001-1zm0 0h5l3 3V7a1 1 0 00-1-1h-4m-7 10H4m8 0h1m-1 0v1a2 2 0 01-2 2H6a2 2 0 01-2-2v-1m8 0H4', 'color' => 'indigo', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-600'],
                ['label' => 'Monthly Revenue', 'value' => '₹'.number_format($summary['total_revenue_month'], 0), 'icon' => 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z', 'color' => 'emerald', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-600'],
                ['label' => 'Monthly Purchases', 'value' => '₹'.number_format($summary['total_purchases_month'], 0), 'icon' => 'M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z', 'color' => 'blue', 'bg' => 'bg-blue-50', 'text' => 'text-blue-600'],
                ['label' => 'Monthly Expenses', 'value' => '₹'.number_format($summary['total_expenses_month'], 0), 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z', 'color' => 'rose', 'bg' => 'bg-rose-50', 'text' => 'text-rose-600'],
                ['label' => 'Receivables', 'value' => '₹'.number_format($summary['pending_receivables'], 0), 'icon' => 'M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z', 'color' => 'amber', 'bg' => 'bg-amber-50', 'text' => 'text-amber-600'],
                ['label' => 'Payables', 'value' => '₹'.number_format($summary['pending_payables'], 0), 'icon' => 'M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z', 'color' => 'orange', 'bg' => 'bg-orange-50', 'text' => 'text-orange-600'],
            ];
        @endphp
        @foreach($tiles as $tile)
            <x-card class="relative overflow-hidden group">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $tile['label'] }}</p>
                        <p class="text-2xl font-black text-slate-900 mt-2">{{ $tile['value'] }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-2xl {{ $tile['bg'] }} {{ $tile['text'] }} flex items-center justify-center group-hover:scale-110 transition-transform duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tile['icon'] }}" />
                        </svg>
                    </div>
                </div>
                <!-- Sparkle Decoration -->
                <div class="absolute -right-4 -bottom-4 w-16 h-16 {{ $tile['bg'] }} rounded-full opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
            </x-card>
        @endforeach
    </div>

    <!-- Summary Tables -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Top Customers by Balance --}}
        <x-card padding="false">
            <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Top Customer Receivables</h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Highest outstanding balances</p>
                </div>
                <div class="p-2 rounded-xl bg-rose-50 text-rose-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                </div>
            </div>
            <div class="overflow-hidden">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-50">
                        @forelse($topCustomers as $c)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-4 font-extrabold text-slate-900">{{ $c->name }}</td>
                                <td class="px-8 py-4 text-right">
                                    <span class="font-black text-rose-600">₹{{ number_format($c->balance, 0) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-8 py-10 text-center text-slate-400 font-medium italic">No outstanding balances</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>

        {{-- Top Dealers by Pending Amount --}}
        <x-card padding="false">
            <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <div>
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Top Dealer Payables</h2>
                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">Upcoming payment obligations</p>
                </div>
                <div class="p-2 rounded-xl bg-amber-50 text-amber-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <div class="overflow-hidden">
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-slate-50">
                        @forelse($topDealers as $d)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-8 py-4 font-extrabold text-slate-900">{{ $d->firm_name }}</td>
                                <td class="px-8 py-4 text-right">
                                    <span class="font-black text-amber-600">₹{{ number_format($d->pending_amount, 0) }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-8 py-10 text-center text-slate-400 font-medium italic">No pending payments</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-card>
    </div>
</div>
@endsection
