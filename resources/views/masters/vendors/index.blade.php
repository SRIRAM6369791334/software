@extends('layouts.app')
@section('title', 'Vendor Master')

@section('content')

<div class="cm-page">

    {{-- Top Bar --}}
    <div class="cm-topbar">
        <div>
            <h1 class="cm-page-title">Vendor Master</h1>
            <p class="cm-page-sub">Directory of logistics and pharmaceutical suppliers</p>
        </div>
        <button onclick="openCreateVendor()" class="cm-btn-primary" type="button">
            <span class="material-symbols-rounded text-[18px]">add_circle</span>
            Register Vendor
        </button>
    </div>

    {{-- Stats --}}
    <div class="cm-stats">
        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-teal">
                <span class="material-symbols-rounded">local_shipping</span>
            </div>
            <div>
                <div class="cm-stat-label">Total Suppliers</div>
                <div class="cm-stat-value">{{ $vendors->total() }}</div>
            </div>
        </div>
        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-blue">
                <span class="material-symbols-rounded">route</span>
            </div>
            <div>
                <div class="cm-stat-label">Route Reach</div>
                <div class="cm-stat-value">{{ $vendors->pluck('route')->filter()->unique()->count() }} Routes</div>
            </div>
        </div>
    </div>

    {{-- Table Card --}}
    <div class="cm-table-card">
        <div class="cm-table-toolbar">
            <form id="search-form" method="GET" style="display: flex; gap: 0.75rem; align-items: flex-start; margin: 0; flex-wrap: wrap;">
                <div style="display: flex; gap: 0.75rem; align-items: center; width: 100%; flex-wrap: wrap;">
                    <div class="cm-search-wrap" style="margin: 0; flex: 1; max-width: 320px;">
                        <svg class="cm-search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input type="text" id="search-input" name="search" value="{{ $search }}"
                            placeholder="Search by firm name, contact or phone…" class="cm-search-input">
                    </div>
                    <button type="submit" class="cm-btn-primary" style="padding: 0.4rem 1rem; height: 38px;">Search</button>
                    <button type="button" class="cm-btn-secondary" onclick="document.getElementById('filter-panel').classList.toggle('cm-hidden')" style="padding: 0.4rem 1rem; height: 38px; display: inline-flex; align-items: center; gap: 6px; background: transparent; border: 1px solid var(--cm-card-border); border-radius: 8px; color: var(--cm-text-secondary); cursor: pointer; font-size: 0.875rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                        Filters
                        @if(request('route'))
                            <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; margin-left: 2px;"></span>
                        @endif
                    </button>
                    @if(request('search') || request('route'))
                        <a href="{{ route('masters.vendors.index') }}" class="cm-btn-secondary" style="padding: 0.4rem 1rem; height: 38px; display: inline-flex; align-items: center; justify-content: center; background: transparent; border: 1px solid var(--cm-card-border); border-radius: 8px; color: var(--cm-text-secondary); text-decoration: none;">Clear</a>
                    @endif
                </div>

                <div id="filter-panel" class="{{ request('route') ? '' : 'cm-hidden' }}" style="width: 100%; padding: 1.25rem; background: var(--cm-bg); border-radius: 12px; border: 1px solid var(--cm-card-border); margin-top: 0.25rem; display: flex; gap: 1.5rem; align-items: flex-end; flex-wrap: wrap;">
                    <div style="display: flex; flex-direction: column; gap: 6px; min-width: 200px;">
                        <label style="font-size: 0.75rem; font-weight: 700; color: var(--cm-text-muted); text-transform: uppercase;">Route / Area</label>
                        <select name="route" class="cm-search-input" onchange="document.getElementById('search-form').submit()">
                            <option value="">All Routes</option>
                            @foreach($routes as $rt)
                                <option value="{{ $rt }}" {{ request('route') == $rt ? 'selected' : '' }}>{{ $rt }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </form>
        </div>

        <div id="table-content">
            <div class="cm-table-wrap">
                <table class="cm-table">
                    <thead>
                        <tr>
                            <th>Firm & Location</th>
                            <th>Point of Contact</th>
                            <th>Route</th>
                            <th>GSTIN</th>
                            <th class="cm-th-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                        <tr class="cm-tr">
                            <td class="cm-td">
                                <div class="cm-identity">
                                    <div class="cm-avatar cm-avatar--{{ strtolower(substr($vendor->firm_name, 0, 1)) }}">
                                        {{ strtoupper(substr($vendor->firm_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('masters.vendors.show', $vendor) }}"
                                            class="cm-cust-name">{{ $vendor->firm_name }}</a>
                                        <div class="cm-cust-meta">{{ $vendor->location ?: 'No Location Specified' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="cm-td">
                                <div class="cm-cust-name">{{ $vendor->contact_person ?: 'No contact person' }}</div>
                                <div class="cm-cust-meta">{{ $vendor->phone }}</div>
                            </td>
                            <td class="cm-td">
                                <span class="cm-route">{{ $vendor->route ?: 'General Sector' }}</span>
                            </td>
                            <td class="cm-td">
                                <span class="cm-gst-mono">{{ $vendor->gst_number ?: 'UNREGISTERED' }}</span>
                            </td>
                            <td class="cm-td">
                                <div class="cm-actions">
                                    <button type="button" 
                                        onclick="openEditVendor('{{ $vendor->id }}', '{{ addslashes($vendor->firm_name) }}', '{{ addslashes($vendor->contact_person) }}', '{{ addslashes($vendor->phone) }}', '{{ $vendor->gst_number }}', '{{ addslashes($vendor->location) }}', '{{ addslashes($vendor->route) }}', '{{ addslashes($vendor->notes) }}')"
                                        class="cm-action-btn cm-action-btn--edit" title="Edit vendor">
                                        <span class="material-symbols-rounded text-[18px]">edit</span>
                                    </button>
                                    <form action="{{ route('masters.vendors.destroy', $vendor) }}" method="POST"
                                        onsubmit="return confirm('Delete {{ $vendor->firm_name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="cm-action-btn cm-action-btn--danger" title="Delete vendor">
                                            <span class="material-symbols-rounded text-[18px]">delete</span>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="cm-empty">
                                <div class="cm-empty-icon">
                                    <span class="material-symbols-rounded text-3xl">inventory_2</span>
                                </div>
                                <p class="cm-empty-title">No vendors found</p>
                                <p class="cm-empty-sub">Start by registering your first supply partner.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
    
            @if($vendors->hasPages())
            <div class="cm-pagination">
                <span class="cm-pg-info">
                    Showing {{ $vendors->firstItem() }}–{{ $vendors->lastItem() }} of {{ $vendors->total() }} vendors
                </span>
                <div class="cm-pg-links">
                    {{ $vendors->withQueryString()->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- ================================================ --}}
{{-- ADD VENDOR SLIDE-OVER                            --}}
{{-- ================================================ --}}
@push('modals')
<div id="create-modal" style="display: none;" class="relative z-[100]" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity opacity-0" id="create-modal-backdrop" onclick="closeCreateVendor()"></div>
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div id="create-modal-panel" class="pointer-events-auto w-screen max-w-md bg-white shadow-2xl flex flex-col h-full border-l border-slate-200 translate-x-full transition-transform duration-500 ease-in-out">
                     
                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                                <span class="material-symbols-rounded text-[20px]">local_shipping</span>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900 leading-tight">Register Vendor</h2>
                                <p class="text-[11px] font-medium text-slate-500">Onboard a new supply partner</p>
                            </div>
                        </div>
                        <button onclick="closeCreateVendor()" type="button" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                            <span class="material-symbols-rounded text-xl">close</span>
                        </button>
                    </div>

                    <form action="{{ route('masters.vendors.store') }}" method="POST" class="flex-1 overflow-y-auto px-6 py-6 custom-scrollbar">
                        @csrf
                        <div class="space-y-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="cm-form-label">Firm Name <span class="cm-required">*</span></label>
                                    <input type="text" name="firm_name" required placeholder="e.g. Apex Feed Suppliers" class="cm-form-input">
                                </div>
                                <div>
                                    <label class="cm-form-label">Contact Person</label>
                                    <input type="text" name="contact_person" placeholder="Manager Name" class="cm-form-input">
                                </div>
                                <div>
                                    <label class="cm-form-label">Phone <span class="cm-required">*</span></label>
                                    <input type="text" name="phone" required placeholder="+91 00000 00000" class="cm-form-input">
                                </div>
                                <div>
                                    <label class="cm-form-label">GSTIN</label>
                                    <input type="text" name="gst_number" placeholder="Optional GSTIN" class="cm-form-input uppercase">
                                </div>
                                <div>
                                    <label class="cm-form-label">Location / City</label>
                                    <input type="text" name="location" placeholder="e.g. Salem, TN" class="cm-form-input">
                                </div>
                                <div class="col-span-2">
                                    <label class="cm-form-label">Route</label>
                                    <input type="text" name="route" placeholder="e.g. Main Highway Route" class="cm-form-input">
                                </div>
                                <div class="col-span-2">
                                    <label class="cm-form-label">Strategic Notes</label>
                                    <textarea name="notes" rows="2" placeholder="Vendor specifications, items supplied..." class="cm-form-input cm-form-textarea"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-8 flex items-center justify-end gap-3 pt-5 border-t border-slate-100">
                            <button type="button" onclick="closeCreateVendor()" class="cm-btn-ghost">Cancel</button>
                            <button type="submit" class="cm-btn-primary">
                                <span class="material-symbols-rounded text-[18px]">check_circle</span>
                                Activate Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================================================ --}}
{{-- EDIT VENDOR SLIDE-OVER                           --}}
{{-- ================================================ --}}
<div id="edit-modal" style="display: none;" class="relative z-[100]" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm transition-opacity opacity-0" id="edit-modal-backdrop" onclick="closeEditVendor()"></div>
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div id="edit-modal-panel" class="pointer-events-auto w-screen max-w-md bg-white shadow-2xl flex flex-col h-full border-l border-slate-200 translate-x-full transition-transform duration-500 ease-in-out">
                     
                    <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-100 text-indigo-600">
                                <span class="material-symbols-rounded text-[20px]">edit_document</span>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900 leading-tight">Edit Vendor</h2>
                                <p class="text-[11px] font-medium text-slate-500">Update supply partner credentials</p>
                            </div>
                        </div>
                        <button onclick="closeEditVendor()" type="button" class="rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                            <span class="material-symbols-rounded text-xl">close</span>
                        </button>
                    </div>

                    <form id="edit-form" method="POST" class="flex-1 overflow-y-auto px-6 py-6 custom-scrollbar">
                        @csrf @method('PUT')
                        <div class="space-y-5">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="cm-form-label">Firm Name <span class="cm-required">*</span></label>
                                    <input type="text" id="edit-firm-name" name="firm_name" required class="cm-form-input">
                                </div>
                                <div>
                                    <label class="cm-form-label">Contact Person</label>
                                    <input type="text" id="edit-contact" name="contact_person" class="cm-form-input">
                                </div>
                                <div>
                                    <label class="cm-form-label">Phone <span class="cm-required">*</span></label>
                                    <input type="text" id="edit-phone" name="phone" required class="cm-form-input">
                                </div>
                                <div>
                                    <label class="cm-form-label">GSTIN</label>
                                    <input type="text" id="edit-gst" name="gst_number" class="cm-form-input uppercase">
                                </div>
                                <div>
                                    <label class="cm-form-label">Location / City</label>
                                    <input type="text" id="edit-location" name="location" class="cm-form-input">
                                </div>
                                <div class="col-span-2">
                                    <label class="cm-form-label">Route</label>
                                    <input type="text" id="edit-route" name="route" class="cm-form-input">
                                </div>
                                <div class="col-span-2">
                                    <label class="cm-form-label">Strategic Notes</label>
                                    <textarea id="edit-notes" name="notes" rows="2" class="cm-form-input cm-form-textarea"></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-8 flex items-center justify-end gap-3 pt-5 border-t border-slate-100">
                            <button type="button" onclick="closeEditVendor()" class="cm-btn-ghost">Cancel</button>
                            <button type="submit" class="cm-btn-primary cm-btn-primary--blue">
                                <span class="material-symbols-rounded text-[18px]">save</span>
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@endsection

@push('styles')
@include('partials.cm-style')
@endpush

@push('scripts')
<script>
    function openCreateVendor() {
        const modal = document.getElementById('create-modal');
        const backdrop = document.getElementById('create-modal-backdrop');
        const panel = document.getElementById('create-modal-panel');
        
        modal.style.display = 'block';
        // Wait a frame to allow display:block to apply before animating opacity/transform
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('translate-x-full');
            panel.classList.add('translate-x-0');
        }, 10);
    }

    function closeCreateVendor() {
        const modal = document.getElementById('create-modal');
        const backdrop = document.getElementById('create-modal-backdrop');
        const panel = document.getElementById('create-modal-panel');
        
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('translate-x-0');
        panel.classList.add('translate-x-full');
        
        setTimeout(() => {
            modal.style.display = 'none';
        }, 500); // Matches transition duration
    }

    function openEditVendor(id, firmName, contact, phone, gst, location, route, notes) {
        // Set form action dynamically
        document.getElementById('edit-form').action = `/masters/vendors/${id}`;
        
        // Populate inputs
        document.getElementById('edit-firm-name').value = firmName;
        document.getElementById('edit-contact').value = contact;
        document.getElementById('edit-phone').value = phone;
        document.getElementById('edit-gst').value = gst;
        document.getElementById('edit-location').value = location;
        document.getElementById('edit-route').value = route;
        document.getElementById('edit-notes').value = notes;

        const modal = document.getElementById('edit-modal');
        const backdrop = document.getElementById('edit-modal-backdrop');
        const panel = document.getElementById('edit-modal-panel');
        
        modal.style.display = 'block';
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            panel.classList.remove('translate-x-full');
            panel.classList.add('translate-x-0');
        }, 10);
    }

    function closeEditVendor() {
        const modal = document.getElementById('edit-modal');
        const backdrop = document.getElementById('edit-modal-backdrop');
        const panel = document.getElementById('edit-modal-panel');
        
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        panel.classList.remove('translate-x-0');
        panel.classList.add('translate-x-full');
        
        setTimeout(() => {
            modal.style.display = 'none';
        }, 500);
    }

    // Live search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        const tableContent = document.getElementById('table-content');
        
        if(searchInput && tableContent) {
            let timeout = null;
            
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                
                timeout = setTimeout(() => {
                    const query = searchInput.value;
                    const url = new URL(window.location.href);
                    if (query) {
                        url.searchParams.set('search', query);
                    } else {
                        url.searchParams.delete('search');
                    }
                    
                    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
                        .then(response => response.text())
                        .then(html => {
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            const newTableContent = doc.getElementById('table-content');
                            
                            if (newTableContent) {
                                tableContent.innerHTML = newTableContent.innerHTML;
                                window.history.pushState({}, '', url);
                            }
                        })
                        .catch(err => {
                            console.error('Search failed:', err);
                        });
                }, 150); // 150ms debounce for faster feel
            });
        }
    });
</script>
@endpush
