<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestrictByRoleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $role = $user->role;

        if ($role === 'moderator' && !$request->routeIs('shipments.create') && !$request->routeIs('shipments.store')) {
            abort(403, 'غير مسموح لك بالوصول إلى هذا الجزء');
        }

        if ($role === 'accountant' && !(
            $request->is('accounting*') ||
            $request->is('reports*') ||
            $request->routeIs('collections.*') ||
            $request->routeIs('expenses.*')
        )) {
            abort(403, 'غير مسموح لك بالوصول إلى هذا الجزء');
        }

        return $next($request);
    }
}
