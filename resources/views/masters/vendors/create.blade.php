@extends('layouts.app')
@section('title', 'Add Vendor')

@section('content')
<div class="mb-6">
    <a href="{{ route('masters.vendors.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to List</a>
    <h1 class="text-2xl font-bold text-gray-900">Add New Vendor</h1>
</div>

<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('masters.vendors.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5 flex flex-col">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Firm Name <span class="text-red-500">*</span></label>
                    <input type="text" name="firm_name" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="Poultry Supplies Pvt Ltd">
                </div>
                <div class="space-y-1.5 flex flex-col">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">GST Number</label>
                    <input type="text" name="gst_number" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="GSTIN">
                </div>
                <div class="space-y-1.5 flex flex-col">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Contact Person</label>
                    <input type="text" name="contact_person" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="Supplier Manager">
                </div>
                <div class="space-y-1.5 flex flex-col">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Phone Number <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="Contact number">
                </div>
                <div class="md:col-span-2 space-y-1.5 flex flex-col">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Item(s) Supplied / Location</label>
                    <input type="text" name="location" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="Feed, Chicks, etc. / City">
                </div>
                <div class="md:col-span-2 space-y-1.5 flex flex-col">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Notes / Details</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all" placeholder="Additional vendor info..."></textarea>
                </div>
            </div>
            
            <div class="pt-4 flex justify-end gap-3">
                <button type="reset" class="px-5 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800 transition-colors">Reset</button>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-lg shadow-sm transition-all">
                    Register Vendor 🏗️
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
