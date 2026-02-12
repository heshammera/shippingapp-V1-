<?php

namespace App\Jobs;

use App\Models\Shipment;
use App\Models\ShipmentStatus;
use App\Shipping\CarrierFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TrackShipmentsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        // Get non-final shipments with external tracking
        $shipments = Shipment::whereNotNull('external_tracking_number')
            ->whereHas('status', function ($query) {
                $query->whereNotIn('code', ['delivered', 'returned', 'cancelled']);
            })
            ->with(['shippingCompany', 'status'])
            ->get();

        Log::info("Auto-Tracking: Checking " . $shipments->count() . " shipments.");

        foreach ($shipments as $shipment) {
            try {
                $carrier = CarrierFactory::make($shipment->shippingCompany);
                $tracking = $carrier->trackShipment($shipment);

                if (!empty($tracking['status'])) {
                    $this->updateStatus($shipment, $tracking['status']);
                }
            } catch (\Exception $e) {
                Log::error("Tracking Job Error for Shipment #{$shipment->id}: " . $e->getMessage());
            }
        }
    }

    protected function updateStatus(Shipment $shipment, string $externalStatus): void
    {
        // Simple mapping (can be expanded)
        $map = [
            'Delivered' => 'delivered',
            'Returned' => 'returned',
            'In Transit' => 'out_for_delivery',
            'Out for Delivery' => 'out_for_delivery',
        ];

        $targetCode = $map[$externalStatus] ?? null;

        if ($targetCode && $shipment->status?->code !== $targetCode) {
            $status = ShipmentStatus::where('code', $targetCode)->first();
            if ($status) {
                $shipment->update(['status_id' => $status->id]);
                Log::info("Shipment #{$shipment->tracking_number} auto-updated to {$targetCode}");
            }
        }
    }
}
