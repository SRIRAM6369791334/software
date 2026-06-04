<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends BaseApiController
{
    /**
     * Handle API login.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'login'    => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors()->toArray(), 422);
        }

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $user = User::where($loginField, $request->login)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('Unauthorized', ['login' => ['These credentials do not match our records.']], 401);
        }

        if (!$user->is_active) {
            return $this->sendError('Forbidden', ['login' => ['Your account is deactivated.']], 403);
        }

        // Generate Token
        $token = $user->createToken('auth_token')->plainTextToken;

        // Log Activity
        ActivityLogger::log('API Login', 'Auth', $user->id);

        return $this->sendResponse([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'       => $user->id,
                'name'     => $user->name,
                'email'    => $user->email,
                'username' => $user->username,
                'roles'    => $user->getRoleNames(),
            ]
        ], 'Login successful');
    }

    /**
     * Handle API logout.
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();
        
        if ($user) {
            // Revoke current token
            $user->currentAccessToken()->delete();
            ActivityLogger::log('API Logout', 'Auth', $user->id);
        }

        return $this->sendResponse([], 'Logged out successfully');
    }

    /**
     * Retrieve the authenticated user's profile.
     */
    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        return $this->sendResponse([
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'username' => $user->username,
            'roles'    => $user->getRoleNames(),
        ], 'Profile retrieved successfully');
    }
}
