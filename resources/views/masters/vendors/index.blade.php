@extends('layouts.app')
@section('title', 'Vendor Master')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-slate-950">Vendor Master</h1>
        <p class="text-sm text-slate-500 mt-0.5">Manage logistics and pharmaceutical suppliers</p>
    </div>
    <div class="mt-4 md:mt-0 flex gap-2">
        <button onclick="document.getElementById('add-vendor-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg shadow-md transition-all">
            + Add Vendor
        </button>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-4 rounded-xl border border-slate-200 shadow-sm">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Vendors</p>
        <h3 class="text-xl font-black text-slate-950">{{ $vendors->total() }}</h3>
    </div>
</div>

<form method="GET" class="mb-4 max-w-sm">
    <div class="relative group">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm group-focus-within:text-emerald-500 transition-colors"></span>
        <input type="text" name="search" value="{{ $search }}" placeholder="Search vendors..."
               class="w-full pl-9 pr-4 py-2 text-sm border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
    </div>
</form>

<div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-slate-200 bg-gradient-to-r from-emerald-50/80 to-sky-50/80">
                    <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Firm Name</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Contact Details</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider hidden md:table-cell">Location</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider">Route</th>
                    <th class="px-5 py-4 text-xs font-bold text-slate-400 uppercase tracking-wider hidden lg:table-cell">GSTIN</th>
                    <th class="px-5 py-4 text-center text-xs font-bold text-slate-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($vendors as $vendor)
                    <tr class="hover:bg-gradient-to-r from-emerald-50/70 to-sky-50/70 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('masters.vendors.show', $vendor) }}" class="font-bold text-emerald-600 hover:text-emerald-700 hover:underline transition-colors">
                                {{ $vendor->firm_name }}
                            </a>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-slate-800">{{ $vendor->contact_person ?: '-' }}</p>
                            <p class="text-xs text-slate-400 font-medium"> {{ $vendor->phone }}</p>
                        </td>
                        <td class="px-5 py-4 text-slate-500 hidden md:table-cell">{{ $vendor->location ?: '-' }}</td>
                        <td class="px-5 py-4 text-slate-600 font-medium">{{ $vendor->route ?: '-' }}</td>
                        <td class="px-5 py-4 text-slate-400 text-xs font-mono hidden lg:table-cell">{{ $vendor->gst_number ?: 'Unregistered' }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('masters.vendors.edit', $vendor) }}" 
                                   class="p-2 rounded-lg hover:bg-blue-50 text-blue-500 transition-colors border border-transparent hover:border-blue-100" title="Edit">✏
                                <form action="{{ route('masters.vendors.destroy', $vendor) }}" method="POST"
                                      onsubmit="return confirm('Remove this vendor record?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg hover:bg-red-50 text-red-500 transition-colors border border-transparent hover:border-red-100" title="Delete"></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-20 text-center text-slate-400 italic font-medium">No vendors found in master record.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($vendors->hasPages())
    <div class="px-5 py-4 border-t border-slate-200 bg-slate-50/20">
        {{ $vendors->withQueryString()->links() }}
    </div>
    @endif
</div>

<div id="add-vendor-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-2xl shadow-lg w-full max-w-lg border border-slate-200">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-base font-semibold text-slate-950">Add Vendor</h2>
            <button onclick="document.getElementById('add-vendor-modal').classList.add('hidden')" class="text-slate-400 text-xl">x</button>
        </div>
        <form action="{{ route('masters.vendors.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-slate-700 mb-1">Firm Name *</label>
                    <input type="text" name="firm_name" required class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
                <div><label class="block text-xs font-medium text-slate-700 mb-1">Contact Person</label>
                    <input type="text" name="contact_person" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-slate-700 mb-1">Phone *</label>
                    <input type="text" name="phone" required class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
                <div><label class="block text-xs font-medium text-slate-700 mb-1">GST Number</label>
                    <input type="text" name="gst_number" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-slate-700 mb-1">Location</label>
                    <input type="text" name="location" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
                <div><label class="block text-xs font-medium text-slate-700 mb-1">Route</label>
                    <input type="text" name="route" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            </div>
            <div><label class="block text-xs font-medium text-slate-700 mb-1">Notes</label>
                <textarea name="notes" rows="2" class="w-full px-3 py-2 text-sm border border-slate-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></textarea>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('add-vendor-modal').classList.add('hidden')" class="px-4 py-2 text-sm text-slate-600">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Add Vendor</button>
            </div>
        </form>
    </div>
</div>
@endsection
