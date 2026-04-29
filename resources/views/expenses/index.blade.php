@extends('layouts.app')
@section('title', 'Expenses & EMI')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Expenses & EMI</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Monitor operational costs and finance obligations</p>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="secondary" size="md">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></x-slot>
                Export CSV
            </x-button>
            <x-button variant="primary" size="md" onclick="toggleModal('add-expense-modal')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></x-slot>
                Record Expense
            </x-button>
        </div>
    </div>

    <!-- Summary Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <x-card padding="false">
            <div class="p-8 flex items-center gap-6">
                <div class="w-16 h-16 rounded-[2rem] bg-red-50 flex items-center justify-center text-red-500 shadow-sm border border-red-100/50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-1">Monthly Expenses</p>
                    <p class="text-3xl font-black text-slate-900 tracking-tight">₹{{ number_format($totals['total_expenses'], 0) }}</p>
                    <p class="text-xs font-bold text-red-500 mt-1 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" /></svg>
                        +12.5% from last month
                    </p>
                </div>
            </div>
        </x-card>
        <x-card padding="false">
            <div class="p-8 flex items-center gap-6">
                <div class="w-16 h-16 rounded-[2rem] bg-amber-50 flex items-center justify-center text-amber-500 shadow-sm border border-amber-100/50">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-1">Outstanding EMIs</p>
                    <p class="text-3xl font-black text-slate-900 tracking-tight">₹{{ number_format($totals['total_emis'], 0) }}</p>
                    <p class="text-xs font-bold text-slate-500 mt-1">4 Active loan schedules</p>
                </div>
            </div>
        </x-card>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        <!-- Expense Register -->
        <div class="lg:col-span-8">
            <x-card padding="false" class="h-full">
                <div class="px-8 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                    <h2 class="text-lg font-black text-slate-900 tracking-tight">Expense Register</h2>
                    <select class="bg-white border-slate-200 rounded-xl py-1.5 px-4 text-xs font-bold text-slate-600 outline-none shadow-sm focus:ring-4 focus:ring-primary-500/10">
                        <option>All Categories</option>
                        <option>Fuel</option>
                        <option>Salary</option>
                    </select>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left border-b border-slate-100">
                                <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Transaction Details</th>
                                <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Category</th>
                                <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Amount</th>
                                <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @forelse($expenses as $e)
                                <tr class="hover:bg-slate-50/50 transition-colors group">
                                    <td class="px-8 py-5">
                                        <p class="font-bold text-slate-900">{{ $e->description }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">{{ $e->date->format('d M, Y') }}</p>
                                    </td>
                                    <td class="px-8 py-5">
                                        <x-badge variant="slate">{{ $e->category }}</x-badge>
                                    </td>
                                    <td class="px-8 py-5 text-right font-black text-red-500">
                                        ₹{{ number_format($e->amount, 0) }}
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex items-center justify-center">
                                            <form action="{{ route('expenses.destroy', $e) }}" method="POST" onsubmit="return confirm('Archive transaction?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="p-2.5 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors shadow-sm opacity-0 group-hover:opacity-100">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center text-slate-400 font-medium italic">No transactions recorded this month</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($expenses->hasPages())
                    <div class="px-8 py-4 border-t border-slate-100 bg-slate-50/30">
                        {{ $expenses->links() }}
                    </div>
                @endif
            </x-card>
        </div>

        <!-- EMI Schedule -->
        <div class="lg:col-span-4">
            <div class="flex items-center justify-between mb-4 px-2">
                <h2 class="text-lg font-black text-slate-900 tracking-tight">EMI Roadmap</h2>
                <x-button variant="ghost" size="sm" class="text-[10px] uppercase tracking-widest font-bold">View History</x-button>
            </div>
            <div class="space-y-4">
                @forelse($emis as $emi)
                    @php
                        $variant = ['Upcoming' => 'primary', 'Paid' => 'success', 'Overdue' => 'danger'][$emi->status] ?? 'slate';
                    @endphp
                    <x-card padding="false" class="group hover:scale-[1.02] transition-transform duration-300">
                        <div class="p-6">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <h3 class="font-extrabold text-slate-900">{{ $emi->item }}</h3>
                                    <div class="flex items-center gap-2 mt-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                        <p class="text-xs font-bold text-slate-500 uppercase tracking-widest">Due: {{ $emi->due_date->format('d M, Y') }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-black text-slate-900 tracking-tight">₹{{ number_format($emi->amount, 0) }}</p>
                                    <x-badge :variant="$variant" class="mt-2">{{ $emi->status }}</x-badge>
                                </div>
                            </div>
                        </div>
                    </x-card>
                @empty
                    <div class="p-10 border-2 border-dashed border-slate-200 rounded-[2.5rem] flex flex-col items-center justify-center text-center">
                        <div class="w-12 h-12 bg-slate-50 rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-widest">All caught up</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Add Expense Modal -->
<div id="add-expense-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xl animate-in fade-in duration-300">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-md border border-white/20 overflow-hidden transform animate-in zoom-in-95 duration-300">
        <div class="flex items-center justify-between px-10 py-8 border-b border-slate-100 bg-slate-50/50">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">New Expense</h2>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Record financial outflow</p>
            </div>
            <button onclick="toggleModal('add-expense-modal')" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors shadow-sm">✕</button>
        </div>
        <form action="{{ route('expenses.store') }}" method="POST" class="p-10 space-y-8">
            @csrf
            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-1">Category *</label>
                    <select name="category" required class="w-full bg-slate-50 border-slate-200 rounded-2xl py-3 px-5 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all">
                        @foreach(['Fuel','Salary','Transport','Utility','Misc'] as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
                    </select>
                </div>
                <x-input label="Date *" type="date" name="date" required :value="date('Y-m-d')" />
            </div>
            <x-input label="Description *" name="description" placeholder="Petrol for transport truck..." required />
            <x-input label="Amount (₹) *" type="number" name="amount" required step="0.01" min="0.01" placeholder="0.00" />
            
            <div class="pt-4 flex gap-4">
                <x-button variant="ghost" class="flex-1" type="button" onclick="toggleModal('add-expense-modal')">Cancel</x-button>
                <x-button variant="primary" class="flex-1" type="submit">Post Expense</x-button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
function toggleModal(id) {
    const modal = document.getElementById(id);
    modal.classList.toggle('hidden');
}
</script>
@endpush
@endsection
