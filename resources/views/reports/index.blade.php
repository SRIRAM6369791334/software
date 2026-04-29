@extends('layouts.app')
@section('title', 'Reports')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Reports</h1>
    <p class="text-sm text-gray-500 mt-0.5">Business summary and analytics</p>
</div>

<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 mb-8">
    @php
        $tiles = [
            ['label'=>'Total Customers',  'value'=>$summary['total_customers'],   'icon'=>'👥','color'=>'blue'],
            ['label'=>'Total Dealers',    'value'=>$summary['total_dealers'],     'icon'=>'🚛','color'=>'indigo'],
            ['label'=>'Revenue (Month)',  'value'=>'₹'.number_format($summary['total_revenue_month'],0,'.', ','),   'icon'=>'💰','color'=>'emerald'],
            ['label'=>'Purchases (Month)','value'=>'₹'.number_format($summary['total_purchases_month'],0,'.', ','),'icon'=>'🛒','color'=>'blue'],
            ['label'=>'Expenses (Month)', 'value'=>'₹'.number_format($summary['total_expenses_month'],0,'.', ','), 'icon'=>'💸','color'=>'red'],
            ['label'=>'Pending Receivables','value'=>'₹'.number_format($summary['pending_receivables'],0,'.', ','),'icon'=>'📥','color'=>'amber'],
            ['label'=>'Pending Payables',  'value'=>'₹'.number_format($summary['pending_payables'],0,'.', ','),   'icon'=>'📤','color'=>'orange'],
        ];
    @endphp
    @foreach($tiles as $tile)
        <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
            <span class="text-xl">{{ $tile['icon'] }}</span>
            <p class="text-xs text-gray-400 mt-2">{{ $tile['label'] }}</p>
            <p class="text-lg font-bold mt-0.5 text-{{ $tile['color'] }}-700">{{ $tile['value'] }}</p>
        </div>
    @endforeach
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Top Customers by Balance --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">⚠️ Top Customers by Outstanding Balance</h2>
        </div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-50">
                @forelse($topCustomers as $c)
                    <tr class="hover:bg-gray-50/50 px-5">
                        <td class="px-5 py-3.5 font-medium text-gray-900">{{ $c->name }}</td>
                        <td class="px-5 py-3.5 text-right font-mono font-bold text-red-600">₹{{ number_format($c->balance, 0, '.', ',') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="px-5 py-6 text-center text-gray-400">No outstanding balances</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Top Dealers by Pending Amount --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-sm font-semibold text-gray-900">⚠️ Top Dealers by Pending Payment</h2>
        </div>
        <table class="w-full text-sm">
            <tbody class="divide-y divide-gray-50">
                @forelse($topDealers as $d)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3.5 font-medium text-gray-900">{{ $d->firm_name }}</td>
                        <td class="px-5 py-3.5 text-right font-mono font-bold text-amber-600">₹{{ number_format($d->pending_amount, 0, '.', ',') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="px-5 py-6 text-center text-gray-400">No pending payments</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
