@extends('layouts.app')
@section('title', 'Daily Billing')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Daily Billing</h1>
            <p class="text-sm text-slate-500 font-medium mt-1 uppercase tracking-widest italic">Sales Registry & Settlement</p>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="ghost" size="md" class="hidden md:flex">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></x-slot>
                Export PDF
            </x-button>
            <x-button variant="primary" size="md" onclick="toggleModal('add-bill-modal')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></x-slot>
                Record Sale
            </x-button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-card class="relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Total Daily Revenue</p>
            <h3 class="text-2xl font-black text-slate-900 tracking-tighter">₹{{ number_format($bills->sum('amount'), 0) }}</h3>
        </x-card>
        <x-card class="relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Stock Outflow</p>
            <h3 class="text-2xl font-black text-slate-900 tracking-tighter">{{ number_format($bills->sum('quantity_kg'), 0) }} <span class="text-xs text-slate-400">KG</span></h3>
        </x-card>
        <x-card class="relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Invoice Count</p>
            <h3 class="text-2xl font-black text-slate-900 tracking-tighter">{{ $bills->total() }} <span class="text-xs text-slate-400">Entries</span></h3>
        </x-card>
    </div>

    <!-- Filters & Search -->
    <x-card padding="false">
        <div class="p-6">
            <form method="GET" class="relative group max-w-2xl">
                <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400 group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Filter by customer name, items, or status..." 
                       class="w-full bg-slate-50 border-slate-200 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 rounded-2xl py-4 pl-12 pr-6 text-sm font-bold text-slate-700 transition-all outline-none border">
            </form>
        </div>
    </x-card>

    <!-- Table Section -->
    <x-card padding="false" class="overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                    <tr class="text-left border-b border-slate-100 bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Transaction ID</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Customer Entity</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Yield Metrics</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Invoice Value</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Settlement</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($bills as $bill)
                        <tr class="hover:bg-slate-50/80 transition-all group">
                            <td class="px-8 py-6">
                                <div class="space-y-1">
                                    <p class="font-black text-slate-900 group-hover:text-primary-600 transition-colors">#INV-{{ str_pad($bill->id, 5, '0', STR_PAD_LEFT) }}</p>
                                    <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        {{ $bill->date->format('d M, Y') }}
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-slate-900 flex items-center justify-center text-[10px] font-black text-primary-500 border border-slate-700 shadow-lg group-hover:rotate-6 transition-transform">
                                        {{ substr($bill->customer->name ?? '?', 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-black text-slate-800 leading-none">{{ $bill->customer->name ?? '—' }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1.5">{{ Str::limit($bill->items_description, 20) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="space-y-1.5">
                                    <p class="text-slate-900 font-black text-base">{{ number_format($bill->quantity_kg, 2) }} <span class="text-[10px] text-slate-400">KG</span></p>
                                    <p class="inline-block px-2 py-0.5 bg-primary-50 text-primary-600 text-[10px] font-black rounded-lg uppercase tracking-widest">₹{{ number_format($bill->rate_per_kg, 2) }} / unit</p>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="font-black text-slate-900 text-lg tracking-tight">₹{{ number_format($bill->amount, 0) }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex justify-center">
                                    @php
                                        $variant = ['Generated' => 'primary', 'Pending' => 'amber', 'Paid' => 'success'][$bill->status] ?? 'slate';
                                    @endphp
                                    <x-badge :variant="$variant" class="px-4 py-1 rounded-full text-[10px]">{{ $bill->status }}</x-badge>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <x-button variant="ghost" size="sm" href="{{ route('billing.daily.gst', $bill->id) }}" title="View Tax Invoice">
                                        <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></x-slot>
                                    </x-button>
                                    <form action="{{ route('billing.daily.destroy', $bill->id) }}" method="POST" onsubmit="return confirm('Archive this transaction?')">
                                        @csrf @method('DELETE')
                                        <x-button variant="ghost" size="sm" type="submit" class="text-rose-500 hover:bg-rose-50 hover:text-rose-600">
                                            <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></x-slot>
                                        </x-button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-32 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 rounded-[2rem] bg-slate-50 flex items-center justify-center text-slate-200 mb-6">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <p class="text-xl font-black text-slate-900 tracking-tight">No Transactions Recorded</p>
                                    <p class="text-sm font-bold text-slate-400 mt-2">Initialize your daily billing by recording your first sale.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($bills->hasPages())
            <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/30">
                {{ $bills->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</div>

<!-- Add Bill Modal -->
<div id="add-bill-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-md animate-in fade-in duration-300">
    <div class="bg-white rounded-[3rem] shadow-3xl w-full max-w-2xl border border-slate-100 overflow-hidden transform animate-in zoom-in-95 duration-300">
        <div class="flex items-center justify-between px-12 py-10 border-b border-slate-50 bg-slate-50/30">
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Record Daily Sale</h2>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.3em] mt-2">Financial Inflow Asset</p>
            </div>
            <button onclick="toggleModal('add-bill-modal')" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-all shadow-sm hover:rotate-90">✕</button>
        </div>
        
        <form action="{{ route('billing.daily.store') }}" method="POST" class="p-12 space-y-10">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest px-1 italic">Client / Customer *</label>
                    <select name="customer_id" required class="w-full bg-slate-50 border-slate-200 rounded-[1.5rem] py-4 px-6 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all border appearance-none">
                        <option value="">Select entity…</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
                <x-input label="Transaction Date *" type="date" name="date" required :value="date('Y-m-d')" />
            </div>

            <div class="bg-slate-900 rounded-[2.5rem] p-10 space-y-8 relative overflow-hidden">
                <div class="absolute top-0 right-0 p-6 opacity-10">
                    <svg class="w-16 h-16 text-primary-500" fill="currentColor" viewBox="0 0 20 20"><path d="M11 3a1 1 0 10-2 0v1a1 1 0 102 0V3zM15.657 5.757a1 1 0 00-1.414-1.414l-.707.707a1 1 0 001.414 1.414l.707-.707zM18 10a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1zM5.05 6.464A1 1 0 106.464 5.05l-.707-.707a1 1 0 00-1.414 1.414l.707.707zM5 10a1 1 0 01-1 1H3a1 1 0 110-2h1a1 1 0 011 1zM8 16v-1a1 1 0 112 0v1a1 1 0 11-2 0zM13.464 15.05a1 1 0 010 1.414l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 14a1 1 0 01-1 1h-1a1 1 0 110-2h1a1 1 0 011 1z" /></svg>
                </div>
                
                <div class="grid grid-cols-2 gap-8 relative z-10">
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Net Quantity (kg)</label>
                        <input type="number" name="quantity_kg" id="daily-qty" step="0.01" oninput="recalcDaily()" placeholder="0.00" 
                               class="w-full bg-slate-800 border-slate-700 rounded-2xl py-4 px-6 text-xl font-black text-white outline-none focus:ring-4 focus:ring-primary-500/20 transition-all border">
                    </div>
                    <div class="space-y-3">
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">Market Rate (₹/kg)</label>
                        <input type="number" name="rate_per_kg" id="daily-rate" step="0.01" oninput="recalcDaily()" placeholder="0.00" 
                               class="w-full bg-slate-800 border-slate-700 rounded-2xl py-4 px-6 text-xl font-black text-white outline-none focus:ring-4 focus:ring-primary-500/20 transition-all border">
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-800 relative z-10">
                    <p class="text-[10px] font-black text-primary-500 uppercase tracking-[0.3em] mb-4 text-center">Settlement Amount</p>
                    <div class="flex items-center justify-center gap-3">
                        <span class="text-2xl font-black text-slate-500 italic">₹</span>
                        <input type="number" name="amount" id="daily-amount" required step="0.01" readonly 
                               class="bg-transparent border-none p-0 text-5xl font-black text-white w-full text-center focus:ring-0 tracking-tighter">
                    </div>
                </div>
            </div>

            <x-input label="Brief Item Specification" name="items_description" placeholder="e.g. Premium Broiler Birds (Grade A)" />

            <div class="space-y-2">
                <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest px-1 italic">Settlement Progress</label>
                <div class="grid grid-cols-3 gap-4">
                    @foreach(['Generated', 'Pending', 'Paid'] as $status)
                        <label class="relative flex items-center justify-center p-4 rounded-2xl border-2 cursor-pointer transition-all border-slate-100 hover:bg-slate-50 peer-checked:border-primary-500 has-[:checked]:border-primary-500 has-[:checked]:bg-primary-50/50">
                            <input type="radio" name="status" value="{{ $status }}" class="sr-only" {{ $loop->first ? 'checked' : '' }}>
                            <span class="text-xs font-black uppercase tracking-widest {{ $status == 'Paid' ? 'text-emerald-600' : ($status == 'Pending' ? 'text-amber-600' : 'text-primary-600') }}">
                                {{ $status }}
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="pt-6 flex flex-col md:flex-row gap-6">
                <x-button variant="ghost" class="flex-1 py-5 rounded-[2rem] text-slate-400 hover:text-rose-500" type="button" onclick="toggleModal('add-bill-modal')">Discard Entry</x-button>
                <x-button variant="primary" class="flex-[2] py-5 rounded-[2rem] shadow-primary-500/20" type="submit">
                    <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></x-slot>
                    Post Transaction
                </x-button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleModal(id) {
    const modal = document.getElementById(id);
    if(modal.classList.contains('hidden')) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    } else {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

function recalcDaily() {
    const qty = parseFloat(document.getElementById('daily-qty').value) || 0;
    const rate = parseFloat(document.getElementById('daily-rate').value) || 0;
    const amountInput = document.getElementById('daily-amount');
    
    if (qty && rate) {
        amountInput.value = (qty * rate).toFixed(2);
        amountInput.classList.add('text-primary-400');
        setTimeout(() => amountInput.classList.remove('text-primary-400'), 300);
    } else {
        amountInput.value = '0.00';
    }
}
</script>
@endpush
