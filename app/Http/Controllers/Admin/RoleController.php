<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\PermissionGroup;
use App\Services\ActivityLogger;

class RoleController extends Controller
{
    private function flushPermissionCache(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }


    public function index()
    {
        $roles = Role::withCount(['permissions', 'users'])->oldest()->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create', [
            'role' => new Role()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        ActivityLogger::log('Created Role', 'RoleManagement', $role->id);
        $this->flushPermissionCache();

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully!');
    }

    public function edit($id)
    {
        $role = Role::findOrFail($id);
        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
        ]);

        $role = Role::findOrFail($id);
        $role->update([
            'name' => $request->name,
        ]);

        ActivityLogger::log('Updated Role', 'RoleManagement', $role->id);
        $this->flushPermissionCache();

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        
        // Anti-Lockout Strategy
        if($role->name === 'admin' || $role->name === 'Super Admin') {
            abort(403, 'SYSTEM: Admin role cannot be deleted.');
        }
        
        $role->delete();

        ActivityLogger::log('Deleted Role', 'RoleManagement', $role->id);
        $this->flushPermissionCache();

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted!');
    }

    public function assignPermissionPage($id)
    {
        $role = Role::findOrFail($id);
        $permissionGroups = PermissionGroup::with('permissions')->orderBy('name')->get();

        return view('admin.roles.assignpermission', compact('role', 'permissionGroups'));
    }

    public function assignPermission(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $role = Role::findOrFail($request->role_id);
        
        // Don't allow changing permissions for Super Admin
        if ($role->name === 'admin' || $role->name === 'Super Admin') {
            return redirect()->route('admin.roles.index')->with('error', 'Admin permissions cannot be modified.');
        }

        $permissions = Permission::whereIn('id', $request->permissions ?? [])->pluck('name')->toArray();
        $role->syncPermissions($permissions);

        ActivityLogger::log('Assigned Permissions to Role', 'RoleManagement', $role->id);
        $this->flushPermissionCache();

        return redirect()->route('admin.roles.index')->with('success', 'Permissions assigned successfully!');
    }
}
