@extends(request()->ajax() ? 'layouts.empty' : 'layouts.app')
@section('title', 'Customer Details - ' . $customer->name)

@section('content')
@if(!request()->ajax())
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
            <form action="{{ route('masters.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Delete {{ $customer->name }}? This will keep their transaction history intact.')">
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

        {{-- EMI Alerts --}}
        @if($overdueEmis->count() > 0 || $upcomingEmis->count() > 0)
        <div style="grid-column: 1 / -1; margin-bottom: -1rem;">
            @if($overdueEmis->count() > 0)
            <div style="background: rgba(220, 38, 38, 0.05); border: 1px solid rgba(220, 38, 38, 0.2); border-left: 4px solid #dc2626; border-radius: 8px; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 12px; margin-bottom: 1rem;">
                <span class="material-symbols-rounded" style="color: #dc2626; font-size: 24px;">error</span>
                <div>
                    <h4 style="margin: 0; color: #b91c1c; font-size: 0.875rem; font-weight: 700;">Overdue EMI Alert!</h4>
                    <p style="margin: 4px 0 0; color: #7f1d1d; font-size: 0.8125rem;">Customer has {{ $overdueEmis->count() }} overdue EMI(s) totaling <strong>Rs {{ number_format($overdueEmis->sum('emi_amount'), 2) }}</strong>.</p>
                </div>
                <a href="{{ route('masters.customers.emi-history', $customer) }}" style="margin-left: auto; background: #dc2626; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">View Details</a>
            </div>
            @endif

            @if($upcomingEmis->count() > 0)
            <div style="background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.2); border-left: 4px solid #f59e0b; border-radius: 8px; padding: 1rem 1.25rem; display: flex; align-items: center; gap: 12px; margin-bottom: 1rem;">
                <span class="material-symbols-rounded" style="color: #f59e0b; font-size: 24px;">warning</span>
                <div>
                    <h4 style="margin: 0; color: #d97706; font-size: 0.875rem; font-weight: 700;">Upcoming EMI</h4>
                    <p style="margin: 4px 0 0; color: #92400e; font-size: 0.8125rem;">Customer has {{ $upcomingEmis->count() }} EMI(s) due in the next 7 days.</p>
                </div>
                <a href="{{ route('masters.customers.emi-history', $customer) }}" style="margin-left: auto; background: #f59e0b; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.75rem; font-weight: 600;">View Details</a>
            </div>
            @endif
        </div>
        @endif

        {{-- Main Column: Tabs --}}
        <div class="cm-main-col">
@endif
            
            <div class="cm-tabs-card" id="cm-tabs-container" x-data="ajaxTabs" @click="handleTabClick" @mouseover="prefetchTab" @popstate.window="window.location.reload()">
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
                    <a href="{{ route('masters.customers.emi-history', $customer) }}" class="cm-tab-link">
                        EMI Schedule
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
@if(!request()->ajax())
            </div>

        </div>

    </div>

</div>
@endif
@endsection

@push('styles')
    @include('masters.customers.partials.profile-style')
@endpush
