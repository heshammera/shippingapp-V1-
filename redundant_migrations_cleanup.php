<?php
$dir = __DIR__ . '/database/migrations';
$backup = $dir . '/dump_backup';
if (!is_dir($backup)) mkdir($backup);

$filesToMove = [
    // Redundant shipments columns (covered by 2025_04_28_085733)
    '2025_04_29_121241_add_governorate_to_shipments_table.php',
    '2025_04_29_121826_add_shipping_price_to_shipments_table.php',
    '2025_04_29_122224_add_product_id_to_shipments_table.php',

    // Redundant users columns (covered by 2025_04_28_111830)
    '2025_05_01_031205_add_phone_address_is_active_to_users_table.php',

    // Redundant role columns (covered by 2025_04_28_112704)
    '2025_05_02_031033_add_role_to_users_table.php',
    '2025_05_05_184706_add_role_to_users_table.php',
];

foreach ($filesToMove as $filename) {
    if (file_exists("$dir/$filename")) {
        rename("$dir/$filename", "$backup/$filename");
        echo "Moved $filename\n";
    } else {
        echo "File not found: $filename\n";
    }
}
