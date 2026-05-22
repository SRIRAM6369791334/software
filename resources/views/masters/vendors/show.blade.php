@extends('layouts.app')
@section('title', 'Vendor Details - ' . $vendor->firm_name)

@section('content')
<div class="cm-page">

    {{-- Back Link --}}
    <a href="{{ route('masters.vendors.index') }}" class="cm-back-btn">
        <span class="material-symbols-rounded" style="font-size: 16px;">arrow_back</span>
        Back to directory
    </a>

    {{-- Top Bar --}}
    <div class="cm-topbar">
        <div class="cm-profile-header">
            <div class="cm-avatar-lg cm-avatar-lg--{{ strtolower(substr($vendor->firm_name, 0, 1)) }}">
                {{ strtoupper(substr($vendor->firm_name, 0, 2)) }}
            </div>
            <div>
                <h1 class="cm-page-title">{{ $vendor->firm_name }}</h1>
                <div class="cm-page-sub">
                    <span class="cm-badge cm-badge--vendor">Supplier Partner</span>
                    <span class="cm-badge cm-badge--route">
                        <span class="material-symbols-rounded" style="font-size: 12px; margin-right: 2px;">alt_route</span>
                        {{ $vendor->route ?: 'General Sector' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="cm-actions-group">
            <a href="{{ route('masters.vendors.edit', $vendor) }}" class="cm-btn-outline">
                <span class="material-symbols-rounded" style="font-size: 16px;">edit</span>
                Edit Profile
            </a>
            <form action="{{ route('masters.vendors.destroy', $vendor) }}" method="POST" onsubmit="return confirm('Archive {{ $vendor->firm_name }}? This will keep their transaction history intact.')" style="display: inline-block;">
                @csrf @method('DELETE')
                <button type="submit" class="cm-btn-danger">
                    <span class="material-symbols-rounded" style="font-size: 16px;">archive</span>
                    Archive
                </button>
            </form>
        </div>
    </div>

    {{-- Layout Grid --}}
    <div class="cm-detail-layout">
        
        {{-- Side Column: Profile & Metadata --}}
        <div class="cm-side-col">
            
            {{-- Profile Card --}}
            <div class="cm-card">
                <h3 class="cm-card-title">
                    <span class="material-symbols-rounded" style="font-size: 16px;">contact_page</span>
                    Profile Credentials
                </h3>
                <div class="cm-info-list">
                    <div class="cm-info-item">
                        <span class="material-symbols-rounded cm-info-icon" style="font-size: 18px;">person</span>
                        <div>
                            <div class="cm-info-label">Contact Person</div>
                            <div class="cm-info-val">{{ $vendor->contact_person ?: 'Not specified' }}</div>
                        </div>
                    </div>
                    <div class="cm-info-item">
                        <span class="material-symbols-rounded cm-info-icon" style="font-size: 18px;">call</span>
                        <div>
                            <div class="cm-info-label">Contact Phone</div>
                            <div class="cm-info-val">{{ $vendor->phone }}</div>
                        </div>
                    </div>
                    <div class="cm-info-item">
                        <span class="material-symbols-rounded cm-info-icon" style="font-size: 18px;">location_on</span>
                        <div>
                            <div class="cm-info-label">Firm Location</div>
                            <div class="cm-info-val">{{ $vendor->location ?: 'Not set' }}</div>
                        </div>
                    </div>
                    <div class="cm-info-item">
                        <span class="material-symbols-rounded cm-info-icon" style="font-size: 18px;">badge</span>
                        <div>
                            <div class="cm-info-label">GSTIN / Registration</div>
                            <div class="cm-info-val cm-info-val--mono">{{ $vendor->gst_number ?: 'Unregistered' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Notes Card --}}
            @if($vendor->notes)
            <div class="cm-card cm-card--notes">
                <h3 class="cm-card-title">
                    <span class="material-symbols-rounded" style="font-size: 16px;">description</span>
                    Vendor Notes
                </h3>
                <div class="cm-notes-body">
                    {{ $vendor->notes }}
                </div>
            </div>
            @endif

        </div>

        {{-- Main Column: Tabs --}}
        <div class="cm-main-col">
            
            <div class="cm-tabs-card">
                {{-- Tabs Navigation --}}
                <div class="cm-tabs-header">
                    <a href="{{ route('masters.vendors.show', $vendor) }}" class="cm-tab-link cm-tab-link--active">
                        Quick Look
                    </a>
                    <a href="{{ route('masters.vendors.purchase-history', $vendor) }}" class="cm-tab-link">
                        Full Purchase History
                    </a>
                </div>

                {{-- Tab Content Pane --}}
                <div class="cm-tab-content">
                    <div class="cm-tab-content-header">
                        <h4 class="cm-tab-title">Recent Supply Activity</h4>
                        <a href="{{ route('purchases.create', ['vendor_name' => $vendor->firm_name]) }}" class="cm-btn-primary cm-btn-primary--sm">
                            <span class="material-symbols-rounded" style="font-size: 14px;">add</span>
                            Record Entry
                        </a>
                    </div>

                    <div class="cm-table-wrap">
                        <table class="cm-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Item Details</th>
                                    <th class="cm-th-right">Quantity</th>
                                    <th class="cm-th-right">Total Bill</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($vendor->purchases()->with('items')->latest()->take(5)->get() as $purchase)
                                <tr class="cm-tr">
                                    <td class="cm-td font-semibold text-slate-700">
                                        {{ $purchase->date->format('d M Y') }}
                                    </td>
                                    <td class="cm-td font-bold text-slate-900">
                                        @if($purchase->items->isNotEmpty())
                                            {{ $purchase->items->pluck('item_name')->join(', ') }}
                                        @else
                                            {{ $purchase->item }}
                                        @endif
                                    </td>
                                    <td class="cm-td cm-td-right font-mono text-slate-600">
                                        @if($purchase->items->isNotEmpty())
                                            {{ number_format($purchase->items->sum('quantity'), 2) }} {{ $purchase->items->first()->unit }}
                                        @else
                                            {{ number_format($purchase->quantity, 2) }} {{ $purchase->unit }}
                                        @endif
                                    </td>
                                    <td class="cm-td cm-td-right font-bold text-slate-900">
                                        Rs {{ number_format($purchase->total_amount, 0) }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="cm-empty">
                                        <div class="cm-empty-icon">
                                            <span class="material-symbols-rounded">inventory_2</span>
                                        </div>
                                        <p class="cm-empty-title">No supplies logged</p>
                                        <p class="cm-empty-sub">No recent transaction entries found for this supplier.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>
@endsection

@push('styles')
<style>
/* ── Reset / Custom CSS Variables (Teal Supplier Matrix) ── */
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

/* ── Container & Layout ── */
.cm-page { padding: 1rem 0 3rem; }

/* ── Back Link ── */
.cm-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--cm-accent-teal);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    text-decoration: none;
    margin-bottom: 1.25rem;
    transition: transform 0.2s ease, color 0.2s ease;
}
.cm-back-btn:hover {
    color: var(--cm-accent-teal-hover);
    transform: translateX(-4px);
}

/* ── Top Bar ── */
.cm-topbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
    gap: 1.5rem;
    flex-wrap: wrap;
}
.cm-profile-header {
    display: flex;
    align-items: center;
    gap: 1.25rem;
}
.cm-avatar-lg {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.375rem;
    font-weight: 700;
    box-shadow: var(--cm-shadow-md);
    background: linear-gradient(135deg, #0d9488, #0ea5e9);
    color: #ffffff;
}

/* Dynamic avatar colors by starting letter */
.cm-avatar-lg--a, .cm-avatar-lg--e, .cm-avatar-lg--i, .cm-avatar-lg--m, .cm-avatar-lg--q, .cm-avatar-lg--u, .cm-avatar-lg--y {
    background: linear-gradient(135deg, #0d9488, #2563eb);
}
.cm-avatar-lg--b, .cm-avatar-lg--f, .cm-avatar-lg--j, .cm-avatar-lg--n, .cm-avatar-lg--r, .cm-avatar-lg--v, .cm-avatar-lg--z {
    background: linear-gradient(135deg, #6366f1, #a855f7);
}
.cm-avatar-lg--c, .cm-avatar-lg--g, .cm-avatar-lg--k, .cm-avatar-lg--o, .cm-avatar-lg--s, .cm-avatar-lg--w {
    background: linear-gradient(135deg, #f59e0b, #ec4899);
}
.cm-avatar-lg--d, .cm-avatar-lg--h, .cm-avatar-lg--l, .cm-avatar-lg--p, .cm-avatar-lg--t, .cm-avatar-lg--x {
    background: linear-gradient(135deg, #ef4444, #f97316);
}

.cm-page-title {
    font-size: 1.625rem;
    font-weight: 800;
    color: var(--cm-text-primary);
    letter-spacing: -0.025em;
    margin: 0;
    line-height: 1.2;
}
.cm-page-sub {
    font-size: 0.8125rem;
    color: var(--cm-text-secondary);
    margin-top: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

/* ── Actions Group ── */
.cm-actions-group {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

/* ── Buttons ── */
.cm-btn-outline {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 18px;
    background: var(--cm-card-bg);
    color: var(--cm-text-secondary);
    border: 1px solid var(--cm-card-border);
    border-radius: 12px;
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s ease;
    text-decoration: none;
    box-shadow: var(--cm-shadow-sm);
}
.cm-btn-outline:hover {
    background: var(--cm-bg);
    color: var(--cm-text-primary);
    border-color: var(--cm-text-secondary);
}

.cm-btn-danger {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 18px;
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fee2e2;
    border-radius: 12px;
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s ease;
    text-decoration: none;
}
.cm-btn-danger:hover {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
    box-shadow: 0 4px 12px rgba(220, 38, 38, 0.15);
}
[data-theme='dark'] .cm-btn-danger {
    background: rgba(220, 38, 38, 0.1);
    border-color: rgba(220, 38, 38, 0.2);
}
[data-theme='dark'] .cm-btn-danger:hover {
    background: #dc2626;
    color: #ffffff;
    border-color: #dc2626;
}

.cm-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: var(--cm-text-primary);
    color: var(--cm-card-bg);
    border: none;
    border-radius: 8px;
    font-size: 0.8125rem;
    font-weight: 600;
    cursor: pointer;
    white-space: nowrap;
    transition: opacity 0.15s;
    text-decoration: none;
}
.cm-btn-primary:hover { opacity: 0.85; }
.cm-btn-primary--sm {
    padding: 6px 12px;
    border-radius: 6px;
    font-size: 0.75rem;
}

/* ── Content Grid ── */
.cm-detail-layout {
    display: grid;
    grid-template-columns: 1fr 2fr;
    gap: 2rem;
}
@media (max-width: 1024px) {
    .cm-detail-layout {
        grid-template-columns: 1fr;
    }
}

/* ── Column 1: Info ── */
.cm-side-col {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.cm-card {
    background: var(--cm-card-bg);
    border: 1px solid var(--cm-card-border);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: var(--cm-shadow-sm);
}

.cm-card-title {
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--cm-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    border-bottom: 1px solid var(--cm-card-border);
    padding-bottom: 0.75rem;
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.cm-info-list {
    display: flex;
    flex-direction: column;
    gap: 1.25rem;
}
.cm-info-item {
    display: flex;
    gap: 10px;
    align-items: flex-start;
}
.cm-info-icon {
    color: var(--cm-text-muted);
    margin-top: 2px;
}
.cm-info-label {
    font-size: 0.6875rem;
    font-weight: 700;
    color: var(--cm-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 2px;
}
.cm-info-val {
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--cm-text-primary);
    word-break: break-word;
}
.cm-info-val--mono {
    font-family: monospace;
    letter-spacing: -0.02em;
}

/* Notes Card special override */
.cm-card--notes {
    border-left: 3px solid var(--cm-accent-teal);
}
.cm-notes-body {
    font-size: 0.8125rem;
    line-height: 1.5;
    color: var(--cm-text-secondary);
    white-space: pre-line;
}

/* ── Column 2: Activity Tabs ── */
.cm-main-col {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.cm-tabs-card {
    background: var(--cm-card-bg);
    border: 1px solid var(--cm-card-border);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--cm-shadow-sm);
}

.cm-tabs-header {
    display: flex;
    border-bottom: 1px solid var(--cm-card-border);
    background: var(--cm-bg);
}
.cm-tab-link {
    flex: 1;
    text-align: center;
    padding: 1.125rem 1rem;
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--cm-text-secondary);
    text-decoration: none;
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
}
.cm-tab-link:hover {
    color: var(--cm-text-primary);
    background: var(--cm-card-bg);
}
.cm-tab-link--active {
    color: var(--cm-accent-teal);
    border-bottom-color: var(--cm-accent-teal);
    background: var(--cm-card-bg);
    font-weight: 700;
}

.cm-tab-content {
    padding: 2rem;
}

.cm-tab-content-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1.5rem;
    gap: 12px;
}

.cm-tab-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--cm-text-primary);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin: 0;
}

/* ── Table Inside Tabs ── */
.cm-table-wrap { overflow-x: auto; }
.cm-table { width: 100%; border-collapse: collapse; font-size: 0.8125rem; }
.cm-table thead tr { border-bottom: 0.5px solid var(--cm-card-border); }
.cm-table th {
    padding: 10px 12px;
    font-size: 0.6875rem;
    font-weight: 600;
    color: var(--cm-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.07em;
    text-align: left;
    background: var(--cm-bg);
    white-space: nowrap;
}
.cm-th-right { text-align: right; }

.cm-tr { transition: background 0.1s; border-bottom: 0.5px solid var(--cm-card-border); }
.cm-tr:hover { background: var(--cm-bg); }
.cm-td {
    padding: 12px;
    vertical-align: middle;
    color: var(--cm-text-primary);
}
.cm-td-right { text-align: right; }
.cm-table tbody tr:last-child .cm-td { border-bottom: none; }

/* ── Badges ── */
.cm-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 4px 10px;
    border-radius: 8px;
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.cm-badge--vendor    { background: var(--cm-accent-teal-light); color: var(--cm-accent-teal); }
.cm-badge--route     { background: rgba(107, 114, 128, 0.06); color: var(--cm-text-secondary); border: 1px solid var(--cm-card-border); }

/* ── Empty State ── */
.cm-empty { padding: 3rem 1rem; text-align: center; }
.cm-empty-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: var(--cm-bg);
    color: var(--cm-text-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
}
.cm-empty-title { font-size: 0.875rem; font-weight: 700; color: var(--cm-text-primary); margin-bottom: 4px; }
.cm-empty-sub   { font-size: 0.8125rem; color: var(--cm-text-muted); }
</style>
@endpush
