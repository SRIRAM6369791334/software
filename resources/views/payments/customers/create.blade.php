@extends('layouts.app')
@section('title', 'Record Customer Payment')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex items-center gap-4">
        <x-button variant="ghost" size="md" href="{{ route('payments.customers.index') }}" class="!p-2">
            <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></x-slot>
        </x-button>
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Record Inflow</h1>
            <p class="text-sm text-slate-500 font-medium mt-1 uppercase tracking-widest italic">Customer Financial Settlement</p>
        </div>
    </div>

    <div class="max-w-3xl">
        <x-card class="relative overflow-hidden">
            <!-- Decorative Element -->
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary-500/5 rounded-full -mr-16 -mt-16 blur-2xl"></div>
            
            <form action="{{ route('payments.customers.store') }}" method="POST" class="p-8 space-y-8 relative">
                @csrf
                
                <!-- Customer Selection -->
                <div class="space-y-3">
                    <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Customer Asset *</label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400 group-focus-within:text-primary-500 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                        </div>
                        <select name="customer_id" required class="w-full bg-slate-50 border-slate-200 rounded-2xl py-4 pl-12 pr-4 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all border appearance-none">
                            <option value="">Choose a customer identity...</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $selected_customer_id == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} (Outstanding: ₹{{ number_format($customer->balance, 0) }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <x-input label="Settlement Date *" type="date" name="date" value="{{ date('Y-m-d') }}" required />
                    
                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Inflow Amount (₹) *</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-500 font-black text-lg group-focus-within:scale-110 transition-transform">
                                ₹
                            </div>
                            <input type="number" name="amount" step="0.01" required 
                                   class="w-full bg-emerald-50/50 border-emerald-100 rounded-2xl py-4 pl-10 pr-4 text-xl font-black text-emerald-900 outline-none focus:ring-4 focus:ring-emerald-500/10 transition-all border placeholder-emerald-300" 
                                   placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Payment Channel</label>
                        <select name="payment_mode" class="w-full bg-slate-50 border-slate-200 rounded-2xl py-4 px-5 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all border">
                            @foreach(['Cash','UPI','Bank Transfer','Cheque'] as $mode)
                                <option value="{{ $mode }}">{{ $mode }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="space-y-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-[0.2em] px-1">Entry Classification</label>
                        <select name="payment_type" class="w-full bg-slate-50 border-slate-200 rounded-2xl py-4 px-5 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all border">
                            <option value="Regular">Regular Settlement</option>
                            <option value="Adjustment">Balance Correction</option>
                            <option value="Opening">Opening Balance Ledger</option>
                        </select>
                    </div>
                </div>

                <x-input label="Transaction Narratives" name="notes" placeholder="Optional notes, reference numbers, or receipt IDs..." rows="3" />

                <div class="pt-6 flex flex-col md:flex-row justify-end items-center gap-6 border-t border-slate-50">
                    <button type="reset" class="text-xs font-black uppercase tracking-widest text-slate-400 hover:text-rose-500 transition-colors">Wipe Form</button>
                    <x-button variant="primary" size="lg" type="submit" class="w-full md:w-auto px-12">
                        <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></x-slot>
                        Finalize Settlement
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection
