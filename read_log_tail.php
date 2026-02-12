<?php
$file = 'migration_log.txt';
if (!file_exists($file)) die("File not found");

// Standard way to tail in PHP
$lines = file($file); // Reads entire file into array. Memory intensive if huge, but migration logs are usually < 10MB.
$tail = array_slice($lines, -200);
echo implode("", $tail);
