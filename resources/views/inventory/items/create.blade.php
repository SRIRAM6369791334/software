@extends('layouts.app')

@section('title', 'Add Item')

@section('content')
<div class="mb-6">
    <a href="{{ route('inventory.items.index') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 uppercase tracking-wider mb-2 inline-block">← Back to Items</a>
    <h1 class="text-2xl font-bold text-gray-900">Add New Item</h1>
    <p class="text-sm text-gray-500 mt-0.5">Register a new poultry resource in the master record</p>
</div>

<div class="max-w-4xl">
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <form action="{{ route('inventory.items.store') }}" method="POST" class="p-6 space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Basic Info --}}
                <div class="space-y-5">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2">1. Item Information</h3>
                    
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-700 uppercase tracking-tight">Item Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required value="{{ old('name') }}" placeholder="Ex: Broiler Feed Starter"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                        @error('name') <p class="text-red-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-700 uppercase tracking-tight">Item Code (Optional)</label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="Ex: FEED-001"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all font-mono">
                        @error('code') <p class="text-red-500 text-[10px] mt-1 font-bold">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase tracking-tight">Type <span class="text-red-500">*</span></label>
                            <select name="type" id="item-type" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                                <option value="">Select...</option>
                                @foreach(['Feed', 'Chick', 'Medicine', 'Vaccine', 'Equipment', 'Other'] as $type)
                                    <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold text-gray-700 uppercase tracking-tight">Category</label>
                            <input type="text" name="category" value="{{ old('category') }}" id="category-input" list="category-options" placeholder="Ex: Starter"
                                   class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-700 uppercase tracking-tight">Brand Name</label>
                        <input type="text" name="brand" value="{{ old('brand') }}" placeholder="Ex: Suguna"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>

                    <div id="chick-breed-container" class="space-y-1.5 hidden">
                        <label class="text-[10px] font-bold text-gray-700 uppercase tracking-tight">Breed Name</label>
                        <input type="text" name="breed" value="{{ old('breed') }}" placeholder="Ex: Cobb 500"
                               class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                    </div>
                </div>

                {{-- Unit Info --}}
                <div class="space-y-5">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest border-b border-gray-100 pb-2">2. Unit Logic</h3>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-700 uppercase tracking-tight">Base Unit <span class="text-red-500">*</span></label>
                        <select name="base_unit" id="base-unit" required class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                            <option value="kg" {{ old('base_unit') == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                            <option value="nos" {{ old('base_unit') == 'nos' ? 'selected' : '' }}>Numbers (nos)</option>
                            <option value="ml" {{ old('base_unit') == 'ml' ? 'selected' : '' }}>Milliliters (ml)</option>
                            <option value="ltr" {{ old('base_unit') == 'ltr' ? 'selected' : '' }}>Liters (ltr)</option>
                            <option value="vial" {{ old('base_unit') == 'vial' ? 'selected' : '' }}>Vial</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold text-gray-700 uppercase tracking-tight">Conversion Factor</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="conversion_rate" id="conversion-rate" step="0.01" value="{{ old('conversion_rate', 1.00) }}"
                                   class="flex-1 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500 outline-none transition-all">
                            <span class="text-[10px] font-bold text-gray-400" id="conversion-label">per Bag</span>
                        </div>
                        <p class="text-[10px] text-gray-400 font-medium italic mt-1">If 1 Bag = 50kg, enter 50.00</p>
                    </div>

                    <div class="pt-4">
                        <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100 border-dashed">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-emerald-800">Is Item Active?</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                                    <div class="w-10 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-600"></div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-6 border-t border-gray-50">
                <a href="{{ route('inventory.items.index') }}" class="px-6 py-2.5 text-sm font-semibold text-gray-600 hover:text-gray-900 transition-colors">Cancel</a>
                <button type="submit" class="px-10 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-lg shadow-md transition-all active:scale-95">
                    Save New Item 💾
                </button>
            </div>
        </form>
    </div>
</div>

<datalist id="category-options">
    <option value="Starter">
    <option value="Grower">
    <option value="Finisher">
    <option value="Pre-Starter">
</datalist>

@push('scripts')
<script>
document.getElementById('item-type').addEventListener('change', function() {
    const type = this.value;
    const catInput = document.getElementById('category-input');
    const breedContainer = document.getElementById('chick-breed-container');
    const baseUnit = document.getElementById('base-unit');
    
    if (type === 'Chick') {
        breedContainer.classList.remove('hidden');
        baseUnit.value = 'nos';
    } else {
        breedContainer.classList.add('hidden');
    }

    if (type === 'Feed') {
        baseUnit.value = 'kg';
    } else if (type === 'Medicine' || type === 'Vaccine') {
        baseUnit.value = 'ml';
    }
});

document.getElementById('base-unit').addEventListener('change', function() {
    const unit = this.value;
    document.getElementById('conversion-label').textContent = `per Bag (in ${unit})`;
});
</script>
@endpush
@endsection
