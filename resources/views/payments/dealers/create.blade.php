@extends('layouts.app')
@section('title', 'Record Dealer Payment')

@section('content')
<div class="mb-6">
    <a href="{{ route('payments.dealers.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Payments</a>
    <h1 class="text-2xl font-bold text-gray-900">Record Dealer Payment</h1>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('payments.dealers.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div class="space-y-1.5 flex flex-col">
                <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Select Dealer <span class="text-red-500">*</span></label>
                <select name="dealer_id" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    <option value="">Choose a dealer...</option>
                    @foreach($dealers as $dealer)
                        <option value="{{ $dealer->id }}" {{ $selected_dealer_id == $dealer->id ? 'selected' : '' }}>
                            {{ $dealer->firm_name }} (Outstanding: ₹{{ number_format($dealer->pending_amount, 2) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5 flex flex-col">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Payment Date <span class="text-red-500">*</span></label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="space-y-1.5 flex flex-col">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Amount Paid (₹) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" step="0.01" required class="w-full px-4 py-2 bg-amber-50 border border-amber-200 text-amber-900 font-bold rounded-lg focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all" placeholder="0.00">
                </div>
            </div>

            <div class="space-y-1.5 flex flex-col">
                <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Payment Mode</label>
                <select name="payment_mode" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    @foreach(['Cash', 'UPI', 'NEFT', 'Cheque'] as $mode)
                        <option value="{{ $mode }}">{{ $mode }}</option>
                    @endforeach
                </select>
            </div>

            <div class="space-y-1.5 flex flex-col">
                <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Notes / Reference</label>
                <textarea name="notes" rows="2" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="Transaction ID, Cheque No, etc."></textarea>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <button type="submit" class="px-8 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-sm transition-all active:scale-95">
                    Save Dealer Payment 🧾
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
