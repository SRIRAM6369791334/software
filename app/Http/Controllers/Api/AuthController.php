<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends BaseApiController
{
    /**
     * Handle API login.
     */
    public function login(Request $request): JsonResponse
    {
        // ✅ Fix 2: Rate Limiting — 5 attempts per 15 minutes per IP
        $throttleKey = 'login:' . Str::lower($request->input('login', '')) . ':' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return $this->sendError('Too many login attempts.', [
                'retry_after_seconds' => $seconds,
            ], 429);
        }

        $validator = Validator::make($request->all(), [
            'login'    => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation error', $validator->errors()->toArray(), 422);
        }

        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = User::where($loginField, $request->login)->first();

        // ✅ Use generic error message — don't reveal if email/username exists
        if (!$user || !Hash::check($request->password, $user->password)) {
            RateLimiter::hit($throttleKey, 900); // 15 minutes decay
            return $this->sendError('Unauthorized', ['login' => ['Invalid credentials.']], 401);
        }

        if (!$user->is_active) {
            return $this->sendError('Forbidden', ['login' => ['Your account is deactivated.']], 403);
        }

        // ✅ Fix 4: Named token with device info for better token management
        $deviceName = $request->input('device_name', $request->userAgent() ?? 'unknown-device');
        $tokenName  = Str::limit($deviceName, 50);
        $token = $user->createToken($tokenName)->plainTextToken;

        // Clear rate limiter on successful login
        RateLimiter::clear($throttleKey);

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
