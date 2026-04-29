@extends('layouts.app')
@section('title', 'Dealer Details')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <a href="{{ route('masters.dealers.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Dealers</a>
        <h1 class="text-2xl font-bold text-gray-900">{{ $dealer->firm_name }}</h1>
        <p class="text-sm text-gray-500 mt-0.5">Dealer Master Record | {{ $dealer->location ?? 'Location not set' }}</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('masters.dealers.edit', $dealer) }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-bold text-gray-700 hover:bg-gray-50 shadow-sm transition-all">Edit Dealer</a>
        <form action="{{ route('masters.dealers.destroy', $dealer) }}" method="POST" onsubmit="return confirm('Delete this dealer record?')">
            @csrf @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-50 text-red-600 border border-red-100 rounded-lg text-sm font-bold hover:bg-red-100 transition-all">Delete</button>
        </form>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Info --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 space-y-4">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-50 pb-2">Firm Details</h3>
            <div class="space-y-3">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Contact Person</p>
                    <p class="text-sm font-semibold text-gray-900">{{ $dealer->contact_person ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Phone</p>
                    <p class="text-sm font-semibold text-emerald-600">{{ $dealer->phone }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">Route / Area</p>
                    <p class="text-sm text-gray-900">{{ $dealer->route ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase">GSTIN</p>
                    <p class="text-sm font-mono text-gray-700">{{ $dealer->gst_number ?: 'Unregistered' }}</p>
                </div>
            </div>
        </div>

        <div class="bg-amber-500 rounded-xl shadow-lg p-6 text-white text-center">
            <p class="text-[10px] font-bold uppercase tracking-widest opacity-80 mb-1">Due Amount</p>
            <h2 class="text-3xl font-black">₹{{ number_format($dealer->pending_amount, 2) }}</h2>
            <div class="mt-4 pt-4 border-t border-white/10 flex justify-center gap-2">
                <a href="{{ route('payments.dealers.create', ['dealer_id' => $dealer->id]) }}" class="px-4 py-1.5 bg-white text-amber-700 text-xs font-bold rounded-lg shadow-sm hover:shadow-md transition-all">Make Payment</a>
            </div>
        </div>
    </div>

    {{-- Tabs & Activity --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex border-b border-gray-100 bg-gray-50/30">
                <a href="{{ route('masters.dealers.show', $dealer) }}" class="px-6 py-4 text-sm font-bold text-emerald-600 border-b-2 border-emerald-600">Overview</a>
                <a href="{{ route('masters.dealers.purchase-history', $dealer) }}" class="px-6 py-4 text-sm font-semibold text-gray-500 hover:text-gray-900">Purchase Orders</a>
                <a href="{{ route('payments.dealers.ledger', $dealer) }}" class="px-6 py-4 text-sm font-semibold text-gray-500 hover:text-gray-900">Payment Ledger</a>
                <a href="{{ route('masters.dealers.outstanding-report', $dealer) }}" class="px-6 py-4 text-sm font-semibold text-gray-500 hover:text-gray-900">Outstanding Report</a>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Recent Purchases</h4>
                        <div class="space-y-3">
                            @forelse($dealer->purchases()->latest()->take(3)->get() as $purchase)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <div>
                                        <p class="text-xs font-bold text-gray-900">{{ $purchase->item }}</p>
                                        <p class="text-[10px] text-gray-500">{{ $purchase->date->format('d M Y') }}</p>
                                    </div>
                                    <p class="text-sm font-bold text-gray-900">₹{{ number_format($purchase->total_amount, 0) }}</p>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 italic">No recent purchases.</p>
                            @endforelse
                        </div>
                    </div>

                    <div class="space-y-4">
                        <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Recent Payments</h4>
                        <div class="space-y-3">
                            @forelse($dealer->payments()->latest()->take(3)->get() as $payment)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded-lg border border-gray-100">
                                    <div>
                                        <p class="text-xs font-bold text-gray-900">Payment - {{ $payment->payment_mode }}</p>
                                        <p class="text-[10px] text-gray-500">{{ $payment->date->format('d M Y') }}</p>
                                    </div>
                                    <p class="text-sm font-bold text-emerald-600">₹{{ number_format($payment->amount, 0) }}</p>
                                </div>
                            @empty
                                <p class="text-xs text-gray-400 italic">No recent payments.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
