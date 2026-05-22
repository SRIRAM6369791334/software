@extends('layouts.app')
@section('title', 'Vendor Master')

@section('content')

<div class="cm-page">

    {{-- Top Bar --}}
    <div class="cm-topbar">
        <div>
            <h1 class="cm-page-title">Vendor Master</h1>
            <p class="cm-page-sub">Directory of logistics and pharmaceutical suppliers</p>
        </div>
        <button onclick="document.getElementById('add-vendor-modal').classList.remove('cm-hidden')"
            class="cm-btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Register Vendor
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
                <div class="cm-stat-label">Total Suppliers</div>
                <div class="cm-stat-value">{{ $vendors->total() }}</div>
            </div>
        </div>
        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <div>
                <div class="cm-stat-label">Route Reach</div>
                <div class="cm-stat-value">{{ $vendors->pluck('route')->filter()->unique()->count() }} Routes</div>
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
                    placeholder="Search by firm name, contact or phone…" class="cm-search-input">
            </form>
        </div>

        <div class="cm-table-wrap">
            <table class="cm-table">
                <thead>
                    <tr>
                        <th>Firm & Location</th>
                        <th>Point of Contact</th>
                        <th>Route</th>
                        <th>GSTIN</th>
                        <th class="cm-th-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vendors as $vendor)
                    <tr class="cm-tr">
                        <td class="cm-td">
                            <div class="cm-identity">
                                <div class="cm-avatar cm-avatar--{{ strtolower(substr($vendor->firm_name, 0, 1)) }}">
                                    {{ strtoupper(substr($vendor->firm_name, 0, 2)) }}
                                </div>
                                <div>
                                    <a href="{{ route('masters.vendors.show', $vendor) }}"
                                        class="cm-cust-name">{{ $vendor->firm_name }}</a>
                                    <div class="cm-cust-meta">{{ $vendor->location ?: 'No Location Specified' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="cm-td">
                            <div class="cm-cust-name">{{ $vendor->contact_person ?: 'No contact person' }}</div>
                            <div class="cm-cust-meta">{{ $vendor->phone }}</div>
                        </td>
                        <td class="cm-td">
                            <span class="cm-route">{{ $vendor->route ?: 'General Sector' }}</span>
                        </td>
                        <td class="cm-td">
                            <span class="cm-gst-mono">{{ $vendor->gst_number ?: 'UNREGISTERED' }}</span>
                        </td>
                        <td class="cm-td">
                            <div class="cm-actions">
                                <button
                                    data-id="{{ $vendor->id }}"
                                    data-firm="{{ $vendor->firm_name }}"
                                    data-contact="{{ $vendor->contact_person }}"
                                    data-phone="{{ $vendor->phone }}"
                                    data-gst="{{ $vendor->gst_number }}"
                                    data-location="{{ $vendor->location }}"
                                    data-route="{{ $vendor->route }}"
                                    data-notes="{{ $vendor->notes }}"
                                    onclick="openEditVendor(this)"
                                    class="cm-action-btn cm-action-btn--edit" title="Edit vendor">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('masters.vendors.destroy', $vendor) }}" method="POST"
                                    onsubmit="return confirm('Archive {{ $vendor->firm_name }}?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="cm-action-btn cm-action-btn--danger" title="Archive vendor">
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
                            <p class="cm-empty-title">No vendors found</p>
                            <p class="cm-empty-sub">Start by registering your first supply partner.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($vendors->hasPages())
        <div class="cm-pagination">
            <span class="cm-pg-info">
                Showing {{ $vendors->firstItem() }}–{{ $vendors->lastItem() }} of {{ $vendors->total() }} vendors
            </span>
            <div class="cm-pg-links">
                {{ $vendors->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>

</div>

{{-- ================================================ --}}
{{-- ADD VENDOR MODAL                                 --}}
{{-- ================================================ --}}
<div id="add-vendor-modal" class="cm-modal-overlay cm-hidden">
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
                    <div class="cm-modal-title">Register Vendor</div>
                    <div class="cm-modal-sub">Onboard a new supply partner</div>
                </div>
            </div>
            <button onclick="document.getElementById('add-vendor-modal').classList.add('cm-hidden')"
                class="cm-close-btn" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('masters.vendors.store') }}" method="POST" class="cm-modal-body">
            @csrf
            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Firm Name <span class="cm-required">*</span></label>
                    <input type="text" name="firm_name" required placeholder="e.g. Apex Feed Suppliers"
                        class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Contact Person</label>
                    <input type="text" name="contact_person" placeholder="Manager Name"
                        class="cm-form-input">
                </div>
            </div>

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Phone <span class="cm-required">*</span></label>
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
                    <input type="text" name="location" placeholder="e.g. Salem, TN"
                        class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Route</label>
                    <input type="text" name="route" placeholder="e.g. Main Highway Route"
                        class="cm-form-input">
                </div>
            </div>

            <div class="cm-form-group">
                <label class="cm-form-label">Strategic Notes</label>
                <textarea name="notes" rows="2" placeholder="Vendor specifications, items supplied..."
                    class="cm-form-input cm-form-textarea"></textarea>
            </div>

            <div class="cm-modal-footer">
                <button type="button"
                    onclick="document.getElementById('add-vendor-modal').classList.add('cm-hidden')"
                    class="cm-btn-ghost">Cancel</button>
                <button type="submit" class="cm-btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Activate Profile
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================ --}}
{{-- EDIT VENDOR MODAL                                --}}
{{-- ================================================ --}}
<div id="edit-vendor-modal" class="cm-modal-overlay cm-hidden">
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
                    <div class="cm-modal-title">Edit Vendor</div>
                    <div class="cm-modal-sub">Update supply partner credentials</div>
                </div>
            </div>
            <button onclick="document.getElementById('edit-vendor-modal').classList.add('cm-hidden')"
                class="cm-close-btn" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form id="edit-vendor-form" method="POST" class="cm-modal-body">
            @csrf @method('PUT')

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Firm Name <span class="cm-required">*</span></label>
                    <input type="text" name="firm_name" id="ev-firm" required class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Contact Person</label>
                    <input type="text" name="contact_person" id="ev-contact" class="cm-form-input">
                </div>
            </div>

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Phone <span class="cm-required">*</span></label>
                    <input type="text" name="phone" id="ev-phone" required class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">GSTIN</label>
                    <input type="text" name="gst_number" id="ev-gst" class="cm-form-input cm-uppercase">
                </div>
            </div>

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Location / City</label>
                    <input type="text" name="location" id="ev-location" class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Route</label>
                    <input type="text" name="route" id="ev-route" class="cm-form-input">
                </div>
            </div>

            <div class="cm-form-group">
                <label class="cm-form-label">Strategic Notes</label>
                <textarea name="notes" id="ev-notes" rows="2" class="cm-form-input cm-form-textarea"></textarea>
            </div>

            <div class="cm-modal-footer">
                <button type="button"
                    onclick="document.getElementById('edit-vendor-modal').classList.add('cm-hidden')"
                    class="cm-btn-ghost">Cancel</button>
                <button type="submit" class="cm-btn-primary cm-btn-primary--blue">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
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
    --cm-accent-blue: #2563eb;
    --cm-accent-blue-hover: #1d4ed8;
    --cm-accent-blue-light: #eff6ff;
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
    --cm-accent-blue-light: rgba(37, 99, 235, 0.1);
    --cm-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.5);
    --cm-shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -2px rgba(0, 0, 0, 0.3);
    --cm-shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.4), 0 4px 6px -4px rgba(0, 0, 0, 0.4);
}

