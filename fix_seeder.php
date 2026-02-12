<?php

$file = 'database/seeders/ShipmentsTableSeeder.php';
$content = file_get_contents($file);

if ($content === false) {
    die("Could not read file: $file\n");
}

$replacements = [
    "/'product_name'\s*=>\s*NULL/i" => "'product_name' => 'Unknown Product'",
    "/'quantity'\s*=>\s*NULL/i" => "'quantity' => 1",
    "/'cost_price'\s*=>\s*NULL/i" => "'cost_price' => 0",
    "/'selling_price'\s*=>\s*NULL/i" => "'selling_price' => 0",
    "/'product_description'\s*=>\s*NULL/i" => "'product_description' => ''",
];

$original_md5 = md5($content);
echo "Original MD5: $original_md5\n";

foreach ($replacements as $pattern => $replacement) {
    $content = preg_replace($pattern, $replacement, $content, -1, $count);
    echo "Replaced $count occurrences of $pattern\n";
}

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
