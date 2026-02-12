<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleAccessControl
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        if (!$user) {
            abort(403);
        }

        $role = $user->role;

        // المودريتور → مسموح فقط صفحة إنشاء الشحنة
        if ($role === 'moderator') {
            if ($request->routeIs('shipments.create') || $request->is('shipments') && $request->isMethod('post')) {
                return $next($request);
            }
            abort(403, 'غير مصرح لك بالوصول.');
        }

        // المحاسب → مسموح فقط الحسابات والتقارير
        if ($role === 'accountant') {
            if (
                $request->routeIs('accounting.*') ||
                $request->routeIs('collections.*') ||
                $request->routeIs('expenses.*') ||
                $request->routeIs('reports.*')
            ) {
                return $next($request);
            }
            abort(403, 'غير مصرح لك بالوصول.');
        }
        
        if (auth()->check() && auth()->user()->role === 'viewer') {
    if (
        $request->is('users*') || 
        $request->is('roles*') || 
        $request->is('settings*') ||
        $request->is('expenses*') ||
        $request->is('delivery-agents*')
    ) {
        abort(403, 'ليس لديك صلاحية الوصول.');
    }
}


        return $next($request);
    }
}

