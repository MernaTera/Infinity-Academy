<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate the CS-Leader-only oversight views (team sales, all-branch leads).
 * Allowed for a CS Leader and for Admin (who can see everything anyway).
 */
class EnsureCsLeader
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        if (!($user->isAdmin() || $user->isCsLeader())) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}
