<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ShipmentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $shipments;

    public function __construct($shipments)
    {
        $this->shipments = $shipments;
    }

    public function collection()
    {
        return $this->shipments;
    }

    public function headings(): array
    {
        return [
            'رقم التتبع',
            'شركة الشحن',
            'الحالة',
            'التكلفة',
            'نوع الشحن',
            'المندوب',
            'تاريخ الإنشاء',
        ];
    }

    public function map($shipment): array
    {
        return [
            $shipment->tracking_number ?? '-',
            $shipment->shippingCompany->name ?? '-',
            $shipment->status->name ?? '-',
            $shipment->cost ?? 0,
            $shipment->shipping_type ?? '-',
            $shipment->deliveryAgent->name ?? '-',
            $shipment->created_at ? $shipment->created_at->format('Y-m-d H:i') : '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
