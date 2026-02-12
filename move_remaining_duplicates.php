<?php
$dir = __DIR__ . '/database/migrations';
$backup = $dir . '/dump_backup';
if (!is_dir($backup)) mkdir($backup);
// Move all July 2025 migrations (Snapshot duplicates)
$files = glob("$dir/2025_07_*.php");
foreach ($files as $file) {
    rename($file, $backup . '/' . basename($file));
    echo "Moved " . basename($file) . "\n";
}
