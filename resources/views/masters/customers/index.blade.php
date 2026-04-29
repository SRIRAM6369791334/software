@extends('layouts.app')
@section('title', 'Customer Master')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Customer Master</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage customer records and details</p>
    </div>
    <button onclick="document.getElementById('add-customer-modal').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700
                   text-white text-sm font-semibold rounded-lg shadow-sm transition-colors">
        + Add Customer
    </button>
</div>

{{-- Search --}}
<form method="GET" class="mb-4 max-w-sm">
    <div class="relative">
        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">🔍</span>
        <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, phone, route..."
               class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
    </div>
</form>

{{-- Table --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Name</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Phone</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden md:table-cell">Address</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider hidden lg:table-cell">GST</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase tracking-wider">Route</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Type</th>
                    <th class="px-5 py-3.5 text-right text-xs font-semibold text-gray-400 uppercase tracking-wider">Balance</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-400 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($customers as $customer)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="px-5 py-3.5 font-medium text-gray-900">{{ $customer->name }}</td>
                        <td class="px-5 py-3.5 text-gray-600">📞 {{ $customer->phone }}</td>
                        <td class="px-5 py-3.5 text-gray-500 hidden md:table-cell">{{ $customer->address ?: '—' }}</td>
                        <td class="px-5 py-3.5 text-gray-500 font-mono text-xs hidden lg:table-cell">{{ $customer->gst_number ?: '—' }}</td>
                        <td class="px-5 py-3.5 text-gray-600">{{ $customer->route ?: '—' }}</td>
                        <td class="px-5 py-3.5 text-center">
                            <span class="px-2 py-0.5 rounded-full text-xs font-medium
                                {{ $customer->type === 'Wholesale' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-600' }}">
                                {{ $customer->type }}
                            </span>
                        </td>
                        <td class="px-5 py-3.5 text-right font-mono font-semibold
                            {{ $customer->balance > 0 ? 'text-red-600' : 'text-gray-400' }}">
                            {{ $customer->balance > 0 ? '₹'.number_format($customer->balance, 0, '.', ',') : '—' }}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center justify-center gap-1">
                                {{-- Edit --}}
                                <button onclick="openEditCustomer({{ $customer->id }},
                                    '{{ addslashes($customer->name) }}','{{ $customer->phone }}',
                                    '{{ addslashes($customer->address) }}','{{ $customer->gst_number }}',
                                    '{{ $customer->route }}','{{ $customer->type }}')"
                                        class="p-1.5 rounded-lg hover:bg-blue-50 text-blue-500 transition-colors" title="Edit">✏️</button>
                                {{-- Delete --}}
                                <form action="{{ route('masters.customers.destroy', $customer) }}" method="POST"
                                      onsubmit="return confirm('Delete {{ $customer->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-1.5 rounded-lg hover:bg-red-50 text-red-500 transition-colors" title="Delete">🗑️</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="px-5 py-10 text-center text-gray-400">No customers found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="px-5 py-3 border-t border-gray-100">
        {{ $customers->withQueryString()->links() }}
    </div>
</div>

{{-- Add Modal --}}
<div id="add-customer-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Add Customer</h2>
            <button onclick="document.getElementById('add-customer-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 text-xl leading-none">✕</button>
        </div>
        <form action="{{ route('masters.customers.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" required placeholder="Customer name"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Phone *</label>
                    <input type="text" name="phone" required placeholder="Phone number"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Address</label>
                <input type="text" name="address" placeholder="Full address"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">GST Number</label>
                    <input type="text" name="gst_number" placeholder="Optional"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Route / Area</label>
                    <input type="text" name="route" placeholder="Route A"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Customer Type</label>
                <select name="type" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="Retail">Retail</option>
                    <option value="Wholesale">Wholesale</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('add-customer-modal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 transition-colors">Cancel</button>
                <button type="submit"
                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg transition-colors">
                    Add Customer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div id="edit-customer-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Edit Customer</h2>
            <button onclick="document.getElementById('edit-customer-modal').classList.add('hidden')"
                    class="text-gray-400 hover:text-gray-600 text-xl leading-none">✕</button>
        </div>
        <form id="edit-customer-form" method="POST" class="p-6 space-y-4">
            @csrf @method('PUT')
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" id="edit-name" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Phone *</label>
                    <input type="text" name="phone" id="edit-phone" required
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Address</label>
                <input type="text" name="address" id="edit-address"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">GST Number</label>
                    <input type="text" name="gst_number" id="edit-gst"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-700 mb-1">Route</label>
                    <input type="text" name="route" id="edit-route"
                           class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                </div>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Customer Type</label>
                <select name="type" id="edit-type" class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-500">
                    <option value="Retail">Retail</option>
                    <option value="Wholesale">Wholesale</option>
                </select>
            </div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('edit-customer-modal').classList.add('hidden')"
                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-800">Cancel</button>
                <button type="submit"
                        class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">
                    Update Customer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openEditCustomer(id, name, phone, address, gst, route, type) {
    const form = document.getElementById('edit-customer-form');
    form.action = `/masters/customers/${id}`;
    document.getElementById('edit-name').value    = name;
    document.getElementById('edit-phone').value   = phone;
    document.getElementById('edit-address').value = address;
    document.getElementById('edit-gst').value     = gst;
    document.getElementById('edit-route').value   = route;
    document.getElementById('edit-type').value    = type;
    document.getElementById('edit-customer-modal').classList.remove('hidden');
}
</script>
@endpush
