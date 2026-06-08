@extends('layouts.app')
@section('title', 'Customer Master')

@section('content')

<div class="cm-page">

    {{-- Top Bar --}}
    <div class="cm-topbar">
        <div>
            <h1 class="cm-page-title">Customer master</h1>
            <p class="cm-page-sub">Directory of retail buyers and wholesale partners</p>
        </div>
        <button onclick="openCreateCustomer()" type="button"
            class="cm-btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Register customer
        </button>
    </div>

    {{-- Stats --}}
    <div class="cm-stats">
        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-teal">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
            </div>
            <div>
                <div class="cm-stat-label">Total active</div>
                <div class="cm-stat-value">{{ $customers->total() }}</div>
            </div>
        </div>
        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-blue">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
            </div>
            <div>
                <div class="cm-stat-label">Wholesale</div>
                <div class="cm-stat-value">{{ $customers->where('type', 'Wholesale')->count() }}</div>
            </div>
        </div>
        <div class="cm-stat-card">
            <div class="cm-stat-icon cm-icon-amber">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 0 1-8 0"/>
                </svg>
            </div>
            <div>
                <div class="cm-stat-label">Retail</div>
                <div class="cm-stat-value">{{ $customers->where('type', 'Retail')->count() }}</div>
            </div>
        </div>
        <div class="cm-stat-card cm-stat-card--danger">
            <div class="cm-stat-icon cm-icon-red">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                    <line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
            </div>
            <div>
                <div class="cm-stat-label">With balance</div>
                <div class="cm-stat-value">{{ $customers->where('balance', '>', 0)->count() }}</div>
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
                            placeholder="Search by name, phone or route…" class="cm-search-input">
                    </div>
                    <button type="submit" class="cm-btn-primary" style="padding: 0.4rem 1rem; height: 38px;">Search</button>
                    <button type="button" class="cm-btn-secondary" onclick="document.getElementById('filter-panel').classList.toggle('cm-hidden')" style="padding: 0.4rem 1rem; height: 38px; display: inline-flex; align-items: center; gap: 6px; background: transparent; border: 1px solid var(--cm-card-border); border-radius: 8px; color: var(--cm-text-secondary); cursor: pointer; font-size: 0.875rem;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"></polygon></svg>
                        Filters
                        @if(request('type') || request('balance'))
                            <span style="width: 8px; height: 8px; background: #10b981; border-radius: 50%; margin-left: 2px;"></span>
                        @endif
                    </button>
                    @if(request('search') || request('type') || request('balance'))
                        <a href="{{ route('masters.customers.index') }}" class="cm-btn-secondary" style="padding: 0.4rem 1rem; height: 38px; display: inline-flex; align-items: center; justify-content: center; background: transparent; border: 1px solid var(--cm-card-border); border-radius: 8px; color: var(--cm-text-secondary); text-decoration: none;">Clear</a>
                    @endif
                </div>

                <div id="filter-panel" class="{{ request('type') || request('balance') ? '' : 'cm-hidden' }}" style="width: 100%; padding: 1.25rem; background: var(--cm-bg); border-radius: 12px; border: 1px solid var(--cm-card-border); margin-top: 0.25rem; display: flex; gap: 1.5rem; align-items: flex-end; flex-wrap: wrap;">
                    <div style="display: flex; flex-direction: column; gap: 6px; min-width: 200px;">
                        <label style="font-size: 0.75rem; font-weight: 700; color: var(--cm-text-muted); text-transform: uppercase;">Customer Type</label>
                        <select name="type" class="cm-search-input" onchange="document.getElementById('search-form').submit()">
                            <option value="">All Types</option>
                            <option value="Retail" {{ request('type') == 'Retail' ? 'selected' : '' }}>Retail</option>
                            <option value="Wholesale" {{ request('type') == 'Wholesale' ? 'selected' : '' }}>Wholesale</option>
                        </select>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 6px; min-width: 200px;">
                        <label style="font-size: 0.75rem; font-weight: 700; color: var(--cm-text-muted); text-transform: uppercase;">Outstanding Balance</label>
                        <select name="balance" class="cm-search-input" onchange="document.getElementById('search-form').submit()">
                            <option value="">All Customers</option>
                            <option value="pending" {{ request('balance') == 'pending' ? 'selected' : '' }}>Has Pending Balance</option>
                            <option value="cleared" {{ request('balance') == 'cleared' ? 'selected' : '' }}>Cleared (No Balance)</option>
                        </select>
                    </div>
                </div>
            </form>
            <div class="cm-toolbar-right">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'pdf']) }}" class="cm-export-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                        stroke-linejoin="round">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
                    </svg>
                    Export PDF
                </a>
            </div>
        </div>

        <div id="table-content">
            <div class="cm-table-wrap">
                <table class="cm-table">
                    <thead>
                        <tr>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Route</th>
                            <th>Type</th>
                            <th class="cm-th-right">Outstanding</th>
                            <th class="cm-th-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                        <tr class="cm-tr">
                            <td class="cm-td">
                                <div class="cm-identity">
                                    <div class="cm-avatar cm-avatar--{{ strtolower(substr($customer->name, 0, 1)) }}">
                                        {{ strtoupper(substr($customer->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <a href="{{ route('masters.customers.show', $customer) }}"
                                            class="cm-cust-name">{{ $customer->name }}</a>
                                        <div class="cm-cust-meta">{{ $customer->gst_number ?: 'No GST' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="cm-td">
                                <div class="cm-cust-name">{{ $customer->phone }}</div>
                                <div class="cm-cust-meta cm-truncate">{{ $customer->address ?: 'No address' }}</div>
                            </td>
                            <td class="cm-td">
                                <span class="cm-route">{{ $customer->route ?: 'General' }}</span>
                            </td>
                            <td class="cm-td">
                                @if($customer->type === 'Wholesale')
                                    <span class="cm-badge cm-badge--wholesale">Wholesale</span>
                                @else
                                    <span class="cm-badge cm-badge--retail">Retail</span>
                                @endif
                            </td>
                            <td class="cm-td cm-td-right">
                                @if($customer->balance > 0)
                                    <span class="cm-balance cm-balance--due">Rs {{ number_format($customer->balance, 0) }}</span>
                                @else
                                    <span class="cm-balance cm-balance--clear">Rs 0</span>
                                @endif
                            </td>
                            <td class="cm-td">
                                <div class="cm-actions">
                                    <a href="{{ route('masters.customers.ledger-pdf', $customer) }}"
                                        class="cm-action-btn" title="Download ledger PDF">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                                            <polyline points="14 2 14 8 20 8"/>
                                            <line x1="16" y1="13" x2="8" y2="13"/>
                                            <line x1="16" y1="17" x2="8" y2="17"/>
                                            <polyline points="10 9 9 9 8 9"/>
                                        </svg>
                                    </a>
                                    <button onclick="openEditCustomer('{{ $customer->id }}', '{{ addslashes($customer->name) }}', '{{ addslashes($customer->phone) }}', '{{ addslashes($customer->address) }}', '{{ $customer->gst_number }}', '{{ addslashes($customer->route) }}', '{{ $customer->type }}')" type="button"
                                        class="cm-action-btn cm-action-btn--edit" title="Edit Customer">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                        </svg>
                                    </button>
                                    <form action="{{ route('masters.customers.destroy', $customer) }}" method="POST"
                                        onsubmit="return confirm('Delete {{ $customer->name }}?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="cm-action-btn cm-action-btn--danger" title="Delete customer">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                                stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                <path d="M10 11v6"/><path d="M14 11v6"/>
                                                <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="cm-empty">
                                <div class="cm-empty-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                                    </svg>
                                </div>
                                <p class="cm-empty-title">No customers found</p>
                                <p class="cm-empty-sub">Start by registering your first buyer.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($customers->hasPages())
            <div class="cm-pagination">
                <span class="cm-pg-info">
                    Showing {{ $customers->firstItem() }}–{{ $customers->lastItem() }} of {{ $customers->total() }} customers
                </span>
                <div class="cm-pg-links">
                    {{ $customers->withQueryString()->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>

</div>

{{-- ================================================ --}}
{{-- ADD CUSTOMER SWEETALERT MODAL                    --}}
{{-- ================================================ --}}
@push('modals')
<div id="create-modal" style="display: none;" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0 duration-300" id="create-modal-backdrop" onclick="closeCreateCustomer()"></div>
    
    <div class="fixed inset-0 z-10 overflow-y-auto pointer-events-none">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="create-modal-panel" class="pointer-events-auto w-full max-w-xl transform transition-all scale-95 opacity-0 duration-300 ease-out">
                
                <div class="swal-form-card mx-auto text-left">
                    <button onclick="closeCreateCustomer()" type="button" class="absolute top-4 right-4 rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-900 transition-colors z-10">
                        <span class="material-symbols-rounded text-xl">close</span>
                    </button>
                    
                    <div class="swal-form-header">
                        <div class="swal-icon-wrapper">
                            <span class="material-symbols-rounded">person_add</span>
                        </div>
                        <h2>Register New Customer</h2>
                        <p>Enter the details below to add a new customer.</p>
                    </div>

                    <form action="{{ route('masters.customers.store') }}" method="POST" class="swal-form">
                        @csrf
                        
                        <div class="swal-input-group">
                            <label>Full Name <span class="required">*</span></label>
                            <div class="swal-input-wrapper">
                                <span class="material-symbols-rounded swal-input-icon">person</span>
                                <input type="text" name="name" required placeholder="e.g. John Doe">
                            </div>
                        </div>

                        <div class="swal-input-group">
                            <label>Phone Number <span class="required">*</span></label>
                            <div class="swal-input-wrapper">
                                <span class="material-symbols-rounded swal-input-icon">call</span>
                                <input type="text" name="phone" required placeholder="e.g. +91 00000 00000">
                            </div>
                        </div>

                        <div class="swal-input-group swal-col-span-2">
                            <label>Store Address</label>
                            <div class="swal-input-wrapper">
                                <span class="material-symbols-rounded swal-input-icon">location_on</span>
                                <textarea name="address" rows="2" placeholder="Street, Area, City..."></textarea>
                            </div>
                        </div>

                        <div class="swal-input-group">
                            <label>GST Number</label>
                            <div class="swal-input-wrapper">
                                <span class="material-symbols-rounded swal-input-icon">badge</span>
                                <input type="text" name="gst_number" placeholder="Optional GSTIN">
                            </div>
                        </div>

                        <div class="swal-input-group">
                            <label>Route / Area</label>
                            <div class="swal-input-wrapper">
                                <span class="material-symbols-rounded swal-input-icon">alt_route</span>
                                <input type="text" name="route" placeholder="e.g. North Sector">
                            </div>
                        </div>

                        <div class="swal-input-group">
                            <label>Customer Type</label>
                            <div class="swal-input-wrapper">
                                <span class="material-symbols-rounded swal-input-icon">store</span>
                                <select name="type">
                                    <option value="Retail">Retail partner</option>
                                    <option value="Wholesale">Wholesale distributor</option>
                                </select>
                            </div>
                        </div>

                        <div class="swal-form-actions">
                            <button type="button" onclick="closeCreateCustomer()" class="swal-btn-cancel">Cancel</button>
                            <button type="submit" class="swal-btn-confirm">Register Customer</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

{{-- ================================================ --}}
{{-- EDIT CUSTOMER SWEETALERT MODAL                   --}}
{{-- ================================================ --}}
<div id="edit-modal" style="display: none;" class="relative z-[100]" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity opacity-0 duration-300" id="edit-modal-backdrop" onclick="closeEditCustomer()"></div>
    
    <div class="fixed inset-0 z-10 overflow-y-auto pointer-events-none">
        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div id="edit-modal-panel" class="pointer-events-auto w-full max-w-xl transform transition-all scale-95 opacity-0 duration-300 ease-out">
                
                <div class="swal-form-card mx-auto text-left">
                    <button onclick="closeEditCustomer()" type="button" class="absolute top-4 right-4 rounded-xl p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-900 transition-colors z-10">
                        <span class="material-symbols-rounded text-xl">close</span>
                    </button>
                    
                    <div class="swal-form-header">
                        <div class="swal-icon-wrapper" style="color: #3b82f6; background: rgba(59, 130, 246, 0.1); border-color: rgba(59, 130, 246, 0.2); box-shadow: 0 0 20px rgba(59, 130, 246, 0.2);">
                            <span class="material-symbols-rounded">edit_document</span>
                        </div>
                        <h2>Edit Customer</h2>
                        <p>Update customer details below.</p>
                    </div>

                    <form id="edit-form" method="POST" class="swal-form">
                        @csrf @method('PUT')
                        
                        <div class="swal-input-group">
                            <label>Full Name <span class="required">*</span></label>
                            <div class="swal-input-wrapper">
                                <span class="material-symbols-rounded swal-input-icon">person</span>
                                <input type="text" id="edit-name" name="name" required>
                            </div>
                        </div>

                        <div class="swal-input-group">
                            <label>Phone Number <span class="required">*</span></label>
                            <div class="swal-input-wrapper">
                                <span class="material-symbols-rounded swal-input-icon">call</span>
                                <input type="text" id="edit-phone" name="phone" required>
                            </div>
                        </div>

                        <div class="swal-input-group swal-col-span-2">
                            <label>Store Address</label>
                            <div class="swal-input-wrapper">
                                <span class="material-symbols-rounded swal-input-icon">location_on</span>
                                <textarea id="edit-address" name="address" rows="2"></textarea>
                            </div>
                        </div>

                        <div class="swal-input-group">
                            <label>GST Number</label>
                            <div class="swal-input-wrapper">
                                <span class="material-symbols-rounded swal-input-icon">badge</span>
                                <input type="text" id="edit-gst" name="gst_number">
                            </div>
                        </div>

                        <div class="swal-input-group">
                            <label>Route / Area</label>
                            <div class="swal-input-wrapper">
                                <span class="material-symbols-rounded swal-input-icon">alt_route</span>
                                <input type="text" id="edit-route" name="route">
                            </div>
                        </div>

                        <div class="swal-input-group">
                            <label>Customer Type</label>
                            <div class="swal-input-wrapper">
                                <span class="material-symbols-rounded swal-input-icon">store</span>
                                <select id="edit-type" name="type">
                                    <option value="Retail">Retail partner</option>
                                    <option value="Wholesale">Wholesale distributor</option>
                                </select>
                            </div>
                        </div>

                        <div class="swal-form-actions">
                            <button type="button" onclick="closeEditCustomer()" class="swal-btn-cancel">Cancel</button>
                            <button type="submit" class="swal-btn-confirm" style="background: #3b82f6; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.25);">Save Changes</button>
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
    function openCreateCustomer() {
        const modal = document.getElementById('create-modal');
        const backdrop = document.getElementById('create-modal-backdrop');
        const panel = document.getElementById('create-modal-panel');
        
        modal.style.display = 'block';
        // Small delay to allow display block to apply before animating opacity
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeCreateCustomer() {
        const modal = document.getElementById('create-modal');
        const backdrop = document.getElementById('create-modal-backdrop');
        const panel = document.getElementById('create-modal-panel');
        
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300); 
    }

    function openEditCustomer(id, name, phone, address, gst, route, type) {
        document.getElementById('edit-form').action = `/masters/customers/${id}`;
        
        document.getElementById('edit-name').value = name;
        document.getElementById('edit-phone').value = phone;
        document.getElementById('edit-address').value = address;
        document.getElementById('edit-gst').value = gst || '';
        document.getElementById('edit-route').value = route || '';
        document.getElementById('edit-type').value = type;

        const modal = document.getElementById('edit-modal');
        const backdrop = document.getElementById('edit-modal-backdrop');
        const panel = document.getElementById('edit-modal-panel');
        
        modal.style.display = 'block';
        setTimeout(() => {
            backdrop.classList.remove('opacity-0');
            backdrop.classList.add('opacity-100');
            
            panel.classList.remove('opacity-0', 'scale-95');
            panel.classList.add('opacity-100', 'scale-100');
        }, 10);
    }

    function closeEditCustomer() {
        const modal = document.getElementById('edit-modal');
        const backdrop = document.getElementById('edit-modal-backdrop');
        const panel = document.getElementById('edit-modal-panel');
        
        backdrop.classList.remove('opacity-100');
        backdrop.classList.add('opacity-0');
        
        panel.classList.remove('opacity-100', 'scale-100');
        panel.classList.add('opacity-0', 'scale-95');
        
        setTimeout(() => {
            modal.style.display = 'none';
        }, 300);
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
                    
                    fetch(url)
                        .then(response => response.text())
                        .then(html => {
                            // Parse the HTML
                            const parser = new DOMParser();
                            const doc = parser.parseFromString(html, 'text/html');
                            
                            // Get the new table content
                            const newTableContent = doc.getElementById('table-content');
                            
                            if (newTableContent) {
                                tableContent.innerHTML = newTableContent.innerHTML;
                                
                                // Update URL without reloading
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
