@extends('layouts.app')
@section('title', 'Upcoming Alerts')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">System Alerts & Notifications</h1>
    <p class="text-sm text-gray-500 mt-0.5">Track upcoming payments and critical system business dates</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- EMI Alerts Section --}}
    <div class="lg:col-span-2 space-y-4">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-amber-50/30 flex justify-between items-center">
                <h2 class="text-base font-bold text-amber-900 flex items-center gap-2">
                    <span>📅</span> Upcoming EMI Payments
                </h2>
                <span class="px-2 py-0.5 bg-amber-100 text-amber-700 text-[10px] font-bold uppercase rounded-full tracking-wider">Next 30 Days</span>
            </div>
            <div class="p-0">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b border-gray-100 bg-gray-50/50">
                            <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Item/Asset</th>
                            <th class="px-5 py-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">Due Date</th>
                            <th class="px-5 py-3 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Amount</th>
                            <th class="px-5 py-3 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($upcomingEmis as $emi)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-5 py-4">
                                    <p class="font-bold text-gray-900">{{ $emi->item }}</p>
                                    <p class="text-xs text-gray-500">{{ $emi->note ?? 'No description' }}</p>
                                </td>
                                <td class="px-5 py-4 text-gray-600">
                                    <span class="{{ $emi->due_date->isToday() ? 'text-red-600 font-bold' : '' }}">
                                        {{ $emi->due_date->format('d M Y') }}
                                    </span>
                                    <p class="text-[10px] uppercase font-bold text-gray-400 mt-0.5">{{ $emi->due_date->diffForHumans() }}</p>
                                </td>
                                <td class="px-5 py-4 text-right font-mono font-bold text-gray-900">
                                    ₹{{ number_format($emi->amount, 0) }}
                                </td>
                                <td class="px-5 py-4 text-center">
                                    @if($emi->status === 'Paid')
                                        <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-xs font-medium rounded-full border border-emerald-100 italic">Paid</span>
                                    @elseif($emi->due_date->isPast())
                                        <span class="px-2 py-0.5 bg-red-50 text-red-700 text-xs font-bold rounded-full border border-red-100 uppercase tracking-tighter">Overdue</span>
                                    @else
                                        <span class="px-2 py-0.5 bg-amber-50 text-amber-700 text-xs font-medium rounded-full border border-amber-100">Pending</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-5 py-12 text-center text-gray-400 italic">No upcoming EMIs found for the next 30 days.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- System Logs/Notifications Sidebar --}}
    <div class="space-y-6">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-widest border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                <span>🛡️</span> System Status
            </h3>
            <div class="space-y-4">
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <p class="text-xs text-gray-600">Database connected & synchronized</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-emerald-500"></div>
                    <p class="text-xs text-gray-600">Daily backups completed (02:00 AM)</p>
                </div>
            </div>
        </div>

        <div class="bg-indigo-600 rounded-xl shadow-lg p-5 text-white">
            <h3 class="text-sm font-bold uppercase tracking-widest mb-2 opacity-80">Pro-Tip</h3>
            <p class="text-xs leading-relaxed opacity-95">
                Keep your EMI data updated to ensure accurate profit and loss projections on the <strong>Analytics Dashboard</strong>.
            </p>
            <a href="{{ route('expenses.index') }}" class="inline-block mt-4 text-[10px] font-bold bg-white/20 hover:bg-white/30 px-3 py-1.5 rounded-lg transition-colors">
                Manage Expenses →
            </a>
        </div>
    </div>
</div>
@endsection
