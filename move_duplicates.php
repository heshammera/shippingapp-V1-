<?php
$dir = __DIR__ . '/database/migrations';
$backup = $dir . '/dump_backup';
if (!is_dir($backup)) mkdir($backup);
$files = glob("$dir/2025_07_23_135048_*.php");
foreach ($files as $file) {
    rename($file, $backup . '/' . basename($file));
    echo "Moved " . basename($file) . "\n";
}
