<?php

// Debugging script to check session and CSRF configuration
// Access via: https://your-vercel-domain.vercel.app/debug-session.php

use Illuminate\Support\Facades\Session;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

echo "<h1>Session & CSRF Debug Info</h1>";
echo "<pre>";

echo "=== Environment ===\n";
echo "APP_ENV: " . env('APP_ENV') . "\n";
echo "APP_DEBUG: " . env('APP_DEBUG') . "\n";
echo "APP_URL: " . env('APP_URL') . "\n";
echo "SESSION_DRIVER: " . env('SESSION_DRIVER') . "\n";
echo "SESSION_SECURE_COOKIE: " . env('SESSION_SECURE_COOKIE') . "\n";
echo "SESSION_LIFETIME: " . env('SESSION_LIFETIME') . "\n\n";

echo "=== Config ===\n";
echo "session.driver: " . config('session.driver') . "\n";
echo "session.secure: " . (config('session.secure') ? 'true' : 'false') . "\n";
echo "session.same_site: " . config('session.same_site') . "\n";
echo "session.cookie: " . config('session.cookie') . "\n\n";

echo "=== Request ===\n";
echo "URL: " . $request->url() . "\n";
echo "Scheme: " . $request->getScheme() . "\n";
echo "Is Secure: " . ($request->isSecure() ? 'YES' : 'NO') . "\n";
echo "Host: " . $request->getHost() . "\n\n";

echo "=== Session ===\n";
echo "Session ID: " . Session::getId() . "\n";
echo "Session Started: " . (Session::isStarted() ? 'YES' : 'NO') . "\n\n";

echo "=== CSRF ===\n";
echo "CSRF Token: " . csrf_token() . "\n\n";

echo "=== Trusted Proxies ===\n";
$trustProxies = new \App\Http\Middleware\TrustProxies($app);
$reflection = new ReflectionClass($trustProxies);
$property = $reflection->getProperty('proxies');
$property->setAccessible(true);
echo "Proxies: " . var_export($property->getValue($trustProxies), true) . "\n\n";

echo "</pre>";
