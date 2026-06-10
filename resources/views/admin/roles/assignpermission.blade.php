@extends('layouts.app')
@section('title', 'Assign Permissions')

@section('content')
<div class="space-y-6">
    <x-page-header 
        title="Assign Permissions" 
        subtitle="Configure access rights for the {{ $role->name }} role"
        backRoute="admin.roles.index"
    />

    <form action="{{ route('admin.roles.assignPermission') }}" method="POST">
        @csrf
        <input type="hidden" name="role_id" value="{{ $role->id }}">
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($permissionGroups as $group)
            <x-card padding="p-0" class="overflow-hidden h-full">
                <div class="bg-zinc-50 dark:bg-zinc-900/50 px-6 py-4 border-b border-zinc-100 dark:border-zinc-800">
                    <h3 class="font-semibold text-zinc-900 dark:text-zinc-100 flex items-center gap-2">
                        <span class="material-symbols-rounded text-emerald-500 text-[18px]">folder_open</span>
                        {{ $group->name }}
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    @foreach($group->permissions as $permission)
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                            class="w-5 h-5 rounded border-zinc-300 text-emerald-600 focus:ring-emerald-500 dark:border-zinc-700 dark:bg-zinc-900 dark:checked:bg-emerald-500 transition-all cursor-pointer"
                            {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-zinc-600 dark:text-zinc-400 group-hover:text-zinc-900 dark:group-hover:text-zinc-100 transition-colors">
                            {{ $permission->name }}
                        </span>
                    </label>
                    @endforeach
                </div>
            </x-card>
            @endforeach
        </div>

        <div class="mt-8 flex justify-end gap-3">
            <x-button href="{{ route('admin.roles.index') }}" variant="secondary">Cancel</x-button>
            <x-button type="submit" variant="primary" icon="save">Save Permissions</x-button>
        </div>
    </form>
</div>
@endsection
