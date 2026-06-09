@extends('layouts.app')
@section('title', 'Purchase Invoices')

@section('content')
<div class="cm-page">

    {{-- Top Bar Header --}}
    <div class="cm-topbar">
        <div>
            <h1 class="cm-page-title">Purchase Invoice Archive</h1>
            <p class="cm-page-sub">Comprehensive audit history and payment states of all procured supplies</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('purchases.entry') }}" class="cm-btn-secondary flex items-center gap-1.5">
                <span class="material-symbols-rounded" style="font-size: 18px;">arrow_back</span>
                Purchase Entry
            </a>
            <a href="{{ route('purchases.export') }}" class="cm-export-btn">
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

    {{-- Summary Cards Grid --}}
    @php
        $totalInvoices = \App\Models\Purchase::count();
        $totalExpenditure = \App\Models\Purchase::sum('total_amount');
        $totalTaxPaid = \App\Models\Purchase::sum('gst_amount');
    @endphp
    <div class="cm-stats mb-8">
        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-blue">
                <span class="material-symbols-rounded">receipt_long</span>
            </div>
            <div>
                <div class="cm-stat-label">Archived Invoices</div>
                <div class="cm-stat-value">{{ number_format($totalInvoices) }}</div>
                <div class="text-xs text-zinc-400 mt-0.5">Cumulative recorded purchases</div>
            </div>
        </div>

        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-teal">
                <span class="material-symbols-rounded">payments</span>
            </div>
            <div>
                <div class="cm-stat-label">Total Expenditure</div>
                <div class="cm-stat-value">₹{{ number_format($totalExpenditure, 2) }}</div>
                <div class="text-xs text-zinc-400 mt-0.5">Net grand total of procurement</div>
            </div>
        </div>

        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-amber">
                <span class="material-symbols-rounded">percent</span>
            </div>
            <div>
                <div class="cm-stat-label">Tax Contribution (GST)</div>
                <div class="cm-stat-value">₹{{ number_format($totalTaxPaid, 2) }}</div>
                <div class="text-xs text-zinc-400 mt-0.5">Consolidated calculated tax</div>
            </div>
        </div>
    </div>

    {{-- Invoices Table Card --}}
    <div class="cm-table-card">
        <div class="cm-table-toolbar">
            <h2 class="cm-toolbar-title">Transaction Ledger</h2>
            <form method="GET" class="cm-search-wrap">
                <span class="material-symbols-rounded cm-search-icon">search</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search vendor or product name..." class="cm-search-input">
            </form>
        </div>

        <div class="cm-table-wrap">
            <table class="cm-table">
                <thead>
                    <tr>
                        <th style="width: 25%;">Vendor Name</th>
                        <th style="width: 25%;">Primary Item Summary</th>
                        <th style="width: 15%;">Billing Date</th>
                        <th style="width: 12%; text-align: right;">GST Amount</th>
                        <th style="width: 13%; text-align: right;">Total Net Amount</th>
                        <th style="width: 10%; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $p)
                        <tr class="cm-tr">
                            <td class="cm-td">
                                <div class="cm-identity">
                                    <div class="cm-avatar cm-avatar--{{ strtolower(substr($p->vendor_name, 0, 1)) }}">
                                        {{ strtoupper(substr($p->vendor_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="cm-cust-name">{{ $p->vendor_name }}</span>
                                        <span class="cm-cust-meta">Invoice ID: {{ $p->invoice_no ?: 'N/A' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="cm-td">
                                @php
                                    $firstItem = $p->items->first();
                                    $othersCount = $p->items->count() - 1;
                                @endphp
                                <div class="flex flex-col gap-1">
                                    @if($firstItem)
                                        <span class="cm-item-chip">
                                            {{ $firstItem->item_name }} 
                                            <b class="ml-1">({{ number_format($firstItem->quantity) }} {{ $firstItem->unit }})</b>
                                        </span>
                                    @else
                                        <span class="text-zinc-400 italic">No products recorded</span>
                                    @endif
                                    
                                    @if($othersCount > 0)
                                        <span class="text-[10px] text-teal-600 dark:text-teal-400 font-bold uppercase tracking-widest pl-1">
                                            + {{ $othersCount }} other product{{ $othersCount > 1 ? 's' : '' }}
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="cm-td text-zinc-500 font-medium">
                                {{ $p->date->format('d M Y') }}
                            </td>
                            <td class="cm-td text-right font-mono text-zinc-500">
                                ₹{{ number_format($p->gst_amount, 2) }}
                            </td>
                            <td class="cm-td text-right font-bold text-zinc-900 dark:text-zinc-100">
                                ₹{{ number_format($p->total_amount, 2) }}
                            </td>
                            <td class="cm-td">
                                <div class="cm-actions justify-center">
                                    <a href="{{ route('purchases.show', $p->id) }}" class="cm-action-btn cm-action-btn--edit text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-950/40" title="View Details">
                                        <span class="material-symbols-rounded">visibility</span>
                                    </a>
                                    <a href="{{ route('purchases.edit', $p->id) }}" class="cm-action-btn cm-action-btn--edit text-teal-600 hover:bg-teal-50 dark:hover:bg-teal-950/40" title="Edit Purchase">
                                        <span class="material-symbols-rounded">edit</span>
                                    </a>
                                    <form action="{{ route('purchases.destroy', $p->id) }}" method="POST" class="delete-form inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)" class="cm-action-btn cm-action-btn--delete text-red-600 hover:bg-red-50 dark:hover:bg-red-950/40" title="Delete Invoice">
                                            <span class="material-symbols-rounded">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="cm-empty">
                                <div class="cm-empty-icon">
                                    <span class="material-symbols-rounded" style="font-size: 32px;">receipt</span>
                                </div>
                                <div class="cm-empty-title">No invoices found</div>
                                <div class="cm-empty-sub">Your query didn't match any recorded transactions</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($purchases->hasPages())
            <div class="cm-pagination-footer">
                {{ $purchases->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@push('styles')
@include('partials.cm-style')
@endpush

@push('scripts')
<script>
    function confirmDelete(button) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete this purchase invoice and revert its stock movements!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#0d9488',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            background: document.documentElement.dataset.theme === 'dark' ? '#111827' : '#ffffff',
            color: document.documentElement.dataset.theme === 'dark' ? '#f3f4f6' : '#0f172a'
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('.delete-form').submit();
            }
        });
    }
</script>
@endpush

