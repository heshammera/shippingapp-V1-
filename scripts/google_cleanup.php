<?php

$vendorDir = __DIR__ . '/../vendor';
$servicesDir = $vendorDir . '/google/apiclient-services/src/Google/Service';

if (!is_dir($servicesDir)) {
    echo "Google Services directory not found at: $servicesDir\n";
    exit(0);
}

echo "Cleaning up Google Services in $servicesDir...\n";

// Services to KEEP
$whitelist = [
    'Sheets',
    'Sheets.php',
    'Drive', // Often needed for file access
    'Drive.php',
    'Oauth2', // Auth
    'Oauth2.php'
];

$iterator = new DirectoryIterator($servicesDir);
$deletedCount = 0;
$keptCount = 0;

foreach ($iterator as $fileinfo) {
    if ($fileinfo->isDot()) continue;

    $filename = $fileinfo->getFilename();

    if (!in_array($filename, $whitelist)) {
        if ($fileinfo->isDir()) {
            // Recursive delete for directories
            $files = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($fileinfo->getPathname(), RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );

            foreach ($files as $file) {
                if ($file->isDir()) {
                    rmdir($file->getRealPath());
                } else {
                    unlink($file->getRealPath());
                }
            }
            rmdir($fileinfo->getPathname());
        } else {
            unlink($fileinfo->getPathname());
        }
        $deletedCount++;
        // echo "Deleted: $filename\n";
    } else {
        $keptCount++;
        echo "Kept: $filename\n";
    }
}

echo "Cleanup complete. Deleted $deletedCount items. Kept $keptCount items.\n";
