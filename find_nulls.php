<?php
$file = 'database/seeders/ShipmentsTableSeeder.php';
$handle = fopen($file, "r");
if ($handle) {
    echo "Searching for 'product_name' => NULL using regex...\n";
    $lineNum = 0;
    while (($line = fgets($handle)) !== false) {
        $lineNum++;
        if (preg_match("/product_name.*=>.*NULL/i", $line)) {
            echo "Found at line: $lineNum\n";
        }
    }
    fclose($handle);
}
