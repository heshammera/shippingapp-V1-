<?php

namespace App\ECommerce;

interface ECommerceInterface
{
    /**
     * Get or search orders from the platform
     */
    public function getOrders(array $filters = []): array;

    /**
     * Get a single order details
     */
    public function getOrder(string $orderId): array;

    /**
     * Update order status on the platform
     */
    public function updateStatus(string $orderId, string $status): bool;

    /**
     * Add tracking info to the order on the platform
     */
    public function addTracking(string $orderId, string $trackingNumber, string $carrier): bool;
}
