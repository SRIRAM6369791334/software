@extends('layouts.app')
@section('title', 'Add New Expense')

@section('content')
<div class="mb-6">
    <a href="{{ route('expenses.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Expenses</a>
    <h1 class="text-2xl font-bold text-gray-900">Record New Expense</h1>
    <p class="text-sm text-gray-500 mt-0.5">Log an operational or miscellaneous expenditure</p>
</div>

<div class="max-w-2xl text-sm">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('expenses.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Date <span class="text-red-500">*</span></label>
                        <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Category <span class="text-red-500">*</span></label>
                        <select name="category" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg">
                            <option value="Feed">Feed</option>
                            <option value="Medicine">Medicine</option>
                            <option value="Labor">Labor</option>
                            <option value="Electricity">Electricity</option>
                            <option value="Transport">Transport</option>
                            <option value="Miscellaneous">Miscellaneous</option>
                        </select>
                    </div>
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Amount (₹) <span class="text-red-500">*</span></label>
                    <input type="number" name="amount" step="0.01" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all" placeholder="0.00">
                </div>

                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all" placeholder="Optional notes about this expense..."></textarea>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                <button type="reset" class="px-6 py-2.5 text-gray-500 font-bold hover:text-gray-700">Clear</button>
                <button type="submit" class="px-10 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-lg shadow-emerald-600/20 transition-all active:scale-95">
                    Save Expense 💰
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
