@extends('layouts.app')
@section('title', 'Dealer Master')

@section('content')

<div class="cm-page">

    {{-- Top Bar --}}
    <div class="cm-topbar">
        <div>
            <h1 class="cm-page-title">Dealer Master</h1>
            <p class="cm-page-sub">Manage relationships with feed, chick, and medicine suppliers</p>
        </div>
        <button onclick="document.getElementById('add-dealer-modal').classList.remove('cm-hidden')"
            class="cm-btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Register Dealer
        </button>
    </div>

    {{-- Stats --}}
    <div class="cm-stats">
        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-teal">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <div class="cm-stat-label">Total Dealers</div>
                <div class="cm-stat-value">{{ $dealers->total() }}</div>
            </div>
        </div>
        <div class="cm-stat-card cm-stat-card--danger">
            <div class="cm-stat-icon cm-icon-red">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
            </div>
            <div>
                <div class="cm-stat-label">Total Payable</div>
                <div class="cm-stat-value">Rs {{ number_format($dealers->sum('pending_amount'), 0) }}</div>
            </div>
        </div>
        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-amber">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                    <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
            </div>
            <div>
                <div class="cm-stat-label">Active Accounts</div>
                <div class="cm-stat-value">{{ $dealers->where('pending_amount', '>', 0)->count() }} with Dues</div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="cm-table-card">
        <div class="cm-table-toolbar">
            <form method="GET" class="cm-search-wrap">
                <svg class="cm-search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" name="search" value="{{ $search }}"
                    placeholder="Search by firm or contact…" class="cm-search-input">
            </form>
        </div>

        <div class="cm-table-wrap">
            <table class="cm-table">
                <thead>
                    <tr>
                        <th>Firm & Location</th>
                        <th>Point of Contact</th>
                        <th>Operational Area</th>
                        <th class="cm-th-right">Pending Balance</th>
                        <th class="cm-th-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($dealers as $dealer)
                    <tr class="cm-tr">
                        <td class="cm-td">
                            <div class="cm-identity">
                                <div class="cm-avatar cm-avatar--{{ strtolower(substr($dealer->firm_name, 0, 1)) }}">
                                    {{ strtoupper(substr($dealer->firm_name, 0, 2)) }}
                                </div>
                                <div>
                                    <a href="{{ route('masters.dealers.show', $dealer) }}"
                                        class="cm-cust-name">{{ $dealer->firm_name }}</a>
                                    <div class="cm-cust-meta">{{ $dealer->location ?: 'No Location' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="cm-td">
                            <div class="cm-cust-name">{{ $dealer->contact_person ?: '-' }}</div>
                            <div class="cm-cust-meta">{{ $dealer->phone }}</div>
                        </td>
                        <td class="cm-td">
                            <span class="cm-route">{{ $dealer->route ?: 'General' }}</span>
                        </td>
                        <td class="cm-td cm-td-right">
                            @if($dealer->pending_amount > 0)
                                <span class="cm-balance cm-balance--due">Rs {{ number_format($dealer->pending_amount, 0) }}</span>
                            @else
                                <span class="cm-balance cm-balance--clear">Rs 0</span>
                            @endif
                        </td>
                        <td class="cm-td">
                            <div class="cm-actions">
                                <a href="{{ route('masters.dealers.ledger-pdf', $dealer) }}"
                                    class="cm-action-btn" title="Download Ledger PDF">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                        <line x1="16" y1="13" x2="8" y2="13"/>
                                        <line x1="16" y1="17" x2="8" y2="17"/>
                                        <polyline points="10 9 9 9 8 9"/>
                                    </svg>
                                </a>
                                <button
                                    data-id="{{ $dealer->id }}"
                                    data-firm="{{ $dealer->firm_name }}"
                                    data-contact="{{ $dealer->contact_person }}"
                                    data-phone="{{ $dealer->phone }}"
                                    data-gst="{{ $dealer->gst_number }}"
                                    data-location="{{ $dealer->location }}"
                                    data-route="{{ $dealer->route }}"
                                    onclick="openEditDealer(this)"
                                    class="cm-action-btn cm-action-btn--edit" title="Edit Dealer">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('masters.dealers.destroy', $dealer) }}" method="POST"
                                    onsubmit="return confirm('Archive {{ $dealer->firm_name }}?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="cm-action-btn cm-action-btn--danger" title="Archive Dealer">
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
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                                </svg>
                            </div>
                            <p class="cm-empty-title">No dealers found</p>
                            <p class="cm-empty-sub">Start by registering your first supplier.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($dealers->hasPages())
        <div class="cm-pagination">
            <span class="cm-pg-info">
                Showing {{ $dealers->firstItem() }}–{{ $dealers->lastItem() }} of {{ $dealers->total() }} dealers
            </span>
            <div class="cm-pg-links">
                {{ $dealers->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>

</div>

{{-- ================================================ --}}
{{-- ADD DEALER MODAL                                 --}}
{{-- ================================================ --}}
<div id="add-dealer-modal" class="cm-modal-overlay cm-hidden">
    <div class="cm-modal">
        <div class="cm-modal-header">
            <div class="cm-modal-title-row">
                <div class="cm-modal-icon cm-modal-icon--green">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                        <line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/>
                    </svg>
                </div>
                <div>
                    <div class="cm-modal-title">Register Dealer</div>
                    <div class="cm-modal-sub">Onboard a new supplier or partner</div>
                </div>
            </div>
            <button onclick="document.getElementById('add-dealer-modal').classList.add('cm-hidden')"
                class="cm-close-btn" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('masters.dealers.store') }}" method="POST" class="cm-modal-body">
            @csrf
            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Firm Identity <span class="cm-required">*</span></label>
                    <input type="text" name="firm_name" required placeholder="e.g. Superior Feed Mills"
                        class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Contact Person</label>
                    <input type="text" name="contact_person" placeholder="e.g. Manager Name"
                        class="cm-form-input">
                </div>
            </div>

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Phone Number <span class="cm-required">*</span></label>
                    <input type="text" name="phone" required placeholder="+91 00000 00000"
                        class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">GSTIN</label>
                    <input type="text" name="gst_number" placeholder="Optional GSTIN"
                        class="cm-form-input cm-uppercase">
                </div>
            </div>

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Location / City</label>
                    <input type="text" name="location" placeholder="e.g. Industrial Estate"
                        class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Route</label>
                    <input type="text" name="route" placeholder="Supply route..."
                        class="cm-form-input">
                </div>
            </div>

            <div class="cm-form-group">
                <label class="cm-form-label">Opening Outstanding (Rs)</label>
                <input type="number" name="pending_amount" step="0.01" value="0.00"
                    class="cm-form-input">
            </div>

            <div class="cm-modal-footer">
                <button type="button"
                    onclick="document.getElementById('add-dealer-modal').classList.add('cm-hidden')"
                    class="cm-btn-ghost">Discard</button>
                <button type="submit" class="cm-btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Register Dealer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================ --}}
{{-- EDIT DEALER MODAL                                --}}
{{-- ================================================ --}}
<div id="edit-dealer-modal" class="cm-modal-overlay cm-hidden">
    <div class="cm-modal">
        <div class="cm-modal-header">
            <div class="cm-modal-title-row">
                <div class="cm-modal-icon cm-modal-icon--blue">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
                <div>
                    <div class="cm-modal-title">Edit Supplier</div>
                    <div class="cm-modal-sub">Modify existing dealer credentials</div>
                </div>
            </div>
            <button onclick="document.getElementById('edit-dealer-modal').classList.add('cm-hidden')"
                class="cm-close-btn" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form id="edit-dealer-form" method="POST" class="cm-modal-body">
            @csrf @method('PUT')

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Firm Name <span class="cm-required">*</span></label>
                    <input type="text" name="firm_name" id="edit-firm" required class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Contact Person</label>
                    <input type="text" name="contact_person" id="edit-contact" class="cm-form-input">
                </div>
            </div>

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Phone Number <span class="cm-required">*</span></label>
                    <input type="text" name="phone" id="edit-phone" required class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">GSTIN</label>
                    <input type="text" name="gst_number" id="edit-gst" class="cm-form-input cm-uppercase">
                </div>
            </div>

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Location</label>
                    <input type="text" name="location" id="edit-location" class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Route</label>
                    <input type="text" name="route" id="edit-route" class="cm-form-input">
                </div>
            </div>

            <div class="cm-modal-footer">
                <button type="button"
                    onclick="document.getElementById('edit-dealer-modal').classList.add('cm-hidden')"
                    class="cm-btn-ghost">Cancel</button>
                <button type="submit" class="cm-btn-primary cm-btn-primary--blue">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Commit Updates
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
/* ── Reset / Custom CSS Variables (Sleek Theme Matrix) ── */
:root {
    --cm-bg: #f8fafc;
    --cm-card-bg: #ffffff;
    --cm-card-border: #e2e8f0;
    --cm-text-primary: #0f172a;
    --cm-text-secondary: #475569;
    --cm-text-muted: #94a3b8;
    --cm-accent-emerald: #10b981;
    --cm-accent-emerald-hover: #059669;
    --cm-accent-emerald-light: #e6fbf2;
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
    --cm-accent-emerald-light: rgba(16, 185, 129, 0.1);
    --cm-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.5);
    --cm-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -2px rgba(0, 0, 0, 0.3);
    --cm-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -4px rgba(0, 0, 0, 0.4);
}

