<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserManagementController extends Controller
{
    public function index(): View
    {
        $users     = User::with('roles')->orderBy('name')->get();
        $roles     = Role::orderBy('name')->get();
        return view('admin.users.index', compact('users', 'roles'));
    }

    public function assignRole(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        // Prevent duplicate assignment
        $exists = UserRole::where('user_id', $request->user_id)
                          ->where('role_id', $request->role_id)
                          ->exists();

        if ($exists) {
            return back()->with('error', 'User already has this role.');
        }

        UserRole::create([
            'user_id'     => $request->user_id,
            'role_id'     => $request->role_id,
            'assigned_by' => Auth::id(),
        ]);

        return back()->with('success', 'Role assigned successfully.');
    }

    public function removeRole(UserRole $userRole): RedirectResponse
    {
        $userRole->delete();
        return back()->with('success', 'Role removed.');
    }

    public function storeRole(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:50|unique:roles,name',
            'description' => 'nullable|string|max:255',
        ]);

        Role::create([
            'name'        => strtolower(trim($request->name)),
            'description' => $request->description,
            'is_system'   => false,
            'created_by'  => Auth::id(),
        ]);

        return back()->with('success', 'Role created.');
    }

    public function destroyRole(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be deleted.');
        }
        $role->delete();
        return back()->with('success', 'Role deleted.');
    }
}
