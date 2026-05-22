@extends('layouts.app')
@section('title', 'Customer Master')

@section('content')

<div class="cm-page">

    {{-- Top Bar --}}
    <div class="cm-topbar">
        <div>
            <h1 class="cm-page-title">Customer master</h1>
            <p class="cm-page-sub">Directory of retail buyers and wholesale partners</p>
        </div>
        <button onclick="document.getElementById('add-customer-modal').classList.remove('cm-hidden')"
            class="cm-btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Register customer
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
                <div class="cm-stat-label">Total active</div>
                <div class="cm-stat-value">{{ $customers->total() }}</div>
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
                <div class="cm-stat-label">Wholesale</div>
                <div class="cm-stat-value">{{ $customers->where('type', 'Wholesale')->count() }}</div>
            </div>
        </div>
        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-amber">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
            </div>
            <div>
                <div class="cm-stat-label">Retail</div>
                <div class="cm-stat-value">{{ $customers->where('type', 'Retail')->count() }}</div>
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
                <div class="cm-stat-label">With balance</div>
                <div class="cm-stat-value">{{ $customers->where('balance', '>', 0)->count() }}</div>
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
                    placeholder="Search by name, phone or route…" class="cm-search-input">
            </form>
            <div class="cm-toolbar-right">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="cm-filter-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Export
                </a>
            </div>
        </div>

        <div class="cm-table-wrap">
            <table class="cm-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Contact</th>
                        <th>Route</th>
                        <th>Type</th>
                        <th class="cm-th-right">Outstanding</th>
                        <th class="cm-th-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $customer)
                    <tr class="cm-tr">
                        <td class="cm-td">
                            <div class="cm-identity">
                                <div class="cm-avatar cm-avatar--{{ strtolower(substr($customer->name, 0, 1)) }}">
                                    {{ strtoupper(substr($customer->name, 0, 2)) }}
                                </div>
                                <div>
                                    <a href="{{ route('masters.customers.show', $customer) }}"
                                        class="cm-cust-name">{{ $customer->name }}</a>
                                    <div class="cm-cust-meta">{{ $customer->gst_number ?: 'No GST' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="cm-td">
                            <div class="cm-cust-name">{{ $customer->phone }}</div>
                            <div class="cm-cust-meta cm-truncate">{{ $customer->address ?: 'No address' }}</div>
                        </td>
                        <td class="cm-td">
                            <span class="cm-route">{{ $customer->route ?: 'General' }}</span>
                        </td>
                        <td class="cm-td">
                            @if($customer->type === 'Wholesale')
                                <span class="cm-badge cm-badge--wholesale">Wholesale</span>
                            @else
                                <span class="cm-badge cm-badge--retail">Retail</span>
                            @endif
                        </td>
                        <td class="cm-td cm-td-right">
                            @if($customer->balance > 0)
                                <span class="cm-balance cm-balance--due">Rs {{ number_format($customer->balance, 0) }}</span>
                            @else
                                <span class="cm-balance cm-balance--clear">Rs 0</span>
                            @endif
                        </td>
                        <td class="cm-td">
                            <div class="cm-actions">
                                <a href="{{ route('masters.customers.ledger-pdf', $customer) }}"
                                    class="cm-action-btn" title="Download ledger PDF">
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
                                    data-id="{{ $customer->id }}"
                                    data-name="{{ $customer->name }}"
                                    data-phone="{{ $customer->phone }}"
                                    data-address="{{ $customer->address }}"
                                    data-gst="{{ $customer->gst_number }}"
                                    data-route="{{ $customer->route }}"
                                    data-type="{{ $customer->type }}"
                                    onclick="openEditCustomer(this)"
                                    class="cm-action-btn cm-action-btn--edit" title="Edit customer">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </button>
                                <form action="{{ route('masters.customers.destroy', $customer) }}" method="POST"
                                    onsubmit="return confirm('Archive {{ $customer->name }}?')" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="cm-action-btn cm-action-btn--danger" title="Archive customer">
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
                        <td colspan="6" class="cm-empty">
                            <div class="cm-empty-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                                </svg>
                            </div>
                            <p class="cm-empty-title">No customers found</p>
                            <p class="cm-empty-sub">Start by registering your first buyer.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($customers->hasPages())
        <div class="cm-pagination">
            <span class="cm-pg-info">
                Showing {{ $customers->firstItem() }}–{{ $customers->lastItem() }} of {{ $customers->total() }} customers
            </span>
            <div class="cm-pg-links">
                {{ $customers->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>

</div>

{{-- ================================================ --}}
{{-- ADD CUSTOMER MODAL                               --}}
{{-- ================================================ --}}
<div id="add-customer-modal" class="cm-modal-overlay cm-hidden">
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
                    <div class="cm-modal-title">Register customer</div>
                    <div class="cm-modal-sub">Onboard a new buyer or partner</div>
                </div>
            </div>
            <button onclick="document.getElementById('add-customer-modal').classList.add('cm-hidden')"
                class="cm-close-btn" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('masters.customers.store') }}" method="POST" class="cm-modal-body">
            @csrf
            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Full name <span class="cm-required">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. John Poultry Hub"
                        class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Phone <span class="cm-required">*</span></label>
                    <input type="text" name="phone" required placeholder="+91 00000 00000"
                        class="cm-form-input">
                </div>
            </div>

            <div class="cm-form-group">
                <label class="cm-form-label">Address</label>
                <textarea name="address" rows="2" placeholder="Store or office address…"
                    class="cm-form-input cm-form-textarea"></textarea>
            </div>

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">GST number</label>
                    <input type="text" name="gst_number" placeholder="Optional GSTIN"
                        class="cm-form-input cm-uppercase">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Route</label>
                    <input type="text" name="route" placeholder="e.g. North Sector"
                        class="cm-form-input">
                </div>
            </div>

            <div class="cm-form-group">
                <label class="cm-form-label">Type</label>
                <select name="type" class="cm-form-input cm-form-select">
                    <option value="Retail">Retail partner</option>
                    <option value="Wholesale">Wholesale distributor</option>
                </select>
            </div>

            <div class="cm-modal-footer">
                <button type="button"
                    onclick="document.getElementById('add-customer-modal').classList.add('cm-hidden')"
                    class="cm-btn-ghost">Cancel</button>
                <button type="submit" class="cm-btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Activate profile
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================ --}}
{{-- EDIT CUSTOMER MODAL                             --}}
{{-- ================================================ --}}
<div id="edit-customer-modal" class="cm-modal-overlay cm-hidden">
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
                    <div class="cm-modal-title">Edit customer</div>
                    <div class="cm-modal-sub">Update existing profile credentials</div>
                </div>
            </div>
            <button onclick="document.getElementById('edit-customer-modal').classList.add('cm-hidden')"
                class="cm-close-btn" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form id="edit-customer-form" method="POST" class="cm-modal-body">
            @csrf @method('PUT')

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Full name <span class="cm-required">*</span></label>
                    <input type="text" name="name" id="edit-name" required class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Phone <span class="cm-required">*</span></label>
                    <input type="text" name="phone" id="edit-phone" required class="cm-form-input">
                </div>
            </div>

            <div class="cm-form-group">
                <label class="cm-form-label">Address</label>
                <textarea name="address" id="edit-address" rows="2"
                    class="cm-form-input cm-form-textarea"></textarea>
            </div>

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">GST number</label>
                    <input type="text" name="gst_number" id="edit-gst"
                        class="cm-form-input cm-uppercase">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Route</label>
                    <input type="text" name="route" id="edit-route" class="cm-form-input">
                </div>
            </div>

            <div class="cm-form-group">
                <label class="cm-form-label">Type</label>
                <select name="type" id="edit-type" class="cm-form-input cm-form-select">
                    <option value="Retail">Retail partner</option>
                    <option value="Wholesale">Wholesale distributor</option>
                </select>
            </div>

            <div class="cm-modal-footer">
                <button type="button"
                    onclick="document.getElementById('edit-customer-modal').classList.add('cm-hidden')"
                    class="cm-btn-ghost">Cancel</button>
                <button type="submit" class="cm-btn-primary cm-btn-primary--blue">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Save changes
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
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
    font-weight: 600;
    color: #0f172a;
    letter-spacing: -0.02em;
}
.cm-page-sub {
    font-size: 0.8125rem;
    color: #64748b;
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
    color: #64748b;
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}
.cm-btn-ghost:hover { background: #f1f5f9; color: #0f172a; }

/* ── Stats ── */
.cm-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 1.5rem;
}
@media (max-width: 900px) { .cm-stats { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 500px) { .cm-stats { grid-template-columns: 1fr; } }

.cm-stat-card {
    background: #fff;
    border: 0.5px solid #e2e8f0;
    border-radius: 12px;
    padding: 1rem 1.125rem;
    display: flex;
    align-items: center;
    gap: 12px;
}
.cm-stat-card--danger { border-color: #fecaca; background: #fff; }

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
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    margin-bottom: 2px;
}
.cm-stat-value {
    font-size: 1.25rem;
    font-weight: 600;
    color: #0f172a;
}

/* ── Table Card ── */
.cm-table-card {
    background: #fff;
    border: 0.5px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
}

.cm-table-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.875rem 1.25rem;
    border-bottom: 0.5px solid #e2e8f0;
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
    color: #94a3b8;
    pointer-events: none;
}
.cm-search-input {
    width: 100%;
    padding: 7px 12px 7px 34px;
    border: 0.5px solid #cbd5e1;
    border-radius: 8px;
    font-size: 0.8125rem;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
}
.cm-search-input:focus {
    border-color: #94a3b8;
    box-shadow: 0 0 0 3px rgba(148,163,184,0.15);
}
.cm-toolbar-right { display: flex; align-items: center; gap: 8px; }
.cm-filter-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 7px 12px;
    border: 0.5px solid #cbd5e1;
    border-radius: 8px;
    font-size: 0.8125rem;
    color: #64748b;
    background: transparent;
    cursor: pointer;
    text-decoration: none;
    transition: background 0.15s, color 0.15s;
}
.cm-filter-btn:hover { background: #f1f5f9; color: #0f172a; }

/* ── Table ── */
.cm-table-wrap { overflow-x: auto; }
.cm-table { width: 100%; border-collapse: collapse; font-size: 0.8125rem; }
.cm-table thead tr { border-bottom: 0.5px solid #e2e8f0; }
.cm-table th {
    padding: 10px 16px;
    font-size: 0.6875rem;
    font-weight: 500;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    text-align: left;
    background: #f8fafc;
    white-space: nowrap;
}
.cm-th-right { text-align: right; }
.cm-th-center { text-align: center; }

.cm-tr { transition: background 0.1s; }
.cm-tr:hover { background: #f8fafc; }
.cm-td {
    padding: 12px 16px;
    border-bottom: 0.5px solid #f1f5f9;
    vertical-align: middle;
    color: #0f172a;
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
.cm-cust-name {
    font-weight: 500;
    color: #0f172a;
    text-decoration: none;
    transition: color 0.15s;
    display: block;
}
a.cm-cust-name:hover { color: #1d4ed8; }
.cm-cust-meta {
    font-size: 0.75rem;
    color: #94a3b8;
    margin-top: 1px;
}
.cm-truncate {
    max-width: 160px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.cm-route { font-size: 0.8125rem; color: #334155; }

/* ── Badges ── */
.cm-badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 4px;
    font-size: 0.6875rem;
    font-weight: 500;
}
.cm-badge--wholesale { background: #dbeafe; color: #1e40af; }
.cm-badge--retail    { background: #d1fae5; color: #065f46; }

/* ── Balance ── */
.cm-balance { font-weight: 500; }
.cm-balance--due   { color: #dc2626; }
.cm-balance--clear { color: #cbd5e1; }

/* ── Action Buttons ── */
.cm-actions { display: flex; align-items: center; justify-content: center; gap: 6px; }
.cm-action-btn {
    width: 30px;
    height: 30px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 0.5px solid #e2e8f0;
    border-radius: 7px;
    background: transparent;
    cursor: pointer;
    color: #94a3b8;
    text-decoration: none;
    transition: border-color 0.15s, color 0.15s, background 0.15s;
}
.cm-action-btn:hover        { border-color: #cbd5e1; color: #334155; background: #f8fafc; }
.cm-action-btn--edit:hover  { border-color: #93c5fd; color: #1d4ed8; background: #eff6ff; }
.cm-action-btn--danger:hover{ border-color: #fca5a5; color: #dc2626; background: #fef2f2; }

/* ── Empty State ── */
.cm-empty { padding: 3rem 1rem; text-align: center; }
.cm-empty-icon {
    width: 48px;
    height: 48px;
    border-radius: 10px;
    background: #f1f5f9;
    color: #94a3b8;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 0.75rem;
}
.cm-empty-title { font-size: 0.9375rem; font-weight: 600; color: #0f172a; margin-bottom: 4px; }
.cm-empty-sub   { font-size: 0.8125rem; color: #94a3b8; }

/* ── Pagination ── */
.cm-pagination {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1.25rem;
    border-top: 0.5px solid #f1f5f9;
    flex-wrap: wrap;
    gap: 8px;
}
.cm-pg-info { font-size: 0.75rem; color: #94a3b8; }
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
    background: #fff;
    border-radius: 16px;
    border: 0.5px solid #e2e8f0;
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
    border-bottom: 0.5px solid #f1f5f9;
    background: #fafafa;
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
.cm-modal-title { font-size: 0.9375rem; font-weight: 600; color: #0f172a; }
.cm-modal-sub   { font-size: 0.75rem; color: #94a3b8; margin-top: 1px; }

.cm-close-btn {
    width: 28px;
    height: 28px;
    border: 0.5px solid #e2e8f0;
    border-radius: 7px;
    background: transparent;
    cursor: pointer;
    color: #94a3b8;
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
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    margin-bottom: 5px;
}
.cm-required { color: #dc2626; }

.cm-form-input {
    display: block;
    width: 100%;
    padding: 8px 10px;
    border: 0.5px solid #cbd5e1;
    border-radius: 8px;
    font-size: 0.8125rem;
    background: #f8fafc;
    color: #0f172a;
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s;
    font-family: inherit;
}
.cm-form-input:focus {
    border-color: #94a3b8;
    box-shadow: 0 0 0 3px rgba(148,163,184,0.15);
    background: #fff;
}
.cm-form-textarea { resize: vertical; min-height: 64px; }
.cm-form-select   { cursor: pointer; }
.cm-uppercase     { text-transform: uppercase; }

/* ── Modal Footer ── */
.cm-modal-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 8px;
    padding-top: 1rem;
    margin-top: 1rem;
    border-top: 0.5px solid #f1f5f9;
}
</style>
@endpush

@push('scripts')
<script>
function openEditCustomer(button) {
    const id = button.getAttribute('data-id');
    const name = button.getAttribute('data-name');
    const phone = button.getAttribute('data-phone');
    const address = button.getAttribute('data-address');
    const gst = button.getAttribute('data-gst');
    const route = button.getAttribute('data-route');
    const type = button.getAttribute('data-type');

    const form = document.getElementById('edit-customer-form');
    form.action = `/masters/customers/${id}`;
    document.getElementById('edit-name').value    = name;
    document.getElementById('edit-phone').value   = phone;
    document.getElementById('edit-address').value = address;
    document.getElementById('edit-gst').value     = gst;
    document.getElementById('edit-route').value   = route;
    document.getElementById('edit-type').value    = type;
    document.getElementById('edit-customer-modal').classList.remove('cm-hidden');
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