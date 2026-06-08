@extends(request()->ajax() ? 'layouts.empty' : 'layouts.app')
@section('title', 'Billing History - ' . $customer->name)

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
                    <a href="{{ route('masters.customers.billing-history', $customer) }}" class="cm-tab-link cm-tab-link--active">
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
                    @php
                        $activeSubTab = request()->has('daily_page') ? 'retail' : 'wholesale';
                    @endphp
                    
                    {{-- Title Row inside Content Pane --}}
                    <div class="cm-tab-title-row">
                        <h4 class="cm-tab-title">Complete Billing Ledger</h4>
                        <a href="{{ route('billing.weekly.export', ['customer_id' => $customer->id]) }}" class="cm-export-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
    </svg>
        Export
    </a>
                    </div>

                    {{-- Billing Summary Mini Cards --}}
                    <div class="cm-billing-summary-grid">
                        <div class="cm-mini-stat-card">
                            <div class="cm-mini-stat-label">Total Bills</div>
                            <div class="cm-mini-stat-val">{{ $weeklyBills->total() + $dailyBills->total() }}</div>
                        </div>
                        <div class="cm-mini-stat-card">
                            <div class="cm-mini-stat-label">Total Billed</div>
                            <div class="cm-mini-stat-val">Rs {{ number_format($totalBilled, 0) }}</div>
                        </div>
                        <div class="cm-mini-stat-card">
                            <div class="cm-mini-stat-label">Avg. Bill Value</div>
                            <div class="cm-mini-stat-val cm-mini-stat-val--green">
                                Rs {{ number_format(($weeklyBills->total() + $dailyBills->total()) > 0 ? $totalBilled / ($weeklyBills->total() + $dailyBills->total()) : 0, 0) }}
                            </div>
                        </div>
                        <div class="cm-mini-stat-card">
                            <div class="cm-mini-stat-label">Current Due</div>
                            <div class="cm-mini-stat-val cm-mini-stat-val--red">Rs {{ number_format($customer->balance, 0) }}</div>
                        </div>
                    </div>

                    {{-- Sub Tabs Navigation --}}
                    <div class="cm-sub-tabs" style="display: flex; gap: 8px; margin-bottom: 1.5rem; border-bottom: 1px solid var(--cm-card-border); padding-bottom: 0.75rem;">
                        <button class="cm-sub-tab @if($activeSubTab === 'wholesale') active @endif" onclick="switchSubTab('wholesale')" style="background: none; border: none; padding: 8px 16px; font-size: 0.8125rem; font-weight: 700; cursor: pointer; color: var(--cm-text-secondary); border-radius: 8px; transition: all 0.2s;">
                            Wholesale Weekly Invoices ({{ $weeklyBills->total() }})
                        </button>
                        <button class="cm-sub-tab @if($activeSubTab === 'retail') active @endif" onclick="switchSubTab('retail')" style="background: none; border: none; padding: 8px 16px; font-size: 0.8125rem; font-weight: 700; cursor: pointer; color: var(--cm-text-secondary); border-radius: 8px; transition: all 0.2s;">
                            Retail Counter Invoices ({{ $dailyBills->total() }})
                        </button>
                    </div>

                    {{-- Wholesale Tab Panel --}}
                    <div id="wholesale-panel" class="cm-sub-tab-panel @if($activeSubTab !== 'wholesale') cm-hidden @endif">
                        <div class="cm-table-wrap">
                            <table class="cm-table">
                                <thead>
                                    <tr>
                                        <th>Bill ID</th>
                                        <th>Period End</th>
                                        <th>Purchased Products</th>
                                        <th class="cm-th-right">Qty (kg)</th>
                                        <th class="cm-th-right">Amount</th>
                                        <th class="cm-th-center">Status</th>
                                        <th class="cm-th-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($weeklyBills as $bill)
                                    <tr class="cm-tr">
                                        <td class="cm-td">
                                            <span class="cm-mono-val">#WB-{{ str_pad($bill->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        </td>
                                        <td class="cm-td">
                                            <div class="cm-bold-val">{{ $bill->period_end->format('d M Y') }}</div>
                                            <div class="cm-meta-sub">{{ $bill->period_start->format('d M') }} - {{ $bill->period_end->format('d M') }}</div>
                                        </td>
                                        <td class="cm-td">
                                            <div style="display: flex; flex-wrap: wrap; gap: 4px; max-width: 250px;">
                                                @if($bill->items_description)
                                                    @foreach(explode(',', $bill->items_description) as $item)
                                                        @if(trim($item))
                                                            <span class="cm-item-chip">{{ trim($item) }}</span>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    <span style="color: var(--cm-text-muted); font-size: 0.75rem;">—</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="cm-td cm-td-right">
                                            <span class="cm-mono-val cm-bold-val">{{ number_format($bill->quantity_kg, 1) }}</span>
                                        </td>
                                        <td class="cm-td cm-td-right">
                                            <span class="cm-bold-val" style="color: var(--cm-text-primary);">Rs {{ number_format($bill->amount, 0) }}</span>
                                        </td>
                                        <td class="cm-td cm-td-center">
                                            @if($bill->status === 'Paid')
                                                <span class="cm-status-pill cm-status-pill--paid">Paid</span>
                                            @elseif($bill->status === 'Pending')
                                                <span class="cm-status-pill cm-status-pill--pending">Pending</span>
                                            @else
                                                <span class="cm-status-pill cm-status-pill--generated">Generated</span>
                                            @endif
                                        </td>
                                        <td class="cm-td cm-td-center">
                                            <a href="{{ route('billing.weekly.show', $bill) }}" class="cm-table-action-btn" title="View details">
                                                <span class="material-symbols-rounded" style="font-size: 16px;">visibility</span>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="cm-empty-cell">
                                            <div class="cm-empty-icon-sub">
                                                <span class="material-symbols-rounded">receipt_long</span>
                                            </div>
                                            <p class="cm-empty-text">No wholesale billing records found.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if($weeklyBills->hasPages())
                        <div class="cm-pagination">
                            <span class="cm-pg-info">
                                Showing {{ $weeklyBills->firstItem() }}–{{ $weeklyBills->lastItem() }} of {{ $weeklyBills->total() }} statements
                            </span>
                            <div class="cm-pg-links">
                                {!! $weeklyBills->appends(request()->except('weekly_page'))->links() !!}
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Retail Tab Panel --}}
                    <div id="retail-panel" class="cm-sub-tab-panel @if($activeSubTab !== 'retail') cm-hidden @endif">
                        <div class="cm-table-wrap">
                            <table class="cm-table">
                                <thead>
                                    <tr>
                                        <th>Invoice ID</th>
                                        <th>Date</th>
                                        <th>Purchased Products</th>
                                        <th class="cm-th-right">Qty (kg)</th>
                                        <th class="cm-th-right">Amount</th>
                                        <th class="cm-th-center">Status</th>
                                        <th class="cm-th-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($dailyBills as $bill)
                                    <tr class="cm-tr">
                                        <td class="cm-td">
                                            <span class="cm-mono-val">#DB-{{ str_pad($bill->id, 5, '0', STR_PAD_LEFT) }}</span>
                                        </td>
                                        <td class="cm-td">
                                            <div class="cm-bold-val">{{ $bill->date->format('d M Y') }}</div>
                                        </td>
                                        <td class="cm-td">
                                            <div style="display: flex; flex-wrap: wrap; gap: 4px; max-width: 250px;">
                                                @forelse($bill->items as $item)
                                                    <span class="cm-item-chip" style="background: rgba(59, 130, 246, 0.08); color: #2563eb; border-color: rgba(59, 130, 246, 0.05);">
                                                        {{ $item->item_name }} ({{ number_format($item->quantity_kg, 1) }} kg)
                                                    </span>
                                                @empty
                                                    <span style="color: var(--cm-text-muted); font-size: 0.75rem;">—</span>
                                                @endforelse
                                            </div>
                                        </td>
                                        <td class="cm-td cm-td-right">
                                            <span class="cm-mono-val cm-bold-val">{{ number_format($bill->items->sum('quantity_kg'), 1) }}</span>
                                        </td>
                                        <td class="cm-td cm-td-right">
                                            <span class="cm-bold-val" style="color: var(--cm-text-primary);">Rs {{ number_format($bill->amount, 0) }}</span>
                                        </td>
                                        <td class="cm-td cm-td-center">
                                            @if($bill->status === 'Paid')
                                                <span class="cm-status-pill cm-status-pill--paid">Paid</span>
                                            @elseif($bill->status === 'Pending')
                                                <span class="cm-status-pill cm-status-pill--pending">Pending</span>
                                            @else
                                                <span class="cm-status-pill cm-status-pill--generated">Generated</span>
                                            @endif
                                        </td>
                                        <td class="cm-td cm-td-center">
                                            <a href="{{ route('billing.daily.invoice', $bill) }}" target="_blank" class="cm-table-action-btn" title="Print Invoice">
                                                <span class="material-symbols-rounded" style="font-size: 16px;">print</span>
                                            </a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="cm-empty-cell">
                                            <div class="cm-empty-icon-sub">
                                                <span class="material-symbols-rounded">receipt_long</span>
                                            </div>
                                            <p class="cm-empty-text">No retail counter billing records found.</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        @if($dailyBills->hasPages())
                        <div class="cm-pagination">
                            <span class="cm-pg-info">
                                Showing {{ $dailyBills->firstItem() }}–{{ $dailyBills->lastItem() }} of {{ $dailyBills->total() }} statements
                            </span>
                            <div class="cm-pg-links">
                                {!! $dailyBills->appends(request()->except('daily_page'))->links() !!}
                            </div>
                        </div>
                        @endif
                    </div>

                    {{-- Tab Switcher JS Script --}}
                    <script>
                        function switchSubTab(tab) {
                            document.querySelectorAll('.cm-sub-tab').forEach(btn => {
                                btn.classList.remove('active');
                            });
                            document.querySelectorAll('.cm-sub-tab-panel').forEach(panel => {
                                panel.classList.add('cm-hidden');
                            });

                            if (tab === 'wholesale') {
                                document.querySelector("button[onclick=\"switchSubTab('wholesale')\"]").classList.add('active');
                                document.getElementById('wholesale-panel').classList.remove('cm-hidden');
                            } else {
                                document.querySelector("button[onclick=\"switchSubTab('retail')\"]").classList.add('active');
                                document.getElementById('retail-panel').classList.remove('cm-hidden');
                            }
                        }
                    </script>
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
