@extends('layouts.app')
@section('title', 'Dealer Details - ' . $dealer->firm_name)

@section('content')
<div class="space-y-6">
    <div class="mb-4">
        <a href="{{ route('masters.dealers.index') }}" class="text-sm font-medium text-emerald-600 hover:text-emerald-700 flex items-center gap-1 transition-colors">
            <span class="material-symbols-rounded text-[20px]">arrow_back</span>
            Back to Directory
        </a>
    </div>

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div class="flex items-center gap-4">
            <x-avatar name="{{ $dealer->firm_name }}" size="lg" />
            <div>
                <h1 class="text-2xl font-bold font-cabinet text-zinc-900 dark:text-zinc-100 tracking-tight">{{ $dealer->firm_name }}</h1>
                <div class="flex items-center gap-2 mt-1">
                    <x-badge color="blue">Supplier / Partner</x-badge>
                    <x-badge color="zinc">
                        <span class="material-symbols-rounded text-[14px] mr-1">alt_route</span>
                        {{ $dealer->route ?: 'General Area' }}
                    </x-badge>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <x-button href="{{ route('masters.dealers.edit', $dealer) }}" variant="secondary" icon="edit">Edit Profile</x-button>
            <form action="{{ route('masters.dealers.destroy', $dealer) }}" method="POST" onsubmit="return confirm('Delete {{ $dealer->firm_name }}? This will keep their transaction history intact.')">
                @csrf @method('DELETE')
                <x-button type="submit" variant="danger" icon="delete">Delete</x-button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="lg:col-span-1 space-y-6">
            <div class="rounded-2xl p-6 bg-gradient-to-br from-amber-600 to-amber-500 text-white shadow-lg relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-40 h-40 bg-white/10 rounded-full blur-2xl"></div>
                <div class="relative z-10 text-center">
                    <div class="text-xs font-bold uppercase tracking-widest text-amber-100 mb-2">Total Payable</div>
                    <div class="text-3xl font-extrabold tracking-tight font-jetbrains mb-6">
                        Rs {{ number_format($dealer->pending_amount, 2) }}
                    </div>
                    <div class="flex flex-col gap-3">
                        <x-button href="{{ route('payments.dealers.create', ['dealer_id' => $dealer->id]) }}" variant="secondary" icon="payments" class="w-full justify-center !text-amber-700 !bg-white hover:!bg-amber-50">
                            Record Payment
                        </x-button>
                        <x-button href="{{ route('masters.dealers.ledger-pdf', $dealer) }}" variant="secondary" icon="download" class="w-full justify-center !bg-amber-700/50 !text-white !border-amber-400/50 hover:!bg-amber-700/70">
                            Download Ledger
                        </x-button>
                    </div>
                </div>
            </div>

            <x-card title="Firm Credentials" icon="contact_page">
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-zinc-400">person</span>
                        <div>
                            <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Contact Person</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $dealer->contact_person ?: '-' }}</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-zinc-400">call</span>
                        <div>
                            <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Contact Phone</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $dealer->phone }}</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-zinc-400">location_on</span>
                        <div>
                            <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">Store Location</div>
                            <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $dealer->location ?: 'Not provided' }}</div>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <span class="material-symbols-rounded text-zinc-400">badge</span>
                        <div>
                            <div class="text-xs font-bold text-zinc-500 uppercase tracking-wider">GSTIN / Registration</div>
                            <div class="font-mono text-sm text-zinc-900 dark:text-zinc-100">{{ $dealer->gst_number ?: 'Unregistered' }}</div>
                        </div>
                    </div>
                </div>
            </x-card>
        </div>

        <div class="lg:col-span-2">
            <x-card padding="p-0" class="overflow-hidden">
                <div class="flex flex-wrap border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
                    <a href="{{ route('masters.dealers.show', $dealer) }}" class="flex-1 text-center py-4 text-sm font-bold text-emerald-600 border-b-2 border-emerald-600 bg-white dark:bg-zinc-900 transition-colors">
                        Quick Overview
                    </a>
                    <a href="{{ route('masters.dealers.purchase-history', $dealer) }}" class="flex-1 text-center py-4 text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        Purchase Orders
                    </a>
                    <a href="{{ route('payments.dealers.ledger', $dealer) }}" class="flex-1 text-center py-4 text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        Payment Ledger
                    </a>
                    <a href="{{ route('masters.dealers.outstanding-report', $dealer) }}" class="flex-1 text-center py-4 text-sm font-medium text-zinc-500 hover:text-zinc-900 dark:hover:text-zinc-100 hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors">
                        Outstanding Report
                    </a>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider mb-4">Recent Purchases</h4>
                        <div class="space-y-3">
                            @forelse($dealer->purchases()->with('items')->latest()->take(3)->get() as $purchase)
                                <div class="flex justify-between items-center p-3 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
                                    <div>
                                        <p class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">
                                            @if($purchase->items->isNotEmpty())
                                                {{ $purchase->items->pluck('item_name')->join(', ') }}
                                            @else
                                                {{ $purchase->item }}
                                            @endif
                                        </p>
                                        <p class="text-xs text-zinc-500 mt-0.5">{{ $purchase->date->format('d M Y') }}</p>
                                    </div>
                                    <div class="font-bold text-sm text-zinc-900 dark:text-zinc-100 font-jetbrains">
                                        Rs {{ number_format($purchase->total_amount, 0) }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-sm italic text-zinc-500">No recent purchases.</div>
                            @endforelse
                        </div>
                    </div>

                    <div>
                        <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 uppercase tracking-wider mb-4">Recent Payments</h4>
                        <div class="space-y-3">
                            @forelse($dealer->payments()->latest()->take(3)->get() as $payment)
                                <div class="flex justify-between items-center p-3 rounded-lg border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900/50">
                                    <div>
                                        <p class="font-semibold text-sm text-zinc-900 dark:text-zinc-100">Payment - {{ $payment->payment_mode }}</p>
                                        <p class="text-xs text-zinc-500 mt-0.5">{{ $payment->date->format('d M Y') }}</p>
                                    </div>
                                    <div class="font-bold text-sm text-emerald-600 dark:text-emerald-400 font-jetbrains">
                                        Rs {{ number_format($payment->amount, 0) }}
                                    </div>
                                </div>
                            @empty
                                <div class="text-sm italic text-zinc-500">No recent payments.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</div>
@endsection
