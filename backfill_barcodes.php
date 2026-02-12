<?php

use App\Models\Shipment;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$kernel->handle(Illuminate\Http\Request::capture());

echo "Backfilling Barcodes...\n";

Shipment::whereNull('barcode')
    ->orWhere('barcode', '')
    ->chunk(200, function ($shipments) {
        foreach ($shipments as $shipment) {
            $shipment->update(['barcode' => $shipment->tracking_number]);
            echo ".";
        }
    });

echo "\nDone!\n";
