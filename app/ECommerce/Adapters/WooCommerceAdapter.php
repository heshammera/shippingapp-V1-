<?php

namespace App\ECommerce\Adapters;

use App\ECommerce\ECommerceInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WooCommerceAdapter implements ECommerceInterface
{
    protected $url;
    protected $consumerKey;
    protected $consumerSecret;

    public function __construct(string $url, string $key, string $secret)
    {
        $this->url = rtrim($url, '/') . '/wp-json/wc/v3/';
        $this->consumerKey = $key;
        $this->consumerSecret = $secret;
    }

    protected function request()
    {
        return Http::withBasicAuth($this->consumerKey, $this->consumerSecret);
    }

    public function getOrders(array $filters = []): array
    {
        try {
            $response = $this->request()->get($this->url . 'orders', $filters);
            return $response->json();
        } catch (\Exception $e) {
            Log::error("WooCommerce error: " . $e->getMessage());
            return [];
        }
    }

    public function getOrder(string $orderId): array
    {
        try {
            $response = $this->request()->get($this->url . "orders/{$orderId}");
            return $response->json();
        } catch (\Exception $e) {
            Log::error("WooCommerce error: " . $e->getMessage());
            return [];
        }
    }

    public function updateStatus(string $orderId, string $status): bool
    {
        try {
            $response = $this->request()->put($this->url . "orders/{$orderId}", [
                'status' => $status,
            ]);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("WooCommerce error: " . $e->getMessage());
            return false;
        }
    }

    public function addTracking(string $orderId, string $trackingNumber, string $carrier): bool
    {
        try {
            // Usually WooCommerce needs a plugin like "Shipment Tracking" for a dedicated API
            // Otherwise we add it as an order note
            $response = $this->request()->post($this->url . "orders/{$orderId}/notes", [
                'note' => "Shipped via {$carrier}. Tracking Number: {$trackingNumber}",
                'customer_note' => true,
            ]);
            return $response->successful();
        } catch (\Exception $e) {
            Log::error("WooCommerce error: " . $e->getMessage());
            return false;
        }
    }
}
