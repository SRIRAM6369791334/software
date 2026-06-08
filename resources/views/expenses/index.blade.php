@extends('layouts.app')
@section('title', 'Expenses & EMI')

@section('content')
<div class="cm-page">
    
    {{-- Top Bar --}}
    <div class="cm-topbar">
        <div>
            <h1 class="cm-page-title">Expenses & EMI</h1>
            <p class="cm-page-sub">Manage operational burn and financial obligations</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('expenses.export') }}" class="cm-export-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
    </svg>
        Export
    </a>
            <button onclick="document.getElementById('add-expense-modal').classList.remove('cm-hidden')"
                    class="cm-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Record Expense
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="cm-stats">
        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-red">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
            <div>
                <div class="cm-stat-label">Monthly Burn</div>
                <div class="cm-stat-value">Rs {{ number_format($totals['total_expenses'], 0) }}</div>
            </div>
        </div>
        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-amber">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="4" width="20" height="16" rx="2" ry="2"/>
                    <line x1="2" y1="10" x2="22" y2="10"/>
                </svg>
            </div>
            <div>
                <div class="cm-stat-label">EMI Total</div>
                <div class="cm-stat-value">Rs {{ number_format($totals['total_emis'], 0) }}</div>
            </div>
        </div>
        <div class="cm-stat-card cm-stat-card--quote" style="grid-column: span 1; display:flex; flex-direction:column; justify-content:center; align-items:flex-start; padding: 1rem 1.25rem;">
            <div class="cm-stat-label" style="color:var(--cm-accent-emerald);">Financial Outlook</div>
            <div style="font-size: 0.875rem; font-style: italic; color:var(--cm-text-secondary); margin-top: 4px; font-weight: 500;">
                "Optimizing cash flow by tracking every penny."
            </div>
        </div>
    </div>

    {{-- Main Grid --}}
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.5rem;">
        
        {{-- Ledger Table Card --}}
        <div class="cm-table-card">
            <div class="cm-table-toolbar">
                <div style="font-size: 0.9375rem; font-weight: 600; color: var(--cm-text-primary);">General Expense Ledger</div>
            </div>
            <div class="cm-table-wrap">
                <table class="cm-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th class="cm-th-right">Amount</th>
                            <th class="cm-th-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $e)
                            <tr class="cm-tr">
                                <td class="cm-td" style="font-weight: 500;">
                                    {{ $e->date->format('M d, Y') }}
                                </td>
                                <td class="cm-td">
                                    <span style="padding: 2px 8px; border-radius: 6px; font-size: 0.6875rem; font-weight: 600; background: var(--cm-bg); color: var(--cm-text-secondary); border: 0.5px solid var(--cm-card-border); text-transform: uppercase;">
                                        {{ $e->category }}
                                    </span>
                                </td>
                                <td class="cm-td">
                                    {{ $e->description }}
                                </td>
                                <td class="cm-td cm-td-right" style="color: #dc2626; font-weight: 600;">
                                    Rs {{ number_format($e->amount, 0) }}
                                </td>
                                <td class="cm-td">
                                    <div class="cm-actions">
                                        <form action="{{ route('expenses.destroy', $e) }}" method="POST" onsubmit="return confirm('Delete this expense entry?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="cm-action-btn cm-action-btn--danger" title="Delete">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round">
                                                    <polyline points="3 6 5 6 21 6"/>
                                                    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                    <path d="M10 11v6"/><path d="M14 11v6"/>
                                                    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="cm-empty">
                                    <div class="cm-empty-icon">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                            <rect x="2" y="4" width="20" height="16" rx="2" ry="2"/>
                                            <line x1="2" y1="10" x2="22" y2="10"/>
                                        </svg>
                                    </div>
                                    <p class="cm-empty-title">No expenses recorded</p>
                                    <p class="cm-empty-sub">No expenses logged in this cycle.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($expenses->hasPages())
                <div class="cm-pagination">
                    <span class="cm-pg-info">
                        Showing {{ $expenses->firstItem() }}–{{ $expenses->lastItem() }} of {{ $expenses->total() }}
                    </span>
                    <div class="cm-pg-links">
                        {{ $expenses->links() }}
                    </div>
                </div>
            @endif
        </div>

        {{-- EMI obligations --}}
        <div>
            <div style="font-size: 0.8125rem; font-weight: 600; color: var(--cm-text-secondary); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 1rem;">
                EMI Obligations
            </div>
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                @forelse($emis as $emi)
                    @php
                        $statusMap = [
                            'Paid' => ['bg' => '#d1fae5', 'color' => '#065f46', 'border' => '#a7f3d0', 'icon' => '✅'],
                            'Overdue' => ['bg' => '#fee2e2', 'color' => '#991b1b', 'border' => '#fecaca', 'icon' => '⚠️'],
                            'Upcoming' => ['bg' => '#dbeafe', 'color' => '#1e40af', 'border' => '#bfdbfe', 'icon' => '⏳']
                        ];
                        $style = $statusMap[$emi->status] ?? $statusMap['Upcoming'];
                    @endphp
                    <div style="background: var(--cm-card-bg); border: 0.5px solid var(--cm-card-border); border-radius: 12px; padding: 1.25rem; display: flex; align-items: center; justify-content: space-between; transition: border-color 0.15s; cursor: default;" onmouseover="this.style.borderColor='var(--cm-accent-emerald)'" onmouseout="this.style.borderColor='var(--cm-card-border)'">
                        <div>
                            <div style="font-size: 1rem; font-weight: 600; color: var(--cm-text-primary); margin-bottom: 4px;">{{ $emi->item }}</div>
                            <div style="display: flex; align-items: center; gap: 8px;">
                                <span style="font-size: 0.6875rem; color: var(--cm-text-muted); text-transform: uppercase;">Due: {{ $emi->due_date->format('M d, Y') }}</span>
                                <span style="padding: 2px 6px; border-radius: 4px; font-size: 0.625rem; font-weight: 700; text-transform: uppercase; background: {{ $style['bg'] }}; color: {{ $style['color'] }}; border: 1px solid {{ $style['border'] }};">
                                    {{ $emi->status }}
                                </span>
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 1.125rem; font-weight: 600; color: var(--cm-text-primary);">Rs {{ number_format($emi->amount, 0) }}</div>
                            <div style="font-size: 0.75rem; margin-top: 2px;">{{ $style['icon'] }}</div>
                        </div>
                    </div>
                @empty
                    <div class="cm-empty" style="background: var(--cm-card-bg); border: 0.5px dashed var(--cm-card-border); border-radius: 12px;">
                        <p class="cm-empty-title">No Active EMIs</p>
                        <p class="cm-empty-sub">You have no upcoming EMI payments.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- Add Expense Modal --}}
