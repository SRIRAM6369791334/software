@extends('layouts.app')
@section('title', 'User Management')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">User Management</h1>
        <p class="text-sm text-gray-500 mt-0.5">Manage users, roles, and permissions</p>
    </div>
    <button onclick="document.getElementById('create-role-modal').classList.remove('hidden')"
            class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-sm font-medium rounded-lg">+ Create Role</button>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Users Table --}}
    <div class="lg:col-span-2 bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">Users & Roles</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 bg-gray-50">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">User</th>
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-400 uppercase">Roles</th>
                    <th class="px-5 py-3.5 text-center text-xs font-semibold text-gray-400 uppercase">Assign Role</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-5 py-3.5">
                            <div class="font-medium text-gray-900">{{ $user->name }}</div>
                            <div class="text-xs text-gray-400">{{ $user->email }}</div>
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex flex-wrap gap-1">
                                @forelse($user->roles as $role)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium
                                                 {{ $role->is_system ? 'bg-emerald-100 text-emerald-700' : 'bg-purple-100 text-purple-700' }}">
                                        {{ $role->name }}
                                        @if(!$role->is_system)
                                            <form action="{{ route('admin.user-roles.destroy', $role->pivot->id ?? 0) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Remove role?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="text-purple-400 hover:text-red-600 leading-none text-sm font-bold ml-0.5">×</button>
                                            </form>
                                        @endif
                                    </span>
                                @empty
                                    <span class="text-xs text-gray-400">No roles</span>
                                @endforelse
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-center">
                            <form action="{{ route('admin.users.assign-role') }}" method="POST" class="flex items-center gap-1 justify-center">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->id }}">
                                <select name="role_id" required class="text-xs border border-gray-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-emerald-500">
                                    <option value="">Role…</option>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="text-xs px-2 py-1 bg-emerald-600 hover:bg-emerald-700 text-white rounded transition-colors">Assign</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Role Management --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden self-start">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-base font-semibold text-gray-900">All Roles</h2>
        </div>
        <ul class="divide-y divide-gray-50">
            @foreach($roles as $role)
                <li class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-900">{{ $role->name }}</p>
                        @if($role->description)
                            <p class="text-xs text-gray-400">{{ $role->description }}</p>
                        @endif
                    </div>
                    @if(!$role->is_system)
                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST"
                              onsubmit="return confirm('Delete role {{ $role->name }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-gray-400 hover:text-red-500 transition-colors">🗑️</button>
                        </form>
                    @else
                        <span class="text-xs text-emerald-600 font-medium bg-emerald-50 px-2 py-0.5 rounded-full">system</span>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
</div>

{{-- Create Role Modal --}}
<div id="create-role-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/40 backdrop-blur-sm">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md border border-gray-100">
        <div class="flex items-center justify-between px-6 py-4 border-b">
            <h2 class="text-base font-semibold text-gray-900">Create Custom Role</h2>
            <button onclick="document.getElementById('create-role-modal').classList.add('hidden')" class="text-gray-400 text-xl">✕</button>
        </div>
        <form action="{{ route('admin.roles.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Role Name *</label>
                <input type="text" name="name" required placeholder="e.g. accountant"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            <div><label class="block text-xs font-medium text-gray-700 mb-1">Description</label>
                <input type="text" name="description" placeholder="What does this role do?"
                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-emerald-500 focus:outline-none"></div>
            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('create-role-modal').classList.add('hidden')" class="px-4 py-2 text-sm text-gray-600">Cancel</button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-lg">Create Role</button>
            </div>
        </form>
    </div>
</div>
@endsection
