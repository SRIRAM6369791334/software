@extends('layouts.app')
@section('title', 'Customer Payments')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Customer Collections</h1>
        <p class="text-gray-500 font-medium">Manage inbound payments and customer ledgers</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <button onclick="document.getElementById('add-payment-modal').classList.remove('hidden')"
                class="px-6 py-4 bg-emerald-600 text-white text-sm font-black rounded-[1.5rem] hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-95">
            + Record Collection 💰
        </button>
        <a href="{{ route('payments.customers.export') }}" class="px-6 py-4 bg-white border border-gray-200 text-gray-400 hover:text-gray-900 text-sm font-bold rounded-[1.5rem] transition-all">
            ⬇ Export CSV
        </a>
    </div>
</div>

{{-- Financial Summary --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 flex items-center gap-6 group hover:border-emerald-200 transition-all">
        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">📥</div>
        <div>
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Collected</h3>
            <p class="text-2xl font-black text-gray-900">₹{{ number_format($payments->sum('amount'), 0) }}</p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 flex items-center gap-6 group hover:border-blue-200 transition-all">
        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">📋</div>
        <div>
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Outstanding</h3>
            <p class="text-2xl font-black text-gray-900">₹{{ number_format($customers->sum('balance'), 0) }}</p>
        </div>
    </div>
    <div class="bg-emerald-600 p-6 rounded-[2.5rem] shadow-xl shadow-emerald-600/20 text-white flex items-center gap-6">
        <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-2xl">⚡</div>
        <div>
            <h3 class="text-[10px] font-black text-emerald-200 uppercase tracking-widest mb-1">Recent Collections</h3>
            <p class="text-2xl font-black text-white">{{ $payments->where('date', '>=', now()->subDays(7))->count() }} This Week</p>
        </div>
    </div>
</div>

{{-- Collections Table --}}
<div class="bg-white rounded-[2.5rem] border border-gray-200 shadow-2xl overflow-hidden mb-12">
    <div class="p-8 border-b border-gray-50 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gray-50/50">
        <form method="GET" class="relative w-full max-w-md">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search customer or reference..."
                   class="w-full pl-12 pr-4 py-4 bg-white border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-medium text-sm">
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-gray-50/50 text-gray-400 font-black uppercase text-[10px] tracking-widest border-b border-gray-100">
                    <th class="px-8 py-5">Customer</th>
                    <th class="px-8 py-5">Collection Date</th>
                    <th class="px-8 py-5 text-right">Amount Received</th>
                    <th class="px-8 py-5 text-center">Payment Mode</th>
                    <th class="px-8 py-5 text-center">Receipt Type</th>
                    <th class="px-8 py-5 text-right">Balance After</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($payments as $p)
                    <tr class="hover:bg-emerald-50/30 transition-all group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center font-black text-gray-500 group-hover:bg-emerald-100 group-hover:text-emerald-600 transition-all">
                                    {{ substr($p->customer->name ?? '?', 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-black text-gray-900 tracking-tight">{{ $p->customer->name ?? '—' }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $p->customer->phone ?? 'NO PHONE' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="font-black text-gray-900 leading-tight">{{ $p->date->format('d M, Y') }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">{{ $p->date->format('l') }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <span class="text-lg font-black text-emerald-600">₹{{ number_format($p->amount, 0) }}</span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-[10px] font-black rounded-lg uppercase tracking-widest">
                                {{ $p->payment_mode }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            @php
                                $typeMap = [
                                    'Full' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700'],
                                    'Part' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700'],
                                    'Advance' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700'],
                                ];
                                $t = $typeMap[$p->payment_type] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600'];
                            @endphp
                            <span class="px-3 py-1 {{ $t['bg'] }} {{ $t['text'] }} text-[9px] font-black rounded-lg tracking-tighter">
                                {{ strtoupper($p->payment_type) }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right font-mono text-gray-400 font-bold">
                            {{ $p->balance_after > 0 ? '₹'.number_format($p->balance_after, 0) : 'CLEARED' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="text-6xl mb-6">💰</div>
                                <h3 class="text-xl font-black text-gray-900">No Collections Found</h3>
                                <p class="text-gray-400 font-medium mt-1">Record your first customer payment today.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
        <div class="p-8 border-t border-gray-50 bg-gray-50/30">
            {{ $payments->withQueryString()->links() }}
        </div>
    @endif
</div>

{{-- Record Payment Modal --}}
<div id="add-payment-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/40 backdrop-blur-md transition-all">
    <div class="bg-white rounded-[3.5rem] shadow-2xl w-full max-w-2xl border border-white/20 overflow-hidden transform transition-all scale-100">
        <div class="flex items-center justify-between px-10 py-8 border-b border-gray-50 bg-gray-50/30">
            <div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight uppercase tracking-widest">Record Collection 📥</h2>
                <p class="text-sm text-gray-500 font-medium">Enter payment details to update customer ledger</p>
            </div>
            <button onclick="document.getElementById('add-payment-modal').classList.add('hidden')" 
                    class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-gray-200 text-gray-400 hover:text-red-500 transition-all shadow-sm">✕</button>
        </div>

        <form action="{{ route('payments.customers.store') }}" method="POST" class="p-10 space-y-8">
            @csrf
            
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">1. Select Customer</label>
                <select name="customer_id" required class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-[1.5rem] focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black text-gray-900">
                    <option value="">Choose customer…</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} (Pending: ₹{{ number_format($c->balance, 0) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">2. Amount (₹)</label>
                    <input type="number" name="amount" required step="0.01" min="0.01" placeholder="0.00"
                           class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-[1.5rem] focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black text-emerald-600 text-xl">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">3. Payment Date</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                           class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-[1.5rem] focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">4. Payment Mode</label>
                    <select name="payment_mode" required class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-[1.5rem] focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black">
                        @foreach(['Cash','UPI','NEFT','Cheque'] as $m)<option value="{{ $m }}">{{ $m }}</option>@endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">5. Receipt Type</label>
                    <select name="payment_type" required class="w-full px-6 py-5 bg-gray-50 border border-gray-200 rounded-[1.5rem] focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black">
                        @foreach(['Part','Full','Advance'] as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
                    </select>
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">6. Remarks / Reference</label>
                <textarea name="notes" rows="2" placeholder="e.g. UPI Transaction ID or Cheque Number..." 
                          class="w-full px-6 py-4 bg-gray-50 border border-gray-200 rounded-[1.5rem] focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-medium"></textarea>
            </div>

            <div class="flex justify-end gap-6 pt-6">
                <button type="button" onclick="document.getElementById('add-payment-modal').classList.add('hidden')" class="px-8 py-4 text-sm font-black text-gray-400 hover:text-gray-900 transition-colors uppercase tracking-widest">Cancel</button>
                <button type="submit" class="px-12 py-5 bg-emerald-600 text-white font-black rounded-3xl hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-95 transform">
                    Record Collection 🚀
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
