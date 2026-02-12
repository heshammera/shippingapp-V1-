<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * التحقق من وجود صلاحية معينة للمستخدم
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
     

    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // التحقق من تسجيل دخول المستخدم
    if (auth()->check() && auth()->user()->hasPermission($permission)) {
            return redirect()->route('login');
        }

        // التحقق من وجود الصلاحية للمستخدم
        if (!auth()->user()->hasPermission($permission)) {
            abort(403, 'ليس لديك صلاحية الوصول إلى هذه الصفحة');
        }

        return $next($request);
    }
}
