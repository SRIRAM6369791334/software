@extends('layouts.app')
@section('title', 'EMI Tracking')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <a href="{{ route('expenses.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Expenses</a>
        <h1 class="text-2xl font-bold text-slate-950">EMI & Loan Installments</h1>
        <p class="text-sm text-slate-500 mt-0.5">Manage fixed monthly business repayments</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('expenses.emis.alerts') }}" class="px-4 py-2 bg-amber-50 text-amber-700 rounded-lg text-sm font-bold border border-amber-100 hover:bg-amber-100 transition-all flex items-center gap-2">
            <span></span> Upcoming Alerts
        </a>
        <a href="{{ route('expenses.emis.create') }}" class="px-4 py-2 bg-gradient-to-r from-emerald-600 to-sky-500 text-white rounded-lg text-sm font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-600/20 transition-all">
            + Setup New EMI
        </a>
    </div>
</div>

<div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-xl border border-slate-200 shadow-sm overflow-hidden text-sm">
    <table class="w-full">
        <thead>
            <tr class="text-left border-b border-slate-200 bg-gradient-to-r from-emerald-50/80 to-sky-50/80">
                <th class="px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Loan Detail</th>
                <th class="px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Due Date</th>
                <th class="px-5 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wider">Bank Name</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">Amount</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($emis as $emi)
                <tr class="hover:bg-gradient-to-r from-emerald-50/70 to-sky-50/70">
                    <td class="px-5 py-4">
                        <p class="font-bold text-slate-950">{{ $emi->loan_name }}</p>
                        <p class="text-[10px] text-slate-400 font-mono uppercase tracking-widest">REF#{{ str_pad($emi->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </td>
                    <td class="px-5 py-4">
                        @php $isOverdue = $emi->status != 'Paid' && $emi->due_date < now(); @endphp
                        <span class="font-semibold {{ $isOverdue ? 'text-red-600' : 'text-slate-950' }}">{{ $emi->due_date->format('d M, Y') }}</span>
                    </td>
                    <td class="px-5 py-4 text-slate-500 italic">{{ $emi->bank_name ?? 'Bank Unknown' }}</td>
                    <td class="px-5 py-4 text-right font-black text-slate-950">Rs {{ number_format($emi->amount, 2) }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $emi->status == 'Paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $emi->status }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex justify-center gap-2">
                            <form action="{{ route('expenses.emis.destroy', $emi) }}" method="POST" onsubmit="return confirm('Delete this EMI record?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded"></button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-slate-400 italic">No EMI records found.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($emis->hasPages())
    <div class="px-5 py-4 border-t border-slate-200">{{ $emis->links() }}</div>
    @endif
</div>
@endsection
