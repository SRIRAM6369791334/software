@extends('layouts.app')
@section('title', 'Financial Comparison')
@section('content')
<div class="mb-6">
    <a href="{{ route('profit.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Overview</a>
    <h1 class="text-2xl font-bold text-gray-900">Year-on-Year Comparison</h1>
    <p class="text-sm text-gray-500 mt-0.5">Comparative financial metrics against previous fiscal periods</p>
</div>
<div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-8 text-center">
    <div class="text-4xl mb-4">📈</div>
    <h3 class="text-lg font-bold text-emerald-900">Comparative Analytics Ready Soon</h3>
    <p class="text-emerald-700 max-w-md mx-auto">Historical data synchronization is in progress. This view will soon provide multi-period growth insights.</p>
</div>
@endsection
