<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Integration;
use App\Services\ECommerceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function handleWooCommerce(Request $request, $integrationId)
    {
        Log::info("Webhook received from WooCommerce for integration #{$integrationId}");

        $integration = Integration::findOrFail($integrationId);
        
        if (!$integration->is_active) {
            return response()->json(['message' => 'Integration is inactive'], 403);
        }

        // WooCommerce sends the order data directly in the request body
        $orderData = $request->all();

        if (empty($orderData['id'])) {
            return response()->json(['message' => 'Invalid order data'], 400);
        }

        $service = new ECommerceService();
        $success = $service->processOrder($integration, $orderData);

        if ($success) {
            return response()->json(['message' => 'Order processed successfully'], 201);
        }

        return response()->json(['message' => 'Order already exists or failed to process'], 200);
    }
}
