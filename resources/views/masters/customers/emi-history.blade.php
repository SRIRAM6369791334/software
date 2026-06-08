@extends(request()->ajax() ? 'layouts.empty' : 'layouts.app')
@section('title', 'EMI Schedule - ' . $customer->name)

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
            <form action="{{ route('masters.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Delete {{ $customer->name }}?')">
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
                    <a href="{{ route('masters.customers.payment-history', $customer) }}" class="cm-tab-link">
                        Payment History
                    </a>
                    <a href="{{ route('masters.customers.emi-history', $customer) }}" class="cm-tab-link cm-tab-link--active">
                        EMI Schedule
                    </a>
                </div>

                {{-- Tab Content Pane --}}
                <div class="cm-tab-content">
                    <div class="cm-tab-title-row">
                        <h4 class="cm-tab-title">Customer EMI Schedule</h4>
                    </div>

                    {{-- EMI Summary Cards --}}
                    <div class="cm-billing-summary-grid">
                        <div class="cm-mini-stat-card">
                            <div class="cm-mini-stat-label">Total EMIs</div>
                            <div class="cm-mini-stat-val">{{ $emis->total() }}</div>
                        </div>
                        <div class="cm-mini-stat-card">
                            <div class="cm-mini-stat-label">Upcoming / Overdue</div>
                            <div class="cm-mini-stat-val cm-mini-stat-val--red">
                                {{ $emis->where('status', 'Upcoming')->count() }}
                            </div>
                        </div>
                        <div class="cm-mini-stat-card">
                            <div class="cm-mini-stat-label">Paid EMIs</div>
                            <div class="cm-mini-stat-val cm-mini-stat-val--green">
                                {{ $emis->where('status', 'Paid')->count() }}
                            </div>
                        </div>
                        <div class="cm-mini-stat-card">
                            <div class="cm-mini-stat-label">Total Amount Due</div>
                            <div class="cm-mini-stat-val">
                                Rs {{ number_format($emis->where('status', 'Upcoming')->sum('emi_amount'), 0) }}
                            </div>
                        </div>
                    </div>

                    {{-- Table --}}
                    <div class="cm-table-wrap">
                        <table class="cm-table">
                            <thead>
                                <tr>
                                    <th>EMI ID</th>
                                    <th>Due Date</th>
                                    <th class="cm-th-right">EMI Amount</th>
                                    <th>Notes / Remarks</th>
                                    <th class="cm-th-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($emis as $emi)
                                <tr class="cm-tr">
                                    <td class="cm-td">
                                        <span class="cm-mono-val">#EMI-{{ str_pad($emi->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    </td>
                                    <td class="cm-td">
                                        <div class="cm-bold-val">{{ \Carbon\Carbon::parse($emi->due_date)->format('d M Y') }}</div>
                                        @if($emi->status === 'Upcoming' && \Carbon\Carbon::parse($emi->due_date)->isPast())
                                            <span style="color: #dc2626; font-size: 0.65rem; font-weight: 700; text-transform: uppercase;">Overdue</span>
                                        @endif
                                    </td>
                                    <td class="cm-td cm-td-right">
                                        <span class="cm-bold-val">Rs {{ number_format($emi->emi_amount, 2) }}</span>
                                    </td>
                                    <td class="cm-td">
                                        <div class="cm-desc-val">{{ $emi->remarks ?: '—' }}</div>
                                    </td>
                                    <td class="cm-td cm-td-center">
                                        @if($emi->status === 'Paid')
                                            <span class="cm-status-pill cm-status-pill--cash">Paid</span>
                                        @else
                                            <span class="cm-status-pill cm-status-pill--pending">Upcoming</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="cm-empty-cell">
                                        <div class="cm-empty-icon-sub">
                                            <span class="material-symbols-rounded">calendar_month</span>
                                        </div>
                                        <p class="cm-empty-text">No EMI schedule found for this customer.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @if($emis->hasPages())
                    <div class="cm-pagination">
                        <span class="cm-pg-info">
                            Showing {{ $emis->firstItem() }}–{{ $emis->lastItem() }} of {{ $emis->total() }} EMIs
                        </span>
                        <div class="cm-pg-links">
                            {!! $emis->links() !!}
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
