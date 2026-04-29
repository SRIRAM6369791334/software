@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Monthly Purchase Report</h1>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded shadow transition">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <a href="{{ route('reports.purchases.export-pdf', ['month' => $month, 'year' => $year]) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow transition">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <form action="{{ route('reports.purchases.monthly') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
                <select name="month" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
                <select name="year" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @foreach(range(now()->year - 5, now()->year) as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md shadow transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500 text-center">
            <p class="text-sm text-gray-500 uppercase font-bold">Total Monthly Purchase</p>
            <p class="text-3xl font-bold text-gray-800">₹{{ number_format($purchases->sum('total_amount'), 2) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-500 text-center">
            <p class="text-sm text-gray-500 uppercase font-bold">Total GST Paid</p>
            <p class="text-3xl font-bold text-gray-800">₹{{ number_format($purchases->sum('gst_amount'), 2) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-indigo-500 text-center">
            <p class="text-sm text-gray-500 uppercase font-bold">Vendor Count</p>
            <p class="text-3xl font-bold text-gray-800">{{ $purchases->unique('vendor_id')->count() }}</p>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vendor Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orders</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total GST</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @php
                    $grouped = $purchases->groupBy('vendor_id');
                @endphp
                @forelse($grouped as $vendorId => $vendorPurchases)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $vendorPurchases->first()->vendor->name ?? $vendorPurchases->first()->vendor_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $vendorPurchases->count() }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">₹{{ number_format($vendorPurchases->sum('total_amount'), 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₹{{ number_format($vendorPurchases->sum('gst_amount'), 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-10 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-warehouse text-4xl mb-4 text-gray-300"></i>
                            <p>No purchase records found for this month.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
