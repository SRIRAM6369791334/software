@extends('layouts.app')
@section('title', 'Customer Details - ' . $customer->name)

@section('content')
<div class="cm-page">

    {{-- Back Link --}}
    <a href="{{ route('masters.customers.index') }}" class="cm-back-btn">
        <span class="material-symbols-rounded" style="font-size: 16px;">arrow_back</span>
        Back to directory
    </a>

    {{-- Top Bar --}}
    <div class="cm-topbar">
        <div class="cm-profile-header">
            <div class="cm-avatar-lg cm-avatar-lg--{{ strtolower(substr($customer->name, 0, 1)) }}">
                {{ strtoupper(substr($customer->name, 0, 2)) }}
            </div>
            <div>
                <h1 class="cm-page-title">{{ $customer->name }}</h1>
                <div class="cm-page-sub">
                    @if($customer->type === 'Wholesale')
                        <span class="cm-badge cm-badge--wholesale">Wholesale Partner</span>
                    @else
                        <span class="cm-badge cm-badge--retail">Retail Buyer</span>
                    @endif
                    <span class="cm-badge cm-badge--route">
                        <span class="material-symbols-rounded" style="font-size: 12px; margin-right: 2px;">alt_route</span>
                        {{ $customer->route ?: 'General Sector' }}
                    </span>
                </div>
            </div>
        </div>

        <div class="cm-actions-group">
            <a href="{{ route('masters.customers.edit', $customer) }}" class="cm-btn-outline">
                <span class="material-symbols-rounded" style="font-size: 16px;">edit</span>
                Edit Profile
            </a>
            <form action="{{ route('masters.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Archive {{ $customer->name }}? This will keep their transaction history intact.')" style="display: inline-block;">
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
        
        {{-- Side Column: Profile & Balance --}}
        <div class="cm-side-col">
            
            {{-- Outstanding Balance Card --}}
            <div class="cm-balance-card">
                <div class="cm-balance-label">Total Outstanding</div>
                <div class="cm-balance-amount">Rs {{ number_format($customer->balance, 2) }}</div>
                
                <div class="cm-balance-actions">
                    <a href="{{ route('payments.customers.create', ['customer_id' => $customer->id]) }}" class="cm-balance-btn-pay">
                        <span class="material-symbols-rounded" style="font-size: 16px; vertical-align: middle; margin-right: 4px;">payments</span>
                        Record Payment
                    </a>
                    <a href="{{ route('masters.customers.ledger-pdf', $customer) }}" class="cm-balance-btn-dl">
                        <span class="material-symbols-rounded" style="font-size: 16px; vertical-align: middle; margin-right: 4px;">download</span>
                        Download Statement
                    </a>
                </div>
            </div>

            {{-- Profile Card --}}
            <div class="cm-card">
                <h3 class="cm-card-title">
                    <span class="material-symbols-rounded" style="font-size: 16px;">contact_page</span>
                    Profile Credentials
                </h3>
                <div class="cm-info-list">
                    <div class="cm-info-item">
                        <span class="material-symbols-rounded cm-info-icon" style="font-size: 18px;">call</span>
                        <div>
                            <div class="cm-info-label">Contact Phone</div>
                            <div class="cm-info-val">{{ $customer->phone }}</div>
                        </div>
                    </div>
                    <div class="cm-info-item">
                        <span class="material-symbols-rounded cm-info-icon" style="font-size: 18px;">location_on</span>
                        <div>
                            <div class="cm-info-label">Store Address</div>
                            <div class="cm-info-val">{{ $customer->address ?: 'Not provided' }}</div>
                        </div>
                    </div>
                    <div class="cm-info-item">
                        <span class="material-symbols-rounded cm-info-icon" style="font-size: 18px;">badge</span>
                        <div>
                            <div class="cm-info-label">GSTIN / Registration</div>
                            <div class="cm-info-val cm-info-val--mono">{{ $customer->gst_number ?: 'Unregistered (No GST)' }}</div>
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
                    <a href="{{ route('masters.customers.show', $customer) }}" class="cm-tab-link cm-tab-link--active">
                        Quick Overview
                    </a>
                    <a href="{{ route('masters.customers.billing-history', $customer) }}" class="cm-tab-link">
                        Billing History
                    </a>
                    <a href="{{ route('masters.customers.payment-history', $customer) }}" class="cm-tab-link">
                        Payment History
                    </a>
                </div>

                {{-- Tab Content Pane --}}
                <div class="cm-tab-content">
                    <h4 class="cm-tab-title">Recent Activity Insights</h4>

                    <div class="cm-overview-grid">
                        <div class="cm-stat-card cm-stat-card--blue">
                            <div class="cm-stat-icon">
                                <span class="material-symbols-rounded">calendar_today</span>
                            </div>
                            <div>
                                <div class="cm-stat-label">Last Bill Date</div>
                                <div class="cm-stat-value">
                                    @if($latestBill)
                                        {{ $latestBill instanceof \App\Models\WeeklyBill ? $latestBill->period_end->format('d M Y') : $latestBill->date->format('d M Y') }}
                                    @else
                                        No bills yet
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="cm-stat-card cm-stat-card--emerald">
                            <div class="cm-stat-icon">
                                <span class="material-symbols-rounded">payments</span>
                            </div>
                            <div>
                                <div class="cm-stat-label">Last Payment</div>
                                <div class="cm-stat-value">
                                    @if($latestPayment)
                                        Rs {{ number_format($latestPayment->amount, 0) }}
                                        <span class="cm-stat-sub">({{ $latestPayment->date->format('d M') }})</span>
                                    @else
                                        N/A
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="cm-overview-stats">
                        <div class="cm-stat-card cm-stat-card--blue">
                            <div class="cm-stat-icon">
                                <span class="material-symbols-rounded">receipt</span>
                            </div>
                            <div>
                                <div class="cm-stat-label">Total Bills</div>
                                <div class="cm-stat-value">
                                    {{ $customer->weekly_bills_count + $customer->daily_bills_count }}
                                    <span class="cm-stat-sub" style="font-size: 0.65rem; display: block; font-weight: 500;">
                                        ({{ $customer->weekly_bills_count }} Whs / {{ $customer->daily_bills_count }} Ret)
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="cm-stat-card cm-stat-card--purple">
                            <div class="cm-stat-icon">
                                <span class="material-symbols-rounded">done_all</span>
                            </div>
                            <div>
                                <div class="cm-stat-label">Total Payments</div>
                                <div class="cm-stat-value">{{ $customer->payments_count }}</div>
                            </div>
                        </div>

                        <div class="cm-stat-card cm-stat-card--emerald">
                            <div class="cm-stat-icon">
                                <span class="material-symbols-rounded">account_balance_wallet</span>
                            </div>
                            <div>
                                <div class="cm-stat-label">Total Paid</div>
                                <div class="cm-stat-value">Rs {{ number_format($customer->payments_sum_amount ?? 0, 0) }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Purchased Products Profile --}}
                    <div style="margin-top: 2.5rem; border-top: 1px solid var(--cm-card-border); padding-top: 2rem;">
                        <h4 class="cm-tab-title" style="margin-bottom: 1.25rem; display: flex; align-items: center; gap: 8px;">
                            <span class="material-symbols-rounded" style="color: var(--cm-accent-emerald); font-size: 20px;">shopping_bag</span>
                            Purchased Products Profile
                        </h4>

                        <div class="cm-overview-grid" style="gap: 1.5rem; margin-bottom: 0;">
                            {{-- Wholesale Purchases --}}
                            <div class="cm-card" style="padding: 1.25rem; background: rgba(59, 130, 246, 0.02); border-color: rgba(59, 130, 246, 0.1); margin: 0; box-shadow: none;">
                                <h5 style="font-size: 0.75rem; font-weight: 700; color: #2563eb; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0; margin-bottom: 1rem; display: flex; align-items: center; gap: 6px;">
                                    <span class="material-symbols-rounded" style="font-size: 16px;">warehouse</span>
                                    Wholesale Products (Weekly Bills)
                                </h5>
                                @forelse($topWholesaleProducts as $prod)
                                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed var(--cm-card-border);">
                                        <div style="font-weight: 600; color: var(--cm-text-primary); font-size: 0.8125rem;">{{ $prod['item_name'] }}</div>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span class="cm-status-pill cm-status-pill--paid" style="background: rgba(59, 130, 246, 0.08); color: #2563eb; font-size: 0.65rem; padding: 1px 6px;">{{ $prod['times_bought'] }}x</span>
                                            <span style="font-weight: 700; color: var(--cm-text-primary); font-size: 0.8125rem;">{{ number_format($prod['total_qty'], 1) }} kg</span>
                                        </div>
                                    </div>
                                @empty
                                    <div style="font-size: 0.75rem; color: var(--cm-text-muted); text-align: center; padding: 1.5rem 0;">
                                        No wholesale product purchases recorded.
                                    </div>
                                @endforelse
                            </div>

                            {{-- Retail Purchases --}}
                            <div class="cm-card" style="padding: 1.25rem; background: rgba(16, 185, 129, 0.02); border-color: rgba(16, 185, 129, 0.1); margin: 0; box-shadow: none;">
                                <h5 style="font-size: 0.75rem; font-weight: 700; color: #059669; text-transform: uppercase; letter-spacing: 0.05em; margin-top: 0; margin-bottom: 1rem; display: flex; align-items: center; gap: 6px;">
                                    <span class="material-symbols-rounded" style="font-size: 16px;">storefront</span>
                                    Retail Products (Daily Invoices)
                                </h5>
                                @forelse($topRetailProducts as $prod)
                                    <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px 0; border-bottom: 1px dashed var(--cm-card-border);">
                                        <div style="font-weight: 600; color: var(--cm-text-primary); font-size: 0.8125rem;">{{ $prod->item_name }}</div>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <span class="cm-status-pill cm-status-pill--paid" style="background: rgba(16, 185, 129, 0.08); color: #059669; font-size: 0.65rem; padding: 1px 6px;">{{ $prod->times_bought }}x</span>
                                            <span style="font-weight: 700; color: var(--cm-text-primary); font-size: 0.8125rem;">{{ number_format($prod->total_qty, 1) }} kg</span>
                                        </div>
                                    </div>
                                @empty
                                    <div style="font-size: 0.75rem; color: var(--cm-text-muted); text-align: center; padding: 1.5rem 0;">
                                        No retail product purchases recorded.
                                    </div>
                                @endforelse
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
    background: linear-gradient(135deg, #059669 0%, #10b981 50%, #06b6d4 100%);
    border-radius: 18px;
    padding: 2.25rem 1.75rem;
    color: #ffffff;
    text-align: center;
    box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.3);
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
    color: #047857;
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
    background: rgba(4, 120, 87, 0.4);
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
    background: rgba(4, 120, 87, 0.6);
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

