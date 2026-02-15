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
