@extends('layouts.app')
@section('title', 'Expenses & EMI')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Expenses & EMI</h1>
        <p class="text-sm text-gray-500 mt-0.5">Track operational costs and EMI schedules</p>
    </div>
    <div class="flex gap-2">
        <button onclick="document.getElementById('add-expense-modal').classList.remove('hidden')"
                class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-sm">+ Add Expense</button>
        <a href="{{ route('expenses.export') }}" class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-sm font-medium rounded-lg">⬇ CSV</a>
    </div>
</div>

{{-- Summary cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-center gap-4">
        <div class="rounded-xl bg-red-50 text-red-600 p-3 text-xl">💸</div>
        <div>
            <p class="text-xs text-gray-400">Total Expenses (This Month)</p>
            <p class="text-xl font-bold">₹{{ number_format($totals['total_expenses'], 0, '.', ',') }}</p>
        </div>
    </div>
    <div class="bg-white rounded-xl border border-gray-200 p-5 shadow-sm flex items-center gap-4">
        <div class="rounded-xl bg-amber-50 text-amber-600 p-3 text-xl">🏦</div>
        <div>
            <p class="text-xs text-gray-400">Total EMI Outstanding</p>
            <p class="text-xl font-bold">₹{{ number_format($totals['total_emis'], 0, '.', ',') }}</p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
    {{-- Expense Table --}}
    <div class="lg:col-span-3 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="text-base font-semibold">Expenses</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 bg-gray-50">
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Date</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Category</th>
                        <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Description</th>
                        <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase">Amount</th>
                        <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-400 uppercase">Del</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($expenses as $e)
                        <tr class="hover:bg-gray-50/50">
                            <td class="px-5 py-3.5 text-gray-500">{{ $e->date->format('d M Y') }}</td>
                            <td class="px-5 py-3.5"><span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs rounded-full">{{ $e->category }}</span></td>
                            <td class="px-5 py-3.5 text-gray-700">{{ $e->description }}</td>
                            <td class="px-5 py-3.5 text-right font-mono font-semibold text-red-600">₹{{ number_format($e->amount, 0, '.', ',') }}</td>
                            <td class="px-5 py-3.5 text-center">
                                <form action="{{ route('expenses.destroy', $e) }}" method="POST"
                                      onsubmit="return confirm('Delete this expense?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-600 transition-colors">🗑️</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">No expenses recorded</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-5 py-3 border-t border-gray-100">{{ $expenses->links() }}</div>
    </div>

    {{-- EMI Cards --}}
    <div class="lg:col-span-2 space-y-3">
        <h2 class="text-base font-semibold text-gray-900 mb-3">EMI Schedule</h2>
        @forelse($emis as $emi)
            @php
                $statusColors = ['Upcoming'=>'bg-blue-50 text-blue-700','Paid'=>'bg-emerald-50 text-emerald-700','Overdue'=>'bg-red-50 text-red-700'];
            @endphp
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-4 flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-gray-900">{{ $emi->item }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">Due: {{ $emi->due_date->format('d M Y') }}</p>
                </div>
                <div class="text-right">
                    <p class="font-bold text-sm text-gray-900">₹{{ number_format($emi->amount, 0, '.', ',') }}</p>
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium mt-1 inline-block {{ $statusColors[$emi->status] ?? 'bg-gray-100 text-gray-600' }}">{{ $emi->status }}</span>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-xl border border-gray-200 p-6 text-center text-gray-400 text-sm">No EMIs scheduled</div>
        @endforelse
    </div>
</div>

<div id="add-expense-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-base font-semibold text-gray-900">Add Expense</h2>
            <button onclick="document.getElementById('add-expense-modal').classList.add('hidden')" class="text-gray-400 text-xl">✕</button>
        </div>
        <form action="{{ route('expenses.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Category *</label>
                    <select name="category" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none">
                        @foreach(['Fuel','Salary','Transport','Utility','Misc'] as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach
                    </select></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Date *</label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Description *</label>
                <input type="text" name="description" required placeholder="Describe the expense"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Amount (₹) *</label>
                <input type="number" name="amount" required step="0.01" min="0.01" placeholder="0.00"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('add-expense-modal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Add Expense</button>
            </div>
        </form>
    </div>
</div>
@endsection
