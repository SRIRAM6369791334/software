@extends('layouts.app')
@section('title', 'Record Vendor Payout')

@section('content')
<div class="animate-fade-in max-w-4xl mx-auto">
    <x-page-header title="Record Vendor Payout" subtitle="Enter payment details to clear supplier and vendor dues">
        <x-slot:actions>
            <x-button variant="outline" href="{{ route('payments.vendors.index') }}" icon="arrow_back">
                Back to Payouts
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl border border-zinc-200/60 dark:border-zinc-800/60 shadow-[0_8px_32px_rgba(0,0,0,0.04)] rounded-3xl overflow-hidden p-6 sm:p-10">
        
        <form action="{{ route('payments.vendors.storeGeneralPayment') }}" method="POST" class="space-y-8" x-data="{ cashAmount: 0, bankAmount: 0, paymentMode: 'Cash', bankTransferType: '' }">
            @csrf
            
            {{-- Vendor Selection Section --}}
            <section class="space-y-4">
                <div class="flex items-center gap-3 border-b border-zinc-100 dark:border-zinc-800 pb-3">
                    <div class="h-10 w-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <span class="material-symbols-rounded">local_shipping</span>
                    </div>
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-zinc-100 tracking-tight font-cabinet">Supplier Details</h3>
                </div>

                <div class="p-6 bg-zinc-50 dark:bg-zinc-800/40 rounded-2xl border border-zinc-200/60 dark:border-zinc-700/60 transition-all hover:border-purple-500/30">
                    @if($selected_vendor_id && $vendors->count() === 1)
                        @php $v = $vendors->first(); @endphp
                        <input type="hidden" name="vendor_id" value="{{ $v->id }}">
                        <div>
                            <span class="block text-xs font-bold text-zinc-500 uppercase mb-2">Vendor</span>
                            <div class="text-lg font-bold text-zinc-800 dark:text-white">
                                {{ $v->firm_name }}
                            </div>
                            <div class="text-sm font-semibold text-rose-500 mt-1">
                                Pending Balance: Rs {{ number_format($v->outstanding_balance, 2) }}
                            </div>
                        </div>
                    @else
                        <x-form.select name="vendor_id" label="Select Vendor" required>
                            <option value="">Choose vendor…</option>
                            @foreach($vendors as $v)
                                <option value="{{ $v->id }}" {{ $selected_vendor_id == $v->id ? 'selected' : '' }}>
                                {{ $v->firm_name }} — Outstanding: Rs {{ number_format($v->outstanding_balance, 0) }}
                                </option>
                            @endforeach
                        </x-form.select>
                    @endif
                </div>
            </section>

            {{-- Day-Load Warning Banner --}}
            @if($selected_vendor_id && $pendingDayLoadCount > 0)
                <div class="p-4 rounded-2xl border border-amber-200 bg-amber-50 dark:border-amber-800/50 dark:bg-amber-900/20 flex items-start gap-3">
                    <span class="material-symbols-rounded text-amber-600 dark:text-amber-400 text-[20px] mt-0.5">warning</span>
                    <div>
                        <p class="text-sm font-bold text-amber-800 dark:text-amber-300">
                            This vendor has {{ $pendingDayLoadCount }} unpaid day-load {{ Str::plural('entry', $pendingDayLoadCount) }}
                        </p>
                        <p class="text-xs text-amber-700 dark:text-amber-400 mt-1">
                            Consider recording payment from the day-load billing page for proper allocation across entries.
                        </p>
                    </div>
                </div>
            @endif

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
                        <input type="number" name="cash_amount" required step="0.01" min="0" x-model.number="cashAmount" class="block w-full rounded-xl border-purple-200 dark:border-purple-800 focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-zinc-900 text-2xl font-black text-zinc-800 dark:text-white shadow-sm py-3 px-4 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Bank Amount (Rs) <span class="text-rose-500">*</span></label>
                        <input type="number" name="bank_amount" required step="0.01" min="0" x-model.number="bankAmount" class="block w-full rounded-xl border-purple-200 dark:border-purple-800 focus:ring-purple-500 focus:border-purple-500 bg-white dark:bg-zinc-900 text-2xl font-black text-zinc-800 dark:text-white shadow-sm py-3 px-4 transition-all">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-zinc-500 uppercase mb-2">Total Amount (Rs)</label>
                        <div class="rounded-xl border border-purple-200 dark:border-purple-800 bg-white dark:bg-zinc-900 text-2xl font-black text-purple-600 dark:text-purple-400 py-3 px-4 shadow-sm" x-text="'Rs ' + (cashAmount + bankAmount).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2})"></div>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-form.input type="date" name="date" label="Payment Date" required value="{{ date('Y-m-d') }}" class="!bg-white dark:!bg-zinc-900 shadow-sm" />
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
                        <div>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2 font-cabinet tracking-wide uppercase">Payment Mode <span class="text-rose-500">*</span></label>
                            <select name="payment_mode" x-model="paymentMode" required class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm shadow-sm">
                                <option value="Cash">Cash</option>
                                <option value="UPI">UPI</option>
                                <option value="NEFT">NEFT</option>
                                <option value="Cheque">Cheque</option>
                            </select>
                        </div>
                        <div x-show="bankAmount > 0" x-transition>
                            <label class="block text-sm font-semibold text-zinc-700 dark:text-zinc-300 mb-2 font-cabinet tracking-wide uppercase">Bank Transfer Type</label>
                            <select name="bank_transfer_type" x-model="bankTransferType" :required="bankAmount > 0" class="w-full rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 px-3 py-2 text-sm shadow-sm">
                                <option value="">Select type...</option>
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
                    
                    <x-form.input name="notes" label="Remarks / Reference" placeholder="e.g. UPI Transaction ID or Cheque Number..." class="!bg-white dark:!bg-zinc-900 shadow-sm" />
                </div>
            </section>

            <div class="flex items-center justify-end gap-4 pt-6 border-t border-zinc-100 dark:border-zinc-800">
                <x-button type="button" variant="outline" href="{{ route('payments.vendors.index') }}" class="hover:bg-zinc-100">Cancel</x-button>
                <x-button type="submit" variant="primary" icon="check_circle" size="lg" class="shadow-xl shadow-purple-500/20 px-8 !bg-purple-600 hover:!bg-purple-700">Confirm & Record</x-button>
            </div>
            
        </form>
    </div>
</div>
@endsection
