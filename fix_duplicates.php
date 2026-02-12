<?php

$file = 'database/seeders/ShipmentsTableSeeder.php';
$lines = file($file);
$counts = [];
$fixed = 0;

$newLines = [];

foreach ($lines as $line) {
    if (preg_match("/('tracking_number'\s*=>\s*')([^']+)(',)/", $line, $matches)) {
        $tn = $matches[2];
        if (!isset($counts[$tn])) {
            $counts[$tn] = 1;
            $newLines[] = $line;
        } else {
            $counts[$tn]++;
            $newTn = $tn . '-' . $counts[$tn];
            // Replace with new TN
            $newLine = $matches[1] . $newTn . $matches[3] . "\n";
            $newLines[] = $newLine;
            $fixed++;
        }
    } else {
        $newLines[] = $line;
    }
}

file_put_contents($file, implode("", $newLines));

echo "Fixed $fixed duplicates.\n";
