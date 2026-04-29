@extends('layouts.app')
@section('title', 'EMI Alerts')

@section('content')
<div class="mb-6">
    <a href="{{ route('expenses.emis.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to EMIs</a>
    <h1 class="text-2xl font-bold text-gray-900">EMI Early Warning Alerts</h1>
    <p class="text-sm text-gray-500 mt-0.5">Repayments due within the next 7 days</p>
</div>

<div class="space-y-4 max-w-3xl">
    @forelse($upcomingEmis as $emi)
        <div class="bg-white rounded-2xl border-l-8 border-amber-400 p-6 shadow-sm flex items-center justify-between group hover:border-emerald-500 transition-all">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center text-xl group-hover:bg-emerald-50 group-hover:text-emerald-600 transition-colors">
                    💸
                </div>
                <div>
                    <h3 class="text-base font-black text-gray-900">{{ $emi->loan_name }}</h3>
                    <p class="text-xs text-gray-500 border-l-2 border-amber-200 pl-2">System Recorded Installment</p>
                </div>
            </div>
            
            <div class="text-right flex items-center gap-8">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Amount Due</p>
                    <p class="text-lg font-black text-emerald-900">₹{{ number_format($emi->amount, 2) }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Deadline</p>
                    <p class="text-sm font-black text-red-600">{{ $emi->due_date->format('d M (D)') }}</p>
                </div>
                <div class="no-print">
                    <button class="px-4 py-2 bg-gray-900 text-white text-xs font-bold rounded-lg hover:bg-emerald-600 transition-all">Pay Now</button>
                </div>
            </div>
        </div>
    @empty
        <div class="py-20 text-center bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
            <div class="text-4xl mb-4">✅</div>
            <h3 class="text-lg font-bold text-gray-900">All caught up!</h3>
            <p class="text-gray-400">No EMI payments due within the next 7 days.</p>
        </div>
    @endforelse
</div>
@endsection
