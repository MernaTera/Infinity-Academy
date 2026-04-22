<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HandleSessionExpiry
{
    /**
     * If the user was previously authenticated but their session is now gone,
     * redirect them to login with a friendly "session expired" message.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Skip auth/guest routes
        if (!$request->is('login', 'logout', 'forgot-password', 'reset-password*')) {

            // If the route needs auth but user is not authenticated
            if (!Auth::check() && $request->session()->has('_previous')) {
                return redirect()->route('login')
                    ->with('session_expired', true);
            }
        }

        return $next($request);
    }
}