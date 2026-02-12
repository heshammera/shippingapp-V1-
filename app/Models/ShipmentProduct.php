<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Services\InventoryService;
use App\Models\Setting;
use App\Models\ShippingCompany;

class ShipmentProduct extends Pivot
{
    protected $table = 'shipment_product';
    
    public $timestamps = true;

    protected static function booted()
    {
        static::created(function ($pivot) {
            $shipment = Shipment::find($pivot->shipment_id);
            if (!$shipment) return;
            
            // Check if shipment is assigned to external company
            $settings = Setting::first();
            $defaultId = (int) ($settings->default_shipping_company_id ?? 0);
            $shippingId = (int) $shipment->shipping_company_id;
            
            // If external company, reserve this specific item
            if ($shippingId !== 0 && $shippingId !== $defaultId) {
                $company = ShippingCompany::find($shippingId);
                $affectsInventory = $company ? (bool) $company->affects_inventory : true;
                
                if ($affectsInventory) {
                    try {
                        \Log::info("ShipmentProduct: Reserving item for shipment #{$shipment->id}, Product #{$pivot->product_id}");
                        
                        app(InventoryService::class)->reserveItem(
                            $shipment,
                            $pivot->product_id,
                            $pivot->color,
                            $pivot->size,
                            $pivot->quantity,
                            'إضافة منتج لشحنة خارجية'
                        );
                    } catch (\Exception $e) {
                        \Log::error("Failed to reserve single item for shipment #{$shipment->id}: " . $e->getMessage());
                    }
                }
            }
        });
    }
    
    public function shipment()
    {
        return $this->belongsTo(Shipment::class);
    }
    
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
