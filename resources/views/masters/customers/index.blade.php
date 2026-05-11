@extends('layouts.app')
@section('title', 'Customer Master')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-6">
    <div>
        <h1 class="text-3xl font-black text-slate-950 tracking-tight">Customer Master</h1>
        <p class="text-slate-500 font-medium">Directory of retail buyers and wholesale partners</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
        <button onclick="document.getElementById('add-customer-modal').classList.remove('hidden')"
                class="px-6 py-4 bg-gradient-to-r from-emerald-600 to-sky-500 text-white text-sm font-black rounded-xl hover:bg-emerald-700 transition-all shadow-md shadow-emerald-600/20 active:scale-95">
            + Register Customer 
        </button>
    </div>
</div>

{{-- Master Stats --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-2xl border border-slate-200 shadow-md shadow-slate-200/60 flex items-center gap-6 group hover:border-emerald-200 transition-all">
        <div class="w-14 h-14 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
            <span class="material-symbols-rounded">groups</span>
        </div>
        <div>
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Total Active</h3>
            <p class="text-2xl font-black text-slate-950">{{ $customers->total() }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-2xl border border-slate-200 shadow-md shadow-slate-200/60 flex items-center gap-6 group hover:border-blue-200 transition-all">
        <div class="w-14 h-14 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
            <span class="material-symbols-rounded">storefront</span>
        </div>
        <div>
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Wholesale</h3>
            <p class="text-2xl font-black text-slate-950">{{ $customers->where('type', 'Wholesale')->count() }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 p-6 rounded-2xl border border-slate-200 shadow-md shadow-slate-200/60 flex items-center gap-6 group hover:border-amber-200 transition-all">
        <div class="w-14 h-14 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
            <span class="material-symbols-rounded">shopping_basket</span>
        </div>
        <div>
            <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Retail</h3>
            <p class="text-2xl font-black text-slate-950">{{ $customers->where('type', 'Retail')->count() }}</p>
        </div>
    </div>
    <div class="bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 p-6 rounded-2xl shadow-md shadow-slate-200/60 text-white flex items-center gap-6">
        <div class="w-14 h-14 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center text-2xl">
            <span class="material-symbols-rounded">warning</span>
        </div>
        <div>
            <h3 class="text-[10px] font-black text-emerald-200 uppercase tracking-widest mb-1">With Balance</h3>
            <p class="text-2xl font-black text-white">{{ $customers->where('balance', '>', 0)->count() }} Accounts</p>
        </div>
    </div>
</div>

{{-- Search & Table --}}
<div class="bg-gradient-to-br from-white via-emerald-50/40 to-sky-50/40 rounded-2xl border border-slate-200 shadow-lg overflow-hidden mb-12">
    <div class="p-8 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-6 bg-gradient-to-r from-emerald-50/80 to-sky-50/80">
        <form method="GET" class="relative w-full max-w-md">
            <span class="material-symbols-rounded absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, phone or route..."
                   class="w-full pl-12 pr-4 py-4 bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-medium text-sm">
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead>
                <tr class="bg-gradient-to-r from-emerald-50/80 to-sky-50/80 text-slate-400 font-black uppercase text-[10px] tracking-widest border-b border-slate-200">
                    <th class="px-8 py-5">Identity</th>
                    <th class="px-8 py-5">Communication</th>
                    <th class="px-8 py-5">Operational Details</th>
                    <th class="px-8 py-5 text-center">Type</th>
                    <th class="px-8 py-5 text-right">Outstanding</th>
                    <th class="px-8 py-5 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($customers as $customer)
                    <tr class="hover:bg-emerald-50/30 transition-all group">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-sky-50 flex items-center justify-center font-black text-slate-500 group-hover:bg-emerald-100 group-hover:text-emerald-600 transition-all">
                                    {{ substr($customer->name, 0, 1) }}
                                </div>
                                <div class="flex flex-col">
                                    <a href="{{ route('masters.customers.show', $customer) }}" class="font-black text-slate-950 tracking-tight hover:text-emerald-600 transition-colors">{{ $customer->name }}</a>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-tighter">{{ $customer->gst_number ?: 'NO GST' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="font-bold text-slate-950 leading-tight"> {{ $customer->phone }}</span>
                                <span class="text-[10px] text-slate-400 font-medium truncate max-w-[150px]">{{ $customer->address ?: 'No Address' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex flex-col">
                                <span class="text-[10px] font-black text-slate-400 uppercase mb-0.5">Assigned Route</span>
                                <span class="font-bold text-slate-700 tracking-tight">{{ $customer->route ?: 'General' }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="px-3 py-1.5 {{ $customer->type === 'Wholesale' ? 'bg-blue-50 text-blue-600' : 'bg-sky-50 text-slate-600' }} text-[9px] font-black rounded-lg tracking-tighter">
                                {{ strtoupper($customer->type) }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-right">
                            <span class="text-lg font-black {{ $customer->balance > 0 ? 'text-red-500' : 'text-slate-300' }}">
                                Rs {{ number_format($customer->balance, 0) }}
                            </span>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('masters.customers.ledger-pdf', $customer) }}"
                                   class="w-9 h-9 flex items-center justify-center bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-xl text-slate-400 hover:text-emerald-600 hover:border-emerald-200 hover:shadow-lg transition-all" title="Download Ledger PDF">
                                    
                                </a>
                                <button onclick="openEditCustomer({{ $customer->id }}, '{{ addslashes($customer->name) }}','{{ $customer->phone }}','{{ addslashes($customer->address) }}','{{ $customer->gst_number }}','{{ $customer->route }}','{{ $customer->type }}')"
                                        class="w-9 h-9 flex items-center justify-center bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-xl text-slate-400 hover:text-blue-600 hover:border-blue-200 hover:shadow-lg transition-all">
                                    ✏
                                </button>
                                <form action="{{ route('masters.customers.destroy', $customer) }}" method="POST" onsubmit="return confirm('Archive {{ $customer->name }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="w-9 h-9 flex items-center justify-center bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 rounded-xl text-slate-400 hover:text-red-600 hover:border-red-200 hover:shadow-lg transition-all">
                                        
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-8 py-20 text-center">
                            <div class="flex flex-col items-center">
                                <div class="mb-6 flex h-16 w-16 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
                                    <span class="material-symbols-rounded text-4xl">folder_open</span>
                                </div>
                                <h3 class="text-xl font-black text-slate-950">No Customers Found</h3>
                                <p class="text-slate-400 font-medium mt-1">Start by adding your first buyer.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
        <div class="p-8 border-t border-slate-100 bg-gradient-to-r from-emerald-50/70 to-sky-50/70">
            {{ $customers->withQueryString()->links() }}
        </div>
    @endif
</div>

{{-- Add Customer Modal --}}
<div id="add-customer-modal" class="hidden fixed inset-0 z-[100] overflow-y-auto bg-gradient-to-br from-slate-950/55 via-emerald-950/45 to-sky-950/45 px-4 py-6 backdrop-blur-sm sm:px-6">
    <div class="mx-auto flex min-h-full w-full max-w-3xl items-start justify-center">
    <div class="flex max-h-[calc(100vh-3rem)] w-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-950/20">
        <div class="flex shrink-0 items-center justify-between gap-4 border-b border-slate-100 bg-white px-6 py-5">
            <div>
                <h2 class="text-2xl font-black text-slate-950 tracking-tight uppercase tracking-widest">Register Customer </h2>
                <p class="text-sm text-slate-500 font-medium">Add a new profile to your business network</p>
            </div>
            <button onclick="document.getElementById('add-customer-modal').classList.add('hidden')" 
                    class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 text-slate-400 hover:text-red-500 transition-all shadow-sm">x</button>
        </div>

        <form action="{{ route('masters.customers.store') }}" method="POST" class="custom-scrollbar overflow-y-auto p-6">
            @csrf
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">1. Full Name *</label>
                    <input type="text" name="name" required placeholder="e.g. John Poultry Hub"
                           class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">2. Phone Number *</label>
                    <input type="text" name="phone" required placeholder="e.g. +91 98765 43210"
                           class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black">
                </div>
            </div>

            <div class="mt-5 space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">3. Delivery Address</label>
                <textarea name="address" rows="2" placeholder="Full store or office location..." 
                          class="w-full px-6 py-4 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-medium"></textarea>
            </div>

            <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">4. GSTIN</label>
                    <input type="text" name="gst_number" placeholder="Optional"
                           class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold uppercase">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">5. Route / Area</label>
                    <input type="text" name="route" placeholder="e.g. North Sector"
                           class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-bold">
                </div>
            </div>

            <div class="mt-5 space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">6. Business Type</label>
                <select name="type" class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 outline-none transition-all font-black">
                    <option value="Retail">Retail Store</option>
                    <option value="Wholesale">Wholesale Distributor</option>
                </select>
            </div>

            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                <button type="button" onclick="document.getElementById('add-customer-modal').classList.add('hidden')" class="rounded-xl px-5 py-3 text-sm font-black uppercase tracking-widest text-slate-500 transition-colors hover:bg-emerald-50 hover:text-slate-950">Cancel</button>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-7 py-3 text-sm font-black text-white shadow-md shadow-emerald-600/20 transition-all hover:bg-emerald-700 active:scale-95">
                    Register Now</button>
            </div>
        </form>
    </div>
</div>
</div>

{{-- Edit Customer Modal --}}
<div id="edit-customer-modal" class="hidden fixed inset-0 z-[100] overflow-y-auto bg-gradient-to-br from-slate-950/55 via-emerald-950/45 to-sky-950/45 px-4 py-6 backdrop-blur-sm sm:px-6">
    <div class="mx-auto flex min-h-full w-full max-w-3xl items-start justify-center">
    <div class="flex max-h-[calc(100vh-3rem)] w-full flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-2xl shadow-slate-950/20">
        <div class="flex shrink-0 items-center justify-between gap-4 border-b border-slate-100 bg-white px-6 py-5">
            <div>
                <h2 class="text-2xl font-black text-slate-950 tracking-tight uppercase tracking-widest">Edit Customer</h2>
                <p class="text-sm text-slate-500 font-medium">Modify existing customer information</p>
            </div>
            <button onclick="document.getElementById('edit-customer-modal').classList.add('hidden')" 
                    class="w-12 h-12 flex items-center justify-center rounded-2xl bg-gradient-to-br from-white via-emerald-50/30 to-sky-50/30 border border-slate-200 text-slate-400 hover:text-red-500 transition-all shadow-sm">x</button>
        </div>

        <form id="edit-customer-form" method="POST" class="custom-scrollbar overflow-y-auto p-6">
            @csrf @method('PUT')
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Full Name</label>
                    <input type="text" name="name" id="edit-name" required class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl font-black outline-none focus:ring-4 focus:ring-emerald-500/10">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Phone</label>
                    <input type="text" name="phone" id="edit-phone" required class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl font-black outline-none focus:ring-4 focus:ring-emerald-500/10">
                </div>
            </div>
            <div class="mt-5 space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Address</label>
                <textarea name="address" id="edit-address" rows="2" class="w-full px-6 py-4 bg-emerald-50 border border-slate-200 rounded-xl font-medium outline-none focus:ring-4 focus:ring-emerald-500/10"></textarea>
            </div>
            <div class="mt-5 grid grid-cols-1 gap-5 sm:grid-cols-2">
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">GSTIN</label>
                    <input type="text" name="gst_number" id="edit-gst" class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl font-bold uppercase outline-none focus:ring-4 focus:ring-emerald-500/10">
                </div>
                <div class="space-y-2">
                    <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Route</label>
                    <input type="text" name="route" id="edit-route" class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl font-bold outline-none focus:ring-4 focus:ring-emerald-500/10">
                </div>
            </div>
            <div class="mt-5 space-y-2">
                <label class="block text-[10px] font-black text-slate-400 uppercase tracking-widest">Business Type</label>
                <select name="type" id="edit-type" class="w-full px-6 py-5 bg-emerald-50 border border-slate-200 rounded-xl font-black outline-none focus:ring-4 focus:ring-emerald-500/10">
                    <option value="Retail">Retail Store</option>
                    <option value="Wholesale">Wholesale Distributor</option>
                </select>
            </div>
            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:justify-end">
                <button type="button" onclick="document.getElementById('edit-customer-modal').classList.add('hidden')" class="rounded-xl px-5 py-3 text-sm font-black uppercase tracking-widest text-slate-500 transition-colors hover:bg-emerald-50 hover:text-slate-950">Cancel</button>
                <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-7 py-3 text-sm font-black text-white shadow-md shadow-emerald-600/20 transition-all hover:bg-emerald-700 active:scale-95">
                    Save Changes</button>
            </div>
        </form>
    </div>
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
