@extends('layouts.app')
@section('title', 'Vendor Analytics')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Vendor Analytics</h1>
        <p class="text-sm text-gray-500 mt-0.5">Comparative spend analysis and order volume breakdown</p>
    </div>
    <div class="flex gap-2">
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition-colors">
            🖨️ Print Analysis
        </button>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Table Section --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden flex flex-col">
        <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Top Vendors by Purchase Volume</h3>
        </div>
        <div class="overflow-x-auto flex-grow">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Vendor Firm</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Orders</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Total Spent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($vendorWise as $data)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-6 py-4 font-medium text-gray-900">{{ $data->vendor->firm_name ?? 'Unknown' }}</td>
                        <td class="px-6 py-4 text-center font-mono text-gray-600">{{ $data->orders }}</td>
                        <td class="px-6 py-4 text-right font-mono font-bold text-emerald-600">₹{{ number_format($data->total, 0, '.', ',') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-12 text-center text-gray-400 italic">No vendor data available.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Chart Section --}}
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm flex flex-col">
        <div class="mb-4">
            <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest text-center">Purchase Distribution (₹)</h3>
        </div>
        <div class="flex-grow relative min-h-[300px]">
            <canvas id="vendorChart"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('vendorChart').getContext('2d');
        const labels = {!! json_encode($vendorWise->map(fn($v) => $v->vendor->firm_name ?? 'Unknown')) !!};
        const data = {!! json_encode($vendorWise->pluck('total')) !!};

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Spent (₹)',
                    data: data,
                    backgroundColor: 'rgba(16, 185, 129, 0.6)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true,
                            color: 'rgba(0,0,0,0.03)'
                        },
                        ticks: {
                            font: { size: 10 },
                            callback: function(value) {
                                return '₹' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10 } }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    });
</script>
@endsection

