<?php
$search = 'TRKGRPNRACT';
$file = 'database/seeders/ShipmentsTableSeeder.php';
$handle = fopen($file, "r");
if ($handle) {
    echo "Searching for $search...\n";
    $lineNum = 0;
    while (($line = fgets($handle)) !== false) {
        $lineNum++;
        if (strpos($line, $search) !== false) {
            echo "Found at line: $lineNum\n";
            echo "Line content: $line";
        }
    }
    fclose($handle);
}
