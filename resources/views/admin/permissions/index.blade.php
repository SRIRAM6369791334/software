@extends('layouts.app')
@section('title', 'Manage Permissions')

@section('content')
<div class="space-y-6">
    <x-page-header 
        title="Permission Management" 
        subtitle="Manage granular access permissions for the application"
    >
        <x-slot:actions>
            @can('manage permissions')
                <x-button href="{{ route('admin.permissions.create') }}" variant="primary" icon="add">
                    Add Permission
                </x-button>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <x-stat-card title="Total Permissions" value="{{ $permissions->count() }}" icon="key" color="emerald" />
        <x-stat-card title="Permission Groups" value="{{ count($groups) }}" icon="category" color="blue" />
        <x-stat-card title="Web Guards" value="{{ $permissions->where('guard_name', 'web')->count() }}" icon="security" color="amber" />
    </div>

    <x-card padding="p-0">
        <x-data-table :headers="['ID', 'Permission Name', 'Group', 'Guard', 'Actions']">
            @forelse($permissions as $permission)
                <tr class="hover:bg-white/80 dark:hover:bg-zinc-800/50 transition-all duration-300 group">
                    <td class="px-6 py-4">
                        <span class="text-sm font-medium text-zinc-900 dark:text-zinc-100">{{ $permission->id }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400 border border-emerald-100/50">
                                <span class="material-symbols-rounded text-sm">vpn_key</span>
                            </div>
                            <span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $permission->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <x-badge variant="info">{{ $groups[$permission->permission_group_id] ?? 'None' }}</x-badge>
                    </td>
                    <td class="px-6 py-4">
                        <x-badge variant="secondary">{{ $permission->guard_name }}</x-badge>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            @can('manage permissions')
                                <x-button href="{{ route('admin.permissions.edit', $permission->id) }}" variant="ghost" size="sm" icon="edit" title="Edit" />
                                <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete permission {{ $permission->name }}?');">
                                    @csrf @method('DELETE')
                                    <x-button type="submit" variant="ghost" size="sm" icon="delete" class="text-rose-500 hover:text-rose-600" title="Delete" />
                                </form>
                            @endcan
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <x-empty-state 
                            icon="key_off" 
                            title="No permissions found" 
                            subtitle="Create granular permissions to control access." 
                        />
                    </td>
                </tr>
            @endforelse
        </x-data-table>
    </x-card>
</div>
@endsection
