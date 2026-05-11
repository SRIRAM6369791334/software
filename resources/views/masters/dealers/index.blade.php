@extends('layouts.app')
@section('title', 'Dealer Master')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h1 class="text-3xl font-black text-slate-950 tracking-tight">Dealer Master</h1>
        <p class="text-slate-500 font-medium">Manage relationships with feed, chick, and medicine suppliers</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <button onclick="document.getElementById('add-dealer-modal').classList.remove('hidden')"
                class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-sky-500 text-white text-sm font-black rounded-xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-600/20 active:scale-95">
            + Register Dealer 
        </button>
    </div>
</div>

{{-- Dealer Insights --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-2xl border border-slate-200 shadow-md shadow-slate-200/60 flex items-center gap-6 group hover:border-emerald-200 transition-all">
        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform"></div>
        <div>
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Dealers</h3>
            <p class="text-2xl font-black text-slate-950">{{ $dealers->total() }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-2xl border border-slate-200 shadow-md shadow-slate-200/60 flex items-center gap-6 group hover:border-red-200 transition-all">
        <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform"></div>
        <div>
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Payable</h3>
            <p class="text-2xl font-black text-slate-950">Rs {{ number_format($dealers->sum('pending_amount'), 0) }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 p-6 rounded-2xl shadow-md shadow-slate-200/60 text-white flex items-center gap-6">
        <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center text-2xl">⚡</div>
        <div>
            <h3 class="text-[10px] font-black text-emerald-200 uppercase tracking-widest mb-1">Active Accounts</h3>
            <p class="text-2xl font-black text-white">{{ $dealers->where('pending_amount', '>', 0)->count() }} with Dues</p>
        </div>
    </div>
</div>

{{-- Main Table --}}
<div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-2xl border border-slate-200 shadow-lg overflow-hidden mb-12">
    <div class="p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gradient-to-r from-emerald-50/80 to-sky-50/80">
        <form method="GET" class="relative w-full max-w-md">
            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by firm or contact..."
                   class="w-full pl-12 pr-4 py-4 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-medium text-sm">
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-gradient-to-r from-emerald-50/80 to-sky-50/80 text-slate-400 font-black uppercase text-[10px] tracking-widest border-b border-slate-200">
                    <th class="px-8 py-5">Firm & Location</th>
                    <th class="px-8 py-5">Point of Contact</th>
                    <th class="px-8 py-5">Operational Area</th>
                    <th class="px-8 py-5 text-right">Pending Balance</th>
                    <th class="px-8 py-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($dealers as $dealer)
                    <tr class="hover:bg-emerald-50/30 transition-all group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center font-black text-slate-500 group-hover:bg-emerald-100 group-hover:text-emerald-600 transition-all">
                                    {{ substr($dealer->firm_name, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <a href="{{ route('masters.dealers.show', $dealer) }}" class="font-black text-slate-950 tracking-tight hover:text-emerald-600 transition-colors">{{ $dealer->firm_name }}</a>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ $dealer->location ?: 'NO LOCATION' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-950 leading-tight">{{ $dealer->contact_person ?: '-' }}</span>
                                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter"> {{ $dealer->phone }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <span class="text-xs font-black text-slate-500 uppercase tracking-widest bg-sky-50 px-3 py-1 rounded-lg">
                                {{ $dealer->route ?: 'GENERAL' }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            @if($dealer->pending_amount > 0)
                                <span class="text-lg font-black text-red-500">Rs {{ number_format($dealer->pending_amount, 0) }}</span>
                            @else
                                <span class="text-sm font-bold text-slate-300">CLEARED</span>
                            @endif
                        </td>
                        <td class="px-8 py-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('masters.dealers.ledger-pdf', $dealer) }}"
                                   class="w-9 h-9 flex items-center justify-center bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-xl text-slate-400 hover:text-primary hover:border-indigo-200 hover:shadow-lg transition-all" title="Download Ledger PDF">
                                    
                                </a>
                                <button onclick="openEditDealer({{ $dealer->id }},'{{ addslashes($dealer->firm_name) }}','{{ addslashes($dealer->contact_person) }}','{{ $dealer->phone }}','{{ $dealer->gst_number }}','{{ addslashes($dealer->location) }}','{{ $dealer->route }}')"
                                        class="w-9 h-9 flex items-center justify-center bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-xl text-slate-400 hover:text-blue-600 hover:border-blue-200 hover:shadow-lg transition-all">
                                    ✏
                                </button>
                                <form action="{{ route('masters.dealers.destroy', $dealer) }}" method="POST" onsubmit="return confirm('Archive dealer record?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-9 h-9 flex items-center justify-center bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-xl text-slate-400 hover:text-red-600 hover:border-red-200 hover:shadow-lg transition-all">
                                        
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="text-6xl mb-6"></div>
                                <h3 class="text-xl font-black text-slate-950">No Dealers Recorded</h3>
                                <p class="text-slate-400 font-medium mt-1">Register your first supplier profile.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($dealers->hasPages())
        <div class="p-8 border-t border-slate-100 bg-gradient-to-r from-emerald-50/70 to-sky-50/70">
            {{ $dealers->withQueryString()->links() }}
        </div>
    @endif
</div>

{{-- Add Dealer Modal --}}
<div id="add-dealer-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200/40 backdrop-blur-md transition-all">
    <div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-3xl shadow-lg w-full max-w-2xl border border-white/20 overflow-hidden">
        <div class="flex items-center justify-between px-10 py-8 border-b border-slate-100 bg-gradient-to-r from-emerald-50/70 to-sky-50/70">
            <div>
                <h2 class="text-2xl font-black text-slate-950 tracking-tight uppercase tracking-widest">Register Dealer </h2>
                <p class="text-sm text-slate-500 font-medium">Add a new supplier to your procurement network</p>
            </div>
            <button onclick="document.getElementById('add-dealer-modal').classList.add('hidden')" 
                    class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 text-slate-400 hover:text-red-500 transition-all shadow-sm">x</button>
        </div>

        <form action="{{ route('masters.dealers.store') }}" method="POST" class="p-10 space-y-8">
            @csrf
            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">1. Firm Name *</label>
                    <input type="text" name="firm_name" required placeholder="e.g. Superior Feed Mills"
                           class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">2. Contact Person</label>
                    <input type="text" name="contact_person" placeholder="e.g. Sales Manager"
                           class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">3. Phone Number *</label>
                    <input type="text" name="phone" required placeholder="e.g. +91..."
                           class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">4. GSTIN</label>
                    <input type="text" name="gst_number" placeholder="Optional"
                           class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold uppercase">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">5. Location</label>
                    <input type="text" name="location" placeholder="e.g. Industrial Estate"
                           class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">6. Route</label>
                    <input type="text" name="route" placeholder="Supply route..."
                           class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold">
                </div>
            </div>

            <div class="flex justify-end gap-6 pt-6">
                <button type="button" onclick="document.getElementById('add-dealer-modal').classList.add('hidden')" class="px-8 py-4 text-sm font-black text-slate-400 hover:text-slate-950 transition-colors uppercase tracking-widest">Cancel</button>
                <button type="submit" class="px-12 py-5 bg-gradient-to-r from-emerald-600 to-sky-500 text-white font-black rounded-3xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-600/20 active:scale-95 transform">
                    Register Dealer 
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Dealer Modal --}}
<div id="edit-dealer-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-6 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200/40 backdrop-blur-md transition-all">
    <div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-3xl shadow-lg w-full max-w-2xl border border-white/20 overflow-hidden">
        <div class="flex items-center justify-between px-10 py-8 border-b border-slate-100 bg-gradient-to-r from-emerald-50/70 to-sky-50/70">
            <div>
                <h2 class="text-2xl font-black text-slate-950 tracking-tight uppercase tracking-widest">Edit Supplier ✏
                <p class="text-sm text-slate-500 font-medium">Modify existing dealer information</p>
            </div>
            <button onclick="document.getElementById('edit-dealer-modal').classList.add('hidden')" 
                    class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 text-slate-400 hover:text-red-500 transition-all shadow-sm">x</button>
        </div>

        <form id="edit-dealer-form" method="POST" class="p-10 space-y-8">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Firm Name</label>
                    <input type="text" name="firm_name" id="ed-firm" required class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl font-black outline-none focus:ring-4 focus:ring-emerald-500/10">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Contact Person</label>
                    <input type="text" name="contact_person" id="ed-contact" class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl font-black outline-none focus:ring-4 focus:ring-emerald-500/10">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Phone</label>
                    <input type="text" name="phone" id="ed-phone" required class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl font-black outline-none focus:ring-4 focus:ring-emerald-500/10">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">GSTIN</label>
                    <input type="text" name="gst_number" id="ed-gst" class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl font-bold uppercase outline-none focus:ring-4 focus:ring-emerald-500/10">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Location</label>
                    <input type="text" name="location" id="ed-location" class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl font-bold outline-none focus:ring-4 focus:ring-emerald-500/10">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Route</label>
                    <input type="text" name="route" id="ed-route" class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl font-bold outline-none focus:ring-4 focus:ring-emerald-500/10">
                </div>
            </div>
            <div class="flex justify-end gap-6 pt-6">
                <button type="button" onclick="document.getElementById('edit-dealer-modal').classList.add('hidden')" class="px-8 py-4 text-sm font-black text-slate-400 hover:text-slate-950 transition-colors uppercase tracking-widest">Cancel</button>
                <button type="submit" class="px-12 py-5 bg-gradient-to-r from-emerald-600 to-sky-500 text-white font-black rounded-3xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-600/20 active:scale-95 transform">
                    Update Supplier 
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditDealer(id, firm, contact, phone, gst, location, route) {
    document.getElementById('edit-dealer-form').action = `/masters/dealers/${id}`;
    document.getElementById('ed-firm').value     = firm;
    document.getElementById('ed-contact').value  = contact;
    document.getElementById('ed-phone').value    = phone;
    document.getElementById('ed-gst').value      = gst;
    document.getElementById('ed-location').value = location;
    document.getElementById('ed-route').value    = route;
    document.getElementById('edit-dealer-modal').classList.remove('hidden');
}
</script>
@endpush
