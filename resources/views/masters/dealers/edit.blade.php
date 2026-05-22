@extends('layouts.app')
@section('title', 'Edit Dealer')

@section('content')
<div class="cm-page">

    {{-- Back Link --}}
    <a href="{{ route('masters.dealers.index') }}" class="cm-back-btn">
        <span class="material-symbols-rounded" style="font-size: 16px;">arrow_back</span>
        Back to Directory
    </a>

    {{-- Top Bar --}}
    <div class="cm-topbar">
        <div>
            <h1 class="cm-page-title">Edit Dealer: {{ $dealer->firm_name }}</h1>
            <p class="cm-page-sub">Update profile credentials or supplier settings</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="cm-form-card">
        <form action="{{ route('masters.dealers.update', $dealer) }}" method="POST" class="cm-form-body">
            @csrf
            @method('PUT')
            
            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Firm Name <span class="cm-required">*</span></label>
                    <input type="text" name="firm_name" required placeholder="e.g. Superior Feed Mills"
                        class="cm-form-input" value="{{ old('firm_name', $dealer->firm_name) }}">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">GST Number</label>
                    <input type="text" name="gst_number" placeholder="Valid GSTIN"
                        class="cm-form-input cm-uppercase" value="{{ old('gst_number', $dealer->gst_number) }}">
                </div>
            </div>

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Contact Person</label>
                    <input type="text" name="contact_person" placeholder="e.g. Sales Manager"
                        class="cm-form-input" value="{{ old('contact_person', $dealer->contact_person) }}">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Phone Number <span class="cm-required">*</span></label>
                    <input type="text" name="phone" required placeholder="+91 00000 00000"
                        class="cm-form-input" value="{{ old('phone', $dealer->phone) }}">
                </div>
            </div>

            <div class="cm-form-group">
                <label class="cm-form-label">Location / City</label>
                <input type="text" name="location" placeholder="Main Street, City"
                    class="cm-form-input" value="{{ old('location', $dealer->location) }}">
            </div>

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Route</label>
                    <input type="text" name="route" placeholder="Delivery Route"
                        class="cm-form-input" value="{{ old('route', $dealer->route) }}">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Outstanding Amount (Rs)</label>
                    <input type="number" name="pending_amount" step="0.01"
                        class="cm-form-input" value="{{ old('pending_amount', $dealer->pending_amount) }}">
                </div>
            </div>

            <div class="cm-form-footer">
                <a href="{{ route('masters.dealers.index') }}" class="cm-btn-ghost">Cancel</a>
                <button type="submit" class="cm-btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round" style="margin-right: 4px;">
                        <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                        <polyline points="17 21 17 13 7 13 7 21"/>
                        <polyline points="7 3 7 8 15 8"/>
                    </svg>
                    Update Dealer
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

/* ── Layout ── */
.cm-page { padding: 1rem 0 3rem; }
.cm-hidden { display: none !important; }

/* ── Back Link ── */
.cm-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--cm-accent-emerald);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    text-decoration: none;
    margin-bottom: 1.25rem;
    transition: transform 0.2s ease, color 0.2s ease;
}
.cm-back-btn:hover {
    color: var(--cm-accent-emerald-hover);
    transform: translateX(-4px);
}

/* ── Top Bar ── */
.cm-topbar {
    margin-bottom: 2rem;
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

/* ── Form Card ── */
.cm-form-card {
    background: var(--cm-card-bg);
    border: 0.5px solid var(--cm-card-border);
    border-radius: 16px;
    max-width: 640px;
    box-shadow: var(--cm-shadow-sm);
    overflow: hidden;
}

.cm-form-body {
    padding: 2rem;
}

.cm-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}
@media (max-width: 580px) {
    .cm-form-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}

.cm-form-group {
    margin-bottom: 1.5rem;
}
.cm-form-group:last-of-type {
    margin-bottom: 0;
}

.cm-form-label {
    display: block;
    font-size: 0.6875rem;
    font-weight: 600;
    color: var(--cm-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.07em;
    margin-bottom: 6px;
}
.cm-required {
    color: #dc2626;
}

.cm-form-input {
    display: block;
    width: 100%;
    padding: 10px 14px;
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
.cm-uppercase {
    text-transform: uppercase;
}

/* ── Buttons ── */
.cm-btn-primary {
    display: inline-flex;
    align-items: center;
    padding: 10px 18px;
    background: #0f172a;
    color: #fff;
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

.cm-btn-ghost {
    display: inline-flex;
    align-items: center;
    padding: 10px 18px;
    background: transparent;
    border: none;
    border-radius: 8px;
    font-size: 0.8125rem;
    color: var(--cm-text-secondary);
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}
.cm-btn-ghost:hover { background: var(--cm-bg); color: var(--cm-text-primary); }

/* ── Form Footer ── */
.cm-form-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 12px;
    padding-top: 1.5rem;
    margin-top: 1.5rem;
    border-top: 0.5px solid var(--cm-card-border);
}
</style>
@endpush