<div id="add-expense-modal" class="cm-modal-overlay cm-hidden">
    <div class="cm-modal">
        <div class="cm-modal-header">
            <div class="cm-modal-title-row">
                <div class="cm-modal-icon cm-modal-icon--green">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </div>
                <div>
                    <div class="cm-modal-title">Record Expense</div>
                    <div class="cm-modal-sub">Log operational expenditures</div>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('add-expense-modal').classList.add('cm-hidden')" class="cm-close-btn" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('expenses.store') }}" method="POST" class="cm-modal-body">
            @csrf
            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Category <span class="cm-required">*</span></label>
                    <select name="category" required class="cm-form-input" style="appearance: auto;">
                        @foreach(['Fuel','Salary','Transport','Utility','Misc'] as $c)
                            <option value="{{ $c }}">{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Date <span class="cm-required">*</span></label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}" class="cm-form-input">
                </div>
            </div>

            <div class="cm-form-group">
                <label class="cm-form-label">Description <span class="cm-required">*</span></label>
                <input type="text" name="description" required placeholder="What was this expense for?" class="cm-form-input">
            </div>

            <div class="cm-form-group">
                <label class="cm-form-label">Amount (Rs) <span class="cm-required">*</span></label>
                <input type="number" name="amount" required step="0.01" min="0.01" placeholder="0.00" class="cm-form-input" style="font-size: 1.25rem; font-weight: 600;">
            </div>

            <div class="cm-modal-footer">
                <button type="button" onclick="document.getElementById('add-expense-modal').classList.add('cm-hidden')" class="cm-btn-ghost">Cancel</button>
                <button type="submit" class="cm-btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Log Expense
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
@include('partials.cm-style')
@endpush

@push('scripts')
<script>
document.querySelectorAll('.cm-modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('cm-hidden');
    });
});
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.cm-modal-overlay').forEach(m => m.classList.add('cm-hidden'));
    }
});
</script>
@endpush

