<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ShipmentStatus;

class ShipmentStatusSeeder extends Seeder
{
    public function run()
    {
        $mappings = [
            'تم التسليم' => 'delivered',
            'تسليم جزئي' => 'partial_return',
            'مرتجع' => 'returned',
            'مرتجع كلي' => 'returned',
            'تم التأكيد' => 'confirmed',
            'جديد' => 'pending',
            'قيد المعالجة' => 'processing',
            'لا يرد' => 'no_answer',
            'تأجيل' => 'rescheduled',
            'خرج للتسليم' => 'out_for_delivery',
        ];

        foreach ($mappings as $name => $code) {
            ShipmentStatus::where('name', 'LIKE', "%$name%")->update(['code' => $code]);
        }
        
        // Ensure default statuses exist if not
        $defaults = [
            ['name' => 'تم التسليم', 'code' => 'delivered', 'color' => 'success', 'sort_order' => 10],
            ['name' => 'مرتجع', 'code' => 'returned', 'color' => 'danger', 'sort_order' => 90],
            ['name' => 'جديد', 'code' => 'pending', 'color' => 'gray', 'sort_order' => 1, 'is_default' => true],
        ];

        foreach ($defaults as $status) {
            ShipmentStatus::updateOrCreate(
                ['code' => $status['code']],
                $status
            );
        }
    }
}
