@extends('layouts.app')
@section('title', 'Dealer Details - ' . $dealer->firm_name)

@section('content')
<div class="cm-page">

    {{-- Back Link --}}
    <a href="{{ route('masters.dealers.index') }}" class="cm-back-btn">
        <span class="material-symbols-rounded" style="font-size: 16px;">arrow_back</span>
        Back to Directory
    </a>

    {{-- Top Bar --}}
    <div class="cm-topbar">
        <div class="cm-profile-header">
            <div class="cm-avatar-lg cm-avatar-lg--{{ strtolower(substr($dealer->firm_name, 0, 1)) }}">
                {{ strtoupper(substr($dealer->firm_name, 0, 2)) }}
            </div>
            <div>
                <h1 class="cm-page-title">{{ $dealer->firm_name }}</h1>
                <div class="cm-page-sub">
                    <span class="cm-badge cm-badge--wholesale">Supplier / Partner</span>
                    <span class="cm-badge cm-badge--route">
                        <span class="material-symbols-rounded" style="font-size: 12px; margin-right: 2px;">alt_route</span>
                        {{ $dealer->route ?: 'General Area' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="cm-actions-group">
            <a href="{{ route('masters.dealers.edit', $dealer) }}" class="cm-btn-outline">
                <span class="material-symbols-rounded" style="font-size: 16px;">edit</span>
                Edit Profile
            </a>
            <form action="{{ route('masters.dealers.destroy', $dealer) }}" method="POST" onsubmit="return confirm('Delete {{ $dealer->firm_name }}? This will keep their transaction history intact.')">
                @csrf @method('DELETE')
                <button type="submit" class="cm-btn-danger">
                    <span class="material-symbols-rounded" style="font-size: 16px;">delete</span>
                    Delete
                </button>
            </form>
        </div>
    </div>

    {{-- Layout Grid --}}
    <div class="cm-detail-layout">
        
        {{-- Side Column: Profile & Balance --}}
        <div class="cm-side-col">
            
            {{-- Outstanding Balance Card --}}
            <div class="cm-balance-card">
                <div class="cm-balance-label">Total Payable</div>
                <div class="cm-balance-amount">Rs {{ number_format($dealer->pending_amount, 2) }}</div>
                
                <div class="cm-balance-actions">
                    <a href="{{ route('payments.dealers.create', ['dealer_id' => $dealer->id]) }}" class="cm-balance-btn-pay">
                        <span class="material-symbols-rounded" style="font-size: 16px; vertical-align: middle; margin-right: 4px;">payments</span>
                        Record Payment
                    </a>
                    <a href="{{ route('masters.dealers.ledger-pdf', $dealer) }}" class="cm-balance-btn-dl">
                        <span class="material-symbols-rounded" style="font-size: 16px; vertical-align: middle; margin-right: 4px;">download</span>
                        Download Ledger
                    </a>
                </div>
            </div>

            {{-- Profile Card --}}
            <div class="cm-card">
                <h3 class="cm-card-title">
                    <span class="material-symbols-rounded" style="font-size: 16px;">contact_page</span>
                    Firm Credentials
                </h3>
                <div class="cm-info-list">
                    <div class="cm-info-item">
                        <span class="material-symbols-rounded cm-info-icon" style="font-size: 18px;">person</span>
                        <div>
                            <div class="cm-info-label">Contact Person</div>
                            <div class="cm-info-val">{{ $dealer->contact_person ?: '-' }}</div>
                        </div>
                    </div>
                    <div class="cm-info-item">
                        <span class="material-symbols-rounded cm-info-icon" style="font-size: 18px;">call</span>
                        <div>
                            <div class="cm-info-label">Contact Phone</div>
                            <div class="cm-info-val">{{ $dealer->phone }}</div>
                        </div>
                    </div>
                    <div class="cm-info-item">
                        <span class="material-symbols-rounded cm-info-icon" style="font-size: 18px;">location_on</span>
                        <div>
                            <div class="cm-info-label">Store Location</div>
                            <div class="cm-info-val">{{ $dealer->location ?: 'Not provided' }}</div>
                        </div>
                    </div>
                    <div class="cm-info-item">
                        <span class="material-symbols-rounded cm-info-icon" style="font-size: 18px;">badge</span>
                        <div>
                            <div class="cm-info-label">GSTIN / Registration</div>
                            <div class="cm-info-val cm-info-val--mono">{{ $dealer->gst_number ?: 'Unregistered (No GST)' }}</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- Main Column: Tabs --}}
        <div class="cm-main-col">
            
            <div class="cm-tabs-card">
                {{-- Tabs Navigation --}}
                <div class="cm-tabs-header">
                    <a href="{{ route('masters.dealers.show', $dealer) }}" class="cm-tab-link cm-tab-link--active">
                        Quick Overview
                    </a>
                    <a href="{{ route('masters.dealers.purchase-history', $dealer) }}" class="cm-tab-link">
                        Purchase Orders
                    </a>
                    <a href="{{ route('payments.dealers.ledger', $dealer) }}" class="cm-tab-link">
                        Payment Ledger
                    </a>
                    <a href="{{ route('masters.dealers.outstanding-report', $dealer) }}" class="cm-tab-link">
                        Outstanding Report
                    </a>
                </div>

                {{-- Tab Content Pane --}}
                <div class="cm-tab-content">
                    <div class="cm-overview-sections">
                        
                        <div class="cm-overview-column">
                            <h4 class="cm-tab-title">Recent Purchases</h4>
                            <div class="cm-activity-list">
                                @forelse($dealer->purchases()->with('items')->latest()->take(3)->get() as $purchase)
                                    <div class="cm-activity-item">
                                        <div>
                                            <p class="cm-activity-title">
                                                @if($purchase->items->isNotEmpty())
                                                    {{ $purchase->items->pluck('item_name')->join(', ') }}
                                                @else
                                                    {{ $purchase->item }}
                                                @endif
                                            </p>
                                            <p class="cm-activity-date">{{ $purchase->date->format('d M Y') }}</p>
                                        </div>
                                        <div class="cm-activity-val">Rs {{ number_format($purchase->total_amount, 0) }}</div>
                                    </div>
                                @empty
                                    <p class="cm-empty-text">No recent purchases.</p>
                                @endforelse
                            </div>
                        </div>

                        <div class="cm-overview-column">
                            <h4 class="cm-tab-title">Recent Payments</h4>
                            <div class="cm-activity-list">
                                @forelse($dealer->payments()->latest()->take(3)->get() as $payment)
                                    <div class="cm-activity-item">
                                        <div>
                                            <p class="cm-activity-title">Payment - {{ $payment->payment_mode }}</p>
                                            <p class="cm-activity-date">{{ $payment->date->format('d M Y') }}</p>
                                        </div>
                                        <div class="cm-activity-val cm-activity-val--success">Rs {{ number_format($payment->amount, 0) }}</div>
                                    </div>
                                @empty
                                    <p class="cm-empty-text">No recent payments.</p>
                                @endforelse
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>

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

