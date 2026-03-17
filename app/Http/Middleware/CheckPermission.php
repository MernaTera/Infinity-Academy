<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Auth\User;

class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $user = auth()->user();

        if (!$user) {
            abort(401, 'Unauthorized');
        }

        if (!$user->isActive()) {
            abort(403, 'Account inactive');
        }

        if (!$user->canDo($permission)) {
            abort(403, 'Forbidden');
        }

        return $next($request);
    }
}