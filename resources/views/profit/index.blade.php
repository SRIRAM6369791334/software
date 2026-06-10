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
        <form method="GET" class="flex items-center gap-3">
            <x-form.input type="date" name="start_date" :value="$startDate" />
            <div class="flex items-center justify-center text-zinc-400 bg-zinc-100 dark:bg-zinc-800 rounded-full w-8 h-8 shrink-0 shadow-inner">
                <span class="material-symbols-rounded text-sm">arrow_forward</span>
            </div>
            <x-form.input type="date" name="end_date" :value="$endDate" />
            <x-button type="submit" variant="secondary">Filter</x-button>
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <x-card class="lg:col-span-2" title="Revenue vs Expenses (Weekly)">
        <canvas id="weeklyChart" class="w-full h-64"></canvas>
    </x-card>
    <x-card class="lg:col-span-1" title="Collection Status">
        <canvas id="collectionChart" class="w-full h-64"></canvas>
    </x-card>
</div>

{{-- Weekly Breakdown Table --}}
<x-card title="Recent Weekly Performance" class="mb-8">
    <x-data-table>
        <x-slot name="head">
            <tr>
                <th class="px-6 py-4 font-semibold tracking-wider">Week</th>
                <th class="px-6 py-4 font-semibold tracking-wider text-right">Revenue</th>
                <th class="px-6 py-4 font-semibold tracking-wider text-right">Purchases</th>
                <th class="px-6 py-4 font-semibold tracking-wider text-right">Expenses</th>
                <th class="px-6 py-4 font-semibold tracking-wider text-right">Net Profit</th>
            </tr>
        </x-slot>
        @foreach($weeklyData as $row)
        <tr>
            <td class="px-6 py-4 font-bold text-zinc-950">{{ $row['week'] }}</td>
            <td class="px-6 py-4 text-right font-mono text-emerald-600"><x-currency :amount="$row['revenue']" /></td>
            <td class="px-6 py-4 text-right font-mono text-amber-600"><x-currency :amount="$row['purchase']" /></td>
            <td class="px-6 py-4 text-right font-mono text-rose-600"><x-currency :amount="$row['expenses']" /></td>
            <td class="px-6 py-4 text-right font-black {{ $row['profit'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                <x-currency :amount="$row['profit']" />
            </td>
        </tr>
        @endforeach
    </x-data-table>
</x-card>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const weeklyData = @json($weeklyData);
    
    // Reverse data to show oldest to newest left to right if it's not already
    const labels = weeklyData.map(d => d.week).reverse();
    const revenue = weeklyData.map(d => d.revenue).reverse();
    const expenses = weeklyData.map(d => d.expenses + d.purchase).reverse();
    const profit = weeklyData.map(d => d.profit).reverse();

    new Chart(document.getElementById('weeklyChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Revenue',
                    data: revenue,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)', // Emerald
                    borderRadius: 4
                },
                {
                    label: 'Total Expenses (Incl. Purchases)',
                    data: expenses,
                    backgroundColor: 'rgba(244, 63, 94, 0.8)', // Rose
                    borderRadius: 4
                },
                {
                    label: 'Net Profit',
                    data: profit,
                    type: 'line',
                    borderColor: 'rgba(59, 130, 246, 1)', // Blue
                    backgroundColor: 'rgba(59, 130, 246, 0.2)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    const breakdown = @json($breakdown);
    const hasData = breakdown.total_collected > 0 || breakdown.pending_collection > 0;

    new Chart(document.getElementById('collectionChart'), {
        type: 'doughnut',
        data: {
            labels: hasData ? ['Collected', 'Pending Collection'] : ['No Data'],
            datasets: [{
                data: hasData ? [breakdown.total_collected, breakdown.pending_collection] : [1],
                backgroundColor: hasData ? [
                    'rgba(16, 185, 129, 0.8)', // Emerald
                    'rgba(245, 158, 11, 0.8)'  // Amber
                ] : ['#e5e7eb'], // Gray for empty state
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    display: hasData 
                },
                tooltip: {
                    enabled: hasData
                }
            },
            cutout: '70%'
        }
    });
});
</script>
@endpush
