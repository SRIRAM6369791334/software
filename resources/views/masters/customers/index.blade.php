@extends('layouts.app')
@section('title', 'Customer Directory')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Customer Directory</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Manage your customer database and credit limits</p>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="secondary" size="md">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></x-slot>
                Export CSV
            </x-button>
            <x-button variant="primary" size="md" onclick="toggleModal('add-customer-modal')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></x-slot>
                Add New Customer
            </x-button>
        </div>
    </div>

    <!-- Filters & Search -->
    <x-card padding="false">
        <div class="p-6 flex flex-col md:flex-row items-center gap-6">
            <div class="flex-1 w-full">
                <form method="GET" class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400 group-focus-within:text-primary-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, phone, or route..." 
                           class="w-full bg-slate-50 border-slate-200 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 rounded-2xl py-3 pl-11 pr-4 text-sm font-medium transition-all outline-none">
                </form>
            </div>
            <div class="flex items-center gap-4 w-full md:w-auto">
                <select class="bg-slate-50 border-slate-200 rounded-2xl py-3 px-6 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all">
                    <option>All Routes</option>
                    <option>Route A</option>
                    <option>Route B</option>
                </select>
                <select class="bg-slate-50 border-slate-200 rounded-2xl py-3 px-6 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all">
                    <option>All Types</option>
                    <option>Retail</option>
                    <option>Wholesale</option>
                </select>
            </div>
        </div>
    </x-card>

    <!-- Table Section -->
    <x-card padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-100 bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Customer Information</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] hidden md:table-cell">Contact & Area</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center">Type</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Outstanding</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-sm font-bold text-slate-500 border border-slate-200 transition-colors group-hover:bg-primary-50 group-hover:text-primary-600 group-hover:border-primary-100">
                                        {{ substr($customer->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 text-base">{{ $customer->name }}</p>
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">ID: #{{ str_pad($customer->id, 4, '0', STR_PAD_LEFT) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6 hidden md:table-cell">
                                <div class="space-y-1.5">
                                    <p class="text-slate-600 font-bold flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                        {{ $customer->phone }}
                                    </p>
                                    <p class="text-xs text-slate-500 font-medium flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                        {{ $customer->route ?: 'Unknown Route' }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-center">
                                <x-badge :variant="$customer->type === 'Wholesale' ? 'success' : 'slate'">{{ $customer->type }}</x-badge>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="text-base font-black {{ $customer->balance > 0 ? 'text-red-500' : 'text-slate-400' }}">
                                    ₹{{ number_format($customer->balance, 0) }}
                                </p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Credit</p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openEditCustomer({{ $customer->id }}, '{{ addslashes($customer->name) }}','{{ $customer->phone }}','{{ addslashes($customer->address) }}','{{ $customer->gst_number }}','{{ $customer->route }}','{{ $customer->type }}')" 
                                            class="p-2.5 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <form action="{{ route('masters.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Archive {{ $customer->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2.5 rounded-xl bg-red-50 text-red-600 hover:bg-red-100 transition-colors shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-8 py-20 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                    </div>
                                    <p class="text-lg font-bold text-slate-900">No customers found</p>
                                    <p class="text-sm text-slate-500 font-medium mt-1">Try adjusting your search or filters</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($customers->hasPages())
            <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/30">
                {{ $customers->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</div>

<!-- Add Customer Modal -->
<div id="add-customer-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xl animate-in fade-in duration-300">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-2xl border border-white/20 overflow-hidden transform animate-in zoom-in-95 duration-300">
        <div class="flex items-center justify-between px-10 py-8 border-b border-slate-100 bg-slate-50/50">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Add New Customer</h2>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Establish new relationship</p>
            </div>
            <button onclick="toggleModal('add-customer-modal')" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors shadow-sm">✕</button>
        </div>
        <form action="{{ route('masters.customers.store') }}" method="POST" class="p-10 space-y-8">
            @csrf
            <div class="grid grid-cols-2 gap-8">
                <x-input label="Full Name *" name="name" placeholder="John Doe" required />
                <x-input label="Phone Number *" name="phone" placeholder="+91 98765 43210" required />
            </div>
            <x-input label="Business Address" name="address" placeholder="123 Poultry Lane, Route A" />
            <div class="grid grid-cols-2 gap-8">
                <x-input label="GST Number" name="gst_number" placeholder="Optional" />
                <x-input label="Route / Area" name="route" placeholder="Route A" />
            </div>
            <div class="space-y-2">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-1">Customer Type</label>
                <div class="flex gap-4">
                    @foreach(['Retail', 'Wholesale'] as $type)
                        <label class="flex-1 cursor-pointer group">
                            <input type="radio" name="type" value="{{ $type }}" {{ $type === 'Retail' ? 'checked' : '' }} class="sr-only peer">
                            <div class="p-4 rounded-2xl border-2 border-slate-100 bg-white text-center transition-all peer-checked:border-primary-500 peer-checked:bg-primary-50 group-hover:bg-slate-50">
                                <p class="text-sm font-bold text-slate-700 peer-checked:text-primary-700">{{ $type }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
            <div class="pt-4 flex gap-4">
                <x-button variant="ghost" class="flex-1" type="button" onclick="toggleModal('add-customer-modal')">Cancel</x-button>
                <x-button variant="primary" class="flex-1" type="submit">Create Customer</x-button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Customer Modal (Similar Structure) -->
<div id="edit-customer-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xl animate-in fade-in duration-300">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-2xl border border-white/20 overflow-hidden transform animate-in zoom-in-95 duration-300">
        <div class="flex items-center justify-between px-10 py-8 border-b border-slate-100 bg-slate-50/50">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Edit Customer</h2>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Update profile details</p>
            </div>
            <button onclick="toggleModal('edit-customer-modal')" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors shadow-sm">✕</button>
        </div>
        <form id="edit-customer-form" method="POST" class="p-10 space-y-8">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-8">
                <x-input label="Full Name *" name="name" id="edit-name" required />
                <x-input label="Phone Number *" name="phone" id="edit-phone" required />
            </div>
            <x-input label="Business Address" name="address" id="edit-address" />
            <div class="grid grid-cols-2 gap-8">
                <x-input label="GST Number" name="gst_number" id="edit-gst" />
                <x-input label="Route / Area" name="route" id="edit-route" />
            </div>
            <div class="space-y-2">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-1">Customer Type</label>
                <select name="type" id="edit-type" class="w-full bg-slate-50 border-slate-200 rounded-2xl py-3 px-5 text-sm font-medium outline-none">
                    <option value="Retail">Retail</option>
                    <option value="Wholesale">Wholesale</option>
                </select>
            </div>
            <div class="pt-4 flex gap-4">
                <x-button variant="ghost" class="flex-1" type="button" onclick="toggleModal('edit-customer-modal')">Cancel</x-button>
                <x-button variant="primary" class="flex-1" type="submit">Update Changes</x-button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleModal(id) {
    const modal = document.getElementById(id);
    modal.classList.toggle('hidden');
}

function openEditCustomer(id, name, phone, address, gst, route, type) {
    const form = document.getElementById('edit-customer-form');
    form.action = `/masters/customers/${id}`;
    document.getElementById('edit-name').value    = name;
    document.getElementById('edit-phone').value   = phone;
    document.getElementById('edit-address').value = address;
    document.getElementById('edit-gst').value     = gst;
    document.getElementById('edit-route').value   = route;
    document.getElementById('edit-type').value    = type;
    toggleModal('edit-customer-modal');
}
</script>
@endpush

