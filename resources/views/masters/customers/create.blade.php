@extends('layouts.app')
@section('title', 'Register Customer')

@section('content')
<div class="swal-form-container">
    <div class="swal-form-card">
        
        <div class="swal-form-header">
            <div class="swal-icon-wrapper">
                <span class="material-symbols-rounded">person_add</span>
            </div>
            <h2>Register New Customer</h2>
            <p>Enter the details below to add a new customer to BizTrack.</p>
        </div>

        <form action="{{ route('masters.customers.store') }}" method="POST" class="swal-form">
            @csrf
            
            <div class="swal-input-group">
                <label>Full Name <span class="required">*</span></label>
                <div class="swal-input-wrapper">
                    <span class="material-symbols-rounded swal-input-icon">person</span>
                    <input type="text" name="name" required placeholder="e.g. John Doe">
                </div>
            </div>

            <div class="swal-input-group">
                <label>Phone Number <span class="required">*</span></label>
                <div class="swal-input-wrapper">
                    <span class="material-symbols-rounded swal-input-icon">call</span>
                    <input type="text" name="phone" required placeholder="e.g. 9876543210">
                </div>
            </div>

            <div class="swal-input-group swal-col-span-2">
                <label>Store Address</label>
                <div class="swal-input-wrapper">
                    <span class="material-symbols-rounded swal-input-icon">location_on</span>
                    <textarea name="address" rows="2" placeholder="Street, Area, City..."></textarea>
                </div>
            </div>

            <div class="swal-input-group">
                <label>GST Number (Optional)</label>
                <div class="swal-input-wrapper">
                    <span class="material-symbols-rounded swal-input-icon">badge</span>
                    <input type="text" name="gst_number" placeholder="22AAAAA0000A1Z5">
                </div>
            </div>

            <div class="swal-input-group">
                <label>Route / Area</label>
                <div class="swal-input-wrapper">
                    <span class="material-symbols-rounded swal-input-icon">alt_route</span>
                    <select name="route_id">
                        <option value="">Select Route</option>
                        @foreach($routes as $route)
                            <option value="{{ $route->id }}">{{ $route->route_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="swal-input-group">
                <label>Customer Type</label>
                <div class="swal-input-wrapper">
                    <span class="material-symbols-rounded swal-input-icon">store</span>
                    <select name="type">
                        <option value="Retail">Retail</option>
                        <option value="Wholesale">Wholesale</option>
                    </select>
                </div>
            </div>

            <div class="swal-input-group">
                <label>Opening Balance (Rs)</label>
                <div class="swal-input-wrapper">
                    <span class="material-symbols-rounded swal-input-icon">account_balance_wallet</span>
                    <input type="number" name="balance" step="0.01" value="0.00">
                </div>
            </div>

            <div class="swal-form-actions">
                <a href="{{ route('masters.customers.index') }}" class="swal-btn-cancel">Cancel</a>
                <button type="submit" class="swal-btn-confirm">Register Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
/* SweetAlert / Sleek Design Theme for Forms */
:root {
    --swal-bg: #f8fafc;
    --swal-card: #ffffff;
    --swal-primary: #10b981;
    --swal-primary-hover: #059669;
    --swal-text-main: #0f172a;
    --swal-text-muted: #64748b;
    --swal-border: #e2e8f0;
    --swal-input-bg: #f8fafc;
}

[data-theme="dark"] {
    --swal-bg: #0f172a;
    --swal-card: #1e293b;
    --swal-primary: #10b981;
    --swal-primary-hover: #34d399;
    --swal-text-main: #f8fafc;
    --swal-text-muted: #94a3b8;
    --swal-border: #334155;
    --swal-input-bg: #0f172a;
}

.swal-form-container {
    display: flex;
    justify-content: center;
    align-items: flex-start;
    padding: 2rem 1rem;
    min-height: calc(100vh - 80px);
}

.swal-form-card {
    background: var(--swal-card);
    border-radius: 24px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15), 0 0 0 1px rgba(0, 0, 0, 0.05);
    width: 100%;
    max-width: 750px;
    overflow: hidden;
    animation: swal-pop 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid var(--swal-border);
}

@keyframes swal-pop {
    0% { transform: scale(0.9) translateY(20px); opacity: 0; }
    100% { transform: scale(1) translateY(0); opacity: 1; }
}

.swal-form-header {
    text-align: center;
    padding: 3rem 2rem 2rem;
    border-bottom: 1px solid var(--swal-border);
    background: linear-gradient(180deg, rgba(16,185,129,0.08) 0%, rgba(16,185,129,0) 100%);
    position: relative;
}

.swal-icon-wrapper {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: rgba(16, 185, 129, 0.1);
    color: var(--swal-primary);
    margin-bottom: 1.25rem;
    border: 4px solid rgba(16, 185, 129, 0.2);
    box-shadow: 0 0 20px rgba(16, 185, 129, 0.2);
}

.swal-icon-wrapper .material-symbols-rounded {
    font-size: 40px;
}

.swal-form-header h2 {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 800;
    color: var(--swal-text-main);
    letter-spacing: -0.03em;
}

.swal-form-header p {
    margin: 0.5rem 0 0;
    font-size: 0.9375rem;
    color: var(--swal-text-muted);
}

.swal-form {
    padding: 2.5rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem 2rem;
}

.swal-input-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.swal-col-span-2 {
    grid-column: 1 / -1;
}

.swal-input-group label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--swal-text-muted);
    padding-left: 0.25rem;
}

