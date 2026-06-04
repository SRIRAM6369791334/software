<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\ActivityLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Services\ActivityLogger;

class UserManagementController extends BaseApiController
{
    /**
     * Display a listing of users, roles, and latest activity logs (Paginated users).
     */
    public function index(Request $request): JsonResponse
    {
        $users = User::with('roles')->orderBy('name')->paginate(15);
        $roles = Role::orderBy('name')->get();
        $activityLogs = ActivityLog::with('user')->latest('timestamp')->take(15)->get();

        return response()->json([
            'success' => true,
            'message' => 'User management index retrieved successfully',
            'data'    => [
                'users'         => $users->items(),
                'roles'         => $roles,
                'activity_logs' => $activityLogs,
            ],
            'pagination' => [
                'current_page' => $users->currentPage(),
                'per_page'     => $users->perPage(),
                'total'        => $users->total(),
            ]
        ]);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|unique:users,username',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|exists:roles,name',
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'username'  => $validated['username'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'is_active' => true,
        ]);

        $user->assignRole($validated['role']);

        ActivityLogger::log("Created User: {$user->username} with role: {$validated['role']}", 'UserManagement', $user->id);

        $user->load('roles');
        return $this->sendResponse($user, 'User created successfully', 201);
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,' . $user->id,
            'role'      => 'required|exists:roles,name',
            'password'  => 'nullable|string|min:6',
            'is_active' => 'nullable|boolean',
        ]);

        $user->update($request->only('name', 'email', 'is_active'));

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($validated['password'])]);
        }

        $user->syncRoles([$validated['role']]);

        ActivityLogger::log("Updated User: {$user->username}", 'UserManagement', $user->id);

        $user->load('roles');
        return $this->sendResponse($user, 'User updated successfully');
    }

    /**
     * Toggle active/inactive status of the user.
     */
    public function toggleStatus(User $user): JsonResponse
    {
        $user->is_active = !$user->is_active;
        $user->save();

        $action = $user->is_active ? 'Activated User' : 'Deactivated User';
        ActivityLogger::log("{$action}: {$user->username}", 'UserManagement', $user->id);

        return $this->sendResponse($user, "User status updated to " . ($user->is_active ? 'Active' : 'Inactive'));
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user): JsonResponse
    {
        if ($user->id === Auth::id()) {
            return $this->sendError('You cannot delete yourself.', [], 422);
        }

        $username = $user->username;
        $id = $user->id;

        $user->delete();

        ActivityLogger::log("Deleted User: {$username}", 'UserManagement', $id);

        return $this->sendResponse([], 'User deleted successfully');
    }
}
