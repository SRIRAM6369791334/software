@extends('layouts.app')
@section('title', 'EMI Tracking')

@section('content')
<div class="mb-6 flex justify-between items-end">
    <div>
        <a href="{{ route('expenses.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Expenses</a>
        <h1 class="text-2xl font-bold text-gray-900">EMI & Loan Installments</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage fixed monthly business repayments</p>
    </div>
    <div class="flex gap-3">
        <a href="{{ route('expenses.emis.alerts') }}" class="px-4 py-2 bg-amber-50 text-amber-700 rounded-lg text-sm font-bold border border-amber-100 hover:bg-amber-100 transition-all flex items-center gap-2">
            <span>🔔</span> Upcoming Alerts
        </a>
        <a href="{{ route('expenses.emis.create') }}" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm font-bold hover:bg-emerald-700 shadow-lg shadow-emerald-600/20 transition-all">
            + Setup New EMI
        </a>
    </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden text-sm">
    <table class="w-full">
        <thead>
            <tr class="text-left border-b border-gray-100 bg-gray-50/50">
                <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Loan Detail</th>
                <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Due Date</th>
                <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Bank Name</th>
                <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Amount</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($emis as $emi)
                <tr class="hover:bg-gray-50/30">
                    <td class="px-5 py-4">
                        <p class="font-bold text-gray-900">{{ $emi->item }}</p>
                        <p class="text-[10px] text-gray-400 font-mono uppercase tracking-widest">REF#{{ str_pad($emi->id, 4, '0', STR_PAD_LEFT) }}</p>
                    </td>
                    <td class="px-5 py-4">
                        @php $isOverdue = $emi->status != 'Paid' && $emi->due_date < now(); @endphp
                        <span class="font-semibold {{ $isOverdue ? 'text-red-600' : 'text-gray-900' }}">{{ $emi->due_date->format('d M, Y') }}</span>
                    </td>
                    <td class="px-5 py-4 text-gray-500 italic">EM-{{ $emi->id }}</td>
                    <td class="px-5 py-4 text-right font-black text-gray-900">₹{{ number_format($emi->amount, 2) }}</td>
                    <td class="px-5 py-4 text-center">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $emi->status == 'Paid' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $emi->status }}
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex justify-center gap-2">
                            <form action="{{ route('expenses.emis.destroy', $emi) }}" method="POST" onsubmit="return confirm('Delete this EMI record?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400 italic">No EMI records found.</td></tr>
            @endforelse
        </tbody>
    </table>
    @if($emis->hasPages())
    <div class="px-5 py-4 border-t border-gray-100">{{ $emis->links() }}</div>
    @endif
</div>
@endsection
