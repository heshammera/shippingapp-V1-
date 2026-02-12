<?php

namespace App\Shipping\Adapters;

use App\Models\Shipment;
use App\Models\ShippingCompany;
use App\Shipping\CarrierInterface;

class InternalAdapter implements CarrierInterface
{
    protected $company;

    public function __construct(ShippingCompany $company)
    {
        $this->company = $company;
    }

    public function createShipment(Shipment $shipment): array
    {
        return [
            'success' => true,
            'tracking_number' => $shipment->tracking_number,
            'reference' => 'INTERNAL-' . $shipment->id,
        ];
    }

    public function cancelShipment(Shipment $shipment): bool
    {
        return true;
    }

    public function trackShipment(Shipment $shipment): array
    {
        return [
            'status' => $shipment->status?->name ?? 'Unknown',
            'history' => [],
        ];
    }

    public function getLabel(Shipment $shipment): string
    {
        return route('shipments.label', $shipment->id);
    }
}
