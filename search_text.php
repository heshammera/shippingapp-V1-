<?php
$search = 'TRKIFBSOIXM';
$file = 'database/seeders/ShipmentsTableSeeder.php';
$handle = fopen($file, "r");
if ($handle) {
    echo "Searching for '$search' in $file...\n";
    $lineNum = 0;
    while (($line = fgets($handle)) !== false) {
        $lineNum++;
        if (strpos($line, $search) !== false) {
            echo "Found at line: $lineNum\n";
            echo "Content: " . trim($line) . "\n";
        }
    }
    fclose($handle);
}
