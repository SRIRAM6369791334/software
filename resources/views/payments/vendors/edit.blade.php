@extends('layouts.app')
@section('title', 'Edit Vendor Payment')

@section('content')
<div class="animate-fade-in max-w-4xl mx-auto">
    <x-page-header title="Edit Vendor Payment" subtitle="Update payment details and synchronize balances">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('payments.vendors.index') }}" icon="arrow_back">
                Back to Payments
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/60 dark:border-zinc-800/60 shadow-[0_8px_32px_rgba(0,0,0,0.04)] rounded-3xl overflow-hidden p-6 sm:p-10">
        
        @if($errors->any())
            <div class="p-4 mb-6 rounded-2xl bg-red-50 dark:bg-red-950/20 border border-red-200 dark:border-red-850/30 text-red-600 dark:text-red-400 text-sm">
                <p class="font-bold mb-1">Please fix the following errors:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('payments.vendors.update', $payment->id) }}" method="POST" class="space-y-8" 
              x-data="{ 
                  cashAmount: {{ (float) ($payment->cash_amount ?? ($payment->payment_mode === 'Cash' ? $payment->amount : 0)) }}, 
                  bankAmount: {{ (float) ($payment->bank_amount ?? ($payment->payment_mode !== 'Cash' ? $payment->amount : 0)) }}, 
                  paymentMode: '{{ $payment->payment_mode }}', 
                  bankTransferType: '{{ $payment->bank_transfer_type ?? ($payment->payment_mode !== 'Cash' ? $payment->payment_mode : 'UPI') }}' 
              }">
            @csrf
            @method('PUT')
            
            {{-- Vendor Information Section --}}
            <section class="space-y-4">
                <div class="flex items-center gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="h-10 w-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <span class="material-symbols-rounded">storefront</span>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 tracking-tight font-cabinet">Vendor Details</h3>
                </div>

                <div class="p-6 bg-zinc-50 dark:bg-zinc-800/40 rounded-2xl border border-zinc-200/60 dark:border-zinc-700/60">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <span class="block text-xs font-bold text-zinc-500 uppercase mb-1">Vendor</span>
                            <div class="text-xl font-black text-zinc-900 dark:text-white">
                                {{ $vendor->firm_name ?? 'Vendor' }}
                            </div>
                            <span class="text-xs text-zinc-400">Contact: {{ $vendor->contact_person ?? '-' }} ({{ $vendor->phone ?? '-' }})</span>
                        </div>
                        <div class="text-left sm:text-right">
                            <span class="block text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Current Outstanding</span>
                            <div class="text-xl font-cabinet font-black text-rose-600 dark:text-rose-400">
                                <x-currency :amount="$vendor->outstanding_balance ?? 0" />
                            </div>
                        </div>
                    </div>

                    @if($payment->day_load_entry_id)
                        <div class="mt-4 pt-3 border-t border-zinc-200/60 dark:border-zinc-700/60 text-xs text-zinc-600 dark:text-zinc-400">
                            Linked to <strong>Day-Load Entry #{{ $payment->day_load_entry_id }}</strong>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Amount Section --}}
            <section class="space-y-4">
                <div class="flex items-center gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="h-10 w-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <span class="material-symbols-rounded">account_balance_wallet</span>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 tracking-tight font-cabinet">Payout Information</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-6 bg-purple-50/50 dark:bg-purple-900/10 rounded-2xl border border-purple-100 dark:border-purple-800/30 mb-4">
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Cash Amount (Rs) <span class="text-rose-500">*</span></label>
                        <input type="number" name="cash_amount" required step="0.01" min="0" x-model.number="cashAmount" onwheel="this.blur()" class="block w-full rounded-xl border-purple-200 dark:border-purple-800 focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-zinc-900 text-2xl font-black text-zinc-800 dark:text-white shadow-sm py-3 px-4 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Bank Amount (Rs) <span class="text-rose-500">*</span></label>
                        <input type="number" name="bank_amount" required step="0.01" min="0" x-model.number="bankAmount" onwheel="this.blur()" class="block w-full rounded-xl border-purple-200 dark:border-purple-800 focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-zinc-900 text-2xl font-black text-zinc-800 dark:text-white shadow-sm py-3 px-4 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Total Amount (Rs)</label>
                        <div class="rounded-xl border border-purple-200 dark:border-purple-800 bg-white dark:bg-zinc-900 text-2xl font-black text-purple-600 dark:text-purple-400 py-3 px-4 shadow-sm" x-text="'Rs ' + (cashAmount + bankAmount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input type="date" name="date" label="Payment Date" required value="{{ $payment->date ? $payment->date->format('Y-m-d') : date('Y-m-d') }}" class="!bg-white dark:!bg-zinc-900 shadow-sm" />
                </div>
            </section>

            {{-- Mode Section --}}
            <section class="space-y-4">
                <div class="flex items-center gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="h-10 w-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <span class="material-symbols-rounded">receipt_long</span>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 tracking-tight font-cabinet">Transaction Details</h3>
                </div>

                <div class="p-6 bg-zinc-50 dark:bg-zinc-800/40 rounded-2xl border border-zinc-200/60 dark:border-zinc-700/60">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <input type="hidden" name="payment_mode" :value="bankAmount > 0 ? (cashAmount > 0 ? 'Split' : bankTransferType) : 'Cash'">
                        <div x-show="bankAmount > 0" x-transition class="md:col-span-2">
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2 font-cabinet tracking-wide uppercase">Bank Transfer Type <span class="text-rose-500">*</span></label>
                            <select name="bank_transfer_type" x-model="bankTransferType" :required="bankAmount > 0" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm shadow-sm">
                                <option value="UPI">UPI</option>
                                <option value="Bank Transfer">Bank Transfer</option>
                                <option value="NEFT">NEFT</option>
                                <option value="RTGS">RTGS</option>
                                <option value="IMPS">IMPS</option>
                                <option value="Cheque">Cheque</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                    </div>
                    
                    <x-form.input name="notes" label="Remarks / Reference" value="{{ $payment->notes }}" placeholder="e.g. UPI Transaction ID or Cheque Number..." class="!bg-white dark:!bg-zinc-900 shadow-sm" />
                </div>
            </section>

            <div class="flex items-center justify-end gap-4 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                <x-button type="button" variant="outline" href="{{ route('payments.vendors.index') }}" class="hover:bg-zinc-100">Cancel</x-button>
                <x-button type="submit" variant="primary" icon="save" size="lg" class="shadow-xl shadow-purple-500/20 px-8 !bg-purple-600 hover:!bg-purple-700">Save & Update</x-button>
            </div>
            
        </form>
    </div>
</div>
@endsection