/* ── Reset & Base ── */
*, *::before, *::after { box-sizing: border-box; }

/* ── Layout ── */
.cm-page { padding: 2rem 0 3rem; }
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
.cm-btn-primary--blue { background: var(--cm-accent-blue); }
.cm-btn-primary--blue:hover { background: var(--cm-accent-blue-hover); opacity: 1; }

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
    grid-template-columns: repeat(2, 1fr);
    gap: 12px;
    margin-bottom: 1.5rem;
}
@media (max-width: 500px) { .cm-stats { grid-template-columns: 1fr; } }

.cm-stat-card {
    background: var(--cm-card-bg);
    border: 0.5px solid var(--cm-card-border);
    border-radius: 12px;
    padding: 1rem 1.125rem;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: var(--cm-shadow-sm);
}

.cm-stat-icon {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.cm-icon-teal  { background: var(--cm-accent-teal-light); color: var(--cm-accent-teal); }
.cm-icon-blue  { background: var(--cm-accent-blue-light); color: var(--cm-accent-blue); }

.cm-stat-label {
    font-size: 0.6875rem;
    color: var(--cm-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.07em;
    margin-bottom: 2px;
}
.cm-stat-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--cm-text-primary);
}

/* ── Table Card ── */
.cm-table-card {
    background: var(--cm-card-bg);
    border: 0.5px solid var(--cm-card-border);
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--cm-shadow-sm);
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
    font-weight: 600;
    color: var(--cm-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.07em;
    text-align: left;
    background: var(--cm-bg);
    white-space: nowrap;
}
.cm-th-center { text-align: center; }

