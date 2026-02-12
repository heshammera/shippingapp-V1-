<?php
$dir = __DIR__ . '/database/migrations';
$backup = $dir . '/dump_backup';
if (!is_dir($backup)) mkdir($backup);

$filesToMove = [
    '2025_05_01_031539_create_permissions_table.php',
    '2025_05_01_031539_create_roles_table.php',
    '2025_05_01_031540_create_permission_role_table.php',
    '2025_05_01_041025_create_settings_table.php',
    '2025_05_01_045504_create_settings_table.php',
    '2025_05_01_050759_create_permission_tables.php',
    '2025_04_28_090043_2025_04_28_085733_add_missing_fields_to_shipments_table.php.php'
];

foreach ($filesToMove as $filename) {
    if (file_exists("$dir/$filename")) {
        rename("$dir/$filename", "$backup/$filename");
        echo "Moved $filename\n";
    } else {
        echo "File not found: $filename\n";
    }
}
