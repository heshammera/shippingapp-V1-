<?php

use Illuminate\Support\Facades\App;
use Spatie\Permission\Models\Role;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    Role::firstOrCreate(['name' => 'viewer']);
    Role::firstOrCreate(['name' => 'delivery_agent']);

    echo "✅ تمت إضافة الأدوار بنجاح.\n";
} catch (\Exception $e) {
    echo "❌ حدث خطأ: " . $e->getMessage() . "\n";
}
