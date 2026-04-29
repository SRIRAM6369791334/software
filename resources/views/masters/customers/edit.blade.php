@extends('layouts.app')
@section('title', 'Edit Customer')

@section('content')
<div class="mb-6">
    <a href="{{ route('masters.customers.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to List</a>
    <h1 class="text-2xl font-bold text-gray-900">Edit Customer: {{ $customer->name }}</h1>
</div>

<div class="max-w-3xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('masters.customers.update', $customer) }}" method="POST" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $customer->name) }}" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Phone Number <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone', $customer->phone) }}" required class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="md:col-span-2 space-y-1.5">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Address</label>
                    <textarea name="address" rows="2" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">{{ old('address', $customer->address) }}</textarea>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">GST Number</label>
                    <input type="text" name="gst_number" value="{{ old('gst_number', $customer->gst_number) }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Route / Area</label>
                    <input type="text" name="route" value="{{ old('route', $customer->route) }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Customer Type</label>
                    <select name="type" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                        <option value="Retail" {{ $customer->type === 'Retail' ? 'selected' : '' }}>Retail</option>
                        <option value="Wholesale" {{ $customer->type === 'Wholesale' ? 'selected' : '' }}>Wholesale</option>
                    </select>
                </div>
                <div class="space-y-1.5">
                    <label class="text-xs font-bold text-gray-700 uppercase tracking-tight">Current Balance (₹)</label>
                    <input type="number" name="balance" step="0.01" value="{{ old('balance', $customer->balance) }}" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                </div>
            </div>
            
            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('masters.customers.index') }}" class="px-5 py-2 text-sm font-semibold text-gray-600 hover:text-gray-800 transition-colors">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg shadow-sm transition-all">
                    Update Customer 💾
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
