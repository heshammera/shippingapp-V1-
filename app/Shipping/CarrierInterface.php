<?php

namespace App\Shipping;

use App\Models\Shipment;

interface CarrierInterface
{
    /**
     * Create a shipment in the carrier system
     */
    public function createShipment(Shipment $shipment): array;

    /**
     * Cancel a shipment in the carrier system
     */
    public function cancelShipment(Shipment $shipment): bool;

    /**
     * Track a shipment from the carrier system
     */
    public function trackShipment(Shipment $shipment): array;

    /**
     * Get the shipping label URL or content
     */
    public function getLabel(Shipment $shipment): string;
}
