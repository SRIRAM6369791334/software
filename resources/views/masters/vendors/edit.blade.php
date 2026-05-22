@extends('layouts.app')
@section('title', 'Edit Vendor')

@section('content')
<div class="cm-page">

    {{-- Back Link --}}
    <a href="{{ route('masters.vendors.index') }}" class="cm-back-btn">
        <span class="material-symbols-rounded" style="font-size: 16px;">arrow_back</span>
        Back to directory
    </a>

    {{-- Title Header --}}
    <div class="cm-form-header">
        <h1 class="cm-page-title">Edit Vendor Profile</h1>
        <p class="cm-page-sub">Update profile details and credentials for {{ $vendor->firm_name }}</p>
    </div>

    {{-- Form Shell --}}
    <div class="cm-form-container">
        <form action="{{ route('masters.vendors.update', $vendor) }}" method="POST" class="cm-card-form">
            @csrf
            @method('PUT')
            
            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Firm Name <span class="cm-required">*</span></label>
                    <input type="text" name="firm_name" value="{{ old('firm_name', $vendor->firm_name) }}" required 
                        class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $vendor->contact_person) }}" 
                        class="cm-form-input">
                </div>
            </div>

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Phone <span class="cm-required">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $vendor->phone) }}" required 
                        class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">GSTIN</label>
                    <input type="text" name="gst_number" value="{{ old('gst_number', $vendor->gst_number) }}" 
                        class="cm-form-input cm-uppercase">
                </div>
            </div>

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Location / City</label>
                    <input type="text" name="location" value="{{ old('location', $vendor->location) }}" 
                        class="cm-form-input">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Route</label>
                    <input type="text" name="route" value="{{ old('route', $vendor->route) }}" 
                        class="cm-form-input">
                </div>
            </div>

            <div class="cm-form-group">
                <label class="cm-form-label">Strategic Notes</label>
                <textarea name="notes" rows="3" class="cm-form-input cm-form-textarea">{{ old('notes', $vendor->notes) }}</textarea>
            </div>

            <div class="cm-form-footer">
                <a href="{{ route('masters.vendors.index') }}" class="cm-btn-ghost">Cancel</a>
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

/* ── Layout & Typography ── */
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
    margin-bottom: 1.5rem;
    transition: transform 0.2s ease, color 0.2s ease;
}
.cm-back-btn:hover {
    color: var(--cm-accent-teal-hover);
    transform: translateX(-4px);
}

.cm-form-header {
    margin-bottom: 1.5rem;
}
.cm-page-title {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--cm-text-primary);
    letter-spacing: -0.02em;
    margin: 0;
}
.cm-page-sub {
    font-size: 0.8125rem;
    color: var(--cm-text-secondary);
    margin-top: 4px;
}

/* ── Form Shell ── */
.cm-form-container {
    max-width: 720px;
}
.cm-card-form {
    background: var(--cm-card-bg);
    border: 1px solid var(--cm-card-border);
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--cm-shadow-md);
}

/* ── Form Layout ── */
.cm-form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.25rem;
    margin-bottom: 1.25rem;
}
@media (max-width: 520px) { .cm-form-grid { grid-template-columns: 1fr; } }

.cm-form-group { margin-bottom: 1.25rem; }
.cm-form-group:last-of-type { margin-bottom: 0; }

.cm-form-label {
    display: block;
    font-size: 0.6875rem;
    font-weight: 700;
    color: var(--cm-text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.08em;
    margin-bottom: 6px;
}
.cm-required { color: #dc2626; }

.cm-form-input {
    display: block;
    width: 100%;
    padding: 10px 14px;
    border: 1px solid var(--cm-card-border);
    border-radius: 8px;
    font-size: 0.8125rem;
    background: var(--cm-bg);
    color: var(--cm-text-primary);
    outline: none;
    transition: border-color 0.15s, box-shadow 0.15s, background-color 0.15s;
    font-family: inherit;
}
.cm-form-input:focus {
    border-color: var(--cm-text-muted);
    box-shadow: 0 0 0 4px rgba(148,163,184,0.12);
    background: var(--cm-card-bg);
}
.cm-form-textarea { resize: vertical; min-height: 80px; }
.cm-uppercase     { text-transform: uppercase; }

/* ── Buttons ── */
.cm-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 10px 20px;
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
    padding: 10px 16px;
    background: transparent;
    border: none;
    border-radius: 8px;
    font-size: 0.8125rem;
    color: var(--cm-text-secondary);
    cursor: pointer;
    transition: background 0.15s, color 0.15s;
}
.cm-btn-ghost:hover { background: var(--cm-bg); color: var(--cm-text-primary); }

.cm-form-footer {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 12px;
    padding-top: 1.5rem;
    border-top: 1px solid var(--cm-card-border);
    margin-top: 1.5rem;
}
</style>
@endpush
