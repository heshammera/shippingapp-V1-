<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUserExpiration
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->expires_at && now()->gt($user->expires_at)) {
            Auth::logout();
            return redirect()->route('login')->with('error', 'انتهت صلاحية حسابك. يرجى التواصل مع الإدارة.');
        }

        return $next($request);
    }
}