/* ── Container & Layout ── */
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
    background: linear-gradient(135deg, #10b981, #06b6d4);
    color: #ffffff;
}

/* Dynamic avatar colors by starting letter */
.cm-avatar-lg--a, .cm-avatar-lg--e, .cm-avatar-lg--i, .cm-avatar-lg--m, .cm-avatar-lg--q, .cm-avatar-lg--u, .cm-avatar-lg--y {
    background: linear-gradient(135deg, #10b981, #3b82f6);
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

/* ── Column 1: Info & Balance ── */
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

/* Balance Card */
.cm-balance-card {
    background: linear-gradient(135deg, #d97706 0%, #f59e0b 50%, #eab308 100%);
    border-radius: 18px;
    padding: 2.25rem 1.75rem;
    color: #ffffff;
    text-align: center;
    box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.3);
    position: relative;
    overflow: hidden;
}
.cm-balance-card::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 60%);
    pointer-events: none;
}
.cm-balance-label {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.15em;
    opacity: 0.85;
    margin-bottom: 0.5rem;
}
.cm-balance-amount {
    font-size: 2.375rem;
    font-weight: 800;
    letter-spacing: -0.03em;
}
.cm-balance-actions {
    margin-top: 1.75rem;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.cm-balance-btn-pay {
    background: #ffffff;
    color: #b45309;
    border: none;
    border-radius: 12px;
    padding: 12px 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
.cm-balance-btn-pay:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(0,0,0,0.15);
    background: #f8fafc;
}
.cm-balance-btn-dl {
    background: rgba(180, 83, 9, 0.4);
    color: #ffffff;
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 12px;
    padding: 12px 20px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
}
.cm-balance-btn-dl:hover {
    transform: translateY(-2px);
    background: rgba(180, 83, 9, 0.6);
    border-color: rgba(255, 255, 255, 0.5);
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
    color: var(--cm-accent-emerald);
    border-bottom-color: var(--cm-accent-emerald);
    background: var(--cm-card-bg);
    font-weight: 700;
}

.cm-tab-content {
    padding: 2rem;
}

.cm-tab-title {
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--cm-text-primary);
    margin-bottom: 1.5rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

/* Overview Layout */
.cm-overview-sections {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
}
@media (max-width: 768px) {
    .cm-overview-sections {
        grid-template-columns: 1fr;
    }
}

.cm-overview-column {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.cm-activity-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.cm-activity-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px;
    background: var(--cm-bg);
    border: 0.5px solid var(--cm-card-border);
    border-radius: 10px;
    transition: transform 0.2s ease;
}
.cm-activity-item:hover {
    transform: translateY(-1px);
}

.cm-activity-title {
    font-size: 0.8125rem;
    font-weight: 600;
    color: var(--cm-text-primary);
    margin: 0;
}
.cm-activity-date {
    font-size: 0.6875rem;
    color: var(--cm-text-muted);
    margin: 2px 0 0;
}
.cm-activity-val {
    font-size: 0.875rem;
    font-weight: 700;
    color: var(--cm-text-primary);
}
.cm-activity-val--success {
    color: var(--cm-accent-emerald);
}

.cm-empty-text {
    font-size: 0.8125rem;
    color: var(--cm-text-muted);
    font-style: italic;
    margin: 0;
}

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
.cm-badge--wholesale { background: rgba(59, 130, 246, 0.12); color: #2563eb; }
.cm-badge--route     { background: rgba(107, 114, 128, 0.06); color: var(--cm-text-secondary); border: 1px solid var(--cm-card-border); }
[data-theme='dark'] .cm-badge--wholesale { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
</style>
@endpush
