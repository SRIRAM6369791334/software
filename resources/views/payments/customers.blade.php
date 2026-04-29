@extends('layouts.app')
@section('title', 'Customer Payments')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Customer Payments</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Audit trail and record management for customer receivables</p>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="primary" size="md" onclick="toggleModal('add-payment-modal')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" /></x-slot>
                Record Payment
            </x-button>
            <x-button variant="secondary" size="md" href="{{ route('payments.customers.export') }}">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></x-slot>
                Export CSV
            </x-button>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="max-w-md">
        <form method="GET" class="relative group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <svg class="w-5 h-5 text-slate-400 group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            </div>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by customer identity..." 
                   class="w-full bg-white border-slate-200 rounded-[1.5rem] py-3.5 pl-12 pr-4 text-sm font-bold text-slate-700 shadow-sm focus:ring-4 focus:ring-primary-500/10 transition-all outline-none border">
        </form>
    </div>

    <!-- Payment History -->
    <x-card padding="false">
        <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
            <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Transaction Ledger</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-100 bg-slate-50/20">
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Customer Entity</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Post Date</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Inflow Amount</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center">Channel</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center">Allocation</th>
                        <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Post-Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($payments as $p)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6 font-extrabold text-slate-900">
                                {{ $p->customer->name ?? '—' }}
                            </td>
                            <td class="px-8 py-6 text-slate-500 font-bold text-xs uppercase tracking-widest">
                                {{ $p->date->format('d M, Y') }}
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="text-base font-black text-emerald-600">₹{{ number_format($p->amount, 0) }}</p>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[10px] rounded-full font-black uppercase tracking-wider">{{ $p->payment_mode }}</span>
                            </td>
                            <td class="px-8 py-6 text-center">
                                @php
                                    $variants = ['Full' => 'success', 'Part' => 'primary', 'Advance' => 'slate'];
                                    $variant = $variants[$p->payment_type] ?? 'slate';
                                @endphp
                                <x-badge :variant="$variant">{{ $p->payment_type }}</x-badge>
                            </td>
                            <td class="px-8 py-6 text-right font-mono font-bold text-slate-400 text-xs">
                                {{ $p->balance_after > 0 ? '₹'.number_format($p->balance_after, 0) : 'CLEARED' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-12 text-center text-slate-400 font-medium italic">No recorded payments found</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
            <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/30">
                {{ $payments->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</div>

<!-- Add Payment Modal -->
<div id="add-payment-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xl animate-in fade-in duration-300">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg border border-white/20 overflow-hidden transform animate-in zoom-in-95 duration-300">
        <div class="flex items-center justify-between px-10 py-8 border-b border-slate-100 bg-slate-50/50">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Record Inflow</h2>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Customer Payment Entry</p>
            </div>
            <button onclick="toggleModal('add-payment-modal')" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors shadow-sm">✕</button>
        </div>
        <form action="{{ route('payments.customers.store') }}" method="POST" class="p-10 space-y-6">
            @csrf
            <div class="space-y-2">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-1">Customer Asset *</label>
                <select name="customer_id" required class="w-full bg-slate-50 border-slate-200 rounded-2xl py-3 px-5 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all">
                    <option value="">Select customer identity…</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}
                            @if($c->balance > 0) (Owed: ₹{{ number_format($c->balance, 0) }}) @endif
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-6">
                <x-input label="Inflow Amount (₹) *" type="number" name="amount" required step="0.01" min="0.01" placeholder="0.00" />
                <x-input label="Payment Date *" type="date" name="date" required value="{{ date('Y-m-d') }}" />
            </div>

            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-1">Payment Mode *</label>
                    <select name="payment_mode" required class="w-full bg-slate-50 border-slate-200 rounded-2xl py-3 px-5 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all">
                        @foreach(['Cash','UPI','NEFT','Cheque'] as $m)<option value="{{ $m }}">{{ $m }}</option>@endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-1">Allocation Type *</label>
                    <select name="payment_type" required class="w-full bg-slate-50 border-slate-200 rounded-2xl py-3 px-5 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all">
                        @foreach(['Full','Part','Advance'] as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
                    </select>
                </div>
            </div>

            <x-input label="Notes & Reference" name="notes" placeholder="Transaction details or receipt ID" />

            <div class="pt-4 flex gap-4">
                <x-button variant="ghost" class="flex-1" type="button" onclick="toggleModal('add-payment-modal')">Discard</x-button>
                <x-button variant="primary" class="flex-1" type="submit">Finalize Entry</x-button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleModal(id) {
    const modal = document.getElementById(id);
    modal.classList.toggle('hidden');
}
</script>
@endpush
