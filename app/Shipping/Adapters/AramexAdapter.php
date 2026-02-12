<?php

namespace App\Shipping\Adapters;

use App\Models\Shipment;
use App\Models\ShippingCompany;
use App\Shipping\CarrierInterface;
use Exception;
use Illuminate\Support\Facades\Log;
use SoapClient;

class AramexAdapter implements CarrierInterface
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
        try {
            if (empty($this->config['username'])) {
                throw new Exception("Aramex credentials missing (username).");
            }

            $payload = AramexSOAPService::buildCreateShipmentPayload($shipment, $this->config);
            
            Log::info("Aramex Request Payload: ", $payload);

            // In real production, uncomment below:
            // $client = new SoapClient($this->config['wsdl_url'] ?? 'https://ws.aramex.net/ShippingAPI.V2/Shipping/Service_1_0.svc?wsdl');
            // $response = $client->CreateShipments($payload);
            // if ($response->HasErrors) { throw new Exception($response->Notifications->Notification->Message); }

            // Simulation of a successful response
            return [
                'success' => true,
                'tracking_number' => 'ARM' . rand(100000, 999999), 
                'reference' => 'ARAMEX-' . $shipment->id,
            ];
        } catch (Exception $e) {
            Log::error("Aramex Error: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function cancelShipment(Shipment $shipment): bool
    {
        Log::info("Aramex: Cancelling shipment #{$shipment->external_tracking_number}");
        return true;
    }

    public function trackShipment(Shipment $shipment): array
    {
        return [
            'status' => 'In Transit',
            'location' => 'Dubai Hub',
            'history' => [
                ['date' => now()->subDay(), 'activity' => 'Shipped from Origin'],
            ]
        ];
    }

    public function getLabel(Shipment $shipment): string
    {
        return "https://aramex.com/label/" . $shipment->external_tracking_number;
    }
}
