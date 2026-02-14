<?php

// Set the application base path
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/../public/index.php';
$_SERVER['SCRIPT_NAME'] = '/index.php';

// Change working directory to public folder
chdir(__DIR__ . '/../public');

// Bootstrap Laravel
require __DIR__ . '/../public/index.php';
