<?php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', '1');

try {
    // Set the application base path
    $_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';
    $_SERVER['SCRIPT_NAME'] = '/index.php';
    
    // Change working directory to public folder
    chdir(__DIR__ . '/../public');
    
    // RAW DEBUG ROUTE (Bypassing Laravel Router)
    if (isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], 'debug-session-raw')) {
        header('Content-Type: application/json');
        echo json_encode([
            'STATUS' => 'Deployment Active',
            'ENV_APP_KEY' => getenv('APP_KEY') ? 'Set' : 'Missing',
            'ENV_SESSION_DRIVER' => getenv('SESSION_DRIVER'),
            'URI' => $_SERVER['REQUEST_URI'],
            'Time' => date('Y-m-d H:i:s'),
            'COOKIES' => $_COOKIE,
            'HEADERS' => getallheaders(),
        ]);
        exit;
    }

    // 🔥 FIX CHICKEN-AND-EGG: Force array session driver for migration route
    if (isset($_SERVER['REQUEST_URI']) && str_contains($_SERVER['REQUEST_URI'], 'migrate-db')) {
        putenv('SESSION_DRIVER=array');
        $_ENV['SESSION_DRIVER'] = 'array';
    } else {
        // Force database driver for everything else
        putenv('SESSION_DRIVER=database');
        $_ENV['SESSION_DRIVER'] = 'database';
    }

    // Bootstrap Laravel
    require __DIR__ . '/../vendor/autoload.php';
    $app = require __DIR__ . '/../bootstrap/app.php';

    // Set storage path to /tmp for Vercel (Read-only filesystem fix)
    $app->useStoragePath('/tmp');
    
    // Ensure storage structure exists in /tmp
    if (!is_dir('/tmp/framework/views')) {
        mkdir('/tmp/framework/views', 0777, true);
    }
    if (!is_dir('/tmp/framework/cache')) {
        mkdir('/tmp/framework/cache', 0777, true);
    }
    if (!is_dir('/tmp/logs')) {
        mkdir('/tmp/logs', 0777, true);
    }
    // Fix APP_URL if it's not set correctly or contains Vercel placeholder
    if (!getenv('APP_URL') || str_contains(getenv('APP_URL'), '${VERCEL_URL}')) {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        putenv("APP_URL={$protocol}{$host}");
        $_ENV['APP_URL'] = "{$protocol}{$host}";
    }

    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

    $response = $kernel->handle(
        $request = \Illuminate\Http\Request::capture()
    );

    $response->send();

    $kernel->terminate($request, $response);
} catch (\Throwable $e) {
    // Display error for debugging
    http_response_code(500);
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString();
    exit(1);
}