/* Mini stat cards inside Quick Overview */
.cm-overview-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1.25rem;
    margin-bottom: 1.5rem;
}
@media (max-width: 640px) {
    .cm-overview-grid {
        grid-template-columns: 1fr;
    }
}

.cm-overview-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}
@media (max-width: 640px) {
    .cm-overview-stats {
        grid-template-columns: 1fr;
    }
}

.cm-stat-card {
    background: var(--cm-card-bg);
    border: 1px solid var(--cm-card-border);
    border-radius: 12px;
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--cm-shadow-sm);
    transition: all 0.2s ease;
}
.cm-stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--cm-shadow-md);
}
.cm-stat-card--purple { background: rgba(139, 92, 246, 0.03); border-color: rgba(139, 92, 246, 0.15); }
.cm-stat-card--blue   { background: rgba(59, 130, 246, 0.03); border-color: rgba(59, 130, 246, 0.15); }
.cm-stat-card--emerald{ background: rgba(16, 185, 129, 0.03); border-color: rgba(16, 185, 129, 0.15); }

.cm-stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.cm-stat-card--purple .cm-stat-icon { background: #ede9fe; color: #6d28d9; }
.cm-stat-card--blue .cm-stat-icon   { background: #dbeafe; color: #1d4ed8; }
.cm-stat-card--emerald .cm-stat-icon { background: #d1fae5; color: #047857; }

.cm-stat-label {
    font-size: 0.6875rem;
    color: var(--cm-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.07em;
    margin-bottom: 2px;
}
.cm-stat-value {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--cm-text-primary);
}
.cm-stat-sub {
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--cm-text-muted);
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
.cm-badge--retail    { background: rgba(16, 185, 129, 0.12); color: #059669; }
.cm-badge--route     { background: rgba(107, 114, 128, 0.06); color: var(--cm-text-secondary); border: 1px solid var(--cm-card-border); }
[data-theme='dark'] .cm-badge--wholesale { background: rgba(59, 130, 246, 0.2); color: #60a5fa; }
[data-theme='dark'] .cm-badge--retail    { background: rgba(16, 185, 129, 0.2); color: #34d399; }
</style>
@endpush
