@extends('layouts.app')
@section('title', 'Dealer Directory')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Dealer Directory</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Manage dealer relationships and supply chains</p>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="secondary" size="md">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></x-slot>
                Export PDF
            </x-button>
            <x-button variant="primary" size="md" onclick="toggleModal('add-dealer-modal')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></x-slot>
                Add New Dealer
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
                    <input type="text" name="search" value="{{ $search }}" placeholder="Search by firm name, contact, or location..." 
                           class="w-full bg-slate-50 border-slate-200 focus:bg-white focus:border-primary-500 focus:ring-4 focus:ring-primary-500/10 rounded-2xl py-3 pl-11 pr-4 text-sm font-medium transition-all outline-none">
                </form>
            </div>
            <div class="flex items-center gap-4 w-full md:w-auto">
                <select class="bg-slate-50 border-slate-200 rounded-2xl py-3 px-6 text-sm font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all">
                    <option>Sort By: Newest</option>
                    <option>Sort By: Name</option>
                    <option>Sort By: Pending</option>
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
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Firm Details</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Contact & GST</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] hidden lg:table-cell">Area / Route</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-right">Pending Dues</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($dealers as $dealer)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-sm font-bold text-slate-500 border border-slate-200 transition-colors group-hover:bg-primary-50 group-hover:text-primary-600 group-hover:border-primary-100">
                                        {{ substr($dealer->firm_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 text-base">{{ $dealer->firm_name }}</p>
                                        <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $dealer->contact_person }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="space-y-1.5">
                                    <p class="text-slate-600 font-bold flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                        {{ $dealer->phone }}
                                    </p>
                                    <p class="text-[10px] font-mono text-slate-400 uppercase tracking-widest">{{ $dealer->gst_number ?: 'NO GST' }}</p>
                                </div>
                            </td>
                            <td class="px-8 py-6 hidden lg:table-cell">
                                <div class="space-y-1">
                                    <p class="text-slate-700 font-bold">{{ $dealer->location }}</p>
                                    <p class="text-[10px] font-bold text-primary-600 uppercase tracking-widest">{{ $dealer->route }}</p>
                                </div>
                            </td>
                            <td class="px-8 py-6 text-right">
                                <p class="text-base font-black {{ $dealer->pending_amount > 0 ? 'text-red-500' : 'text-slate-400' }}">
                                    ₹{{ number_format($dealer->pending_amount, 0) }}
                                </p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Due</p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openEditDealer({{ $dealer->id }},'{{ addslashes($dealer->firm_name) }}','{{ addslashes($dealer->contact_person) }}','{{ $dealer->phone }}','{{ $dealer->gst_number }}','{{ addslashes($dealer->location) }}','{{ $dealer->route }}')" 
                                            class="p-2.5 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors shadow-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                    </button>
                                    <form action="{{ route('masters.dealers.destroy', $dealer) }}" method="POST" onsubmit="return confirm('Archive {{ $dealer->firm_name }}?')">
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
                                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    </div>
                                    <p class="text-lg font-bold text-slate-900">No dealers registered</p>
                                    <p class="text-sm text-slate-500 font-medium mt-1">Start by adding your first supply partner</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($dealers->hasPages())
            <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/30">
                {{ $dealers->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</div>

<!-- Add Dealer Modal -->
<div id="add-dealer-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xl animate-in fade-in duration-300">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-2xl border border-white/20 overflow-hidden transform animate-in zoom-in-95 duration-300">
        <div class="flex items-center justify-between px-10 py-8 border-b border-slate-100 bg-slate-50/50">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Add New Dealer</h2>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">New supply partnership</p>
            </div>
            <button onclick="toggleModal('add-dealer-modal')" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors shadow-sm">✕</button>
        </div>
        <form action="{{ route('masters.dealers.store') }}" method="POST" class="p-10 space-y-8">
            @csrf
            <div class="grid grid-cols-2 gap-8">
                <x-input label="Firm Name *" name="firm_name" placeholder="Poultry Supplies Ltd" required />
                <x-input label="Contact Person" name="contact_person" placeholder="Manager Name" />
            </div>
            <div class="grid grid-cols-2 gap-8">
                <x-input label="Phone Number *" name="phone" placeholder="+91 98765 43210" required />
                <x-input label="GST Number" name="gst_number" placeholder="Optional" />
            </div>
            <div class="grid grid-cols-2 gap-8">
                <x-input label="Location" name="location" placeholder="City / Town" />
                <x-input label="Route / Area" name="route" placeholder="North Route" />
            </div>
            <div class="pt-4 flex gap-4">
                <x-button variant="ghost" class="flex-1" type="button" onclick="toggleModal('add-dealer-modal')">Cancel</x-button>
                <x-button variant="primary" class="flex-1" type="submit">Register Dealer</x-button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Dealer Modal -->
<div id="edit-dealer-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xl animate-in fade-in duration-300">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-2xl border border-white/20 overflow-hidden transform animate-in zoom-in-95 duration-300">
        <div class="flex items-center justify-between px-10 py-8 border-b border-slate-100 bg-slate-50/50">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Edit Dealer</h2>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Update partner info</p>
            </div>
            <button onclick="toggleModal('edit-dealer-modal')" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors shadow-sm">✕</button>
        </div>
        <form id="edit-dealer-form" method="POST" class="p-10 space-y-8">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-8">
                <x-input label="Firm Name *" name="firm_name" id="ed-firm" required />
                <x-input label="Contact Person" name="contact_person" id="ed-contact" />
            </div>
            <div class="grid grid-cols-2 gap-8">
                <x-input label="Phone Number *" name="phone" id="ed-phone" required />
                <x-input label="GST Number" name="gst_number" id="ed-gst" />
            </div>
            <div class="grid grid-cols-2 gap-8">
                <x-input label="Location" name="location" id="ed-location" />
                <x-input label="Route / Area" name="route" id="ed-route" />
            </div>
            <div class="pt-4 flex gap-4">
                <x-button variant="ghost" class="flex-1" type="button" onclick="toggleModal('edit-dealer-modal')">Cancel</x-button>
                <x-button variant="primary" class="flex-1" type="submit">Save Updates</x-button>
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

function openEditDealer(id, firm, contact, phone, gst, location, route) {
    const form = document.getElementById('edit-dealer-form');
    form.action = `/masters/dealers/${id}`;
    document.getElementById('ed-firm').value     = firm;
    document.getElementById('ed-contact').value  = contact;
    document.getElementById('ed-phone').value    = phone;
    document.getElementById('ed-gst').value      = gst;
    document.getElementById('ed-location').value = location;
    document.getElementById('ed-route').value    = route;
    toggleModal('edit-dealer-modal');
}
</script>
@endpush
