@extends('layouts.app')
@section('title', 'Order-wise Profitability')
@section('content')
<div class="mb-6">
    <a href="{{ route('profit.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Overview</a>
    <h1 class="text-2xl font-bold text-gray-900">Order-wise Profit Tracking</h1>
    <p class="text-sm text-gray-500 mt-0.5">Individual profitability metrics for every customer order</p>
</div>
<div class="bg-blue-50 border border-blue-200 rounded-2xl p-8 text-center">
    <div class="text-4xl mb-4">🔍</div>
    <h3 class="text-lg font-bold text-blue-900">Coming Soon: Transactional Deep-dive</h3>
    <p class="text-blue-700 max-w-md mx-auto">We are refining the order-level ledger integration to provide precise margin calculations for every sale.</p>
</div>
@endsection
