<?php

namespace App\Observers;

use App\Models\Shipment;
use App\Models\Setting;
use App\Models\ShippingCompany;
use App\Services\InventoryService;

class ShipmentObserver
{
    /**
     * Handle the Shipment "created" event
     */
    public function created(Shipment $shipment): void
    {
        $settings = Setting::first();
        if (!$settings) return;

        $service = app(InventoryService::class);
        $defaultId = (int) ($settings->default_shipping_company_id ?? 0);
        $shippingId = (int) $shipment->shipping_company_id;

        // If shipment is created with external company, reserve immediately
        if ($shippingId !== 0 && $shippingId !== $defaultId) {
            $company = ShippingCompany::find($shippingId);
            $affectsInventory = $company ? (bool) $company->affects_inventory : true;
            
            if ($affectsInventory) {
                try {
                    \Log::info("ShipmentObserver: Reserving stock for newly created shipment #{$shipment->id} with external company #{$shippingId}");
                    $service->reserveForShipment($shipment, 'شحنة جديدة مع شركة خارجية');
                } catch (\Exception $e) {
                    \Log::error("Failed to reserve stock for new shipment #{$shipment->id}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Handle the Shipment "updated" event
     */
    public function updated(Shipment $shipment): void
    {
        $settings = Setting::first();
        if (!$settings) return;

        $service = app(InventoryService::class);

        // (1) Shipping Company Changed: Reserve/Release Logic
        if ($shipment->wasChanged('shipping_company_id')) {
            $beforeId = (int) $shipment->getOriginal('shipping_company_id');
            $afterId = (int) $shipment->shipping_company_id;
            $defaultId = (int) ($settings->default_shipping_company_id ?? 0);

            $beforeCompany = $beforeId ? ShippingCompany::find($beforeId) : null;
            $afterCompany = $afterId ? ShippingCompany::find($afterId) : null;

            $beforeAffects = $beforeCompany ? (bool) $beforeCompany->affects_inventory : true;
            $afterAffects = $afterCompany ? (bool) $afterCompany->affects_inventory : true;

            $fromDefaultToExternal = ($beforeId === $defaultId && $afterId !== $defaultId);
            $fromExternalToDefault = ($beforeId !== $defaultId && $afterId === $defaultId);

            // Reserve when moving to external company
            if ($fromDefaultToExternal && $afterAffects) {
                try {
                    $service->reserveForShipment($shipment, 'شحنة منقولة لشركة خارجية');
                } catch (\Exception $e) {
                    \Log::error("Failed to reserve stock for shipment #{$shipment->id}: " . $e->getMessage());
                }
            }

            // Release when returning to default company
            if ($fromExternalToDefault && $beforeAffects) {
                try {
                    $service->releaseForShipment($shipment, 'شحنة معادة للشركة الافتراضية');
                } catch (\Exception $e) {
                    \Log::error("Failed to release stock for shipment #{$shipment->id}: " . $e->getMessage());
                }
            }
        }

        // (2) Status Changed: Deduct/Return Logic + Accounting
        if ($shipment->wasChanged('status_id')) {
            $status = $shipment->status;
            
            if (!$status) return;

            // Delivered (Revenue + Agent Collection)
            if ($status->code === 'delivered') {
                try {
                    $service->deductForShipment($shipment, 'تم توصيل الشحنة للعميل');
                    
                    // Trigger Accounting
                    app(\App\Services\AccountingService::class)->createShipmentEntry($shipment);

                    // Update delivered timestamp  
                    if (is_null($shipment->delivered_at)) {
                        $shipment->forceFill(['delivered_at' => now()])->saveQuietly();
                    }
                } catch (\Exception $e) {
                    \Log::error("Failed to process delivered shipment #{$shipment->id}: " . $e->getMessage());
                }
            }

            // Returned (Restock)
            if ($status->code === 'returned') {
                try {
                    $service->returnToStockOnReturn($shipment, 'تم إرجاع الشحنة من العميل');
                    
                    // Update returned timestamp
                    if (is_null($shipment->returned_at)) {
                        $shipment->forceFill(['returned_at' => now()])->saveQuietly();
                    }
                } catch (\Exception $e) {
                    \Log::error("Failed to process returned shipment #{$shipment->id}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Helper to get IDs from settings (handling backward compatibility)
     */
    private function getSettingIds(Setting $settings, string $singleKey, string $multiKey): array
    {
        // Check for new multi-select key first (if we add it later)
        if (isset($settings->$multiKey)) {
            $val = json_decode($settings->$multiKey, true);
            if (is_array($val)) return array_map('intval', $val);
        }

        // Check for existing single key (stored in 'value' column usually if using Setting model helper, 
        // but here $settings is a Row from first(). We need to be careful how we accessed it)
        // In the original code: $settings = Setting::first();
        // The Setting model has 'key' and 'value'. 
        // Calling $settings->delivered_status_id works ONLY if the column exists or via accessor. 
        // BUT Setting model shown has only 'key', 'value', 'description' columns!
        // The original code $settings->delivered_status_id WAS WRONG if it trusted Setting::first() to be a row with that column.
        // Wait, looking at Setting::first(), it returns the FIRST ROW of settings table (e.g. key='company_name').
        // It does NOT return an object with all settings as properties unless there's a specific logic.
        
        // CORRECTION: The existing code $settings = Setting::first(); is likely WRONG/BUGGY if 'settings' table is EAV (Key-Value).
        // If Setting::first() returns just one row (e.g. company_name), then $settings->delivered_status_id would be null/undefined!
        // That explains why it wasn't working!
        
        // We must fetch values using Setting::getValue().
        
        $singleVal = Setting::getValue($singleKey);
        // Also check if singleVal is actually a JSON array (if we saved it that way)
        $decoded = json_decode($singleVal, true);
        if (is_array($decoded)) {
            return array_map('intval', $decoded);
        }
        
        return $singleVal ? [(int)$singleVal] : [];
    }

    /**
     * Handle the Shipment "deleting" event
     */
    public function deleting(Shipment $shipment): void
    {
        // Release reserved stock if shipment is deleted
        try {
            app(InventoryService::class)->releaseForShipment($shipment, 'حذف الشحنة');
        } catch (\Exception $e) {
            \Log::error("Failed to release stock for deleted shipment #{$shipment->id}: " . $e->getMessage());
        }
    }
}
