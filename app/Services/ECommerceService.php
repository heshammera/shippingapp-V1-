<?php

namespace App\Services;

use App\Models\Integration;
use App\Models\Shipment;
use App\Models\ShipmentStatus;
use App\Models\Product;
use App\ECommerce\Adapters\WooCommerceAdapter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ECommerceService
{
    public function syncOrders(Integration $integration)
    {
        if ($integration->platform !== 'woocommerce') {
            return 0;
        }

        $creds = $integration->credentials;
        $adapter = new WooCommerceAdapter($integration->url, $creds['consumer_key'], $creds['consumer_secret']);

        $orders = $adapter->getOrders(['status' => 'processing']);
        $importedCount = 0;

        foreach ($orders as $orderData) {
            if ($this->processOrder($integration, $orderData)) {
                $importedCount++;
            }
        }

        $integration->update(['last_sync_at' => now()]);
        return $importedCount;
    }

    public function processOrder(Integration $integration, array $orderData): bool
    {
        $prefix = match($integration->platform) {
            'woocommerce' => 'WC-',
            'shopify' => 'SH-',
            default => 'EXT-',
        };

        $extRef = $prefix . $orderData['id'];

        // Check if order already exists
        if (Shipment::where('external_reference', $extRef)->exists()) {
            return false;
        }

        DB::beginTransaction();
        try {
            $status = ShipmentStatus::where('code', 'pending')->first();

            $shipment = Shipment::create([
                'customer_name' => ($orderData['billing']['first_name'] ?? '') . ' ' . ($orderData['billing']['last_name'] ?? ''),
                'customer_phone' => $orderData['billing']['phone'] ?? '',
                'customer_address' => ($orderData['billing']['address_1'] ?? '') . ', ' . ($orderData['billing']['city'] ?? ''),
                'governorate' => $orderData['billing']['state'] ?? '',
                'total_amount' => $orderData['total'] ?? 0,
                'shipping_price' => $orderData['shipping_total'] ?? 0,
                'status_id' => $status?->id,
                'external_reference' => $extRef,
                'tracking_number' => 'TRK-' . strtoupper(uniqid()),
                'notes' => $orderData['customer_note'] ?? ($orderData['note'] ?? ''),
            ]);

            foreach ($orderData['line_items'] ?? [] as $item) {
                $product = Product::where('sku', $item['sku'])->first() ?? Product::where('name', $item['name'])->first();
                
                if ($product) {
                    $shipment->products()->attach($product->id, [
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'color' => '', 
                        'size' => '',
                    ]);
                }
            }

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to import external order #{$orderData['id']}: " . $e->getMessage());
            return false;
        }
    }
}