.swal-input-group label .required {
    color: #ef4444;
}

.swal-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.swal-input-icon {
    position: absolute;
    left: 1.25rem;
    color: var(--swal-text-muted);
    font-size: 22px;
    pointer-events: none;
    opacity: 0.6;
    transition: color 0.3s;
}

.swal-input-wrapper input,
.swal-input-wrapper select,
.swal-input-wrapper textarea {
    width: 100%;
    background: var(--swal-input-bg);
    border: 1.5px solid var(--swal-border);
    color: var(--swal-text-main);
    padding: 0.875rem 1rem 0.875rem 3.25rem;
    border-radius: 14px;
    font-size: 0.9375rem;
    font-family: inherit;
    font-weight: 500;
    outline: none;
    transition: all 0.25s ease;
}

.swal-input-wrapper select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748b'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path সীম/%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 1.2em;
}

.swal-input-wrapper textarea {
    padding-top: 1rem;
    resize: vertical;
    min-height: 100px;
}

.swal-input-wrapper input:focus,
.swal-input-wrapper select:focus,
.swal-input-wrapper textarea:focus {
    border-color: var(--swal-primary);
    background: var(--swal-card);
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.15);
}

.swal-input-wrapper input:focus ~ .swal-input-icon,
.swal-input-wrapper select:focus ~ .swal-input-icon,
.swal-input-wrapper textarea:focus ~ .swal-input-icon {
    color: var(--swal-primary);
    opacity: 1;
}

.swal-form-actions {
    grid-column: 1 / -1;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 1.25rem;
    padding-top: 1.5rem;
    margin-top: 1rem;
    border-top: 1px solid var(--swal-border);
}

.swal-btn-cancel {
    padding: 0.875rem 1.5rem;
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--swal-text-muted);
    background: transparent;
    border: none;
    border-radius: 12px;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.2s;
}

.swal-btn-cancel:hover {
    background: rgba(100, 116, 139, 0.1);
    color: var(--swal-text-main);
}

.swal-btn-confirm {
    padding: 0.875rem 2.5rem;
    font-size: 0.9375rem;
    font-weight: 700;
    color: #ffffff;
    background: var(--swal-primary);
    border: none;
    border-radius: 12px;
    cursor: pointer;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.swal-btn-confirm:hover {
    background: var(--swal-primary-hover);
    transform: translateY(-2px);
    box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
}

@media (max-width: 768px) {
    .swal-form {
        grid-template-columns: 1fr;
        padding: 1.5rem;
    }
    
    .swal-form-header {
        padding: 2rem 1.5rem 1.5rem;
    }
}
</style>
@endpush
