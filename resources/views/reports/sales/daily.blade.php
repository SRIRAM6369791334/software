@extends('layouts.app')

@section('title', 'Daily Sales Report')

@section('content')
<div class="mb-8 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Daily Sales Report</h1>
        <p class="text-gray-500 font-medium">Sales summary for {{ $date }}.</p>
    </div>
    <a href="{{ route('reports.sales.export-pdf', ['date' => $date]) }}" class="px-4 py-2 rounded-xl bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700">
        Export PDF
    </a>
</div>

<div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm mb-6">
    <form action="{{ route('reports.sales.daily') }}" method="GET" class="flex flex-col gap-3 md:flex-row md:items-end">
        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Date</label>
            <input type="date" name="date" value="{{ $date }}" class="rounded-lg border border-gray-200 px-3 py-2">
        </div>
        <button type="submit" class="px-4 py-2 rounded-lg bg-gray-900 text-white text-sm font-bold">Apply</button>
    </form>
</div>

<div class="grid grid-cols-1 sm:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm"><p class="text-xs text-gray-500 uppercase font-bold">Total Sale</p><p class="text-2xl font-black">₹{{ number_format($totalSale, 2) }}</p></div>
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm"><p class="text-xs text-gray-500 uppercase font-bold">GST</p><p class="text-2xl font-black">₹{{ number_format($totalGST, 2) }}</p></div>
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm"><p class="text-xs text-gray-500 uppercase font-bold">Cash</p><p class="text-2xl font-black">₹{{ number_format($cashSales, 2) }}</p></div>
    <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm"><p class="text-xs text-gray-500 uppercase font-bold">Credit</p><p class="text-2xl font-black">₹{{ number_format($creditSales, 2) }}</p></div>
</div>

<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
            <tr>
                <th class="px-5 py-3 text-left">Invoice</th>
                <th class="px-5 py-3 text-left">Customer</th>
                <th class="px-5 py-3 text-left">Payment</th>
                <th class="px-5 py-3 text-right">Net Amount</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($dailyBills as $bill)
                <tr>
                    <td class="px-5 py-4 font-semibold">{{ $bill->invoice_number }}</td>
                    <td class="px-5 py-4">{{ $bill->customer->name ?? 'N/A' }}</td>
                    <td class="px-5 py-4">{{ ucfirst($bill->payment_mode) }}</td>
                    <td class="px-5 py-4 text-right font-bold">₹{{ number_format($bill->net_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4" class="px-5 py-12 text-center text-gray-500">No sales records found for this date.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
