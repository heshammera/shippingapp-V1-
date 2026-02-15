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
    require __DIR__ . '/../public/index.php';
} catch (\Throwable $e) {
    // Display error for debugging
    http_response_code(500);
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " on line " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString();
    exit(1);
}
