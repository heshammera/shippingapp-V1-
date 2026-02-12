<?php
$dir = __DIR__ . '/database/migrations';
$backup = $dir . '/dump_backup';
if (!is_dir($backup)) mkdir($backup);

// Move incomplete April tables to backup
rename("$dir/2025_04_26_180401_create_roles_table.php", "$backup/2025_04_26_180401_create_roles_table.php");
rename("$dir/2025_04_26_180402_create_permissions_table.php", "$backup/2025_04_26_180402_create_permissions_table.php");

// Restore complete Spatie migration from backup
rename("$backup/2025_05_01_050759_create_permission_tables.php", "$dir/2025_05_01_050759_create_permission_tables.php");

echo "Swapped roles/permissions migrations.\n";
