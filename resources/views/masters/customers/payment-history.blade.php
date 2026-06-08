@extends(request()->ajax() ? 'layouts.empty' : 'layouts.app')
@section('title', 'Payment History - ' . $customer->name)

@section('content')
@if(!request()->ajax())
<div class="cm-page">

    {{-- Back Link --}}
    <a href="{{ route('masters.customers.show', $customer) }}" class="cm-back-btn">
        <span class="material-symbols-rounded" style="font-size: 16px;">arrow_back</span>
        Back to details
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

        {{-- Main Column: Tabs --}}
        <div class="cm-main-col">
@endif
            
            <div class="cm-tabs-card" id="cm-tabs-container" x-data="ajaxTabs" @click="handleTabClick" @mouseover="prefetchTab" @popstate.window="window.location.reload()">
                {{-- Tabs Navigation --}}
                <div class="cm-tabs-header">
                    <a href="{{ route('masters.customers.show', $customer) }}" class="cm-tab-link">
                        Quick Overview
                    </a>
                    <a href="{{ route('masters.customers.billing-history', $customer) }}" class="cm-tab-link">
                        Billing History
                    </a>
                    <a href="{{ route('masters.customers.payment-history', $customer) }}" class="cm-tab-link cm-tab-link--active">
                        Payment History
                    </a>
                    <a href="{{ route('masters.customers.emi-history', $customer) }}" class="cm-tab-link">
                        EMI Schedule
                    </a>
                </div>

                {{-- Tab Content Pane --}}
                <div class="cm-tab-content">
                    
                    {{-- Title Row inside Content Pane --}}
                    <div class="cm-tab-title-row">
                        <h4 class="cm-tab-title">Complete Payment Ledger</h4>
                        <div class="cm-actions-group">
                            <a href="{{ route('payments.customers.create', ['customer_id' => $customer->id]) }}" class="cm-btn-outline cm-btn-outline--sm" style="border-color: var(--cm-accent-emerald); color: var(--cm-accent-emerald);">
                                <span class="material-symbols-rounded" style="font-size: 14px;">add_circle</span>
                                Record Payment
                            </a>
                            <a href="{{ route('payments.customers.export', ['customer_id' => $customer->id]) }}" class="cm-export-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
    </svg>
        Export
    </a>
                        </div>
                    </div>

                    {{-- Payment Summary Mini Cards --}}
                    <div class="cm-billing-summary-grid">
                        <div class="cm-mini-stat-card">
                            <div class="cm-mini-stat-label">Total Receipts</div>
                            <div class="cm-mini-stat-val">{{ $payments->total() }}</div>
                        </div>
                        <div class="cm-mini-stat-card">
                            <div class="cm-mini-stat-label">Total Received</div>
                            <div class="cm-mini-stat-val cm-mini-stat-val--green">Rs {{ number_format($totalPaid, 0) }}</div>
                        </div>
                        <div class="cm-mini-stat-card">
                            <div class="cm-mini-stat-label">Avg. Receipt</div>
                            <div class="cm-mini-stat-val">Rs {{ number_format($payments->total() > 0 ? $totalPaid / $payments->total() : 0, 0) }}</div>
                        </div>
                        <div class="cm-mini-stat-card">
                            <div class="cm-mini-stat-label">Last Payment</div>
                            <div class="cm-mini-stat-val" style="font-size: 0.9375rem; line-height: 1.6;">{{ $payments->first()?->date->format('d M Y') ?? 'None yet' }}</div>
                        </div>
                    </div>

                    {{-- Payments History Table --}}
                    <div class="cm-table-wrap">
                        <table class="cm-table">
                            <thead>
                                <tr>
                                    <th>Date Received</th>
                                    <th>Method / Mode</th>
                                    <th>Internal Notes</th>
                                    <th class="cm-th-right">Amount Paid</th>
                                    <th class="cm-th-right">Balance After</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                <tr class="cm-tr">
                                    <td class="cm-td">
                                        <div class="cm-bold-val">{{ $payment->date->format('d M Y') }}</div>
                                        <div class="cm-meta-sub">{{ $payment->created_at->format('H:i A') }}</div>
                                    </td>
                                    <td class="cm-td">
                                        <span class="cm-method-tag cm-method-tag--{{ strtolower($payment->payment_mode) }}">
                                            {{ $payment->payment_mode }}
                                        </span>
                                    </td>
                                    <td class="cm-td">
                                        <div class="cm-desc-val" title="{{ $payment->notes }}">
                                            {{ $payment->notes ?: '-' }}
                                        </div>
                                    </td>
                                    <td class="cm-td cm-td-right">
                                        <span class="cm-bold-val" style="color: var(--cm-accent-emerald); font-size: 0.9375rem;">Rs {{ number_format($payment->amount, 0) }}</span>
                                    </td>
                                    <td class="cm-td cm-td-right">
                                        <span class="cm-mono-val">Rs {{ number_format($payment->balance_after, 0) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="cm-empty-cell">
                                        <div class="cm-empty-icon-sub">
                                            <span class="material-symbols-rounded">payments</span>
                                        </div>
                                        <p class="cm-empty-text">No payment receipts found for this customer.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Controls --}}
                    @if($payments->hasPages())
                    <div class="cm-pagination">
                        <span class="cm-pg-info">
                            Showing {{ $payments->firstItem() }}–{{ $payments->lastItem() }} of {{ $payments->total() }} receipts
                        </span>
                        <div class="cm-pg-links">
                            {{ $payments->links() }}
                        </div>
                    </div>
                    @endif

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
