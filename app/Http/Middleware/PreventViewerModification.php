<?php

// app/Http/Middleware/PreventViewerModification.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventViewerModification
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check() && auth()->user()->role === 'viewer') {
            // فقط السماح بالطلبات GET و HEAD للمشاهدة
            if (!in_array($request->method(), ['GET', 'HEAD'])) {
                abort(403, 'عذراً، ليس لديك صلاحية تعديل البيانات.');
            }
        }

        return $next($request);
    }
}

