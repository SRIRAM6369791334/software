@extends('layouts.app')
@section('title', 'Expenses & EMI')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h1 class="text-3xl font-black text-slate-950 tracking-tight">Expenses & EMI</h1>
        <p class="text-slate-500 font-medium">Manage operational burn and financial obligations</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <a href="{{ route('expenses.export') }}" class="px-6 py-4 border border-slate-200 text-slate-600 text-xs font-black rounded-xl hover:bg-emerald-50 transition-all uppercase tracking-widest">
            Export CSV ⬇
        </a>
        <button onclick="document.getElementById('add-expense-modal').classList.remove('hidden')"
                class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-sky-500 text-white text-sm font-black rounded-xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-600/20 active:scale-95">
            + Record Expense 
        </button>
    </div>
</div>

{{-- Operational Burn Summary --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-2xl border border-slate-200 shadow-md shadow-slate-200/60 flex items-center gap-6 group hover:border-red-200 transition-all">
        <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform"></div>
        <div>
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Monthly Burn</h3>
            <p class="text-2xl font-black text-slate-950">Rs {{ number_format($totals['total_expenses'], 0) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-2xl border border-slate-200 shadow-md shadow-slate-200/60 flex items-center gap-6 group hover:border-amber-200 transition-all">
        <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform"></div>
        <div>
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">EMI Total</h3>
            <p class="text-2xl font-black text-slate-950">Rs {{ number_format($totals['total_emis'], 0) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 p-6 rounded-2xl shadow-md shadow-slate-200/60 text-white flex items-center gap-6 col-span-2">
        <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center text-2xl"></div>
        <div>
            <h3 class="text-[10px] font-black text-emerald-200 uppercase tracking-widest mb-1">Financial Outlook</h3>
            <p class="text-2xl font-black text-white italic">"Optimizing cash flow by tracking every penny of operational cost."</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    {{-- Expense Ledger --}}
    <div class="lg:col-span-2 bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-2xl border border-slate-200 shadow-lg overflow-hidden">
        <div class="p-8 border-b border-slate-100 bg-gradient-to-r from-emerald-50/80 to-sky-50/80">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">General Expense Ledger</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead>
                    <tr class="bg-gradient-to-r from-emerald-50/80 to-sky-50/80 text-slate-400 font-black uppercase text-[10px] tracking-widest border-b border-slate-200">
                        <th class="px-8 py-5">Date</th>
                        <th class="px-8 py-5">Category</th>
                        <th class="px-8 py-5">Description</th>
                        <th class="px-8 py-5 text-right">Amount</th>
                        <th class="px-8 py-5 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($expenses as $e)
                        <tr class="hover:bg-gradient-to-r from-emerald-50/70 to-sky-50/70 transition-all group">
                            <td class="px-8 py-5">
                                <span class="font-black text-slate-950 tracking-tighter">{{ $e->date->format('M d, Y') }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <span class="px-3 py-1 bg-sky-50 text-slate-600 text-[10px] font-black rounded-lg uppercase tracking-widest border border-slate-200">
                                    {{ $e->category }}
                                </span>
                            </td>
                            <td class="px-8 py-5 font-bold text-slate-700 tracking-tight">{{ $e->description }}</td>
                            <td class="px-8 py-5 text-right font-black text-red-500 text-lg tracking-tighter">
                                Rs {{ number_format($e->amount, 0) }}
                            </td>
                            <td class="px-8 py-5 text-center">
                                <form action="{{ route('expenses.destroy', $e) }}" method="POST" onsubmit="return confirm('Archive this expense entry?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-9 h-9 flex items-center justify-center bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-xl text-slate-300 hover:text-red-600 hover:border-red-200 hover:shadow-lg transition-all">
                                        
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-8 py-20 text-center text-slate-400 italic">No expenses recorded this cycle.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-8 border-t border-slate-100 bg-gradient-to-r from-emerald-50/70 to-sky-50/70">
            {{ $expenses->links() }}
        </div>
    </div>

    {{-- EMI Obligations --}}
    <div class="space-y-6">
        <div class="flex items-center justify-between mb-2">
            <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">EMI Obligations</h3>
        </div>
        @forelse($emis as $emi)
            @php
                $statusMap = [
                    'Paid' => ['class' => 'bg-emerald-50 text-emerald-600 border-emerald-100', 'icon' => '✅'],
                    'Overdue' => ['class' => 'bg-red-50 text-red-600 border-red-100', 'icon' => '⚠️'],
                    'Upcoming' => ['class' => 'bg-blue-50 text-blue-600 border-blue-100', 'icon' => '⏳']
                ];
                $style = $statusMap[$emi->status] ?? $statusMap['Upcoming'];
            @endphp
            <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-2xl border border-slate-200 shadow-md shadow-slate-200/60 group hover:border-emerald-200 transition-all flex items-center justify-between">
                <div>
                    <h4 class="font-black text-slate-950 tracking-tight text-lg mb-1">{{ $emi->item }}</h4>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Due: {{ $emi->due_date->format('M d, Y') }}</span>
                        <span class="px-2 py-0.5 {{ $style['class'] }} border text-[8px] font-black rounded-md uppercase tracking-tighter">{{ $emi->status }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <p class="text-xl font-black text-slate-950 tracking-tighter">Rs {{ number_format($emi->amount, 0) }}</p>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $style['icon'] }}</span>
                </div>
            </div>
        @empty
            <div class="bg-emerald-50 border-2 border-dashed border-slate-200 rounded-2xl p-12 text-center">
                <p class="text-slate-400 font-bold">No active EMI schedules.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- Add Expense Modal --}}
<div id="add-expense-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200/40 backdrop-blur-md transition-all">
    <div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-3xl shadow-lg w-full max-w-xl border border-white/20 overflow-hidden">
        <div class="flex items-center justify-between px-10 py-8 border-b border-slate-100 bg-gradient-to-r from-emerald-50/70 to-sky-50/70">
            <div>
                <h2 class="text-2xl font-black text-slate-950 tracking-tight uppercase tracking-widest">Record Expense </h2>
                <p class="text-sm text-slate-500 font-medium">Log operational expenditures for current cycle</p>
            </div>
            <button onclick="document.getElementById('add-expense-modal').classList.add('hidden')" 
                    class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 text-slate-400 hover:text-red-500 transition-all shadow-sm">x</button>
        </div>

        <form action="{{ route('expenses.store') }}" method="POST" class="p-10 space-y-8">
            @csrf
            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Category *</label>
                    <select name="category" required class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black">
                        @foreach(['Fuel','Salary','Transport','Utility','Misc'] as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Date *</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                           class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Description *</label>
                <input type="text" name="description" required placeholder="What was this expense for?"
                       class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black">
            </div>

            <div class="space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Amount (Rs ) *</label>
                <input type="number" name="amount" required step="0.01" min="0.01" placeholder="0.00"
                       class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black text-2xl tracking-tighter">
            </div>

            <div class="flex justify-end gap-6 pt-6">
                <button type="button" onclick="document.getElementById('add-expense-modal').classList.add('hidden')" class="px-8 py-4 text-sm font-black text-slate-400 hover:text-slate-950 transition-colors uppercase tracking-widest">Cancel</button>
                <button type="submit" class="px-12 py-5 bg-gradient-to-r from-emerald-600 to-sky-500 text-white font-black rounded-3xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-600/20 active:scale-95 transform">
                    Log Expense 
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
