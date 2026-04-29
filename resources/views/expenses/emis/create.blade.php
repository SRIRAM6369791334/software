@extends('layouts.app')
@section('title', 'Setup New EMI')

@section('content')
<div class="mb-6">
    <a href="{{ route('expenses.emis.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to EMIs</a>
    <h1 class="text-2xl font-bold text-gray-900">Setup New EMI</h1>
    <p class="text-sm text-gray-500 mt-0.5">Define periodic installment details</p>
</div>

<div class="max-w-2xl text-sm">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('expenses.emis.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Loan / EMI Name <span class="text-red-500">*</span></label>
                    <input type="text" name="item" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all" placeholder="e.g. Poultry House Loan, Vehicle EMI">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Monthly Amount (₹) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" step="0.01" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Due Date <span class="text-red-500">*</span></label>
                        <input type="date" name="due_date" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Current Status</label>
                        <select name="status" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                            <option value="Upcoming">Upcoming</option>
                            <option value="Paid">Already Paid</option>
                            <option value="Overdue">Overdue</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                <button type="reset" class="px-6 py-2.5 text-gray-500 font-bold hover:text-gray-700">Clear</button>
                <button type="submit" class="px-10 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-lg shadow-emerald-600/20 transition-all active:scale-95">
                    Save EMI Schedule 📋
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