.cm-tr { transition: background 0.1s; border-bottom: 0.5px solid var(--cm-card-border); }
.cm-tr:hover { background: var(--cm-bg); }
.cm-td {
    padding: 12px 16px;
    vertical-align: middle;
    color: var(--cm-text-primary);
}
.cm-table tbody tr:last-child .cm-td { border-bottom: none; }

/* ── Identity Cell ── */
.cm-identity { display: flex; align-items: center; gap: 10px; }
.cm-avatar {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: var(--cm-accent-blue-light);
    color: var(--cm-accent-blue);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    flex-shrink: 0;
}

/* Dynamic avatar colors by starting letter */
.cm-avatar--a, .cm-avatar--e, .cm-avatar--i, .cm-avatar--m, .cm-avatar--q, .cm-avatar--u, .cm-avatar--y {
    background: linear-gradient(135deg, #10b981, #3b82f6); color: #ffffff;
}
.cm-avatar--b, .cm-avatar--f, .cm-avatar--j, .cm-avatar--n, .cm-avatar--r, .cm-avatar--v, .cm-avatar--z {
    background: linear-gradient(135deg, #6366f1, #a855f7); color: #ffffff;
}
.cm-avatar--c, .cm-avatar--g, .cm-avatar--k, .cm-avatar--o, .cm-avatar--s, .cm-avatar--w {
    background: linear-gradient(135deg, #f59e0b, #ec4899); color: #ffffff;
}
.cm-avatar--d, .cm-avatar--h, .cm-avatar--l, .cm-avatar--p, .cm-avatar--t, .cm-avatar--x {
    background: linear-gradient(135deg, #ef4444, #f97316); color: #ffffff;
}

.cm-cust-name {
    font-weight: 600;
    color: var(--cm-text-primary);
    text-decoration: none;
    transition: color 0.15s;
    display: block;
}
a.cm-cust-name:hover { color: var(--cm-accent-teal); }
.cm-cust-meta {
    font-size: 0.75rem;
    color: var(--cm-text-muted);
    margin-top: 1px;
}
.cm-route { font-size: 0.8125rem; color: var(--cm-text-secondary); font-weight: 500; }
.cm-gst-mono { font-family: monospace; font-size: 0.75rem; color: var(--cm-text-secondary); }

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
.cm-action-btn:hover        { border-color: var(--cm-text-secondary); color: var(--cm-text-primary); background: var(--cm-bg); }
.cm-action-btn--edit:hover  { border-color: #93c5fd; color: var(--cm-accent-blue); background: var(--cm-accent-blue-light); }
.cm-action-btn--danger:hover{ border-color: #fca5a5; color: #dc2626; background: rgba(220, 38, 38, 0.05); }

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
.cm-empty-title { font-size: 0.9375rem; font-weight: 700; color: var(--cm-text-primary); margin-bottom: 4px; }
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
    box-shadow: var(--cm-shadow-lg);
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
.cm-modal-icon--green { background: var(--cm-accent-teal-light); color: var(--cm-accent-teal); }
.cm-modal-icon--blue  { background: var(--cm-accent-blue-light); color: var(--cm-accent-blue); }
.cm-modal-title { font-size: 0.9375rem; font-weight: 700; color: var(--cm-text-primary); }
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
.cm-close-btn:hover { background: rgba(220, 38, 38, 0.05); color: #dc2626; border-color: #fca5a5; }

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
    font-weight: 600;
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
    border-color: var(--cm-text-muted);
    box-shadow: 0 0 0 3px rgba(148,163,184,0.15);
    background: var(--cm-card-bg);
}
.cm-form-textarea { resize: vertical; min-height: 64px; }
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
function openEditVendor(button) {
    const id = button.getAttribute('data-id');
    const firm = button.getAttribute('data-firm');
    const contact = button.getAttribute('data-contact');
    const phone = button.getAttribute('data-phone');
    const gst = button.getAttribute('data-gst');
    const location = button.getAttribute('data-location');
    const route = button.getAttribute('data-route');
    const notes = button.getAttribute('data-notes');

    const form = document.getElementById('edit-vendor-form');
    form.action = `/masters/vendors/${id}`;
    document.getElementById('ev-firm').value    = firm;
    document.getElementById('ev-contact').value = contact;
    document.getElementById('ev-phone').value   = phone;
    document.getElementById('ev-gst').value     = gst;
    document.getElementById('ev-location').value = location;
    document.getElementById('ev-route').value   = route;
    document.getElementById('ev-notes').value   = notes;
    document.getElementById('edit-vendor-modal').classList.remove('cm-hidden');
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
