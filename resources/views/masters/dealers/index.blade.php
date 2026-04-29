@extends('layouts.app')
@section('title', 'Dealer Master')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Dealer Master</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage dealer records and purchase orders</p>
    </div>
    <button onclick="document.getElementById('add-dealer-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700
                   text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
        + Add Dealer
    </button>
</div>

<form method="GET" class="mb-4 max-w-sm">
    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
        <input type="text" name="search" value="{{ $search }}" placeholder="Search dealers..."
               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
    </div>
</form>

<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Firm Name</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Contact</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:table-cell">Location</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Route</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Pending</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($dealers as $dealer)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3.5 font-medium text-gray-900">{{ $dealer->firm_name }}</td>
                        <td class="px-5 py-3.5">
                            <div class="text-sm text-gray-800">{{ $dealer->contact_person ?: '—' }}</div>
                            <div class="text-xs text-gray-400">📞 {{ $dealer->phone }}</div>
                        </td>
                        <td class="px-5 py-3.5 text-gray-500 hidden md:table-cell">{{ $dealer->location ?: '—' }}</td>
                        <td class="px-5 py-3.5 text-gray-600">{{ $dealer->route ?: '—' }}</td>
                        <td class="px-5 py-3.5 text-right font-mono font-semibold {{ $dealer->pending_amount > 0 ? 'text-red-600' : 'text-gray-400' }}">
                            {{ $dealer->pending_amount > 0 ? '₹'.number_format($dealer->pending_amount, 0, '.', ',') : '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="openEditDealer({{ $dealer->id }},'{{ addslashes($dealer->firm_name) }}','{{ addslashes($dealer->contact_person) }}','{{ $dealer->phone }}','{{ $dealer->gst_number }}','{{ addslashes($dealer->location) }}','{{ $dealer->route }}')"
                                        class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-500 transition-colors">✏️</button>
                                <form action="{{ route('masters.dealers.destroy', $dealer) }}" method="POST"
                                      onsubmit="return confirm('Delete {{ $dealer->firm_name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg hover:bg-red-50 text-red-500">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-5 py-10 text-center text-gray-400">No dealers found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">{{ $dealers->withQueryString()->links() }}</div>
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
