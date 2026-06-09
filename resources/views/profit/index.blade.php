@extends('layouts.app')
@section('title', 'Profit & Loss Overview')

@section('content')
<x-page-header 
    title="Profit & Loss Dashboard" 
    subtitle="Real-time financial performance overview">
    <div class="flex gap-3">
        <x-button variant="secondary" href="{{ route('profit.monthly') }}">Monthly Breakdown</x-button>
        <x-button variant="primary" href="{{ route('profit.expense-vs-income') }}">Expense vs Income</x-button>
    </div>
</x-page-header>

<x-card class="mb-4">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <form method="GET" class="flex items-center gap-2">
            <x-form.input type="date" name="start_date" :value="$startDate" />
            <span class="text-zinc-400 font-black">-></span>
            <x-form.input type="date" name="end_date" :value="$endDate" />
            <x-button type="submit" variant="secondary" class="ml-2">Filter</x-button>
        </form>
        <div class="flex items-center gap-2">
            <x-button variant="outline" href="{{ route('profit.export', ['start_date' => $startDate, 'end_date' => $endDate]) }}">
                Export
            </x-button>
            <x-button variant="primary" href="{{ route('profit.export-pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}">
                Download PDF
            </x-button>
        </div>
    </div>
</x-card>

<div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
    <x-stat-card title="Total Billed" value="Rs {{ number_format($breakdown['total_billed'], 2) }}" icon="ph-receipt" color="sky" />
    <x-stat-card title="Total Collected" value="Rs {{ number_format($breakdown['total_collected'], 2) }}" icon="ph-wallet" color="emerald" />
    <x-stat-card title="Billed Profit" value="Rs {{ number_format($breakdown['billed_profit'], 2) }}" icon="ph-chart-line-up" color="amber" />
    <x-stat-card title="Collected Profit" value="Rs {{ number_format($breakdown['collected_profit'], 2) }}" icon="ph-chart-pie-slice" color="emerald" />
    <x-stat-card title="Pending Collection" value="Rs {{ number_format($breakdown['pending_collection'], 2) }}" icon="ph-warning-circle" color="{{ $breakdown['pending_collection'] > 0 ? 'rose' : 'emerald' }}" />
</div>

{{-- Weekly Breakdown Table --}}
<x-card title="Recent Weekly Performance" class="mb-8">
    <x-data-table>
        <x-slot name="head">
            <tr>
                <th>Week</th>
                <th class="text-right">Revenue</th>
                <th class="text-right">Purchases</th>
                <th class="text-right">Expenses</th>
                <th class="text-right">Net Profit</th>
            </tr>
        </x-slot>
        @foreach($weeklyData as $row)
        <tr>
            <td class="font-bold text-zinc-950">{{ $row['week'] }}</td>
            <td class="text-right font-mono text-emerald-600"><x-currency :amount="$row['revenue']" /></td>
            <td class="text-right font-mono text-amber-600"><x-currency :amount="$row['purchase']" /></td>
            <td class="text-right font-mono text-rose-600"><x-currency :amount="$row['expenses']" /></td>
            <td class="text-right font-black {{ $row['profit'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                <x-currency :amount="$row['profit']" />
            </td>
        </tr>
        @endforeach
    </x-data-table>
</x-card>
@endsection
