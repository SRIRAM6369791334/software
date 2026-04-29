@extends('layouts.app')
@section('title', 'Customer Payments')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Customer Payments</h1>
        <p class="text-sm text-gray-500 mt-0.5">Record and view customer payment history</p>
    </div>
    <div class="flex gap-2">
        <button onclick="document.getElementById('add-payment-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
            + Add Payment
        </button>
        <a href="{{ route('payments.customers.export') }}" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-sm font-medium rounded-lg transition-colors">⬇ CSV</a>
    </div>
</div>

<form method="GET" class="mb-4 max-w-sm">
    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
        <input type="text" name="search" value="{{ $search }}" placeholder="Search by customer..."
               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
    </div>
</form>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Customer</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Date</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">Amount</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-400 uppercase">Mode</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-400 uppercase">Type</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase hidden md:table-cell">Balance After</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payments as $p)
                    @php $typeColors = ['Full'=>'bg-emerald-100 text-emerald-700','Part'=>'bg-amber-100 text-amber-700','Advance'=>'bg-blue-100 text-blue-700']; @endphp
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3.5 font-medium text-gray-900">{{ $p->customer->name ?? '—' }}</td>
                        <td class="px-5 py-3.5 text-gray-500">{{ $p->date->format('d M Y') }}</td>
                        <td class="px-5 py-3.5 text-right font-mono font-semibold text-emerald-700">₹{{ number_format($p->amount, 0, '.', ',') }}</td>
                        <td class="px-5 py-3.5 text-center"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">{{ $p->payment_mode }}</span></td>
                        <td class="px-5 py-3.5 text-center"><span class="px-2 py-0.5 text-xs rounded-full font-medium {{ $typeColors[$p->payment_type] ?? 'bg-gray-100 text-gray-600' }}">{{ $p->payment_type }}</span></td>
                        <td class="px-5 py-3.5 text-right font-mono text-gray-500 hidden md:table-cell">{{ $p->balance_after > 0 ? '₹'.number_format($p->balance_after, 0, '.', ',') : '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">No payments recorded</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $payments->withQueryString()->links() }}</div>
</div>

<div id="add-payment-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-base font-semibold text-gray-900">Add Payment</h2>
            <button onclick="document.getElementById('add-payment-modal').classList.add('hidden')" class="text-gray-400 text-xl">✕</button>
        </div>
        <form action="{{ route('payments.customers.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Customer *</label>
                <select name="customer_id" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                    <option value="">Select customer…</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}
                            @if($c->balance > 0) (Balance: ₹{{ number_format($c->balance,0) }}) @endif
                        </option>
                    @endforeach
                </select></div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Amount (₹) *</label>
                    <input type="number" name="amount" required step="0.01" min="0.01" placeholder="0.00"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Date *</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Payment Mode *</label>
                    <select name="payment_mode" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        @foreach(['Cash','UPI','NEFT','Cheque'] as $m)<option value="{{ $m }}">{{ $m }}</option>@endforeach
                    </select></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Payment Type *</label>
                    <select name="payment_type" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        @foreach(['Full','Part','Advance'] as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
                    </select></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea></div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('add-payment-modal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Record Payment</button>
            </div>
        </form>
    </div>
</div>
@endsection
