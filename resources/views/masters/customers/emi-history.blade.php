@extends(request()->ajax() ? 'layouts.empty' : 'layouts.app')
@section('title', 'EMI Schedule - ' . $customer->name)

@section('content')
@if(!request()->ajax())
<div class="space-y-6">
    <div class="mb-4">
        <a href="{{ route('masters.customers.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition-colors">
            <span class="material-symbols-rounded text-[20px]">arrow_back</span>
            Back to Directory
        </a>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <x-avatar name="{{ $customer->name }}" size="lg" />
            <div>
                <h1 class="text-2xl font-bold font-cabinet text-zinc-900 dark:text-zinc-100 tracking-tight">{{ $customer->name }}</h1>
                <div class="flex items-center gap-2 mt-1">
                    @if($customer->type === 'Wholesale')
                        <x-badge color="blue">Wholesale Partner</x-badge>
                    @else
                        <x-badge color="rose">Retail Buyer</x-badge>
                    @endif
                    <x-badge color="zinc">
                        <span class="material-symbols-rounded text-[14px] mr-1">alt_route</span>
                        {{ $customer->route ?: 'General Sector' }}
                    </x-badge>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <x-button href="{{ route('masters.customers.edit', $customer) }}" variant="secondary" icon="edit">Edit Profile</x-button>
            <form action="{{ route('masters.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Delete {{ $customer->name }}?')">
                @csrf @method('DELETE')
                <x-button type="submit" variant="danger" icon="delete">Delete</x-button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-2xl p-6 bg-gradient-to-br from-rose-600 to-rose-500 text-white shadow-lg relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                <div class="relative z-10 text-center">
                    <div class="text-xs font-bold uppercase tracking-widest text-rose-100 mb-2">Total Outstanding</div>
                    <div class="text-3xl font-extrabold tracking-tight font-jetbrains mb-6">
                        Rs {{ number_format($customer->balance, 2) }}
                    </div>
                    <div class="flex flex-col gap-3">
                        <x-button href="{{ route('payments.customers.create', ['customer_id' => $customer->id]) }}" variant="secondary" icon="payments" class="w-full justify-center !text-rose-700 !bg-white hover:!bg-rose-50">
                            Record Payment
                        </x-button>
                    </div>
                </div>
            </div>

            <x-card title="Profile Credentials" icon="contact_page">
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-zinc-400">call</span>
                        <div>
                            <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Contact Phone</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $customer->phone }}</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-zinc-400">location_on</span>
                        <div>
                            <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Store Address</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $customer->address ?: 'Not provided' }}</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-zinc-400">badge</span>
                        <div>
                            <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">GSTIN / Registration</div>
                            <div class="font-mono text-sm text-zinc-900 dark:text-zinc-100">{{ $customer->gst_number ?: 'Unregistered' }}</div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="lg:col-span-2 space-y-6">
@endif
            <div id="cm-tabs-container" x-data="ajaxTabs" @click="handleTabClick" @mouseover="prefetchTab" @popstate.window="window.location.reload()" class="bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 rounded-xl overflow-hidden shadow-sm">
                
                <div class="flex flex-wrap border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
                    <a href="{{ route('masters.customers.show', $customer) }}" class="flex-1 text-center py-4 text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        Quick Overview
                    </a>
                    <a href="{{ route('masters.customers.billing-history', $customer) }}" class="flex-1 text-center py-4 text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        Billing History
                    </a>
                    <a href="{{ route('masters.customers.payment-history', $customer) }}" class="flex-1 text-center py-4 text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        Payment History
                    </a>
                    <a href="{{ route('masters.customers.emi-history', $customer) }}" class="flex-1 text-center py-4 text-sm font-bold text-emerald-600 border-b-2 border-emerald-600 bg-white dark:bg-zinc-900 transition-colors">
                        EMI Schedule
                    </a>
                </div>

                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider">Customer EMI Schedule</h4>
                    </div>

                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <x-stat-card title="Total EMIs" value="{{ $emis->total() }}" color="zinc" />
                        <x-stat-card title="Upcoming/Overdue" value="{{ $emis->where('status', 'Upcoming')->count() }}" color="rose" />
                        <x-stat-card title="Paid EMIs" value="{{ $emis->where('status', 'Paid')->count() }}" color="emerald" />
                        <x-stat-card title="Total Amount Due" value="{{ number_format($emis->where('status', 'Upcoming')->sum('emi_amount'), 0) }}" prefix="Rs " color="amber" />
                    </div>

                    <x-data-table :headers="['EMI ID', 'Due Date', ['label' => 'EMI Amount', 'align' => 'right'], 'Notes / Remarks', ['label' => 'Status', 'align' => 'center']]">
                        @forelse($emis as $emi)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/50">
                                <td class="px-6 py-4 font-mono text-sm">#EMI-{{ str_pad($emi->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-sm">{{ \Carbon\Carbon::parse($emi->due_date)->format('d M Y') }}</div>
                                    @if($emi->status === 'Upcoming' && \Carbon\Carbon::parse($emi->due_date)->isPast())
                                        <div class="text-[10px] font-bold text-rose-600 uppercase tracking-wider mt-0.5">Overdue</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-sm font-jetbrains">Rs {{ number_format($emi->emi_amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-zinc-600">{{ $emi->remarks ?: '—' }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($emi->status === 'Paid')
                                        <x-badge color="emerald">Paid</x-badge>
                                    @else
                                        <x-badge color="amber">Upcoming</x-badge>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center py-8 text-zinc-500">No EMI schedule found for this customer.</td></tr>
                        @endforelse
                        @if($emis->hasPages())
                            <x-slot:pagination>
                                {!! $emis->links() !!}
                            </x-slot:pagination>
                        @endif
                    </x-data-table>
                </div>
            </div>
@if(!request()->ajax())
        </div>
    </div>
</div>
@endif
@endsection
