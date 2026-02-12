<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\ShipmentStatus;

class AppSettingsSeeder extends Seeder
{
    public function run(): void
    {
        // ضمّن المفاتيح الثلاثة (قيم افتراضية آمنة)
        $delivered = ShipmentStatus::where('is_delivered_semantic', true)->first();
        $returned  = ShipmentStatus::where('is_returned_semantic', true)->first();

        foreach ([
            'default_shipping_company_id' => null,
            'delivered_status_id'         => optional($delivered)->id,
            'returned_status_id'          => optional($returned)->id,
        ] as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
