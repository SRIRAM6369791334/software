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

<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
    <x-stat-card title="Total Billed" prefix="Rs" value="{{ number_format($breakdown['total_billed'], 2) }}" icon="ph-receipt" color="sky" />
    <x-stat-card title="Total Collected" prefix="Rs" value="{{ number_format($breakdown['total_collected'], 2) }}" icon="ph-wallet" color="emerald" />
    <x-stat-card title="Billed Profit" prefix="Rs" value="{{ number_format($breakdown['billed_profit'], 2) }}" icon="ph-chart-line-up" color="amber" />
    <x-stat-card title="Collected Profit" prefix="Rs" value="{{ number_format($breakdown['collected_profit'], 2) }}" icon="ph-chart-pie-slice" color="emerald" />
    <x-stat-card title="Pending Collection" prefix="Rs" value="{{ number_format($breakdown['pending_collection'], 2) }}" icon="ph-warning-circle" color="{{ $breakdown['pending_collection'] > 0 ? 'rose' : 'emerald' }}" />
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
        <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors group">
            <td class="px-6 py-4 font-bold text-zinc-950 dark:text-zinc-50">{{ $row['week'] }}</td>
            <td class="px-6 py-4 text-right font-mono text-emerald-600 dark:text-emerald-400 group-hover:scale-[1.02] transition-transform"><x-currency :amount="$row['revenue']" /></td>
            <td class="px-6 py-4 text-right font-mono text-amber-600 dark:text-amber-400 group-hover:scale-[1.02] transition-transform"><x-currency :amount="$row['purchase']" /></td>
            <td class="px-6 py-4 text-right font-mono text-rose-600 dark:text-rose-400 group-hover:scale-[1.02] transition-transform"><x-currency :amount="$row['expenses']" /></td>
            <td class="px-6 py-4 text-right font-black {{ $row['profit'] >= 0 ? 'text-emerald-700 dark:text-emerald-300' : 'text-rose-700 dark:text-rose-300' }} group-hover:scale-[1.05] transition-transform">
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
    
    const labels = weeklyData.map(d => d.week).reverse();
    const revenue = weeklyData.map(d => d.revenue).reverse();
    const expenses = weeklyData.map(d => d.expenses + d.purchase).reverse();
    const profit = weeklyData.map(d => d.profit).reverse();

    const ctxWeekly = document.getElementById('weeklyChart').getContext('2d');
    
    const revGrad = ctxWeekly.createLinearGradient(0, 0, 0, 300);
    revGrad.addColorStop(0, 'rgba(16, 185, 129, 1)'); // Emerald 500
    revGrad.addColorStop(1, 'rgba(16, 185, 129, 0.1)');

    const expGrad = ctxWeekly.createLinearGradient(0, 0, 0, 300);
    expGrad.addColorStop(0, 'rgba(244, 63, 94, 1)'); // Rose 500
    expGrad.addColorStop(1, 'rgba(244, 63, 94, 0.1)');

    const profitGrad = ctxWeekly.createLinearGradient(0, 0, 0, 300);
    profitGrad.addColorStop(0, 'rgba(59, 130, 246, 0.5)'); // Blue 500
    profitGrad.addColorStop(1, 'rgba(59, 130, 246, 0)');

    new Chart(ctxWeekly, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Revenue',
                    data: revenue,
                    backgroundColor: revGrad,
                    borderRadius: 6,
                    borderSkipped: 'bottom',
                    barPercentage: 0.6,
                    maxBarThickness: 48,
                },
                {
                    label: 'Total Expenses',
                    data: expenses,
                    backgroundColor: expGrad,
                    borderRadius: 6,
                    borderSkipped: 'bottom',
                    barPercentage: 0.6,
                    maxBarThickness: 48,
                },
                {
                    label: 'Net Profit',
                    data: profit,
                    type: 'line',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: profitGrad,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: 'rgba(59, 130, 246, 1)',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
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
                legend: { 
                    position: 'bottom',
                    labels: { usePointStyle: true, padding: 20, font: { family: "'Outfit', sans-serif" } }
                },
                tooltip: {
                    backgroundColor: 'rgba(24, 24, 27, 0.9)',
                    titleFont: { family: "'Cabinet Grotesk', sans-serif", size: 14 },
                    bodyFont: { family: "'Outfit', sans-serif", size: 13 },
                    padding: 12,
                    cornerRadius: 12,
                    displayColors: true,
                    usePointStyle: true
                }
            },
            scales: {
                x: { 
                    grid: { display: false }, 
                    border: { display: false },
                    ticks: { font: { family: "'Outfit', sans-serif" } }
                },
                y: { 
                    beginAtZero: true,
                    grid: { color: 'rgba(161, 161, 170, 0.1)', borderDash: [5, 5] },
                    border: { display: false },
                    ticks: { font: { family: "'Outfit', sans-serif" } }
                }
            }
        }
    });

    const breakdown = @json($breakdown);
    const hasData = breakdown.total_collected > 0 || breakdown.pending_collection > 0;

    const ctxColl = document.getElementById('collectionChart').getContext('2d');
    const collGrad = ctxColl.createLinearGradient(0, 0, 0, 300);
    collGrad.addColorStop(0, '#10b981');
    collGrad.addColorStop(1, '#047857');

    const pendGrad = ctxColl.createLinearGradient(0, 0, 0, 300);
    pendGrad.addColorStop(0, '#f59e0b');
    pendGrad.addColorStop(1, '#b45309');

    new Chart(ctxColl, {
        type: 'doughnut',
        data: {
            labels: hasData ? ['Collected', 'Pending Collection'] : ['No Data'],
            datasets: [{
                data: hasData ? [breakdown.total_collected, breakdown.pending_collection] : [1],
                backgroundColor: hasData ? [collGrad, pendGrad] : ['#e5e7eb'],
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { 
                    position: 'bottom',
                    display: hasData,
                    labels: { usePointStyle: true, padding: 20, font: { family: "'Outfit', sans-serif" } }
                },
                tooltip: {
                    enabled: hasData,
                    backgroundColor: 'rgba(24, 24, 27, 0.9)',
                    titleFont: { family: "'Cabinet Grotesk', sans-serif" },
                    bodyFont: { family: "'Outfit', sans-serif" },
                    padding: 12,
                    cornerRadius: 12,
                    usePointStyle: true
                }
            },
            cutout: '75%',
            layout: { padding: 10 }
        }
    });
});
</script>
@endpush
