@extends('layouts.app')
@section('title', 'Expense Categories')

@section('content')
<div class="mb-6">
    <a href="{{ route('expenses.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Expenses</a>
    <h1 class="text-2xl font-bold text-gray-900">Expense Analysis by Category</h1>
    <p class="text-sm text-gray-500 mt-0.5">Classification of business expenditures</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    @forelse($categories as $cat)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 hover:shadow-md transition-shadow group">
            <div class="flex justify-between items-start mb-4">
                <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-black group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                    {{ substr($cat->category, 0, 1) }}
                </div>
                <div class="text-right">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Total Spent</p>
                    <p class="text-lg font-black text-gray-900">₹{{ number_format($cat->total, 2) }}</p>
                </div>
            </div>
            <h3 class="text-base font-bold text-gray-800 mb-1 capitalize">{{ $cat->category }}</h3>
            <p class="text-xs text-gray-500 mb-6">{{ $cat->count }} transactions recorded</p>
            
            <div class="flex gap-2">
                <a href="{{ route('expenses.index', ['category' => $cat->category]) }}" class="text-[10px] font-bold text-emerald-600 uppercase tracking-tight hover:underline">View Logs →</a>
            </div>
        </div>
    @empty
        <div class="col-span-full py-20 text-center bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
            <p class="text-gray-400 italic">No expense categories detected yet.</p>
        </div>
    @endforelse
</div>
@endsection
