@extends('layouts.app')
@section('title', 'Record Dealer Payout')

@section('content')
<div class="animate-fade-in max-w-4xl mx-auto">
    <x-page-header title="Record Payout" subtitle="Enter payment details to clear supplier dues">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('payments.dealers.index') }}" icon="arrow_back">
                Back to Payouts
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/60 dark:border-zinc-800/60 shadow-[0_8px_32px_rgba(0,0,0,0.04)] rounded-3xl overflow-hidden p-6 sm:p-10">
        
        <form action="{{ route('payments.dealers.store') }}" method="POST" class="space-y-8">
            @csrf
            
            {{-- Dealer Selection Section --}}
            <section class="space-y-4">
                <div class="flex items-center gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="h-10 w-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <span class="material-symbols-rounded">storefront</span>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 tracking-tight font-cabinet">Supplier Details</h3>
                </div>

                <div class="p-6 bg-zinc-50 dark:bg-zinc-800/40 rounded-2xl border border-zinc-200/60 dark:border-zinc-700/60 transition-all hover:border-blue-500/30">
                    <x-form.select name="dealer_id" label="Select Dealer" required>
                        <option value="">Choose dealer…</option>
                        @foreach($dealers as $d)
                            <option value="{{ $d->id }}" {{ $selected_dealer_id == $d->id ? 'selected' : '' }}>
                                {{ $d->firm_name }} — Pending: Rs {{ number_format($d->pending_amount, 0) }}
                            </option>
                        @endforeach
                    </x-form.select>
                </div>
            </section>

            {{-- Amount Section --}}
            <section class="space-y-4">
                <div class="flex items-center gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="h-10 w-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <span class="material-symbols-rounded">account_balance_wallet</span>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 tracking-tight font-cabinet">Payout Information</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 p-6 bg-blue-50/50 dark:bg-blue-900/10 rounded-2xl border border-blue-100 dark:border-blue-800/30">
                    <div>
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2 font-cabinet tracking-wide uppercase">Amount Paid (Rs) <span class="text-rose-500">*</span></label>
                        <input type="number" name="amount" required step="0.01" min="0.01" placeholder="0.00" class="block w-full rounded-xl border-blue-200 dark:border-blue-800 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-zinc-900 text-3xl font-black text-blue-600 dark:text-blue-400 placeholder:text-zinc-300 dark:placeholder:text-zinc-600 shadow-sm py-4 px-5 transition-all">
                    </div>
                    <x-form.input type="date" name="date" label="Payment Date" required value="{{ date('Y-m-d') }}" class="!bg-white dark:!bg-zinc-900 shadow-sm" />
                </div>
            </section>

            {{-- Mode Section --}}
            <section class="space-y-4">
                <div class="flex items-center gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="h-10 w-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <span class="material-symbols-rounded">receipt_long</span>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 tracking-tight font-cabinet">Transaction Details</h3>
                </div>

                <div class="p-6 bg-zinc-50 dark:bg-zinc-800/40 rounded-2xl border border-zinc-200/60 dark:border-zinc-700/60">
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-3 font-cabinet tracking-wide uppercase">Payment Mode <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            @foreach(['NEFT','Cheque','UPI','Cash'] as $mode)
                                <label class="flex items-center justify-center p-3 border border-zinc-200 dark:border-zinc-700 rounded-xl cursor-pointer bg-white dark:bg-zinc-900 shadow-sm hover:shadow-md hover:border-blue-400 dark:hover:border-blue-500 transition-all group has-[:checked]:border-blue-500 has-[:checked]:ring-2 has-[:checked]:ring-blue-500 has-[:checked]:bg-blue-50 dark:has-[:checked]:bg-blue-900/20">
                                    <input type="radio" name="payment_mode" value="{{ $mode }}" {{ $loop->first ? 'checked' : '' }} class="sr-only">
                                    <span class="text-sm font-bold text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-zinc-100 transition-colors">{{ $mode }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    
                    <x-form.input name="notes" label="Remarks / Reference" placeholder="e.g. UPI Transaction ID or Cheque Number..." class="!bg-white dark:!bg-zinc-900 shadow-sm" />
                </div>
            </section>

            <div class="flex items-center justify-end gap-4 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                <x-button type="button" variant="outline" href="{{ route('payments.dealers.index') }}" class="hover:bg-zinc-100">Cancel</x-button>
                <x-button type="submit" variant="primary" icon="check_circle" size="lg" class="shadow-xl shadow-blue-500/20 px-8 !bg-blue-600 hover:!bg-blue-700">Confirm & Record</x-button>
            </div>
            
        </form>
    </div>
</div>
@endsection
