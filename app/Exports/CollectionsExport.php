<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CollectionsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $collections;

    public function __construct($collections)
    {
        $this->collections = $collections;
    }

    public function collection()
    {
        return $this->collections;
    }

    public function headings(): array
    {
        return [
            'التاريخ',
            'شركة الشحن',
            'المبلغ',
            'ملاحظات',
        ];
    }

    public function map($collection): array
    {
        return [
            $collection->collection_date ? $collection->collection_date->format('Y-m-d') : '-',
            $collection->shippingCompany->name ?? '-',
            $collection->amount ?? 0,
            $collection->notes ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
