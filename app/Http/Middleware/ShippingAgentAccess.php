<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ShippingAgentAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();

        // لو المستخدم هو مندوب الشحن
        if ($user && $user->role === 'shipping_agent') {
            // السماح فقط بصفحة الشحنات (route اسمه shipments.index)
            if ($request->routeIs('shipments.index')) {
                return $next($request); // يسمح بالوصول
            }

            // لو حاول يدخل صفحة تانية، نرجعه لصفحة الشحنات مع رسالة خطأ
            return redirect()->route('shipments.index')->with('error', 'غير مسموح بالدخول لهذه الصفحة');
        }

        // لو مش مندوب الشحن، يسمح له بالدخول كالمعتاد
        return $next($request);
    }
}
