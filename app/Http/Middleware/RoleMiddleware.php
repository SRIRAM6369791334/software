<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    const HIERARCHY = ['viewer' => 1, 'staff' => 2, 'manager' => 3, 'admin' => 4];

    public function handle(Request $request, Closure $next, string $minRole): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $userLevel = $user->roles->map(fn($r) => self::HIERARCHY[$r->name] ?? 0)->max() ?? 0;
        $required  = self::HIERARCHY[$minRole] ?? 0;

        if ($userLevel < $required) {
            abort(403, 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
