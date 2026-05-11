@extends('layouts.app')
@section('title', 'Dealer Payments')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h1 class="text-3xl font-black text-slate-950 tracking-tight">Dealer Payouts</h1>
        <p class="text-slate-500 font-medium">Track payments made to suppliers and feed dealers</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <button onclick="document.getElementById('add-payment-modal').classList.remove('hidden')"
                class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-sky-500 text-white text-sm font-black rounded-xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-100 active:scale-95">
            + Record Payout 
        </button>
        <a href="{{ route('payments.dealers.export') }}" class="px-6 py-4 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 text-slate-400 hover:text-slate-950 text-sm font-bold rounded-xl transition-all">
            ⬇ Export CSV
        </a>
    </div>
</div>

{{-- Financial Summary --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-2xl border border-slate-200 shadow-md shadow-slate-200/60 flex items-center gap-6 group hover:border-indigo-200 transition-all">
        <div class="w-14 h-14 bg-indigo-50 text-primary rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform"></div>
        <div>
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Paid Out</h3>
            <p class="text-2xl font-black text-slate-950">Rs {{ number_format($payments->sum('amount'), 0) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-2xl border border-slate-200 shadow-md shadow-slate-200/60 flex items-center gap-6 group hover:border-red-200 transition-all">
        <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform"></div>
        <div>
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Payable to Dealers</h3>
            <p class="text-2xl font-black text-slate-950">Rs {{ number_format($dealers->sum('pending_amount'), 0) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 p-6 rounded-2xl shadow-md shadow-slate-200/60 text-white flex items-center gap-6">
        <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center text-2xl"></div>
        <div>
            <h3 class="text-[10px] font-black text-indigo-200 uppercase tracking-widest mb-1">Active Suppliers</h3>
            <p class="text-2xl font-black text-white">{{ $dealers->where('pending_amount', '>', 0)->count() }} with Balances</p>
        </div>
    </div>
</div>

{{-- Payouts Table --}}
<div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-2xl border border-slate-200 shadow-lg overflow-hidden mb-12">
    <div class="p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gradient-to-r from-emerald-50/80 to-sky-50/80">
        <form method="GET" class="relative w-full max-w-md">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search dealer or reference..."
                   class="w-full pl-12 pr-4 py-4 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all font-medium text-sm">
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-gradient-to-r from-emerald-50/80 to-sky-50/80 text-slate-400 font-black uppercase text-[10px] tracking-widest border-b border-slate-200">
                    <th class="px-8 py-5">Dealer / Firm</th>
                    <th class="px-8 py-5">Payout Date</th>
                    <th class="px-8 py-5 text-right">Amount Paid</th>
                    <th class="px-8 py-5 text-center">Payment Mode</th>
                    <th class="px-8 py-5 text-right">Balance After</th>
                    <th class="px-8 py-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($payments as $p)
                    <tr class="hover:bg-indigo-50/30 transition-all group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center font-black text-slate-500 group-hover:bg-indigo-100 group-hover:text-primary transition-all">
                                    {{ substr($p->dealer->firm_name ?? '?', 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-black text-slate-950 tracking-tight">{{ $p->dealer->firm_name ?? '-' }}</span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ $p->dealer->contact_person ?? 'NO CONTACT' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="font-black text-slate-950 leading-tight">{{ $p->date->format('d M, Y') }}</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ $p->date->format('l') }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <span class="text-lg font-black text-red-600">Rs {{ number_format($p->amount, 0) }}</span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="px-3 py-1 bg-sky-50 text-slate-600 text-[10px] font-black rounded-lg uppercase tracking-widest">
                                {{ $p->payment_mode }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right font-mono text-slate-400 font-bold">
                            Rs {{ number_format($p->pending_balance_after, 0) }}
                        </td>
                        <td class="px-8 py-5 text-center">
                            <a href="{{ route('payments.dealers.ledger', $p->dealer_id) }}" 
                               class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-xl text-slate-400 hover:text-primary hover:border-indigo-200 hover:shadow-lg transition-all font-black text-[9px] uppercase tracking-widest">
                                Ledger 
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="text-6xl mb-6"></div>
                                <h3 class="text-xl font-black text-slate-950">No Payouts Recorded</h3>
                                <p class="text-slate-400 font-medium mt-1">Ready to clear your dealer balances?</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payments->hasPages())
        <div class="p-8 border-t border-slate-100 bg-gradient-to-r from-emerald-50/70 to-sky-50/70">
            {{ $payments->withQueryString()->links() }}
        </div>
    @endif
</div>

{{-- Record Payout Modal --}}
<div id="add-payment-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200/40 backdrop-blur-md transition-all">
    <div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-3xl shadow-lg w-full max-w-2xl border border-white/20 overflow-hidden transform transition-all scale-100">
        <div class="flex items-center justify-between px-10 py-8 border-b border-slate-100 bg-gradient-to-r from-emerald-50/70 to-sky-50/70">
            <div>
                <h2 class="text-2xl font-black text-slate-950 tracking-tight uppercase tracking-widest">Record Payout </h2>
                <p class="text-sm text-slate-500 font-medium">Enter payment made to clear supplier dues</p>
            </div>
            <button onclick="document.getElementById('add-payment-modal').classList.add('hidden')" 
                    class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 text-slate-400 hover:text-red-500 transition-all shadow-sm">x</button>
        </div>

        <form action="{{ route('payments.dealers.store') }}" method="POST" class="p-10 space-y-8">
            @csrf
            
            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">1. Select Dealer</label>
                <select name="dealer_id" required class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all font-black text-slate-950">
                    <option value="">Choose dealer…</option>
                    @foreach($dealers as $d)
                        <option value="{{ $d->id }}">{{ $d->firm_name }} (Pending: Rs {{ number_format($d->pending_amount, 0) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">2. Amount Paid (Rs )</label>
                    <input type="number" name="amount" required step="0.01" min="0.01" placeholder="0.00"
                           class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all font-black text-red-600 text-xl">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">3. Payment Date</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                           class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all font-black">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">4. Payment Mode</label>
                <div class="grid grid-cols-4 gap-3">
                    @foreach(['NEFT','Cheque','UPI','Cash'] as $mode)
                        <label class="relative flex flex-col items-center p-4 border border-slate-200 rounded-2xl cursor-pointer hover:bg-emerald-50 transition-all has-[:checked]:bg-indigo-50 has-[:checked]:border-indigo-500">
                            <input type="radio" name="payment_mode" value="{{ $mode }}" class="hidden" {{ $loop->first ? 'checked' : '' }}>
                            <span class="text-[10px] font-black text-slate-950">{{ $mode }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">5. Transaction Reference</label>
                <input type="text" name="notes" placeholder="Transaction ID, Cheque #, or reference..." 
                       class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all font-medium">
            </div>

            <div class="flex justify-end gap-6 pt-6">
                <button type="button" onclick="document.getElementById('add-payment-modal').classList.add('hidden')" class="px-8 py-4 text-sm font-black text-slate-400 hover:text-slate-950 transition-colors uppercase tracking-widest">Cancel</button>
                <button type="submit" class="px-12 py-5 bg-gradient-to-r from-emerald-600 to-sky-500 text-white font-black rounded-3xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-100 active:scale-95 transform">
                    Confirm Payout 
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
