@extends('layouts.app')
@section('title', 'Purchase Invoices')

@section('content')
<div class="animate-fade-in">
    <x-page-header title="Purchase Invoice Archive" subtitle="Comprehensive audit history and payment states of all procured supplies">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('purchases.export') }}" icon="download">
                Export
            </x-button>
            <x-button variant="primary" href="{{ route('purchases.entry') }}" icon="add">
                Purchase Entry
            </x-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Stats --}}
    @php
        $totalInvoices = \App\Models\Purchase::count();
        $totalExpenditure = \App\Models\Purchase::sum('total_amount');
        $totalTaxPaid = \App\Models\Purchase::sum('gst_amount');
    @endphp
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-stat-card 
            label="Archived Invoices" 
            value="{{ number_format($totalInvoices) }}" 
            icon="receipt_long" 
            color="blue" />
        <x-stat-card 
            label="Total Expenditure" 
            value="Rs {{ number_format($totalExpenditure, 2) }}" 
            icon="payments" 
            color="emerald" />
        <x-stat-card 
            label="Tax Contribution (GST)" 
            value="Rs {{ number_format($totalTaxPaid, 2) }}" 
            icon="percent" 
            color="amber" />
    </div>

    {{-- Main Grid --}}
    <x-card>
        <div class="p-4 border-b border-zinc-200/50 dark:border-zinc-800/50 flex justify-between items-center">
            <h2 class="font-cabinet text-lg font-bold text-zinc-900 dark:text-zinc-50">Transaction Ledger</h2>
            <form method="GET" class="relative w-full max-w-sm">
                <span class="material-symbols-rounded absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-[20px]">search</span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search vendor or product name..." class="w-full pl-10 pr-4 py-2 border border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900 rounded-lg text-sm focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 transition-colors dark:text-zinc-100">
            </form>
        </div>
        
        <x-data-table :headers="['Vendor Name', 'Primary Item Summary', 'Billing Date', 'GST Amount', 'Total Net Amount', 'Actions']">
            @forelse($purchases as $p)
                <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50 transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <x-avatar name="{{ $p->vendor_name }}" size="sm" />
                            <div>
                                <div class="font-bold text-zinc-900 dark:text-zinc-100">{{ $p->vendor_name }}</div>
                                <div class="text-xs text-zinc-500">Invoice ID: {{ $p->invoice_no ?: 'N/A' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $firstItem = $p->items->first();
                            $othersCount = $p->items->count() - 1;
                        @endphp
                        <div class="flex flex-col gap-1">
                            @if($firstItem)
                                <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold w-max">
                                    {{ $firstItem->item_name }} 
                                    <span class="font-bold opacity-80 ml-1">({{ number_format($firstItem->quantity) }} {{ $firstItem->unit }})</span>
                                </div>
                            @else
                                <span class="text-zinc-400 italic text-sm">No products recorded</span>
                            @endif
                            
                            @if($othersCount > 0)
                                <span class="text-[10px] text-zinc-500 dark:text-zinc-400 font-bold uppercase tracking-widest pl-2 mt-0.5">
                                    + {{ $othersCount }} other product{{ $othersCount > 1 ? 's' : '' }}
                                </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400 font-medium">
                        {{ $p->date->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 font-jetbrains font-medium text-zinc-500">
                        <x-currency :amount="$p->gst_amount" />
                    </td>
                    <td class="px-6 py-4 font-jetbrains font-bold text-zinc-900 dark:text-zinc-100">
                        <x-currency :amount="$p->total_amount" />
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <a href="{{ route('purchases.show', $p->id) }}" class="text-zinc-400 hover:text-blue-600 transition-colors" title="View Details">
                                <span class="material-symbols-rounded text-lg">visibility</span>
                            </a>
                            @can('edit purchases')
                            <a href="{{ route('purchases.edit', $p->id) }}" class="text-zinc-400 hover:text-emerald-600 transition-colors" title="Edit Purchase">
                                <span class="material-symbols-rounded text-lg">edit</span>
                            </a>
                            @endcan
                            @can('delete purchases')
                            <form action="{{ route('purchases.destroy', $p->id) }}" method="POST" class="delete-form inline">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDelete(this)" class="text-zinc-400 hover:text-rose-600 transition-colors" title="Delete Invoice">
                                    <span class="material-symbols-rounded text-lg">delete</span>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <x-slot:empty>
                    <x-empty-state 
                        icon="receipt_long" 
                        title="No invoices found" 
                        description="Your query didn't match any recorded transactions." />
                </x-slot:empty>
            @endforelse

            @if($purchases->hasPages())
                <x-slot:pagination>
                    {{ $purchases->withQueryString()->links() }}
                </x-slot:pagination>
            @endif
        </x-data-table>
    </x-card>
</div>
@endsection

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

