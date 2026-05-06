@extends('layouts.app')

@section('title', 'Edit Warehouse')

@section('content')
<div class="mb-6">
    <a href="{{ route('inventory.warehouses.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Warehouses</a>
    <h1 class="text-2xl font-bold text-gray-900">Edit Warehouse</h1>
    <p class="text-sm text-gray-500 mt-0.5">Update details for {{ $warehouse->name }}</p>
</div>

<div class="max-w-2xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('inventory.warehouses.update', $warehouse->id) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')
            
            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-700 uppercase tracking-tight">Warehouse Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" required value="{{ old('name', $warehouse->name) }}"
                       class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all font-bold text-emerald-600">
                @error('name') <p class="text-red-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
            </div>

            <div class="space-y-1.5">
                <label class="text-[10px] font-bold text-gray-700 uppercase tracking-tight">Address / Location</label>
                <textarea name="location" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">{{ old('location', $warehouse->location) }}</textarea>
            </div>

            <div class="pt-4">
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-700">Is Location Active?</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ $warehouse->is_active ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-50">
                <a href="{{ route('inventory.warehouses.index') }}" class="px-6 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">Cancel</a>
                <button type="submit" class="px-10 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-md transition-all active:scale-95">
                    Save Changes 💾
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
