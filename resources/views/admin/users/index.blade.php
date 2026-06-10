@extends('layouts.app')
@section('title', 'Sovereign Console')

@section('content')
<div class="space-y-6">
    <x-page-header 
        title="Sovereign Console" 
        subtitle="Identity orchestration and system-wide security audit"
    >
        <x-slot:actions>
            <x-button onclick="openModal('userModal')" variant="primary" icon="person_add">
                Deploy Agent
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <x-stat-card title="Total Profiles" value="{{ $users->count() }}" icon="group" color="emerald" />
        <x-stat-card title="Active Agents" value="{{ $users->where('is_active', true)->count() }}" icon="monitor_heart" color="blue" />
        <x-stat-card title="Admins" value="{{ $users->filter(fn($u) => $u->hasRole('admin') || $u->hasRole('Super Admin'))->count() }}" icon="admin_panel_settings" color="amber" />
        <x-stat-card title="System Events" value="{{ count($activityLogs) }}" icon="analytics" color="rose" />
    </div>

    <x-card padding="p-0">
        <x-data-table :headers="['Agent Profile', 'Contact', 'Primary Role', 'Status', 'Actions']">
            @forelse($users as $user)
                <tr class="hover:bg-white/80 dark:hover:bg-zinc-800/50 transition-all duration-300 group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-50 to-sky-50 dark:from-emerald-500/20 dark:to-sky-500/20 flex items-center justify-center font-bold text-zinc-600 dark:text-zinc-300 border border-emerald-100 dark:border-emerald-500/20">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <div>
                                <div class="font-medium text-zinc-900 dark:text-zinc-100">{{ $user->name }}</div>
                                <div class="text-xs text-zinc-500 dark:text-zinc-400 font-jetbrains">{{ $user->username }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm text-zinc-600 dark:text-zinc-400">{{ $user->email }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <x-badge variant="{{ $user->roles->first()?->name === 'admin' ? 'info' : 'secondary' }}">
                            {{ $user->roles->first()?->name ?? 'No Role' }}
                        </x-badge>
                    </td>
                    <td class="px-6 py-4">
                        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    class="relative inline-flex h-6 w-11 shrink-0 cursor-pointer items-center rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:ring-offset-2 {{ $user->is_active ? 'bg-emerald-500' : 'bg-zinc-200 dark:bg-zinc-700' }}">
                                <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $user->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                            </button>
                        </form>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <x-button onclick="editUser({{ $user }})" variant="ghost" size="sm" icon="settings" title="Settings" />
                            @if(auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirm('Delete this agent profile?');">
                                    @csrf @method('DELETE')
                                    <x-button type="submit" variant="ghost" size="sm" icon="delete" class="text-rose-500 hover:text-rose-600" title="Delete" />
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center">
                        <x-empty-state 
                            icon="person_off" 
                            title="No profiles found" 
                            subtitle="Deploy your first system agent." 
                        />
                    </td>
                </tr>
            @endforelse
        </x-data-table>
    </x-card>
</div>

@push('modals')
    {{-- Create/Edit User Modal --}}
    <div id="userModal" class="fixed inset-0 z-[100] hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-zinc-900/40 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-zinc-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-zinc-100 dark:border-zinc-800">
                    <form id="userForm" action="{{ route('admin.users.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="_method" id="formMethod" value="POST">
                        <div class="bg-white dark:bg-zinc-900 px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-emerald-100 dark:bg-emerald-500/20 sm:mx-0 sm:h-10 sm:w-10">
                                    <span class="material-symbols-rounded text-emerald-600 dark:text-emerald-400">person_add</span>
                                </div>
                                <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg font-semibold leading-6 text-zinc-900 dark:text-zinc-100" id="modalTitle">Deploy System Agent</h3>
                                    <div class="mt-4 space-y-4">
                                        <x-form.input name="name" id="userName" label="Full Name" icon="person" required />
                                        <x-form.input name="username" id="userUsername" label="Username" icon="badge" required />
                                        <x-form.input type="email" name="email" id="userEmail" label="Email Address" icon="mail" required />
                                        <x-form.input type="password" name="password" id="userPassword" label="Password" icon="lock" />
                                        <p class="text-xs text-zinc-500 -mt-2" id="passwordHelp">Leave blank to keep existing password when editing.</p>
                                        <x-form.input name="role" id="role" label="Primary Role" icon="admin_panel_settings" required list="roleOptions" placeholder="Type or select a role..." />
                                        <datalist id="roleOptions">
                                            @foreach($roles as $r)
                                                <option value="{{ $r->name }}"></option>
                                            @endforeach
                                        </datalist>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-zinc-50 dark:bg-zinc-900/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-zinc-100 dark:border-zinc-800">
                            <x-button type="submit" variant="primary" class="w-full sm:ml-3 sm:w-auto">Save Agent</x-button>
                            <x-button type="button" variant="secondary" onclick="closeModal('userModal')" class="mt-3 w-full sm:mt-0 sm:w-auto">Cancel</x-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
<script>
    function openModal(modalId) {
        document.getElementById('modalTitle').innerText = 'Deploy System Agent';
        document.getElementById('userForm').action = "{{ route('admin.users.store') }}";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('userForm').reset();
        document.getElementById('username').readOnly = false;
        document.getElementById('passwordHelp').classList.add('hidden');
        document.getElementById('password').required = true;
        
        document.getElementById(modalId).classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function editUser(user) {
        document.getElementById('modalTitle').innerText = 'Edit System Agent';
        document.getElementById('userForm').action = `/admin/users/${user.id}`;
        document.getElementById('formMethod').value = 'PUT';
        
        document.getElementById('name').value = user.name;
        document.getElementById('username').value = user.username;
        document.getElementById('username').readOnly = true; // Username shouldn't change
        document.getElementById('email').value = user.email;
        document.getElementById('password').required = false;
        document.getElementById('passwordHelp').classList.remove('hidden');
        
        if (user.roles && user.roles.length > 0) {
            document.getElementById('role').value = user.roles[0].name;
        }
        
        document.getElementById('userModal').classList.remove('hidden');
    }
</script>
@endpush
@endsection
