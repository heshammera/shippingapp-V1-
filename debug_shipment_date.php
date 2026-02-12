<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Shipment;
use Illuminate\Support\Facades\Schema;

echo "--- Debugging Shipment Date ---\n";

// 1. Check if column exists
if (!Schema::hasColumn('shipments', 'shipping_date')) {
    echo "❌ ERROR: Column 'shipping_date' does NOT exist in 'shipments' table!\n";
    exit;
} else {
    echo "✅ Column 'shipping_date' exists.\n";
}

// 2. Get a shipment
$shipment = Shipment::latest()->first();
if (!$shipment) {
    echo "❌ No shipments found to test.\n";
    exit;
}
echo "Testing with Shipment ID: {$shipment->id}\n";
echo "Current Date: " . ($shipment->shipping_date ?? 'NULL') . "\n";

// 3. Update
$newDate = now()->addDays(rand(1, 30))->format('Y-m-d');
echo "Attempting to specific date: $newDate\n";

$shipment->shipping_date = $newDate;
$saved = $shipment->save(); // Should trigger observers

if ($saved) {
    echo "✅ Save returned TRUE.\n";
} else {
    echo "❌ Save returned FALSE.\n";
}

// 4. Verify persistence
$shipment->refresh();
echo "Reloaded Date: " . ($shipment->shipping_date ? $shipment->shipping_date->format('Y-m-d') : 'NULL') . "\n";

if (($shipment->shipping_date ? $shipment->shipping_date->format('Y-m-d') : '') === $newDate) {
    echo "✅ SUCCESS: Date persisted correctly.\n";
} else {
    echo "❌ FAILURE: Date did not persist!\n";
}
