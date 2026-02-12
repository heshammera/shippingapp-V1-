<?php

namespace App\Shipping\Adapters;

use App\Models\Shipment;
use App\Models\ShippingCompany;
use App\Shipping\CarrierInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DHLAdapter implements CarrierInterface
{
    protected $company;
    protected $config;

    public function __construct(ShippingCompany $company)
    {
        $this->company = $company;
        $this->config = $company->api_settings;
    }

    public function createShipment(Shipment $shipment): array
    {
        Log::info("DHL: Creating shipment via REST API #{$shipment->tracking_number}");

        // Simulate REST Response
        return [
            'success' => true,
            'tracking_number' => 'DHL' . rand(100000, 999999),
            'reference' => 'DHL-' . $shipment->id,
        ];
    }

    public function cancelShipment(Shipment $shipment): bool
    {
        return true;
    }

    public function trackShipment(Shipment $shipment): array
    {
        return ['status' => 'Delivered'];
    }

    public function getLabel(Shipment $shipment): string
    {
        return "https://dhl.com/labels/" . $shipment->external_tracking_number;
    }
}