/* ── Reset & Base ── */
*, *::before, *::after { box-sizing: border-box; }

/* ── Layout ── */
.cm-page { padding: 1rem 0 3rem; }
.cm-hidden { display: none !important; }

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
    font-weight: 600;
    color: var(--cm-text-primary);
    letter-spacing: -0.02em;
}
.cm-page-sub {
    font-size: 0.8125rem;
    color: var(--cm-text-muted);
    margin-top: 2px;
}

/* ── Buttons ── */
.cm-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 8px 16px;
    background: #0f172a;
    color: #fff;
    border: none;
    border-radius: 8px;
    font-size: 0.8125rem;
    font-weight: 500;
    cursor: pointer;
    white-space: nowrap;
    transition: opacity 0.15s;
    text-decoration: none;
}
.cm-btn-primary:hover { opacity: 0.85; }
.cm-btn-primary--blue { background: #1d4ed8; }
.cm-btn-primary--blue:hover { background: #1e40af; opacity: 1; }

.cm-btn-ghost {
    display: inline-flex;
    align-items: center;
    padding: 8px 14px;
    background: transparent;
    border: none;
    border-radius: 8px;
    font-size: 0.8125rem;
    color: var(--cm-text-secondary);
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}
.cm-btn-ghost:hover { background: var(--cm-bg); color: var(--cm-text-primary); }

/* ── Stats ── */
.cm-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 1.5rem;
}
@media (max-width: 900px) { .cm-stats { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 500px) { .cm-stats { grid-template-columns: 1fr; } }

.cm-stat-card {
    background: var(--cm-card-bg);
    border: 0.5px solid var(--cm-card-border);
    border-radius: 12px;
    padding: 1rem 1.125rem;
    display: flex;
    align-items: center;
    gap: 12px;
}
.cm-stat-card--danger { border-color: #fecaca; }

.cm-stat-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.cm-icon-teal  { background: #d1fae5; color: #065f46; }
.cm-icon-blue  { background: #dbeafe; color: #1e40af; }
.cm-icon-amber { background: #fef3c7; color: #92400e; }
.cm-icon-red   { background: #fee2e2; color: #991b1b; }

.cm-stat-label {
    font-size: 0.6875rem;
    color: var(--cm-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.07em;
    margin-bottom: 2px;
}
.cm-stat-value {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--cm-text-primary);
}

/* ── Table Card ── */
.cm-table-card {
    background: var(--cm-card-bg);
    border: 0.5px solid var(--cm-card-border);
    border-radius: 12px;
    overflow: hidden;
}

.cm-table-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.875rem 1.25rem;
    border-bottom: 0.5px solid var(--cm-card-border);
    gap: 10px;
    flex-wrap: wrap;
}
.cm-search-wrap {
    position: relative;
    flex: 1;
    max-width: 320px;
}
.cm-search-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--cm-text-muted);
    pointer-events: none;
}
.cm-search-input {
    width: 100%;
    padding: 7px 12px 7px 34px;
    border: 0.5px solid var(--cm-card-border);
    border-radius: 8px;
    font-size: 0.8125rem;
    background: var(--cm-bg);
    color: var(--cm-text-primary);
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.cm-search-input:focus {
    border-color: var(--cm-text-muted);
    box-shadow: 0 0 0 3px rgba(148,163,184,0.15);
}

/* ── Table ── */
.cm-table-wrap { overflow-x: auto; }
.cm-table { width: 100%; border-collapse: collapse; font-size: 0.8125rem; }
.cm-table thead tr { border-bottom: 0.5px solid var(--cm-card-border); }
.cm-table th {
    padding: 10px 16px;
    font-size: 0.6875rem;
    font-weight: 500;
    color: var(--cm-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.07em;
    text-align: left;
    background: var(--cm-bg);
    white-space: nowrap;
}
.cm-th-right { text-align: right; }
.cm-th-center { text-align: center; }

.cm-tr { transition: background 0.1s; }
.cm-tr:hover { background: var(--cm-bg); }
.cm-td {
    padding: 12px 16px;
    border-bottom: 0.5px solid var(--cm-card-border);
    vertical-align: middle;
    color: var(--cm-text-primary);
}
.cm-table tbody tr:last-child .cm-td { border-bottom: none; }
.cm-td-right { text-align: right; }

/* ── Identity Cell ── */
.cm-identity { display: flex; align-items: center; gap: 10px; }
.cm-avatar {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: #dbeafe;
    color: #1e40af;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 600;
    flex-shrink: 0;
}

/* Dynamic avatar colors by starting letter */
.cm-avatar--a, .cm-avatar--e, .cm-avatar--i, .cm-avatar--m, .cm-avatar--q, .cm-avatar--u, .cm-avatar--y {
    background: #d1fae5; color: #065f46;
}
.cm-avatar--b, .cm-avatar--f, .cm-avatar--j, .cm-avatar--n, .cm-avatar--r, .cm-avatar--v, .cm-avatar--z {
    background: #dbeafe; color: #1e40af;
}
.cm-avatar--c, .cm-avatar--g, .cm-avatar--k, .cm-avatar--o, .cm-avatar--s, .cm-avatar--w {
    background: #fef3c7; color: #92400e;
}
.cm-avatar--d, .cm-avatar--h, .cm-avatar--l, .cm-avatar--p, .cm-avatar--t, .cm-avatar--x {
    background: #fee2e2; color: #991b1b;
}

.cm-cust-name {
    font-weight: 500;
    color: var(--cm-text-primary);
    text-decoration: none;
    transition: color 0.15s;
    display: block;
}
a.cm-cust-name:hover { color: var(--cm-accent-emerald); }
.cm-cust-meta {
    font-size: 0.75rem;
    color: var(--cm-text-muted);
    margin-top: 1px;
}
.cm-route { font-size: 0.8125rem; color: var(--cm-text-secondary); }

/* ── Balance ── */
.cm-balance { font-weight: 500; }
.cm-balance--due   { color: #dc2626; }
.cm-balance--clear { color: var(--cm-text-muted); }

/* ── Action Buttons ── */
.cm-actions { display: flex; align-items: center; justify-content: center; gap: 6px; }
.cm-action-btn {
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 0.5px solid var(--cm-card-border);
    border-radius: 7px;
    background: transparent;
    cursor: pointer;
    color: var(--cm-text-muted);
    text-decoration: none;
    transition: border-color 0.15s, color 0.15s, background 0.15s;
}
.cm-action-btn:hover        { border-color: var(--cm-text-muted); color: var(--cm-text-primary); background: var(--cm-bg); }
.cm-action-btn--edit:hover  { border-color: #93c5fd; color: #1d4ed8; background: #eff6ff; }
.cm-action-btn--danger:hover{ border-color: #fca5a5; color: #dc2626; background: #fef2f2; }

/* ── Empty State ── */
.cm-empty { padding: 3rem 1rem; text-align: center; }
.cm-empty-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    background: var(--cm-bg);
    color: var(--cm-text-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
}
.cm-empty-title { font-size: 0.9375rem; font-weight: 600; color: var(--cm-text-primary); margin-bottom: 4px; }
.cm-empty-sub   { font-size: 0.8125rem; color: var(--cm-text-muted); }

/* ── Pagination ── */
.cm-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1.25rem;
    border-top: 0.5px solid var(--cm-card-border);
    flex-wrap: wrap;
    gap: 8px;
}
.cm-pg-info { font-size: 0.75rem; color: var(--cm-text-muted); }
.cm-pg-links { display: flex; gap: 4px; }

/* ── Modal Overlay ── */
.cm-modal-overlay {
    position: fixed;
    inset: 0;
    z-index: 200;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(4px);
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding: 5vh 1rem 2rem;
    overflow-y: auto;
}
.cm-modal {
    background: var(--cm-card-bg);
    border-radius: 16px;
    border: 0.5px solid var(--cm-card-border);
    width: 100%;
    max-width: 520px;
    overflow: hidden;
    box-shadow: 0 20px 40px -8px rgba(15,23,42,0.15);
}

/* ── Modal Header ── */
.cm-modal-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.125rem 1.5rem;
    border-bottom: 0.5px solid var(--cm-card-border);
    background: var(--cm-bg);
}
.cm-modal-title-row { display: flex; align-items: center; gap: 10px; }
.cm-modal-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.cm-modal-icon--green { background: #d1fae5; color: #065f46; }
.cm-modal-icon--blue  { background: #dbeafe; color: #1e40af; }
.cm-modal-title { font-size: 0.9375rem; font-weight: 600; color: var(--cm-text-primary); }
.cm-modal-sub   { font-size: 0.75rem; color: var(--cm-text-muted); margin-top: 1px; }

.cm-close-btn {
    width: 28px;
    height: 28px;
    border: 0.5px solid var(--cm-card-border);
    border-radius: 7px;
    background: transparent;
    cursor: pointer;
    color: var(--cm-text-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background 0.15s, color 0.15s, border-color 0.15s;
}
.cm-close-btn:hover { background: #fee2e2; color: #dc2626; border-color: #fca5a5; }

/* ── Modal Body / Form ── */
.cm-modal-body { padding: 1.25rem 1.5rem; }
.cm-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 12px;
}
@media (max-width: 480px) { .cm-form-grid { grid-template-columns: 1fr; } }

.cm-form-group { margin-bottom: 12px; }
.cm-form-group:last-of-type { margin-bottom: 0; }

.cm-form-label {
    display: block;
    font-size: 0.6875rem;
    font-weight: 500;
    color: var(--cm-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.07em;
    margin-bottom: 5px;
}
.cm-required { color: #dc2626; }

.cm-form-input {
    display: block;
    width: 100%;
    padding: 8px 10px;
    border: 0.5px solid var(--cm-card-border);
    border-radius: 8px;
    font-size: 0.8125rem;
    background: var(--cm-bg);
    color: var(--cm-text-primary);
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
    font-family: inherit;
}
.cm-form-input:focus {
    border-color: var(--cm-text-secondary);
    box-shadow: 0 0 0 3px rgba(148,163,184,0.15);
    background: var(--cm-card-bg);
}
.cm-uppercase     { text-transform: uppercase; }

/* ── Modal Footer ── */
.cm-modal-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    padding-top: 1rem;
    margin-top: 1rem;
    border-top: 0.5px solid var(--cm-card-border);
}
</style>
@endpush

@push('scripts')
<script>
function openEditDealer(button) {
    const id = button.getAttribute('data-id');
    const firm = button.getAttribute('data-firm');
    const contact = button.getAttribute('data-contact');
    const phone = button.getAttribute('data-phone');
    const gst = button.getAttribute('data-gst');
    const location = button.getAttribute('data-location');
    const route = button.getAttribute('data-route');

    const form = document.getElementById('edit-dealer-form');
    form.action = `/masters/dealers/${id}`;
    document.getElementById('edit-firm').value    = firm;
    document.getElementById('edit-contact').value = contact;
    document.getElementById('edit-phone').value   = phone;
    document.getElementById('edit-gst').value     = gst;
    document.getElementById('edit-location').value = location;
    document.getElementById('edit-route').value   = route;
    document.getElementById('edit-dealer-modal').classList.remove('cm-hidden');
}

// Close modals on overlay click
document.querySelectorAll('.cm-modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('cm-hidden');
    });
});

// Close modals on Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.cm-modal-overlay').forEach(m => m.classList.add('cm-hidden'));
    }
});
</script>
@endpush