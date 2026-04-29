@extends('layouts.app')
@section('title', 'Outstanding Report - ' . $dealer->firm_name)

@section('content')
<div class="mb-6">
    <a href="{{ route('masters.dealers.show', $dealer) }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Dealer Details</a>
    <h1 class="text-2xl font-bold text-gray-900">Outstanding Report</h1>
    <p class="text-sm text-gray-500 mt-0.5">{{ $dealer->firm_name }} | Reconciliation summary</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/30">
                <h3 class="text-sm font-bold text-gray-900 uppercase tracking-tight">Financial Summary</h3>
            </div>
            <div class="p-6 grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                    <p class="text-[10px] font-bold text-emerald-600 uppercase mb-1">Total Purchased</p>
                    <p class="text-xl font-black text-emerald-900">₹{{ number_format($dealer->purchases()->sum('total_amount'), 2) }}</p>
                </div>
                <div class="p-4 bg-blue-50 rounded-xl border border-blue-100">
                    <p class="text-[10px] font-bold text-blue-600 uppercase mb-1">Total Paid</p>
                    <p class="text-xl font-black text-blue-900">₹{{ number_format($dealer->payments()->sum('amount'), 2) }}</p>
                </div>
                <div class="p-4 bg-amber-50 rounded-xl border border-amber-100">
                    <p class="text-[10px] font-bold text-amber-600 uppercase mb-1">Current Outstanding</p>
                    <p class="text-xl font-black text-amber-900">₹{{ number_format($dealer->pending_amount, 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden p-6">
            <h4 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-tight">Aging Analysis (Conceptual)</h4>
            <div class="space-y-4">
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">0 - 30 Days</span>
                    <span class="font-bold text-gray-900">₹{{ number_format($dealer->pending_amount * 0.7, 0) }}</span>
                </div>
                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-emerald-500 h-full" style="width: 70%"></div>
                </div>
                
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">31 - 60 Days</span>
                    <span class="font-bold text-gray-900">₹{{ number_format($dealer->pending_amount * 0.2, 0) }}</span>
                </div>
                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-amber-500 h-full" style="width: 20%"></div>
                </div>

                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-600">60+ Days</span>
                    <span class="font-bold text-red-600">₹{{ number_format($dealer->pending_amount * 0.1, 0) }}</span>
                </div>
                <div class="w-full bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-red-500 h-full" style="width: 10%"></div>
                </div>
            </div>
        </div>
    </div>

    <div class="space-y-6">
        <div class="bg-gray-900 rounded-xl shadow-xl p-6 text-white">
            <h3 class="text-xs font-bold uppercase tracking-widest text-emerald-400 mb-4">Payment Health</h3>
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-lg">⚡</div>
                    <div>
                        <p class="text-[10px] font-bold uppercase opacity-60">Avg. Payment Days</p>
                        <p class="text-sm font-bold">12 Days</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center text-lg">📈</div>
                    <div>
                        <p class="text-[10px] font-bold uppercase opacity-60">Credit Limit</p>
                        <p class="text-sm font-bold">₹5,00,000</p>
                    </div>
                </div>
            </div>
            <button class="w-full mt-6 py-2 bg-emerald-600 hover:bg-emerald-500 text-xs font-bold rounded-lg transition-colors">Generate Official PDF</button>
        </div>
    </div>
</div>
@endsection
