@extends('layouts.app')
@section('title', 'Add Item')

@section('content')

<div class="cm-page">

    {{-- Top Bar --}}
    <div class="cm-topbar">
        <div>
            <a href="{{ route('inventory.items.index') }}" class="cm-route" style="display:inline-block; margin-bottom: 5px;">← Back to Items</a>
            <h1 class="cm-page-title">Add New Item</h1>
            <p class="cm-page-sub">Register a new poultry resource in the master record</p>
        </div>
    </div>

    <div class="cm-table-card" style="max-width: 800px;">
        <form action="{{ route('inventory.items.store') }}" method="POST" style="padding: 1.5rem;">
            @csrf
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                {{-- Basic Info --}}
                <div>
                    <h3 style="font-size: 0.75rem; font-weight: 700; color: var(--cm-text-muted); text-transform: uppercase; border-bottom: 0.5px solid var(--cm-card-border); padding-bottom: 0.5rem; margin-bottom: 1rem;">1. Item Information</h3>
                    
                    <div class="cm-form-group">
                        <label class="cm-form-label">Item Name <span class="cm-required">*</span></label>
                        <input type="text" name="name" required value="{{ old('name') }}" placeholder="Ex: Broiler Feed Starter"
                               class="cm-form-input">
                        @error('name') <p style="color: #dc2626; font-size: 0.75rem; margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>

                    <div class="cm-form-group">
                        <label class="cm-form-label">Item Code (Optional)</label>
                        <input type="text" name="code" value="{{ old('code') }}" placeholder="Ex: FEED-001"
                               class="cm-form-input" style="font-family: monospace;">
                        @error('code') <p style="color: #dc2626; font-size: 0.75rem; margin-top: 4px;">{{ $message }}</p> @enderror
                    </div>

                    <div class="cm-form-grid">
                        <div class="cm-form-group">
                            <label class="cm-form-label">Type <span class="cm-required">*</span></label>
                            <select name="type" id="item-type" required class="cm-form-input">
                                <option value="">Select...</option>
                                @foreach(['Feed', 'Chick', 'Medicine', 'Vaccine', 'Equipment', 'Other'] as $type)
                                    <option value="{{ $type }}" {{ old('type') == $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="cm-form-group">
                            <label class="cm-form-label">Category</label>
                            <input type="text" name="category" value="{{ old('category') }}" id="category-input" list="category-options" placeholder="Ex: Starter"
                                   class="cm-form-input">
                        </div>
                    </div>

                    <div class="cm-form-group">
                        <label class="cm-form-label">Brand Name</label>
                        <input type="text" name="brand" value="{{ old('brand') }}" placeholder="Ex: Suguna"
                               class="cm-form-input">
                    </div>

                    <div id="chick-breed-container" class="cm-form-group cm-hidden">
                        <label class="cm-form-label">Breed Name</label>
                        <input type="text" name="breed" value="{{ old('breed') }}" placeholder="Ex: Cobb 500"
                               class="cm-form-input">
                    </div>
                </div>

                {{-- Unit Info --}}
                <div>
                    <h3 style="font-size: 0.75rem; font-weight: 700; color: var(--cm-text-muted); text-transform: uppercase; border-bottom: 0.5px solid var(--cm-card-border); padding-bottom: 0.5rem; margin-bottom: 1rem;">2. Unit Logic</h3>

                    <div class="cm-form-group">
                        <label class="cm-form-label">Base Unit <span class="cm-required">*</span></label>
                        <select name="base_unit" id="base-unit" required class="cm-form-input">
                            <option value="kg" {{ old('base_unit') == 'kg' ? 'selected' : '' }}>Kilograms (kg)</option>
                            <option value="nos" {{ old('base_unit') == 'nos' ? 'selected' : '' }}>Numbers (nos)</option>
                            <option value="ml" {{ old('base_unit') == 'ml' ? 'selected' : '' }}>Milliliters (ml)</option>
                            <option value="ltr" {{ old('base_unit') == 'ltr' ? 'selected' : '' }}>Liters (ltr)</option>
                            <option value="vial" {{ old('base_unit') == 'vial' ? 'selected' : '' }}>Vial</option>
                        </select>
                    </div>

                    <div class="cm-form-group">
                        <label class="cm-form-label">Conversion Factor</label>
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <input type="number" name="conversion_rate" id="conversion-rate" step="0.01" value="{{ old('conversion_rate', 1.00) }}"
                                   class="cm-form-input" style="flex: 1;">
                            <span style="font-size: 0.75rem; font-weight: 600; color: var(--cm-text-muted);" id="conversion-label">per Bag</span>
                        </div>
                        <p style="font-size: 0.75rem; color: var(--cm-text-muted); margin-top: 4px; font-style: italic;">If 1 Bag = 50kg, enter 50.00</p>
                    </div>

                    <div style="margin-top: 1.5rem; padding: 1rem; background: var(--cm-bg); border-radius: 8px; border: 0.5px dashed var(--cm-card-border);">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <span style="font-size: 0.8125rem; font-weight: 600; color: var(--cm-text-primary);">Is Item Active?</span>
                            <input type="checkbox" name="is_active" value="1" checked style="width: 18px; height: 18px;">
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 10px; margin-top: 2rem; padding-top: 1rem; border-top: 0.5px solid var(--cm-card-border);">
                <a href="{{ route('inventory.items.index') }}" class="cm-btn-ghost">Cancel</a>
                <button type="submit" class="cm-btn-primary cm-btn-primary--blue">Save New Item</button>
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

@endsection

@push('styles')
<style>
/* ── Theme Variables & Dark Mode Matrix ── */
:root {
    --cm-bg: #f8fafc;
    --cm-card-bg: #ffffff;
    --cm-card-border: #e2e8f0;
    --cm-text-primary: #0f172a;
    --cm-text-secondary: #475569;
    --cm-text-muted: #94a3b8;
    --cm-accent-teal: #0d9488;
    --cm-accent-blue: #2563eb;
    --cm-shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

[data-theme='dark'] {
    --cm-bg: #090d16;
    --cm-card-bg: #111827;
    --cm-card-border: #1f2937;
    --cm-text-primary: #f3f4f6;
    --cm-text-secondary: #9ca3af;
    --cm-text-muted: #6b7280;
}

*, *::before, *::after { box-sizing: border-box; }

.cm-page { padding: 2rem 0 3rem; }
.cm-hidden { display: none !important; }

.cm-topbar { margin-bottom: 1.5rem; }
.cm-page-title { font-size: 1.375rem; font-weight: 700; color: var(--cm-text-primary); letter-spacing: -0.02em; }
.cm-page-sub { font-size: 0.8125rem; color: var(--cm-text-secondary); margin-top: 2px; }
.cm-route { font-size: 0.75rem; color: var(--cm-accent-blue); text-decoration: none; font-weight: 600; text-transform: uppercase; }

.cm-table-card {
    background: var(--cm-card-bg);
    border: 0.5px solid var(--cm-card-border);
    border-radius: 12px;
    box-shadow: var(--cm-shadow-sm);
}

.cm-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 12px; }
@media (max-width: 480px) { .cm-form-grid { grid-template-columns: 1fr; } }
.cm-form-group { margin-bottom: 12px; }
.cm-form-label {
    display: block; font-size: 0.6875rem; font-weight: 600; color: var(--cm-text-secondary);
    text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 5px;
}
.cm-required { color: #dc2626; }
.cm-form-input {
    width: 100%; padding: 8px 10px; border: 0.5px solid var(--cm-card-border);
    border-radius: 8px; font-size: 0.8125rem; background: var(--cm-bg); color: var(--cm-text-primary);
    outline: none; transition: border-color 0.15s; font-family: inherit;
}
.cm-form-input:focus { border-color: var(--cm-text-muted); }

.cm-btn-primary {
    display: inline-flex; align-items: center; padding: 8px 16px; background: var(--cm-text-primary);
    color: var(--cm-card-bg); border: none; border-radius: 8px; font-size: 0.8125rem; font-weight: 600;
    cursor: pointer; transition: opacity 0.15s; text-decoration: none;
}
.cm-btn-primary:hover { opacity: 0.85; }
.cm-btn-primary--blue { background: var(--cm-accent-blue); }
.cm-btn-ghost {
    padding: 8px 14px; background: transparent; border: none; border-radius: 8px; font-size: 0.8125rem;
    color: var(--cm-text-secondary); cursor: pointer; text-decoration: none;
}
.cm-btn-ghost:hover { background: var(--cm-bg); color: var(--cm-text-primary); }

@media (max-width: 768px) {
    div[style*="grid-template-columns: 1fr 1fr"] { grid-template-columns: 1fr !important; }
}
</style>
@endpush

@push('scripts')
<script>
document.getElementById('item-type').addEventListener('change', function() {
    const type = this.value;
    const catInput = document.getElementById('category-input');
    const breedContainer = document.getElementById('chick-breed-container');
    const baseUnit = document.getElementById('base-unit');
    
    if (type === 'Chick') {
        breedContainer.classList.remove('cm-hidden');
        baseUnit.value = 'nos';
    } else {
        breedContainer.classList.add('cm-hidden');
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
