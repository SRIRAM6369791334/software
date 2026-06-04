@extends('layouts.app')
@section('title', 'Add New Expense')

@section('content')
<div class="cm-page">
    
    <div class="cm-topbar">
        <div>
            <a href="{{ route('expenses.index') }}" class="cm-back-link">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
                </svg>
                Back to Expenses
            </a>
            <h1 class="cm-page-title" style="margin-top: 4px;">Record New Expense</h1>
            <p class="cm-page-sub">Log an operational or miscellaneous expenditure</p>
        </div>
    </div>

    <div class="cm-form-container">
        <form action="{{ route('expenses.store') }}" method="POST" class="cm-card-form">
            @csrf
            
            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Date <span class="cm-required">*</span></label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Category <span class="cm-required">*</span></label>
                    <select name="category" required class="cm-form-input" style="appearance: auto;">
                        <option value="Feed">Feed</option>
                        <option value="Medicine">Medicine</option>
                        <option value="Labor">Labor</option>
                        <option value="Electricity">Electricity</option>
                        <option value="Transport">Transport</option>
                        <option value="Miscellaneous">Miscellaneous</option>
                    </select>
                </div>
            </div>

            <div class="cm-form-group">
                <label class="cm-form-label">Amount (Rs) <span class="cm-required">*</span></label>
                <input type="number" name="amount" step="0.01" required class="cm-form-input" placeholder="0.00" style="font-size: 1.25rem; font-weight: 600;">
            </div>

            <div class="cm-form-group">
                <label class="cm-form-label">Description</label>
                <textarea name="description" rows="3" class="cm-form-input" placeholder="Optional notes about this expense..."></textarea>
            </div>

            <div class="cm-form-footer">
                <button type="reset" class="cm-btn-ghost">Clear</button>
                <button type="submit" class="cm-btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Save Expense
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
/* ── Theme Variables ── */
:root {
    --cm-bg: #f8fafc;
    --cm-card-bg: #ffffff;
    --cm-card-border: #e2e8f0;
    --cm-text-primary: #0f172a;
    --cm-text-secondary: #475569;
    --cm-text-muted: #94a3b8;
    --cm-accent-emerald: #10b981;
    --cm-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --cm-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
}
[data-theme='dark'] {
    --cm-bg: #090d16;
    --cm-card-bg: #111827;
    --cm-card-border: #1f2937;
    --cm-text-primary: #f3f4f6;
    --cm-text-secondary: #9ca3af;
    --cm-text-muted: #6b7280;
    --cm-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.5);
    --cm-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -2px rgba(0, 0, 0, 0.3);
}

.cm-page { padding: 1rem 0 3rem; }
.cm-topbar { margin-bottom: 1.5rem; }
.cm-back-link {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 0.75rem; font-weight: 600; color: var(--cm-accent-emerald);
    text-transform: uppercase; letter-spacing: 0.05em; text-decoration: none;
}
.cm-page-title { font-size: 1.375rem; font-weight: 600; color: var(--cm-text-primary); }
.cm-page-sub { font-size: 0.8125rem; color: var(--cm-text-muted); }

.cm-form-container { max-width: 600px; }
.cm-card-form {
    background: var(--cm-card-bg);
    border: 0.5px solid var(--cm-card-border);
    border-radius: 12px;
    padding: 1.5rem;
    box-shadow: var(--cm-shadow-md);
}

.cm-form-grid {
    display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px;
}
@media (max-width: 480px) { .cm-form-grid { grid-template-columns: 1fr; } }
.cm-form-group { margin-bottom: 12px; }
.cm-form-label {
    display: block; font-size: 0.6875rem; font-weight: 500;
    color: var(--cm-text-secondary); text-transform: uppercase;
    letter-spacing: 0.07em; margin-bottom: 5px;
}
.cm-required { color: #dc2626; }
.cm-form-input {
    display: block; width: 100%; padding: 8px 10px;
    border: 0.5px solid var(--cm-card-border); border-radius: 8px;
    font-size: 0.8125rem; background: var(--cm-bg);
    color: var(--cm-text-primary); outline: none; font-family: inherit;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.cm-form-input:focus {
    border-color: var(--cm-text-secondary);
    box-shadow: 0 0 0 3px rgba(148,163,184,0.15);
    background: var(--cm-card-bg);
}

.cm-form-footer {
    display: flex; justify-content: flex-end; align-items: center; gap: 8px;
    padding-top: 1.25rem; margin-top: 1.25rem;
    border-top: 0.5px solid var(--cm-card-border);
}
.cm-btn-ghost {
    padding: 8px 14px; background: transparent; border: none; border-radius: 8px;
    font-size: 0.8125rem; color: var(--cm-text-secondary); cursor: pointer;
}
.cm-btn-ghost:hover { background: var(--cm-bg); color: var(--cm-text-primary); }
.cm-btn-primary {
    display: inline-flex; align-items: center; gap: 6px; padding: 8px 16px;
    background: #0f172a; color: #fff; border: none; border-radius: 8px;
    font-size: 0.8125rem; font-weight: 500; cursor: pointer;
}
.cm-btn-primary:hover { opacity: 0.85; }
</style>
@endpush
