@extends('layouts.app')
@section('title', 'Purchase Invoices')

@section('content')
<div class="cm-page">

    {{-- Top Bar Header --}}
    <div class="cm-topbar">
        <div>
            <h1 class="cm-page-title">Purchase Invoice Archive</h1>
            <p class="cm-page-sub">Comprehensive audit history and payment states of all procured supplies</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('purchases.entry') }}" class="cm-btn-secondary flex items-center gap-1.5">
                <span class="material-symbols-rounded" style="font-size: 18px;">arrow_back</span>
                Purchase Entry
            </a>
            <a href="{{ route('purchases.export') }}" class="cm-export-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
    </svg>
        Export
    </a>
        </div>
    </div>

    {{-- Summary Cards Grid --}}
    @php
        $totalInvoices = \App\Models\Purchase::count();
        $totalExpenditure = \App\Models\Purchase::sum('total_amount');
        $totalTaxPaid = \App\Models\Purchase::sum('gst_amount');
    @endphp
    <div class="cm-stats-grid mb-8">
        <div class="cm-stats-card">
            <div class="cm-stats-header">
                <span class="cm-stats-title">Archived Invoices</span>
                <span class="material-symbols-rounded text-blue-500 cm-stats-icon bg-blue-50 dark:bg-blue-950/40">receipt_long</span>
            </div>
            <div class="cm-stats-value">{{ number_format($totalInvoices) }}</div>
            <p class="cm-stats-sub">Cumulative recorded purchases</p>
        </div>

        <div class="cm-stats-card cm-stats-card--highlight">
            <div class="cm-stats-header">
                <span class="cm-stats-title text-emerald-800 dark:text-emerald-200">Total Expenditure</span>
                <span class="material-symbols-rounded text-emerald-600 cm-stats-icon bg-emerald-100 dark:bg-emerald-950/40">payments</span>
            </div>
            <div class="cm-stats-value text-emerald-700 dark:text-emerald-400">₹{{ number_format($totalExpenditure, 2) }}</div>
            <p class="cm-stats-sub text-emerald-600/80 dark:text-emerald-500">Net grand total of procurement</p>
        </div>

        <div class="cm-stats-card">
            <div class="cm-stats-header">
                <span class="cm-stats-title">Tax Contribution (GST)</span>
                <span class="material-symbols-rounded text-amber-500 cm-stats-icon bg-amber-50 dark:bg-amber-950/40">percent</span>
            </div>
            <div class="cm-stats-value">₹{{ number_format($totalTaxPaid, 2) }}</div>
            <p class="cm-stats-sub">Consolidated calculated tax</p>
        </div>
    </div>

    {{-- Invoices Table Card --}}
    <div class="cm-table-card">
        <div class="cm-table-toolbar">
            <h2 class="cm-toolbar-title">Transaction Ledger</h2>
            <form method="GET" class="cm-search-wrap">
                <span class="material-symbols-rounded cm-search-icon">search</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search vendor or product name..." class="cm-search-input">
            </form>
        </div>

        <div class="cm-table-wrap">
            <table class="cm-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Vendor Name</th>
                        <th style="width: 25%;">Primary Item Summary</th>
                        <th style="width: 15%;">Billing Date</th>
                        <th style="width: 12%; text-align: right;">GST Amount</th>
                        <th style="width: 13%; text-align: right;">Total Net Amount</th>
                        <th style="width: 10%; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $p)
                        <tr class="cm-tr">
                            <td class="cm-td">
                                <div class="cm-identity">
                                    <div class="cm-avatar cm-avatar--{{ strtolower(substr($p->vendor_name, 0, 1)) }}">
                                        {{ strtoupper(substr($p->vendor_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="cm-cust-name">{{ $p->vendor_name }}</span>
                                        <span class="cm-cust-meta">Invoice ID: {{ $p->invoice_no ?: 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="cm-td">
                                @php
                                    $firstItem = $p->items->first();
                                    $othersCount = $p->items->count() - 1;
                                @endphp
                                <div class="flex flex-col gap-1">
                                    @if($firstItem)
                                        <span class="cm-item-chip">
                                            {{ $firstItem->item_name }} 
                                            <b class="ml-1">({{ number_format($firstItem->quantity) }} {{ $firstItem->unit }})</b>
                                        </span>
                                    @else
                                        <span class="text-slate-400 italic">No products recorded</span>
                                    @endif
                                    
                                    @if($othersCount > 0)
                                        <span class="text-[10px] text-teal-600 dark:text-teal-400 font-bold uppercase tracking-widest pl-1">
                                            + {{ $othersCount }} other product{{ $othersCount > 1 ? 's' : '' }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="cm-td text-slate-500 font-medium">
                                {{ $p->date->format('d M Y') }}
                            </td>
                            <td class="cm-td text-right font-mono text-slate-500">
                                ₹{{ number_format($p->gst_amount, 2) }}
                            </td>
                            <td class="cm-td text-right font-bold text-slate-900 dark:text-slate-100">
                                ₹{{ number_format($p->total_amount, 2) }}
                            </td>
                            <td class="cm-td">
                                <div class="cm-actions justify-center">
                                    <a href="{{ route('purchases.show', $p->id) }}" class="cm-action-btn cm-action-btn--edit text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/40" title="View Details">
                                        <span class="material-symbols-rounded">visibility</span>
                                    </a>
                                    <a href="{{ route('purchases.edit', $p->id) }}" class="cm-action-btn cm-action-btn--edit text-teal-600 hover:bg-teal-50 dark:hover:bg-teal-950/40" title="Edit Purchase">
                                        <span class="material-symbols-rounded">edit</span>
                                    </a>
                                    <form action="{{ route('purchases.destroy', $p->id) }}" method="POST" class="delete-form inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)" class="cm-action-btn cm-action-btn--delete text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40" title="Delete Invoice">
                                            <span class="material-symbols-rounded">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="cm-empty">
                                <div class="cm-empty-icon">
                                    <span class="material-symbols-rounded" style="font-size: 32px;">receipt</span>
                                </div>
                                <div class="cm-empty-title">No invoices found</div>
                                <div class="cm-empty-sub">Your query didn't match any recorded transactions</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="cm-pagination-footer">
                {{ $purchases->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@push('styles')
<style>
/* ── Theme Variables & Dark Mode Matrix ── */
:root {
    --cm-bg: #f8fafc;
    --cm-card-bg: #ffffff;
    --cm-card-border: #e2e8f0;
    --cm-text-primary: #0f172a;
    --cm-text-secondary: #475569;
    --cm-text-muted: #94a3b8;
    --cm-accent-teal: #0d9488;
    --cm-accent-teal-hover: #0f766e;
    --cm-accent-teal-light: #f0fdfa;
    --cm-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --cm-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
    --cm-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -4px rgba(0, 0, 0, 0.05);
}

[data-theme='dark'] {
    --cm-bg: #090d16;
    --cm-card-bg: #111827;
    --cm-card-border: #1f2937;
    --cm-text-primary: #f3f4f6;
    --cm-text-secondary: #9ca3af;
    --cm-text-muted: #6b7280;
    --cm-accent-teal-light: rgba(13, 148, 136, 0.1);
    --cm-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.5);
    --cm-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -2px rgba(0, 0, 0, 0.3);
    --cm-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -4px rgba(0, 0, 0, 0.4);
}

.cm-page { padding: 1rem 0 3rem; }

/* ── Top Bar ── */
.cm-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    gap: 1rem;
    flex-wrap: wrap;
}
.cm-page-title {
    font-size: 1.375rem;
    font-weight: 700;
    color: var(--cm-text-primary);
    letter-spacing: -0.02em;
}
.cm-page-sub {
    font-size: 0.8125rem;
    color: var(--cm-text-secondary);
    margin-top: 2px;
}

/* ── Buttons ── */
.cm-btn-secondary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    background: var(--cm-card-bg);
    color: var(--cm-text-secondary);
    border: 1px solid var(--cm-card-border);
    border-radius: 8px;
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.15s, color 0.15s, border-color 0.15s;
    text-decoration: none;
}
.cm-btn-secondary:hover {
    background: var(--cm-bg);
    color: var(--cm-text-primary);
    border-color: var(--cm-text-muted);
}
.cm-btn-secondary .material-symbols-rounded { font-size: 16px; }

.cm-btn-ghost {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 14px;
    background: transparent;
    border: none;
    border-radius: 8px;
    font-size: 0.8125rem;
    color: var(--cm-text-secondary);
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
    text-decoration: none;
}
.cm-btn-ghost:hover { background: var(--cm-card-border); color: var(--cm-text-primary); }

/* ── Stats Dashboard Grid ── */
.cm-stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1.25rem;
}
@media (max-width: 768px) {
    .cm-stats-grid { grid-template-columns: 1fr; }
}
.cm-stats-card {
    background: var(--cm-card-bg);
    border: 1px solid var(--cm-card-border);
    border-radius: 14px;
    padding: 1.25rem 1.5rem;
    box-shadow: var(--cm-shadow-sm);
    display: flex;
    flex-direction: column;
}
.cm-stats-card--highlight {
    background: linear-gradient(135deg, var(--cm-card-bg), var(--cm-accent-teal-light));
    border-color: rgba(13, 148, 136, 0.2);
}
.cm-stats-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.5rem;
}
.cm-stats-title {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--cm-text-secondary);
}
.cm-stats-icon {
    width: 32px;
    height: 32px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 18px;
}
.cm-stats-value {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--cm-text-primary);
    line-height: 1.2;
}
.cm-stats-sub {
    font-size: 0.75rem;
    color: var(--cm-text-muted);
    margin-top: 4px;
}

/* ── Table Card ── */
.cm-table-card {
    background: var(--cm-card-bg);
    border: 1px solid var(--cm-card-border);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--cm-shadow-sm);
}
.cm-table-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--cm-card-border);
    gap: 10px;
    flex-wrap: wrap;
}
.cm-toolbar-title { font-size: 0.9375rem; font-weight: 700; color: var(--cm-text-primary); }

