@extends('layouts.app')
@section('title', 'Manage Roles')

@section('content')
<div class="space-y-6">
    <x-page-header 
        title="Role Management" 
        subtitle="Manage system roles and their associated permissions"
    >
        <x-slot:actions>
            @can('manage roles')
                <x-button href="{{ route('admin.roles.create') }}" variant="primary" icon="add">
                    Add Role
                </x-button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-stat-card title="Total Roles" value="{{ $roles->count() }}" icon="admin_panel_settings" color="emerald" />
        <x-stat-card title="System Roles" value="{{ $roles->whereIn('name', ['admin', 'Super Admin'])->count() }}" icon="shield" color="blue" />
        <x-stat-card title="Custom Roles" value="{{ $roles->whereNotIn('name', ['admin', 'Super Admin'])->count() }}" icon="group_work" color="amber" />
    </div>

    <x-card padding="p-0">
        <x-data-table :headers="['ID', 'Role Name', 'Guard', 'Actions']">
            @forelse($roles as $role)
                <tr class="hover:bg-white/80 dark:hover:bg-zinc-800/50 transition-all duration-300 group">
                    <td class="px-6 py-4">
                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $role->id }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-500/10 dark:to-teal-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 shadow-sm border border-emerald-100/50">
                                <span class="material-symbols-rounded text-lg">badge</span>
                            </div>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $role->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <x-badge variant="secondary">{{ $role->guard_name }}</x-badge>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            @can('manage roles')
                                <x-button href="{{ route('admin.roles.assignPermissionPage', $role->id) }}" variant="secondary" size="sm" icon="vpn_key" title="Permissions" />
                                <x-button href="{{ route('admin.roles.edit', $role->id) }}" variant="ghost" size="sm" icon="edit" title="Edit" />
                                @if($role->name !== 'admin' && $role->name !== 'Super Admin')
                                    <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete {{ $role->name }} role?');">
                                        @csrf @method('DELETE')
                                        <x-button type="submit" variant="ghost" size="sm" icon="delete" class="text-rose-500 hover:text-rose-600" title="Delete" />
                                    </form>
                                @endif
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center">
                        <x-empty-state 
                            icon="admin_panel_settings" 
                            title="No roles found" 
                            subtitle="Create roles to assign permissions to users." 
                        />
                    </td>
                </tr>
            @endforelse
        </x-data-table>
    </x-card>
</div>
@endsection
