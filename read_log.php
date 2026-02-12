<?php
$file = 'seed_log_final_2.txt';
$size = filesize($file);
$offset = max(0, $size - 5000);
$handle = fopen($file, "r");
fseek($handle, $offset);
$content = fread($handle, 5000);
fclose($handle);

// Try to convert if looks like UTF-16LE
if (strpos($content, "\0") !== false) {
     $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
}
echo "Tailing log:\n";
echo $content;
