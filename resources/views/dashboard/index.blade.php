@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p class="text-sm text-gray-500 mt-0.5">Overview of your poultry business</p>
    </div>
    <div class="text-right">
        <p class="text-xs text-gray-400 font-medium uppercase tracking-wider">Today's Date</p>
        <p class="text-sm font-semibold text-gray-700">{{ date('l, d M Y') }}</p>
    </div>
</div>

{{-- EMI Alerts --}}
@if($upcomingEmis->isNotEmpty())
<div class="mb-8 p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-start gap-4">
    <div class="text-2xl text-amber-600 mt-0.5">🔔</div>
    <div class="flex-1">
        <h3 class="text-sm font-bold text-amber-900">Upcoming EMI Alerts</h3>
        <div class="mt-2 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            @foreach($upcomingEmis as $emi)
            <div class="bg-white/50 p-3 rounded-lg border border-amber-100 flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-gray-900">{{ $emi->item }}</p>
                    <p class="text-[10px] text-amber-700 mt-0.5">Due: {{ $emi->due_date->format('d M') }}</p>
                </div>
                <p class="text-sm font-bold text-gray-900">₹{{ number_format($emi->amount, 0) }}</p>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

{{-- Stats Grid --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-start gap-4 transition-all hover:shadow-md">
        <div class="rounded-xl bg-emerald-50 text-emerald-600 p-3 text-xl">💰</div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Daily Revenue (Bills)</p>
            <p class="text-xl font-bold mt-0.5">₹{{ number_format($stats['todayRevenue'], 0, '.', ',') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $stats['purchaseCount'] }} purchase entries</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-start gap-4 transition-all hover:shadow-md">
        <div class="rounded-xl bg-blue-50 text-blue-600 p-3 text-xl">👥</div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Total Customers</p>
            <p class="text-xl font-bold mt-0.5">{{ $stats['totalCustomers'] }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Retail & Wholesale</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-start gap-4 transition-all hover:shadow-md">
        <div class="rounded-xl bg-amber-50 text-amber-600 p-3 text-xl">⚠️</div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Customer Outstandings</p>
            <p class="text-xl font-bold mt-0.5">₹{{ number_format($stats['pendingPayments'], 0, '.', ',') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">{{ $stats['pendingCount'] }} accounts</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-start gap-4 transition-all hover:shadow-md">
        <div class="rounded-xl bg-purple-50 text-purple-600 p-3 text-xl">📈</div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Monthly Sale Value</p>
            <p class="text-xl font-bold mt-0.5">₹{{ number_format($stats['monthlyRevenue'], 0, '.', ',') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">This Month (Bills)</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-start gap-4 transition-all hover:shadow-md">
        <div class="rounded-xl bg-red-50 text-red-600 p-3 text-xl">🛒</div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Purchases (MTD)</p>
            <p class="text-xl font-bold mt-0.5">₹{{ number_format($stats['monthlyPurchase'], 0, '.', ',') }}</p>
            <p class="text-xs text-gray-400 mt-0.5">Total stock inward</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-start gap-4 transition-all hover:shadow-md">
        <div class="rounded-xl bg-indigo-50 text-indigo-600 p-3 text-xl">🚛</div>
        <div>
            <p class="text-xs text-gray-500 font-medium">Dealer Outstandings</p>
            <p class="text-xl font-bold mt-0.5">{{ $stats['activeDealers'] }} Accounts</p>
            <p class="text-xs text-gray-400 mt-0.5">Purchase cycle active</p>
        </div>
    </div>

</div>

{{-- Recent Sales --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h2 class="text-base font-semibold text-gray-900">Recent Sales Activity</h2>
            <p class="text-xs text-gray-500 mt-0.5">Latest bills generated</p>
        </div>
        <a href="{{ route('billing.daily.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">View All →</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Date</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Items</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Qty (kg)</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Amount</th>
                    <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($recentSales as $sale)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3.5 font-medium text-gray-900">{{ $sale->customer->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-gray-500">{{ $sale->date->format('d M Y') }}</td>
                        <td class="px-5 py-3.5 text-gray-500 italic">{{ Str::limit($sale->items_description, 20) }}</td>
                        <td class="px-5 py-3.5 text-right font-mono text-gray-600">{{ number_format($sale->quantity_kg, 2) }}</td>
                        <td class="px-5 py-3.5 text-right font-semibold font-mono text-gray-900">₹{{ number_format($sale->amount, 0, '.', ',') }}</td>
                        <td class="px-5 py-3.5 text-center">
                            @php
                                $statusColors = ['Generated'=>'bg-blue-50 text-blue-700','Pending'=>'bg-amber-50 text-amber-700','Paid'=>'bg-emerald-50 text-emerald-700'];
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$sale->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $sale->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400 italic">No recent sales records found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
