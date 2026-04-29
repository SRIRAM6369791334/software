@extends('layouts.app')
@section('title', 'Weekly Billing')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Weekly Billing</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Generate and manage weekly customer invoices</p>
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

    <!-- Filters & Search -->
    <x-card padding="false">
        <div class="p-6 flex flex-col md:flex-row items-center gap-6">
            <div class="flex-1 w-full">
                <form method="GET" class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400 group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by customer, invoice number..." 
                           class="w-full bg-slate-50 border-slate-200 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 rounded-2xl py-3 pl-11 pr-4 text-sm font-medium transition-all outline-none">
                </form>
            </div>
        </div>
    </x-card>

    <!-- Table Section -->
    <x-card padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-100 bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Invoice #</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Customer & Period</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Qty (kg)</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Amount</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center">Status</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($bills as $bill)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <span class="font-mono text-xs font-bold text-slate-400">#{{ $bill->invoice_number }}</span>
                            </td>
                            <td class="px-8 py-6">
                                <div class="space-y-1">
                                    <p class="font-extrabold text-slate-900">{{ $bill->customer->name ?? '—' }}</p>
                                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1.5">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        {{ $bill->period_start->format('d M') }} - {{ $bill->period_end->format('d M, Y') }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="text-slate-900 font-black">{{ number_format($bill->quantity_kg, 2) }} <span class="text-[10px] text-slate-400">KG</span></p>
                            </td>
                            <td class="px-8 py-6 text-right font-black text-slate-900 text-base">
                                ₹{{ number_format($bill->amount, 0) }}
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex justify-center">
                                    @php
                                        $variant = ['Generated' => 'primary', 'Pending' => 'amber', 'Paid' => 'success'][$bill->status] ?? 'slate';
                                    @endphp
                                    <x-badge :variant="$variant">{{ $bill->status }}</x-badge>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('billing.weekly.show', $bill) }}" target="_blank" class="p-2.5 rounded-xl bg-slate-100 text-slate-600 hover:bg-primary-500 hover:text-white transition-all shadow-sm" title="Print Invoice">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                    </a>
                                    <a href="{{ route('billing.weekly.whatsapp', $bill) }}" target="_blank" class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600 hover:bg-emerald-500 hover:text-white transition-all shadow-sm" title="WhatsApp Message">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.412-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.656zm6.222-4.032c1.503.893 3.129 1.364 4.791 1.365 5.279 0 9.571-4.292 9.573-9.571 0-2.559-1.011-4.954-2.846-6.79-1.835-1.835-4.23-2.845-6.79-2.845-5.287 0-9.577 4.291-9.579 9.578 0 1.762.476 3.48 1.376 4.981l-.894 3.253 3.361-.881zm11.752-6.513c-.232-.115-1.371-.678-1.586-.756-.215-.078-.371-.115-.527.115-.156.231-.605.756-.742.913-.137.156-.273.176-.505.06-.232-.115-.979-.36-1.866-1.157-.691-.616-1.158-1.377-1.294-1.608-.137-.232-.015-.357.101-.472.104-.103.232-.271.347-.406.115-.135.153-.231.23-.385.078-.154.039-.289-.02-.406-.058-.117-.527-1.271-.722-1.739-.191-.459-.383-.396-.527-.404-.136-.007-.293-.008-.449-.008-.156 0-.41.058-.625.289-.215.231-.82.801-.82 1.956s.84 2.271.957 2.426c.117.154 1.652 2.523 4.003 3.537.559.241 1.002.395 1.341.503.562.179 1.074.154 1.478.094.452-.067 1.371-.56 1.566-1.1s.195-.999.137-1.1c-.058-.101-.215-.156-.447-.271z"/></svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center justify-center text-slate-300">
                                    <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                    <p class="text-lg font-bold text-slate-900">No invoices generated</p>
                                    <p class="text-sm font-medium mt-1">Start by generating a single or bulk bill.</p>
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
<div id="bulk-bill-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xl animate-in fade-in duration-300">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-4xl border border-white/20 overflow-hidden transform animate-in zoom-in-95 duration-300">
        <div class="flex items-center justify-between px-10 py-8 border-b border-slate-100 bg-slate-50/50">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Bulk Invoicing</h2>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Efficiency at scale</p>
            </div>
            <button onclick="toggleModal('bulk-bill-modal')" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors shadow-sm">✕</button>
        </div>
        <form action="{{ route('billing.weekly.bulk') }}" method="POST" class="p-10">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <div class="space-y-4">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest px-1">Target Customers</p>
                    <div class="max-h-[300px] overflow-y-auto border border-slate-100 rounded-3xl p-4 space-y-2 bg-slate-50/30 custom-scroll">
                        @foreach($customers as $c)
                            <label class="flex items-center gap-3 p-3 hover:bg-white hover:shadow-sm rounded-2xl cursor-pointer transition-all group">
                                <input type="checkbox" name="customer_ids[]" value="{{ $c->id }}" class="w-5 h-5 rounded-lg border-slate-300 text-primary-500 focus:ring-primary-500/20">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-700 truncate group-hover:text-slate-900">{{ $c->name }}</p>
                                    <p class="text-[10px] text-slate-400 font-medium">Route: {{ $c->route ?: 'Standard' }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                <div class="space-y-6">
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest px-1">Invoice Configuration</p>
                    <div class="grid grid-cols-2 gap-6">
                        <x-input label="Period Start *" type="date" name="period_start" required />
                        <x-input label="Period End *" type="date" name="period_end" required />
                    </div>
                    <x-input label="Standard Amount (₹) *" type="number" name="amount" required step="0.01" placeholder="0.00" />
                    <div class="space-y-2">
                        <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-1">Initial Status</label>
                        <select name="status" class="w-full bg-slate-50 border-slate-200 rounded-2xl py-3 px-5 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all">
                            <option value="Generated">Generated</option>
                            <option value="Pending">Pending</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="pt-10 mt-10 border-t border-slate-100 flex gap-4">
                <x-button variant="ghost" class="flex-1" type="button" onclick="toggleModal('bulk-bill-modal')">Cancel</x-button>
                <x-button variant="primary" class="flex-1" type="submit">Process Bulk Invoices</x-button>
            </div>
        </form>
    </div>
</div>

<!-- Single Bill Modal -->
<div id="add-bill-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xl animate-in fade-in duration-300">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-2xl border border-white/20 overflow-hidden transform animate-in zoom-in-95 duration-300">
        <div class="flex items-center justify-between px-10 py-8 border-b border-slate-100 bg-slate-50/50">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Manual Invoice</h2>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Single generation</p>
            </div>
            <button onclick="toggleModal('add-bill-modal')" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors shadow-sm">✕</button>
        </div>
        <form action="{{ route('billing.weekly.store') }}" method="POST" class="p-10 space-y-8">
            @csrf
            <div class="space-y-2">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-1">Customer *</label>
                <select name="customer_id" required class="w-full bg-slate-50 border-slate-200 rounded-2xl py-3 px-5 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all">
                    <option value="">Select customer…</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} (Route: {{ $c->route ?: 'N/A' }})</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-8">
                <x-input label="Period Start *" type="date" name="period_start" required />
                <x-input label="Period End *" type="date" name="period_end" required />
            </div>
            <div class="grid grid-cols-2 gap-8">
                <x-input label="Quantity (kg)" type="number" name="quantity_kg" step="0.01" placeholder="0.00" />
                <x-input label="Total Amount (₹) *" type="number" name="amount" required step="0.01" placeholder="0.00" />
            </div>
            <div class="space-y-2">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-1">Status</label>
                <select name="status" class="w-full bg-slate-50 border-slate-200 rounded-2xl py-3 px-5 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all">
                    <option value="Generated">Generated</option>
                    <option value="Pending">Pending</option>
                    <option value="Paid">Paid</option>
                </select>
            </div>
            <div class="pt-4 flex gap-4">
                <x-button variant="ghost" class="flex-1" type="button" onclick="toggleModal('add-bill-modal')">Cancel</x-button>
                <x-button variant="primary" class="flex-1" type="submit">Generate Invoice</x-button>
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
