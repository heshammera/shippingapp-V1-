<?php

$file = 'database/seeders/ShipmentsTableSeeder.php';
$content = file_get_contents($file);

if ($content === false) {
    die("Could not read file: $file\n");
}

// Replace status_id 34 with 37 (Unspecified)
$pattern = "/'status_id'\s*=>\s*34/";
$replacement = "'status_id' => 37";

$original_md5 = md5($content);
echo "Original MD5: $original_md5\n";

$content = preg_replace($pattern, $replacement, $content, -1, $count);
echo "Replaced $count occurrences of status_id => 34\n";

$new_md5 = md5($content);
echo "New MD5: $new_md5\n";

if ($original_md5 !== $new_md5) {
    if (file_put_contents($file, $content) === false) {
        die("Could not write to file: $file\n");
    }
    echo "File updated successfully.\n";
} else {
    echo "No changes made.\n";
}

?>
