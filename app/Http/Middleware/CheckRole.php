public function handle($request, Closure $next, $role)
{
    if (auth()->user()->role !== $role) {
        // لو مش الدور المطلوب، يعيد التوجيه
        return redirect('/home'); 
    }

    return $next($request);
}
