@extends('layouts.app')
@section('title', 'Edit EMI')

@section('content')
<div class="mb-6 animate-fade-in max-w-2xl mx-auto">
    <a href="{{ route('expenses.emis.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2">
        <span class="material-symbols-rounded text-sm">arrow_back</span>
        Back to EMIs
    </a>
    <h1 class="font-cabinet text-3xl font-bold tracking-tight text-zinc-900 dark:text-zinc-50">Edit EMI</h1>
    <p class="mt-1 font-outfit text-sm text-zinc-500 dark:text-zinc-400">Update installment amount, due date, or status</p>
</div>

<div class="max-w-2xl mx-auto animate-fade-in">
    <x-card class="mb-6">
        <div class="p-6">
            <h3 class="font-cabinet font-bold text-zinc-800 dark:text-zinc-100 mb-3 text-lg">EMI / Loan Information</h3>
            <div class="grid grid-cols-2 gap-4 text-sm font-outfit">
                <div>
                    <span class="text-zinc-500 block mb-0.5">Loan / Reference Name:</span>
                    <strong class="text-zinc-900 dark:text-zinc-100">{{ $emi->loan_name }}</strong>
                </div>
                <div>
                    <span class="text-zinc-500 block mb-0.5">Type:</span>
                    <strong class="text-zinc-900 dark:text-zinc-100">
                        {{ $emi->emi_type }} 
                        @if(in_array($emi->emi_type, ['Customer', 'Dealer']))
                            (To Receive)
                        @else
                            (To Pay)
                        @endif
                    </strong>
                </div>
                @if($emi->emi_type === 'Customer' && $emi->customer)
                <div class="col-span-2">
                    <span class="text-zinc-500 block mb-0.5">Associated Customer:</span>
                    <strong class="text-zinc-900 dark:text-zinc-100">{{ $emi->customer->name }}</strong>
                </div>
                @elseif($emi->emi_type === 'Dealer' && $emi->dealer)
                <div class="col-span-2">
                    <span class="text-zinc-500 block mb-0.5">Associated Dealer:</span>
                    <strong class="text-zinc-900 dark:text-zinc-100">{{ $emi->dealer->firm_name }}</strong>
                </div>
                @elseif($emi->emi_type === 'Vendor' && $emi->vendor)
                <div class="col-span-2">
                    <span class="text-zinc-500 block mb-0.5">Associated Vendor:</span>
                    <strong class="text-zinc-900 dark:text-zinc-100">{{ $emi->vendor->firm_name }}</strong>
                </div>
                @endif
            </div>
        </div>
    </x-card>

    <x-card>
        <div class="p-6">
            <form action="{{ route('expenses.emis.update', $emi) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <x-form.input type="number" name="amount" label="Installment Amount (Rs)" step="0.01" value="{{ $emi->amount }}" required />
                    <x-form.input type="date" name="due_date" label="Due Date" value="{{ $emi->due_date->format('Y-m-d') }}" required />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    <x-form.select name="status" label="Current Status" required>
                        <option value="Upcoming" {{ $emi->status === 'Upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="Paid" {{ $emi->status === 'Paid' ? 'selected' : '' }}>Paid</option>
                        <option value="Overdue" {{ $emi->status === 'Overdue' ? 'selected' : '' }}>Overdue</option>
                    </x-form.select>
                </div>

                <div class="pt-5 border-t border-zinc-200/50 dark:border-zinc-800/50 flex justify-between items-center gap-3">
                    @if($emi->status !== 'Paid')
                    <button type="submit" formmethod="POST" formaction="{{ route('expenses.emis.close-full', $emi) }}" 
                            class="font-outfit text-sm font-semibold text-rose-600 hover:text-rose-700 bg-rose-50 hover:bg-rose-100 px-4 py-2 rounded-xl transition-all flex items-center gap-1.5"
                            onclick="return confirm('Are you sure you want to close the entire loan group and mark all installments as Paid?')">
                        <span class="material-symbols-rounded text-base">assignment_turned_in</span> Close Entire Loan
                    </button>
                    @else
                    <div></div>
                    @endif
                    <div class="flex gap-3">
                        <x-button type="reset" variant="outline">Reset</x-button>
                        <x-button type="submit" variant="primary" icon="save">Update Installment</x-button>
                    </div>
                </div>
            </form>
        </div>
    </x-card>
</div>
@endsection
