<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Please log in to continue.');
        }

        if ($user->isSuspended()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account has been suspended. Please contact the administrator.');
        }

        if (!$user->isActive()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Your account is currently inactive.');
        }

        if (!$user->hasRole($roles)) {
            abort(403, 'Unauthorized. You do not have permission to access this area.');
        }

        return $next($request);
    }
}