.cm-search-wrap { position: relative; width: 100%; max-width: 280px; }
.cm-search-icon { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); color: var(--cm-text-muted); font-size: 18px; pointer-events: none; }
.cm-search-input { width: 100%; padding: 7px 12px 7px 34px; border: 1px solid var(--cm-card-border); border-radius: 8px; font-size: 0.8125rem; background: var(--cm-bg); color: var(--cm-text-primary); outline: none; transition: border-color 0.15s; }
.cm-search-input:focus { border-color: var(--cm-text-muted); }

/* ── Table ── */

.cm-table { width: 100%; border-collapse: collapse; font-size: 0.8125rem; }
.cm-table th {
    padding: 10px 14px;
    font-size: 0.6875rem;
    font-weight: 700;
    color: var(--cm-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    text-align: left;
    background: var(--cm-bg);
    white-space: nowrap;
}
.cm-tr { border-bottom: 1px solid var(--cm-card-border); transition: background-color 0.1s; }
.cm-tr:hover { background-color: var(--cm-bg); }
.cm-td { padding: 12px 14px; vertical-align: middle; color: var(--cm-text-primary); }

.cm-identity { display: flex; align-items: center; gap: 10px; }
.cm-avatar {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    flex-shrink: 0;
}
.cm-avatar--a, .cm-avatar--e, .cm-avatar--i, .cm-avatar--m, .cm-avatar--q, .cm-avatar--u, .cm-avatar--y { background: linear-gradient(135deg, #10b981, #3b82f6); color: #ffffff; }
.cm-avatar--b, .cm-avatar--f, .cm-avatar--j, .cm-avatar--n, .cm-avatar--r, .cm-avatar--v, .cm-avatar--z { background: linear-gradient(135deg, #6366f1, #a855f7); color: #ffffff; }
.cm-avatar--c, .cm-avatar--g, .cm-avatar--k, .cm-avatar--o, .cm-avatar--s, .cm-avatar--w { background: linear-gradient(135deg, #f59e0b, #ec4899); color: #ffffff; }
.cm-avatar--d, .cm-avatar--h, .cm-avatar--l, .cm-avatar--p, .cm-avatar--t, .cm-avatar--x { background: linear-gradient(135deg, #ef4444, #f97316); color: #ffffff; }

.cm-cust-name { font-weight: 600; color: var(--cm-text-primary); display: block; }
.cm-cust-meta { font-size: 0.75rem; color: var(--cm-text-muted); margin-top: 1px; display: block; }

.cm-item-chip {
    display: inline-flex;
    align-items: center;
    padding: 3px 8px;
    background: var(--cm-accent-teal-light);
    color: var(--cm-accent-teal);
    border: 1.5px solid rgba(13, 148, 136, 0.08);
    font-size: 0.6875rem;
    font-weight: 700;
    border-radius: 20px;
    max-width: max-content;
}

/* Actions list */
.cm-actions { display: flex; align-items: center; gap: 6px; }
.cm-action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    border: none;
    background: transparent;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
    text-decoration: none;
}
.cm-action-btn .material-symbols-rounded { font-size: 16px; }

/* Empty state */
.cm-empty { padding: 3rem 1.5rem; text-align: center; color: var(--cm-text-muted); }
.cm-empty-icon {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: var(--cm-bg);
    color: var(--cm-text-muted);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
}
.cm-empty-title { font-size: 0.875rem; font-weight: 700; color: var(--cm-text-primary); margin-bottom: 2px; }
.cm-empty-sub { font-size: 0.75rem; color: var(--cm-text-muted); }

/* Pagination footer overlay */
.cm-pagination-footer {
    padding: 1rem 1.25rem;
    border-top: 1px solid var(--cm-card-border);
}
</style>
@endpush

@push('scripts')
<script>
    function confirmDelete(button) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete this purchase invoice and revert its stock movements!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d9488',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            background: document.documentElement.dataset.theme === 'dark' ? '#111827' : '#ffffff',
            color: document.documentElement.dataset.theme === 'dark' ? '#f3f4f6' : '#0f172a'
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('.delete-form').submit();
            }
        });
    }
</script>
@endpush

