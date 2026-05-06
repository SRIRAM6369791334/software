@extends('layouts.app')
@section('title', 'Weekly Billing')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h1 class="text-3xl font-black text-gray-900 tracking-tight">Weekly Billing</h1>
        <p class="text-gray-500 font-medium">Manage settlements and customer invoices</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <button onclick="document.getElementById('bulk-bill-modal').classList.remove('hidden')"
                class="px-5 py-3 bg-indigo-50 text-indigo-700 text-sm font-black rounded-2xl hover:bg-indigo-100 transition-all border border-indigo-100 shadow-sm">
            📦 Bulk Generate
        </button>
        <button onclick="document.getElementById('add-bill-modal').classList.remove('hidden')"
                class="px-5 py-3 bg-emerald-600 text-white text-sm font-black rounded-2xl hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20">
            + New Single Bill
        </button>
        <a href="{{ route('billing.weekly.export') }}" class="px-5 py-3 bg-white border border-gray-200 text-gray-400 hover:text-gray-900 text-sm font-bold rounded-2xl transition-all">
            ⬇ CSV
        </a>
    </div>
</div>

{{-- Performance Stats Header --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 flex items-center gap-6">
        <div class="w-14 h-14 bg-indigo-50 rounded-2xl flex items-center justify-center text-2xl">🧾</div>
        <div>
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Generated</h3>
            <p class="text-2xl font-black text-gray-900">{{ $bills->total() }} <span class="text-xs text-gray-400 font-bold ml-1">Invoices</span></p>
        </div>
    </div>
    <div class="bg-white p-6 rounded-[2.5rem] border border-gray-100 shadow-xl shadow-gray-200/50 flex items-center gap-6">
        <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-2xl text-amber-600">⏳</div>
        <div>
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Outstanding</h3>
            <p class="text-2xl font-black text-gray-900">₹{{ number_format($bills->where('status', 'Pending')->sum('amount'), 0) }}</p>
        </div>
    </div>
    <div class="bg-emerald-600 p-6 rounded-[2.5rem] shadow-xl shadow-emerald-600/20 text-white flex items-center gap-6">
        <div class="w-14 h-14 bg-white/10 rounded-2xl flex items-center justify-center text-2xl">💰</div>
        <div>
            <h3 class="text-[10px] font-black text-emerald-200 uppercase tracking-widest mb-1">Total Revenue</h3>
            <p class="text-2xl font-black">₹{{ number_format($bills->where('status', 'Paid')->sum('amount'), 0) }}</p>
        </div>
    </div>
</div>

{{-- Main List Section --}}
<div class="bg-white rounded-[2.5rem] border border-gray-200 shadow-2xl overflow-hidden mb-12">
    <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
        <form method="GET" class="relative w-full max-w-md">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">🔍</span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search customer or invoice..."
                   class="w-full pl-12 pr-4 py-3 bg-white border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-medium text-sm">
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-gray-50/50 text-gray-400 font-black uppercase text-[10px] tracking-widest border-b border-gray-100">
                    <th class="px-8 py-5">Inv No</th>
                    <th class="px-8 py-5">Customer</th>
                    <th class="px-8 py-5">Period</th>
                    <th class="px-8 py-5 text-right">Quantity</th>
                    <th class="px-8 py-5 text-right">Amount</th>
                    <th class="px-8 py-5 text-center">Status</th>
                    <th class="px-8 py-5 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($bills as $bill)
                    <tr class="hover:bg-emerald-50/30 transition-colors group">
                        <td class="px-8 py-5">
                            <span class="font-mono text-xs font-bold text-gray-400 group-hover:text-emerald-600 transition-colors">
                                #{{ $bill->invoice_no ?? $bill->invoice_number }}
                            </span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center font-black text-gray-500 group-hover:bg-emerald-100 group-hover:text-emerald-600 transition-all">
                                    {{ substr($bill->customer->name ?? '?', 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-black text-gray-900">{{ $bill->customer->name ?? '—' }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $bill->customer->route ?? 'General' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-700">{{ $bill->period_start->format('d M') }} - {{ $bill->period_end->format('d M') }}</span>
                                <span class="text-[10px] text-gray-400 font-bold uppercase">{{ $bill->period_end->format('Y') }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <span class="font-black text-gray-900">{{ number_format($bill->quantity_kg, 2) }}</span>
                            <span class="text-[10px] text-gray-400 font-bold">Kg</span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex flex-col items-end">
                                <span class="text-lg font-black text-gray-900">₹{{ number_format($bill->amount, 0) }}</span>
                                <span class="text-[10px] text-emerald-600 font-black uppercase tracking-tighter">Incl. GST</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center">
                            @php
                                $statusMap = [
                                    'Generated' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-600', 'label' => 'GENERATED'],
                                    'Pending'   => ['bg' => 'bg-amber-50', 'text' => 'text-amber-600', 'label' => 'PENDING'],
                                    'Paid'      => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-600', 'label' => 'PAID'],
                                ];
                                $st = $statusMap[$bill->status] ?? $statusMap['Pending'];
                            @endphp
                            <span class="px-3 py-1.5 {{ $st['bg'] }} {{ $st['text'] }} text-[10px] font-black rounded-lg tracking-widest">
                                {{ $st['label'] }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <div class="flex items-center justify-end gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                <a href="{{ route('billing.weekly.show', $bill) }}" target="_blank" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 hover:bg-emerald-600 hover:text-white transition-all shadow-sm" title="Print Invoice">
                                    👁️
                                </a>
                                <a href="{{ route('billing.weekly.pdf', $bill) }}" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Download PDF">
                                    📥
                                </a>
                                <a href="{{ route('billing.weekly.whatsapp', $bill) }}" target="_blank" class="w-9 h-9 flex items-center justify-center rounded-xl bg-gray-100 text-gray-400 hover:bg-emerald-500 hover:text-white transition-all shadow-sm" title="WhatsApp Message">
                                    💬
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="text-5xl mb-4">📭</div>
                                <h3 class="text-xl font-black text-gray-900">No Bills Found</h3>
                                <p class="text-gray-400 font-medium mt-1">Start generating invoices for your customers.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bills->hasPages())
        <div class="p-8 border-t border-gray-50 bg-gray-50/30">
            {{ $bills->withQueryString()->links() }}
        </div>
    @endif
</div>

{{-- MODALS (Upgraded to Luminous Overlay) --}}

{{-- Single Bill Modal --}}
<div id="add-bill-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gray-900/40 backdrop-blur-md transition-all">
    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-xl border border-white/20 overflow-hidden transform transition-all scale-100">
        <div class="flex items-center justify-between px-10 py-8 border-b border-gray-50 bg-gray-50/30">
            <div>
                <h2 class="text-2xl font-black text-gray-900 tracking-tight">Generate Invoice</h2>
                <p class="text-sm text-gray-500 font-medium">Create a single weekly settlement</p>
            </div>
            <button onclick="document.getElementById('add-bill-modal').classList.add('hidden')" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-gray-200 text-gray-400 hover:text-red-500 transition-all shadow-sm">✕</button>
        </div>
        
        <form action="{{ route('billing.weekly.store') }}" method="POST" class="p-10 space-y-8">
            @csrf
            
            <div class="space-y-6">
                {{-- Customer --}}
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">1. Select Customer</label>
                    <select name="customer_id" required class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold text-gray-900">
                        <option value="">Select customer…</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->route }})</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">2. From Date</label>
                        <input type="date" name="period_start" required class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">3. To Date</label>
                        <input type="date" name="period_end" required class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">4. Quantity (Kg)</label>
                        <input type="number" name="quantity_kg" step="0.01" placeholder="0.00" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black text-xl">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">5. Total Amount (₹)</label>
                        <input type="number" name="amount" required step="0.01" placeholder="0.00" class="w-full px-5 py-4 bg-emerald-50 border border-emerald-100 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black text-xl text-emerald-700">
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">6. Initial Status</label>
                    <select name="status" class="w-full px-5 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold">
                        <option value="Generated">Generated (Invoice Created)</option>
                        <option value="Pending">Pending (Awaiting Confirmation)</option>
                        <option value="Paid">Paid (Cash Collected)</option>
                    </select>
                </div>
            </div>

            <button type="submit" class="w-full py-5 bg-emerald-600 text-white font-black rounded-3xl hover:bg-emerald-700 transition-all shadow-xl shadow-emerald-600/20 active:scale-95">
                Confirm & Generate Bill 🧾
            </button>
        </form>
    </div>
</div>

{{-- Bulk Modal --}}
<div id="bulk-bill-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-6 bg-indigo-900/40 backdrop-blur-md transition-all">
    <div class="bg-white rounded-[3.5rem] shadow-2xl w-full max-w-3xl border border-white/20 overflow-hidden transform transition-all">
        <div class="flex items-center justify-between px-10 py-8 border-b border-gray-50 bg-indigo-50/30">
            <div>
                <h2 class="text-2xl font-black text-indigo-900 tracking-tight">Bulk Generation</h2>
                <p class="text-sm text-indigo-500 font-medium">Generate settlements for multiple customers at once</p>
            </div>
            <button onclick="document.getElementById('bulk-bill-modal').classList.add('hidden')" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white border border-indigo-100 text-indigo-300 hover:text-red-500 transition-all shadow-sm">✕</button>
        </div>

        <form action="{{ route('billing.weekly.bulkStore') }}" method="POST" class="p-10">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                <div class="space-y-4">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">Select Customers</label>
                    <div class="h-[300px] overflow-y-auto pr-2 custom-scrollbar space-y-2">
                        @foreach($customers as $c)
                            <label class="group flex items-center gap-3 p-4 bg-gray-50 border border-gray-100 rounded-2xl hover:bg-indigo-50 hover:border-indigo-200 transition-all cursor-pointer">
                                <input type="checkbox" name="customer_ids[]" value="{{ $c->id }}" class="w-5 h-5 rounded-lg text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-gray-700 group-hover:text-indigo-700">{{ $c->name }}</span>
                                    <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $c->route }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>
                
                <div class="space-y-6">
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest">Global Settings</label>
                    
                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500">Period Start</label>
                                <input type="date" name="period_start" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-indigo-500 font-bold text-sm">
                            </div>
                            <div class="space-y-2">
                                <label class="text-[10px] font-bold text-gray-500">Period End</label>
                                <input type="date" name="period_end" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none focus:border-indigo-500 font-bold text-sm">
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-500">Standard Amount (₹)</label>
                            <input type="number" name="amount" required step="0.01" placeholder="0.00" class="w-full px-5 py-4 bg-indigo-50 border border-indigo-100 rounded-2xl outline-none focus:ring-4 focus:ring-indigo-500/10 font-black text-2xl text-indigo-700">
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-bold text-gray-500">Initial Status</label>
                            <select name="status" class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl outline-none font-bold text-sm">
                                <option value="Generated">Generated</option>
                                <option value="Pending">Pending</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit" class="w-full py-5 bg-indigo-600 text-white font-black rounded-3xl hover:bg-indigo-700 transition-all shadow-xl shadow-indigo-600/20 active:scale-95">
                            Generate Bulk Bills 📦
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
    .custom-scrollbar::-webkit-scrollbar { width: 5px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
</style>
@endpush
