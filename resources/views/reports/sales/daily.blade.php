@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Daily Sales Report</h1>
        <div class="flex space-x-2">
            <button onclick="window.print()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded shadow transition">
                <i class="fas fa-print mr-2"></i>Print
            </button>
            <a href="{{ route('reports.sales.export-pdf', ['date' => $date]) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded shadow transition">
                <i class="fas fa-file-pdf mr-2"></i>Export PDF
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white p-6 rounded-lg shadow-md mb-8">
        <form action="{{ route('reports.sales.daily') }}" method="GET" class="flex items-end space-x-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Select Date</label>
                <input type="date" name="date" value="{{ $date }}" class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-md shadow transition">
                Filter
            </button>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-blue-500">
            <p class="text-sm text-gray-500 uppercase font-bold">Total Sale</p>
            <p class="text-2xl font-bold text-gray-800">₹{{ number_format($dailyBills->sum('amount'), 2) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-green-500">
            <p class="text-sm text-gray-500 uppercase font-bold">Total GST</p>
            <p class="text-2xl font-bold text-gray-800">₹{{ number_format($dailyBills->sum('gst_amount'), 2) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-indigo-500">
            <p class="text-sm text-gray-500 uppercase font-bold">Cash Sales</p>
            <p class="text-2xl font-bold text-gray-800">₹{{ number_format($dailyBills->where('payment_mode', 'cash')->sum('amount'), 2) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg shadow-md border-l-4 border-yellow-500">
            <p class="text-sm text-gray-500 uppercase font-bold">Credit Sales</p>
            <p class="text-2xl font-bold text-gray-800">₹{{ number_format($dailyBills->where('payment_mode', 'credit')->sum('amount'), 2) }}</p>
        </div>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Route</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">GST</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($dailyBills as $bill)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $bill->customer->name ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bill->customer->route ?? '—' }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-bold">₹{{ number_format($bill->amount, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₹{{ number_format($bill->gst_amount, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 uppercase">
                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $bill->payment_mode == 'cash' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $bill->payment_mode }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $bill->date->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i>
                            <p>No sales records found for this date.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
