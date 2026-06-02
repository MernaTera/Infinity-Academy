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
        if (!$request->is('login', 'logout', 'forgot-password', 'reset-password*')) {

            if (!Auth::check() && !$request->routeIs('login') && $request->session()->has('_previous')) {
                return redirect()->route('login')
                    ->with('session_expired', true);
            }

            if (Auth::check()) {
                $user = Auth::user();
                if (!$user->is_active) {
                    Auth::logout();
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                    return redirect()->to(route('login') . '?deactivated=1');
                }
            }
        }

        return $next($request);
    }
}