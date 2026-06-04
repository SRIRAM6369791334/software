@extends('layouts.app')
@section('title', 'Setup New EMI')

@section('content')
<div class="mb-6">
    <a href="{{ route('expenses.emis.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to EMIs</a>
    <h1 class="text-2xl font-bold text-slate-950">Setup New EMI</h1>
    <p class="text-sm text-slate-500 mt-0.5">Define periodic installment details</p>
</div>

<div class="max-w-2xl text-sm">
    <div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-xl border border-slate-200 shadow-sm overflow-hidden">
        <form action="{{ route('expenses.emis.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="space-y-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-tight">EMI Type <span class="text-red-500">*</span></label>
                    <select name="emi_type" id="emi_type" class="w-full px-4 py-2 bg-emerald-50 border border-slate-200 rounded-lg" onchange="toggleEntitySelect()">
                        <option value="Bank Loan">Bank Loan / Finance</option>
                        <option value="Customer">Customer</option>
                        <option value="Dealer">Dealer</option>
                    </select>
                </div>

                <div class="space-y-1.5" id="loan_name_div">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-tight">Loan / EMI Name <span class="text-red-500">*</span></label>
                    <input type="text" name="loan_name" id="loan_name" class="w-full px-4 py-2 bg-emerald-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all" placeholder="e.g. Poultry House Loan, Vehicle EMI">
                </div>

                <div class="space-y-1.5 hidden" id="entity_div">
                    <label class="text-xs font-bold text-slate-700 uppercase tracking-tight" id="entity_label">Select Person <span class="text-red-500">*</span></label>
                    <select name="entity_id" id="entity_id" class="w-full px-4 py-2 bg-emerald-50 border border-slate-200 rounded-lg">
                        <option value="">-- Select --</option>
                    </select>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-tight">Monthly Amount (Rs ) <span class="text-red-500">*</span></label>
                        <input type="number" name="amount" step="0.01" required class="w-full px-4 py-2 bg-emerald-50 border border-slate-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 outline-none transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-tight">Due Date <span class="text-red-500">*</span></label>
                        <input type="date" name="due_date" required class="w-full px-4 py-2 bg-emerald-50 border border-slate-200 rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-700 uppercase tracking-tight">Current Status</label>
                        <select name="status" class="w-full px-4 py-2 bg-emerald-50 border border-slate-200 rounded-lg">
                            <option value="Upcoming">Upcoming</option>
                            <option value="Paid">Already Paid</option>
                            <option value="Overdue">Overdue</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-200 flex justify-end gap-3">
                <button type="reset" class="px-6 py-2.5 text-slate-500 font-bold hover:text-slate-700">Clear</button>
                <button type="submit" class="px-10 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-lg shadow-emerald-600/20 transition-all active:scale-95">
                    Save EMI Schedule 
                </button>
            </div>
        </form>
    </div>
</div>
</div>

<script>
    const customers = @json($customers ?? []);
    const dealers = @json($dealers ?? []);
    
    function toggleEntitySelect() {
        const type = document.getElementById('emi_type').value;
        const entityDiv = document.getElementById('entity_div');
        const entityLabel = document.getElementById('entity_label');
        const entitySelect = document.getElementById('entity_id');
        const loanNameDiv = document.getElementById('loan_name_div');
        const loanNameInput = document.getElementById('loan_name');
        
        entitySelect.innerHTML = '<option value="">-- Select --</option>';
        
        if (type === 'Bank Loan') {
            entityDiv.classList.add('hidden');
            entitySelect.required = false;
            
            loanNameDiv.classList.remove('hidden');
            loanNameInput.required = true;
        } else {
            entityDiv.classList.remove('hidden');
            entitySelect.required = true;
            
            loanNameDiv.classList.add('hidden');
            loanNameInput.required = false;
            
            const list = type === 'Customer' ? customers : dealers;
            entityLabel.innerHTML = 'Select ' + type + ' <span class="text-red-500">*</span>';
            
            list.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.text = type === 'Customer' ? item.name : item.firm_name;
                entitySelect.appendChild(option);
            });
        }
    }
    
    document.addEventListener('DOMContentLoaded', toggleEntitySelect);
</script>
@endsection
