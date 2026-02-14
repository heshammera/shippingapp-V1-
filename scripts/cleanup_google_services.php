<?php

$vendorDir = __DIR__ . '/../vendor';
$googleServicesDir = $vendorDir . '/google/apiclient-services/src';

if (!is_dir($googleServicesDir)) {
    echo "Google Services directory not found: $googleServicesDir\n";
    exit(0);
}

// Services to keep
$whitelist = [
    'Sheets',
    'Drive',
    'Oauth2',
    'YouTube', // Just in case
    'Gmail'    // Just in case
];

$iterator = new DirectoryIterator($googleServicesDir);

echo "Cleaning up Google Services...\n";

foreach ($iterator as $fileinfo) {
    if ($fileinfo->isDot()) {
        continue;
    }

    $filename = $fileinfo->getFilename();

    if ($fileinfo->isDir() && !in_array($filename, $whitelist)) {
        // Recursively delete directory
        deleteDirectory($fileinfo->getPathname());
        // echo "Deleted: $filename\n";
    } elseif ($fileinfo->isFile() && !in_array(str_replace('.php', '', $filename), $whitelist)) {
        // Delete file if it's a direct service file (unlikely in new structure but good hygiene)
        unlink($fileinfo->getPathname());
    }
}

echo "Google Services cleanup completed.\n";

function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }

    return rmdir($dir);
}
