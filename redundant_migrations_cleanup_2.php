<?php
$dir = __DIR__ . '/database/migrations';
$backup = $dir . '/dump_backup';
if (!is_dir($backup)) mkdir($backup);

$filesToMove = [
    '2025_05_01_073501_add_description_to_permissions_table.php',
    '2025_05_01_081543_add_description_to_roles_table.php',
];

foreach ($filesToMove as $filename) {
    if (file_exists("$dir/$filename")) {
        rename("$dir/$filename", "$backup/$filename");
        echo "Moved $filename\n";
    } else {
        echo "File not found: $filename\n";
    }
}
