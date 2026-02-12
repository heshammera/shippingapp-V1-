<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShipmentTemplateExport implements FromArray, WithHeadings, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        return [
            [
                'TRK-EXAMPLE-001', // tracking_number
                'محمد أحمد',       // customer_name
                '01012345678',     // customer_phone
                '01112345678',     // alternate_phone (optional)
                'القاهرة',         // governorate
                'مدينة نصر - بجوار النادي', // customer_address
                'تيشيرت بولو',     // product_name
                'احمر',            // color (optional)
                'L',               // size (optional)
                '2',               // quantity
                '150',             // selling_price
                'عميل مميز'        // notes
            ],
            // Second example row? maybe not needed, one is enough.
        ];
    }

    public function headings(): array
    {
        return [
            'tracking_number',
            'customer_name',
            'customer_phone',
            'alternate_phone',
            'governorate',
            'customer_address',
            'product_name',
            'color',
            'size',
            'quantity',
            'selling_price',
            'notes',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1    => ['font' => ['bold' => true]],
        ];
    }
}
