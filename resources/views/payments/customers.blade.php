@extends('layouts.app')
@section('title', 'Customer Payments')

@section('content')

<div class="cm-page">

    {{-- Top Bar --}}
    <div class="cm-topbar">
        <div>
            <h1 class="cm-page-title">Customer Collections</h1>
            <p class="cm-page-sub">Manage inbound payments and customer ledgers</p>
        </div>
        <div class="cm-toolbar-right" style="gap: 12px;">
            <a href="{{ route('payments.customers.export') }}" class="cm-export-btn">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round">
        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
        <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
    </svg>
        Export
    </a>
            <button onclick="document.getElementById('add-payment-modal').classList.remove('cm-hidden')"
                class="cm-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Record Collection
            </button>
        </div>
    </div>

    {{-- Stats --}}
    <div class="cm-stats" style="grid-template-columns: repeat(3, 1fr);">
        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-teal">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                </svg>
            </div>
            <div>
                <div class="cm-stat-label">Total Collected</div>
                <div class="cm-stat-value">Rs {{ number_format($payments->sum('amount'), 0) }}</div>
            </div>
        </div>
        <div class="cm-stat-card cm-stat-card--danger">
            <div class="cm-stat-icon cm-icon-red">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/><path d="M21 21v-2a4 4 0 0 0-3-3.87"/><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <div>
                <div class="cm-stat-label">Total Outstanding</div>
                <div class="cm-stat-value">Rs {{ number_format($customers->sum('balance'), 0) }}</div>
            </div>
        </div>
        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M2 12h4l2-9 5 18 2-9h5"/>
                </svg>
            </div>
            <div>
                <div class="cm-stat-label">Recent Collections</div>
                <div class="cm-stat-value">{{ $payments->where('date', '>=', now()->subDays(7))->count() }} <span style="font-size: 12px; font-weight: normal; color: #94a3b8;">This Week</span></div>
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
                    placeholder="Search customer or reference…" class="cm-search-input">
            </form>
        </div>

        <div class="cm-table-wrap">
            <table class="cm-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Collection Date</th>
                        <th class="cm-th-right">Amount Received</th>
                        <th class="cm-th-center">Payment Mode</th>
                        <th class="cm-th-center">Receipt Type</th>
                        <th class="cm-th-right">Balance After</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                    <tr class="cm-tr">
                        <td class="cm-td">
                            <div class="cm-identity">
                                <div class="cm-avatar cm-avatar--{{ strtolower(substr($p->customer->name ?? '?', 0, 1)) }}">
                                    {{ strtoupper(substr($p->customer->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <div class="cm-cust-name">{{ $p->customer->name ?? '-' }}</div>
                                    <div class="cm-cust-meta">{{ $p->customer->phone ?? 'NO PHONE' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="cm-td">
                            <div class="cm-cust-name">{{ $p->date->format('d M, Y') }}</div>
                            <div class="cm-cust-meta">{{ $p->date->format('l') }}</div>
                        </td>
                        <td class="cm-td cm-td-right">
                            <span class="cm-amount">Rs {{ number_format($p->amount, 0) }}</span>
                        </td>
                        <td class="cm-td cm-th-center">
                            <span class="cm-badge cm-badge--gray">{{ $p->payment_mode }}</span>
                        </td>
                        <td class="cm-td cm-th-center">
                            @php
                                $typeMap = [
                                    'Full' => 'cm-badge--green',
                                    'Part' => 'cm-badge--amber',
                                    'Advance' => 'cm-badge--blue',
                                ];
                                $badgeClass = $typeMap[$p->payment_type] ?? 'cm-badge--gray';
                            @endphp
                            <span class="cm-badge {{ $badgeClass }}">{{ strtoupper($p->payment_type) }}</span>
                        </td>
                        <td class="cm-td cm-td-right">
                            @if($p->balance_after > 0)
                                <span class="cm-balance cm-balance--due">Rs {{ number_format($p->balance_after, 0) }}</span>
                            @else
                                <span class="cm-balance cm-balance--clear">CLEARED</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="cm-empty">
                            <div class="cm-empty-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                </svg>
                            </div>
                            <p class="cm-empty-title">No Collections Found</p>
                            <p class="cm-empty-sub">Record your first customer payment today.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($payments->hasPages())
        <div class="cm-pagination">
            <span class="cm-pg-info">
                Showing {{ $payments->firstItem() }}–{{ $payments->lastItem() }} of {{ $payments->total() }} payments
            </span>
            <div class="cm-pg-links">
                {{ $payments->withQueryString()->links() }}
            </div>
        </div>
        @endif
    </div>

</div>

{{-- ================================================ --}}
{{-- ADD PAYMENT MODAL                                --}}
{{-- ================================================ --}}
<div id="add-payment-modal" class="cm-modal-overlay cm-hidden">
    <div class="cm-modal">
        <div class="cm-modal-header">
            <div class="cm-modal-title-row">
                <div class="cm-modal-icon cm-modal-icon--green">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </div>
                <div>
                    <div class="cm-modal-title">Record Collection</div>
                    <div class="cm-modal-sub">Enter payment details to update customer ledger</div>
                </div>
            </div>
            <button onclick="document.getElementById('add-payment-modal').classList.add('cm-hidden')"
                class="cm-close-btn" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                    stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('payments.customers.store') }}" method="POST" class="cm-modal-body">
            @csrf
            
            <div class="cm-form-group">
                <label class="cm-form-label">Customer <span class="cm-required">*</span></label>
                <select name="customer_id" required class="cm-form-input cm-form-select">
                    <option value="">Choose customer…</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }} (Pending: Rs {{ number_format($c->balance, 0) }})</option>
                    @endforeach
                </select>
            </div>

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Amount (Rs) <span class="cm-required">*</span></label>
                    <input type="number" name="amount" required step="0.01" min="0.01" placeholder="0.00"
                        class="cm-form-input" style="font-weight: 600;">
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Payment Date <span class="cm-required">*</span></label>
                    <input type="date" name="date" required value="{{ date('Y-m-d') }}"
                        class="cm-form-input">
                </div>
            </div>

            <div class="cm-form-grid">
                <div class="cm-form-group">
                    <label class="cm-form-label">Payment Mode <span class="cm-required">*</span></label>
                    <select name="payment_mode" required class="cm-form-input cm-form-select">
                        @foreach(['Cash','UPI','NEFT','Cheque'] as $m)<option value="{{ $m }}">{{ $m }}</option>@endforeach
                    </select>
                </div>
                <div class="cm-form-group">
                    <label class="cm-form-label">Receipt Type <span class="cm-required">*</span></label>
                    <select name="payment_type" required class="cm-form-input cm-form-select">
                        @foreach(['Part','Full','Advance'] as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
                    </select>
                </div>
            </div>

            <div class="cm-form-group">
                <label class="cm-form-label">Remarks / Reference</label>
                <textarea name="notes" rows="2" placeholder="e.g. UPI Transaction ID or Cheque Number..."
                    class="cm-form-input cm-form-textarea"></textarea>
            </div>

            <div class="cm-modal-footer">
                <button type="button"
                    onclick="document.getElementById('add-payment-modal').classList.add('cm-hidden')"
                    class="cm-btn-ghost">Cancel</button>
                <button type="submit" class="cm-btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                        stroke-linejoin="round">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Record Collection
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
@include('partials.cm-style')
@endpush

@push('scripts')
<script>
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

