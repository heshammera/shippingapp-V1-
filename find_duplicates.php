<?php

$file = 'database/seeders/ShipmentsTableSeeder.php';
$handle = fopen($file, "r");
$out = fopen('duplicates_utf8.txt', 'w');

if ($handle) {
    $counts = [];
    $lines = [];
    $lineNum = 0;
    while (($line = fgets($handle)) !== false) {
        $lineNum++;
        if (preg_match("/'tracking_number'\s*=>\s*'([^']+)'/", $line, $matches)) {
            $tn = $matches[1];
            if (!isset($counts[$tn])) {
                $counts[$tn] = 0;
                $lines[$tn] = [];
            }
            $counts[$tn]++;
            $lines[$tn][] = $lineNum;
        }
    }
    fclose($handle);

    fwrite($out, "Duplicate Tracking Numbers found:\n");
    $countDuplicates = 0;
    foreach ($counts as $tn => $count) {
        if ($count > 1) {
             fwrite($out, "$tn:" . implode(",", $lines[$tn]) . "\n");
             $countDuplicates++;
             if ($countDuplicates >= 20) break;
        }
    }
    fclose($out);
    echo "Done.\n";
} else {
    echo "Error opening file.";
}
