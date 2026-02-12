<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ModeratorAccess
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check() && Auth::user()->role == 'moderator') {
            // المودريتور يسمح له فقط بصفحة اضافة شحنة
            if ($request->is('shipments/create') || $request->is('shipments')) {
                return $next($request);
            } else {
                abort(403, 'غير مصرح لك بالدخول');
            }
        }

        return $next($request);
    }
}
