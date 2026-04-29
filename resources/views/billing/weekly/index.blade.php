@extends('layouts.app')
@section('title', 'Weekly Billing')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Weekly Billing</h1>
            <p class="text-sm text-slate-500 font-medium mt-1 uppercase tracking-widest italic">Consolidated Invoicing System</p>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="secondary" size="md" onclick="toggleModal('bulk-bill-modal')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></x-slot>
                Bulk Generate
            </x-button>
            <x-button variant="primary" size="md" onclick="toggleModal('add-bill-modal')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></x-slot>
                Single Bill
            </x-button>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <x-card class="relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Weekly Gross Turnover</p>
            <h3 class="text-2xl font-black text-slate-900 tracking-tighter">₹{{ number_format($bills->sum('amount'), 0) }}</h3>
        </x-card>
        <x-card class="relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Consolidated Volume</p>
            <h3 class="text-2xl font-black text-slate-900 tracking-tighter">{{ number_format($bills->sum('quantity_kg'), 0) }} <span class="text-xs text-slate-400">KG</span></h3>
        </x-card>
        <x-card class="relative overflow-hidden group">
            <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/5 rounded-full group-hover:scale-150 transition-transform duration-500"></div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Active Statements</p>
            <h3 class="text-2xl font-black text-slate-900 tracking-tighter">{{ $bills->total() }} <span class="text-xs text-slate-400">Invoices</span></h3>
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
                <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Filter by customer, invoice # or routing..." 
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
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Reference No.</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Client & Cycle Period</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Aggregate Qty</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Statement Value</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Settlement</th>
                        <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($bills as $bill)
                        <tr class="hover:bg-slate-50/80 transition-all group">
                            <td class="px-8 py-6">
                                <span class="font-mono text-xs font-black text-slate-400">#{{ $bill->invoice_number }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-2xl bg-slate-900 flex items-center justify-center text-[10px] font-black text-primary-500 border border-slate-700 shadow-lg group-hover:rotate-6 transition-transform">
                                        {{ substr($bill->customer->name ?? '?', 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="font-black text-slate-800 leading-none group-hover:text-primary-600 transition-colors">{{ $bill->customer->name ?? '—' }}</p>
                                        <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1.5">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                            {{ $bill->period_start->format('d M') }} — {{ $bill->period_end->format('d M, Y') }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="text-slate-900 font-black text-base">{{ number_format($bill->quantity_kg, 2) }} <span class="text-[10px] text-slate-400">KG</span></p>
                            </td>
                            <td class="px-8 py-6 text-right font-black text-slate-900 text-lg tracking-tight">
                                ₹{{ number_format($bill->amount, 0) }}
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
                                    <x-button variant="ghost" size="sm" href="{{ route('billing.weekly.show', $bill) }}" target="_blank" title="Print Invoice">
                                        <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></x-slot>
                                    </x-button>
                                    <x-button variant="ghost" size="sm" href="{{ route('billing.weekly.whatsapp', $bill) }}" target="_blank" class="text-emerald-500 hover:bg-emerald-50" title="Send WhatsApp">
                                        <x-slot name="icon"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.412-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.656zm6.222-4.032c1.503.893 3.129 1.364 4.791 1.365 5.279 0 9.571-4.292 9.573-9.571 0-2.559-1.011-4.954-2.846-6.79-1.835-1.835-4.23-2.845-6.79-2.845-5.287 0-9.577 4.291-9.579 9.578 0 1.762.476 3.48 1.376 4.981l-.894 3.253 3.361-.881z" fill="currentColor"/></x-slot>
                                    </x-button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-32 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 rounded-[2rem] bg-slate-50 flex items-center justify-center text-slate-200 mb-6">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    </div>
                                    <p class="text-xl font-black text-slate-900 tracking-tight">No Statements Found</p>
                                    <p class="text-sm font-bold text-slate-400 mt-2">Initialize your weekly billing by generating a single or bulk bill.</p>
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

<!-- Bulk Generation Modal -->
<div id="bulk-bill-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-md animate-in fade-in duration-300">
    <div class="bg-white rounded-[3rem] shadow-3xl w-full max-w-4xl border border-slate-100 overflow-hidden transform animate-in zoom-in-95 duration-300">
        <div class="flex items-center justify-between px-12 py-10 border-b border-slate-50 bg-slate-50/30">
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Bulk Invoicing</h2>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.3em] mt-2">Efficiency at Industrial Scale</p>
            </div>
            <button onclick="toggleModal('bulk-bill-modal')" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-all shadow-sm hover:rotate-90">✕</button>
        </div>
        
        <form action="{{ route('billing.weekly.bulk') }}" method="POST" class="p-12">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div class="space-y-6">
                    <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest px-1 italic">Target Customer Selection</label>
                    <div class="max-h-[350px] overflow-y-auto border border-slate-100 rounded-[2rem] p-6 space-y-3 bg-slate-50/30 custom-scroll">
                        @foreach($customers as $c)
                            <label class="flex items-center gap-4 p-4 hover:bg-white hover:shadow-xl rounded-2xl cursor-pointer transition-all group border border-transparent hover:border-slate-100">
                                <input type="checkbox" name="customer_ids[]" value="{{ $c->id }}" class="w-6 h-6 rounded-lg border-slate-300 text-primary-500 focus:ring-primary-500/20 transition-all">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-black text-slate-700 truncate group-hover:text-primary-600">{{ $c->name }}</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Route: {{ $c->route ?: 'Standard' }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <div class="space-y-8">
                    <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest px-1 italic">Invoice Configuration</label>
                    <div class="grid grid-cols-2 gap-6">
                        <x-input label="Period Start *" type="date" name="period_start" required />
                        <x-input label="Period End *" type="date" name="period_end" required />
                    </div>
                    
                    <x-input label="Standard Amount (₹) *" type="number" name="amount" required step="0.01" placeholder="0.00" />
                    
                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest px-1 italic">Initial Settlement</label>
                        <select name="status" class="w-full bg-slate-50 border-slate-200 rounded-[1.5rem] py-4 px-6 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all border appearance-none">
                            <option value="Generated">Generated</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="pt-12 mt-12 border-t border-slate-100 flex flex-col md:flex-row gap-6">
                <x-button variant="ghost" class="flex-1 py-5 rounded-[2rem] text-slate-400 hover:text-rose-500" type="button" onclick="toggleModal('bulk-bill-modal')">Abort Process</x-button>
                <x-button variant="primary" class="flex-[2] py-5 rounded-[2rem] shadow-primary-500/20" type="submit">
                    <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></x-slot>
                    Generate Selected Invoices
                </x-button>
            </div>
        </form>
    </div>
</div>

<!-- Single Bill Modal -->
<div id="add-bill-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-md animate-in fade-in duration-300">
    <div class="bg-white rounded-[3rem] shadow-3xl w-full max-w-2xl border border-slate-100 overflow-hidden transform animate-in zoom-in-95 duration-300">
        <div class="flex items-center justify-between px-12 py-10 border-b border-slate-50 bg-slate-50/30">
            <div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Manual Invoice</h2>
                <p class="text-[10px] text-slate-400 font-black uppercase tracking-[0.3em] mt-2">Single Point Generation</p>
            </div>
            <button onclick="toggleModal('add-bill-modal')" class="w-12 h-12 rounded-2xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-all shadow-sm hover:rotate-90">✕</button>
        </div>
        
        <form action="{{ route('billing.weekly.store') }}" method="POST" class="p-12 space-y-10">
            @csrf
            <div class="space-y-2">
                <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest px-1 italic">Target Customer *</label>
                <select name="customer_id" required class="w-full bg-slate-50 border-slate-200 rounded-[1.5rem] py-4 px-6 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all border appearance-none">
                    <option value="">Select entity…</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} (Route: {{ $c->route ?: 'N/A' }})</option>
                    @endforeach
                </select>
            </div>
            
            <div class="grid grid-cols-2 gap-8">
                <x-input label="Cycle Start *" type="date" name="period_start" required />
                <x-input label="Cycle End *" type="date" name="period_end" required />
            </div>
            
            <div class="grid grid-cols-2 gap-8">
                <x-input label="Yield Qty (kg)" type="number" name="quantity_kg" step="0.01" placeholder="0.00" />
                <x-input label="Net Value (₹) *" type="number" name="amount" required step="0.01" placeholder="0.00" />
            </div>
            
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
                <x-button variant="ghost" class="flex-1 py-5 rounded-[2rem] text-slate-400 hover:text-rose-500" type="button" onclick="toggleModal('add-bill-modal')">Discard Draft</x-button>
                <x-button variant="primary" class="flex-[2] py-5 rounded-[2rem] shadow-primary-500/20" type="submit">
                    <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></x-slot>
                    Generate Invoice
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
</script>
@endpush
