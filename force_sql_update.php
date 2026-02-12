<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

echo "Starting Update...\n";
$affected = \DB::update("UPDATE shipments SET barcode = tracking_number WHERE barcode IS NULL OR barcode = ''");
echo "Affected Rows: " . $affected . "\n";

$check = \App\Models\Shipment::where('tracking_number', '13637')->first();
echo "Check 13637 Barcode: " . ($check->barcode ?? 'NULL') . "\n";
