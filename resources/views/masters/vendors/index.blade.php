@extends('layouts.app')
@section('title', 'Vendor Directory')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Vendor Directory</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Manage suppliers and inventory partners</p>
        </div>
        <div class="flex items-center gap-3">
            <x-button variant="secondary" size="md">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></x-slot>
                Export CSV
            </x-button>
            <x-button variant="primary" size="md" onclick="toggleModal('add-vendor-modal')">
                <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></x-slot>
                Add New Vendor
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
        </div>
    </x-card>

    <!-- Table Section -->
    <x-card padding="false">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-left border-b border-slate-100 bg-slate-50/50">
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Supplier Information</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Contact Details</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] hidden lg:table-cell">Area / Location</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] hidden lg:table-cell">GST Info</th>
                        <th class="px-8 py-5 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($vendors as $vendor)
                        <tr class="hover:bg-slate-50/50 transition-colors group">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-sm font-bold text-slate-500 border border-slate-200 transition-colors group-hover:bg-primary-50 group-hover:text-primary-600 group-hover:border-primary-100">
                                        {{ substr($vendor->firm_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-extrabold text-slate-900 text-base">{{ $vendor->firm_name }}</p>
                                        <p class="text-xs text-slate-500 font-medium mt-0.5">Primary Vendor</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-8 py-6">
                                <div class="space-y-1.5">
                                    <p class="text-slate-900 font-bold text-sm">{{ $vendor->contact_person }}</p>
                                    <p class="text-slate-600 font-bold flex items-center gap-2">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" /></svg>
                                        {{ $vendor->phone }}
                                    </p>
                                </div>
                            </td>
                            <td class="px-8 py-6 hidden lg:table-cell">
                                <div class="space-y-1">
                                    <p class="text-slate-700 font-bold">{{ $vendor->location ?: 'N/A' }}</p>
                                    <p class="text-[10px] font-bold text-primary-600 uppercase tracking-widest">{{ $vendor->route ?: 'Direct' }}</p>
                                </div>
                            </td>
                            <td class="px-8 py-6 hidden lg:table-cell">
                                <p class="text-[10px] font-mono text-slate-400 uppercase tracking-[0.2em]">{{ $vendor->gst_number ?: 'NO GST REGISTERED' }}</p>
                            </td>
                            <td class="px-8 py-6">
                                <div class="flex items-center justify-center gap-2">
                                    <form action="{{ route('masters.vendors.destroy', $vendor) }}" method="POST" onsubmit="return confirm('Archive {{ $vendor->firm_name }}?')">
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
                                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                                    </div>
                                    <p class="text-lg font-bold text-slate-900">No vendors found</p>
                                    <p class="text-sm text-slate-500 font-medium mt-1">Add your supply chain partners here</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($vendors->hasPages())
            <div class="px-8 py-6 border-t border-slate-100 bg-slate-50/30">
                {{ $vendors->withQueryString()->links() }}
            </div>
        @endif
    </x-card>
</div>

<!-- Add Vendor Modal -->
<div id="add-vendor-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xl animate-in fade-in duration-300">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-2xl border border-white/20 overflow-hidden transform animate-in zoom-in-95 duration-300">
        <div class="flex items-center justify-between px-10 py-8 border-b border-slate-100 bg-slate-50/50">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Add New Vendor</h2>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Establish new supply line</p>
            </div>
            <button onclick="toggleModal('add-vendor-modal')" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors shadow-sm">✕</button>
        </div>
        <form action="{{ route('masters.vendors.store') }}" method="POST" class="p-10 space-y-8">
            @csrf
            <div class="grid grid-cols-2 gap-8">
                <x-input label="Firm Name *" name="firm_name" placeholder="Poultry Feeders Inc" required />
                <x-input label="Contact Person" name="contact_person" placeholder="Sales Manager" />
            </div>
            <div class="grid grid-cols-2 gap-8">
                <x-input label="Phone Number *" name="phone" placeholder="+91 98765 43210" required />
                <x-input label="GST Number" name="gst_number" placeholder="Optional" />
            </div>
            <div class="grid grid-cols-2 gap-8">
                <x-input label="Location" name="location" placeholder="Industrial Area" />
                <x-input label="Route / Area" name="route" placeholder="Hub Route" />
            </div>
            <div class="space-y-2">
                <label class="text-[11px] font-bold text-slate-500 uppercase tracking-widest px-1">Notes / Terms</label>
                <textarea name="notes" rows="2" class="w-full bg-slate-50 border-slate-200 rounded-2xl py-3 px-5 text-sm font-medium outline-none focus:ring-4 focus:ring-primary-500/10 transition-all" placeholder="Payment terms, special instructions..."></textarea>
            </div>
            <div class="pt-4 flex gap-4">
                <x-button variant="ghost" class="flex-1" type="button" onclick="toggleModal('add-vendor-modal')">Cancel</x-button>
                <x-button variant="primary" class="flex-1" type="submit">Create Vendor</x-button>
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
</script>
@endpush
