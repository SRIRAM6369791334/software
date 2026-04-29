@extends('layouts.app')
@section('title', 'Batch-wise Profit Analysis')
@section('content')
<div class="mb-6">
    <a href="{{ route('profit.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Overview</a>
    <h1 class="text-2xl font-bold text-gray-900">Batch Performance Analysis</h1>
    <p class="text-sm text-gray-500 mt-0.5">Detailed profitability tracking per poultry batch</p>
</div>
<div class="bg-amber-50 border border-amber-200 rounded-2xl p-8 text-center">
    <div class="text-4xl mb-4">🏗️</div>
    <h3 class="text-lg font-bold text-amber-900">Module Under Construction</h3>
    <p class="text-amber-700 max-w-md mx-auto">This specialized analytical view is currently being optimized for batch-level data integration. Please check back later.</p>
</div>
@endsection
