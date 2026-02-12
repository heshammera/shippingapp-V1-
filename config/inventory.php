<?php

return [
    'engine' => env('INVENTORY_ENGINE', 'v2'), // v1 or v2
    'v2' => [
        'shadow_mode' => (bool) env('INVENTORY_V2_SHADOW_MODE', true), // لو true يسجّل فقط بدون تعديل stock
    ],
    // لو true يسمح بتشغيل المنطق القديم (خصم/إرجاع على تغيير الحالة)
    'allow_legacy_status_based' => (bool) env('INVENTORY_ALLOW_LEGACY_STATUS_BASED', false),
];
