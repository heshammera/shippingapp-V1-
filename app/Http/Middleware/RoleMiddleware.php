<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{   
public function handle(Request $request, Closure $next, $roles)
{
    $userRole = $request->user()?->roles->first()?->name;

    if (!$userRole || !in_array($userRole, explode('|', $roles))) {
        abort(403, 'غير مصرح لك بالدخول.');
    }

    return $next($request);
}

}
