@extends('layouts.app')
@section('title', 'User Management')

@section('content')
<div class="space-y-8">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">User Management</h1>
            <p class="text-sm text-slate-500 font-medium mt-1">Manage platform access, roles, and security permissions</p>
        </div>
        <x-button variant="secondary" size="md" onclick="toggleModal('create-role-modal')">
            <x-slot name="icon"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" /></x-slot>
            Create Custom Role
        </x-button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Users Table --}}
        <div class="lg:col-span-2">
            <x-card padding="false">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider">Platform Users</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="text-left border-b border-slate-100">
                                <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Identity</th>
                                <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Assigned Roles</th>
                                <th class="px-8 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] text-center">Security Access</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($users as $user)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-full bg-slate-100 flex items-center justify-center text-slate-400 font-bold">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-extrabold text-slate-900">{{ $user->name }}</p>
                                                <p class="text-[11px] text-slate-400 font-medium">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-wrap gap-2">
                                            @forelse($user->roles as $role)
                                                @php
                                                    $variant = $role->is_system ? 'success' : 'primary';
                                                @endphp
                                                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider
                                                             {{ $role->is_system ? 'bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100' : 'bg-primary-50 text-primary-600 ring-1 ring-primary-100' }}">
                                                    {{ $role->name }}
                                                    @if(!$role->is_system)
                                                        <form action="{{ route('admin.user-roles.destroy', $role->pivot->id ?? 0) }}" method="POST" class="inline"
                                                              onsubmit="return confirm('Revoke this access role?')">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="hover:text-rose-600 transition-colors">✕</button>
                                                        </form>
                                                    @endif
                                                </div>
                                            @empty
                                                <span class="text-[10px] font-bold text-slate-300 uppercase tracking-widest italic">No roles assigned</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <form action="{{ route('admin.users.assign-role') }}" method="POST" class="flex items-center gap-2">
                                            @csrf
                                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                                            <select name="role_id" required class="flex-1 bg-slate-50 border-slate-200 rounded-xl py-2 px-3 text-[11px] font-bold text-slate-700 outline-none focus:ring-4 focus:ring-primary-500/10 transition-all">
                                                <option value="">+ Grant Access</option>
                                                @foreach($roles as $r)
                                                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                                                @endforeach
                                            </select>
                                            <button type="submit" class="p-2 bg-slate-900 text-white rounded-xl hover:bg-primary-500 transition-all shadow-sm">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>

        {{-- Role Management --}}
        <div class="space-y-6">
            <x-card padding="false">
                <div class="px-8 py-6 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider text-center">Defined Roles</h2>
                </div>
                <div class="divide-y divide-slate-50">
                    @foreach($roles as $role)
                        <div class="px-8 py-5 flex items-center justify-between group hover:bg-slate-50/50 transition-colors">
                            <div class="min-w-0">
                                <p class="text-sm font-black text-slate-900 flex items-center gap-2">
                                    {{ $role->name }}
                                    @if($role->is_system)
                                        <span class="text-[8px] bg-slate-100 text-slate-400 px-1.5 py-0.5 rounded uppercase">sys</span>
                                    @endif
                                </p>
                                @if($role->description)
                                    <p class="text-[10px] text-slate-400 font-medium truncate mt-0.5">{{ $role->description }}</p>
                                @endif
                            </div>
                            @if(!$role->is_system)
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST"
                                      onsubmit="return confirm('Destroy role {{ $role->name }}? This will revoke access for all assigned users.')"
                                      class="opacity-0 group-hover:opacity-100 transition-opacity">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-slate-300 hover:text-rose-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>
    </div>
</div>

{{-- Create Role Modal --}}
<div id="create-role-modal" class="hidden fixed inset-0 z-[60] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xl animate-in fade-in duration-300">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-lg border border-white/20 overflow-hidden transform animate-in zoom-in-95 duration-300">
        <div class="flex items-center justify-between px-10 py-8 border-b border-slate-100 bg-slate-50/50">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Access Control</h2>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Define new security role</p>
            </div>
            <button onclick="toggleModal('create-role-modal')" class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center text-slate-400 hover:text-slate-900 transition-colors shadow-sm">✕</button>
        </div>
        <form action="{{ route('admin.roles.store') }}" method="POST" class="p-10 space-y-6">
            @csrf
            <x-input label="Role Name *" name="name" required placeholder="e.g. Finance Manager" />
            <x-input label="Brief Description" name="description" placeholder="Grant view/edit access to accounting modules" />
            
            <div class="pt-4 flex gap-4">
                <x-button variant="ghost" class="flex-1" type="button" onclick="toggleModal('create-role-modal')">Cancel</x-button>
                <x-button variant="primary" class="flex-1" type="submit">Create Access Role</x-button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function toggleModal(id) {
    const modal = document.getElementById(id);
    modal.classList.toggle('hidden');
}
</script>
@endpush
