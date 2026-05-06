@extends('layouts.app')
@section('title', 'Dealer Master')

@section('content')
<div class="flex flex-col md:flex-row md:items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dealer Master</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage key suppliers for feed and chicks</p>
    </div>
    <div class="mt-4 md:mt-0 flex gap-2">
        <button onclick="document.getElementById('add-dealer-modal').classList.remove('hidden')"
                class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700
                       text-white text-sm font-semibold rounded-lg shadow-md transition-all">
            + Add Dealer
        </button>
    </div>
</div>

{{-- Summary Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Dealers</p>
        <h3 class="text-xl font-black text-gray-900">{{ $dealers->total() }}</h3>
    </div>
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Pending</p>
        <h3 class="text-xl font-black text-red-600">₹{{ number_format($dealers->sum('pending_amount'), 0) }}</h3>
    </div>
</div>

<form method="GET" class="mb-4 max-w-sm">
    <div class="relative group">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm group-focus-within:text-emerald-500 transition-colors">🔍</span>
        <input type="text" name="search" value="{{ $search }}" placeholder="Search dealers by name or route..."
               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 transition-all">
    </div>
</form>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50/50">
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Firm Name</th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Contact Details</th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider hidden md:table-cell">Location</th>
                    <th class="px-5 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Route</th>
                    <th class="px-5 py-4 text-right text-xs font-bold text-gray-400 uppercase tracking-wider">Pending Amount</th>
                    <th class="px-5 py-4 text-center text-xs font-bold text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($dealers as $dealer)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('masters.dealers.show', $dealer) }}" class="font-bold text-emerald-600 hover:text-emerald-700 hover:underline transition-colors">
                                {{ $dealer->firm_name }}
                            </a>
                        </td>
                        <td class="px-5 py-4">
                            <p class="font-semibold text-gray-800">{{ $dealer->contact_person ?: '—' }}</p>
                            <p class="text-xs text-gray-400 font-medium">📞 {{ $dealer->phone }}</p>
                        </td>
                        <td class="px-5 py-4 text-gray-500 hidden md:table-cell">{{ $dealer->location ?: '—' }}</td>
                        <td class="px-5 py-4 text-gray-600 font-medium">{{ $dealer->route ?: '—' }}</td>
                        <td class="px-5 py-4 text-right">
                            @if($dealer->pending_amount > 0)
                                <span class="font-black text-red-600">₹{{ number_format($dealer->pending_amount, 0) }}</span>
                            @else
                                <span class="text-gray-300">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <button onclick="openEditDealer({{ $dealer->id }},'{{ addslashes($dealer->firm_name) }}','{{ addslashes($dealer->contact_person) }}','{{ $dealer->phone }}','{{ $dealer->gst_number }}','{{ addslashes($dealer->location) }}','{{ $dealer->route }}')"
                                        class="p-2 rounded-lg hover:bg-blue-50 text-blue-500 transition-colors border border-transparent hover:border-blue-100" title="Edit">✏️</button>
                                <form action="{{ route('masters.dealers.destroy', $dealer) }}" method="POST"
                                      onsubmit="return confirm('Remove this dealer record?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 rounded-lg hover:bg-red-50 text-red-500 transition-colors border border-transparent hover:border-red-100" title="Delete">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-20 text-center text-gray-400 italic font-medium">No dealers found in master record.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($dealers->hasPages())
    <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/20">
        {{ $dealers->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- Add Modal --}}
<div id="add-dealer-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-base font-semibold text-gray-900">Add Dealer</h2>
            <button onclick="document.getElementById('add-dealer-modal').classList.add('hidden')" class="text-gray-400 text-xl">✕</button>
        </div>
        <form action="{{ route('masters.dealers.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Firm Name *</label>
                    <input type="text" name="firm_name" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Contact Person</label>
                    <input type="text" name="contact_person" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Phone *</label>
                    <input type="text" name="phone" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">GST Number</label>
                    <input type="text" name="gst_number" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Route</label>
                    <input type="text" name="route" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('add-dealer-modal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Add Dealer</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="edit-dealer-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-base font-semibold text-gray-900">Edit Dealer</h2>
            <button onclick="document.getElementById('edit-dealer-modal').classList.add('hidden')" class="text-gray-400 text-xl">✕</button>
        </div>
        <form id="edit-dealer-form" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Firm Name *</label>
                    <input type="text" name="firm_name" id="ed-firm" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Contact Person</label>
                    <input type="text" name="contact_person" id="ed-contact" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Phone *</label>
                    <input type="text" name="phone" id="ed-phone" required class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">GST Number</label>
                    <input type="text" name="gst_number" id="ed-gst" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" id="ed-location" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
                <div><label class="block text-xs font-medium text-gray-700 mb-1">Route</label>
                    <input type="text" name="route" id="ed-route" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('edit-dealer-modal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Update</button>
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
