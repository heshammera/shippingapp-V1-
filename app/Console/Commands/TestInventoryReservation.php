<?php

namespace App\Console\Commands;

use App\Models\Shipment;
use App\Services\InventoryService;
use Illuminate\Console\Command;

class TestInventoryReservation extends Command
{
    protected $signature = 'inventory:test-reservation {shipment_id?}';
    protected $description = 'Test inventory reservation for a shipment';

    public function handle()
    {
        $shipmentId = $this->argument('shipment_id');
        
        if ($shipmentId) {
            $shipment = Shipment::find($shipmentId);
        } else {
            $shipment = Shipment::first();
        }
        
        if (!$shipment) {
            $this->error('No shipment found!');
            return 1;
        }
        
        $this->info("Testing reservation for Shipment #{$shipment->id}");
        $this->info("Tracking: {$shipment->tracking_number}");
        $this->info("Shipping Company ID: {$shipment->shipping_company_id}");
        $this->info("Products count: {$shipment->products->count()}");
        
        if ($shipment->products->count() === 0) {
            $this->warn('⚠️ This shipment has NO products attached!');
            return 1;
        }
        
        $this->newLine();
        $this->info('Products in shipment:');
        foreach ($shipment->products as $product) {
            $this->line("  - {$product->name} | Color: {$product->pivot->color} | Size: {$product->pivot->size} | Qty: {$product->pivot->quantity}");
            
            // Check if variant exists
            $variant = \App\Models\ProductVariant::where('product_id', $product->id)
                ->where('color', $product->pivot->color)
                ->where('size', $product->pivot->size)
                ->first();
            
            if ($variant) {
                $this->info("    ✓ Variant found: Stock={$variant->stock_quantity}, Reserved={$variant->reserved_quantity}, Available={$variant->available_quantity}");
            } else {
                $this->error("    ✗ NO VARIANT FOUND! Cannot reserve stock.");
                $this->warn("    → You need to create a variant for: Product #{$product->id}, Color: {$product->pivot->color}, Size: {$product->pivot->size}");
            }
        }
        
        $this->newLine();
        
        if ($this->confirm('Do you want to manually trigger reservation?', false)) {
            try {
                $service = app(InventoryService::class);
                $service->reserveForShipment($shipment, 'Manual test via command');
                
                $this->info('✅ Reservation successful! Check stock_movements table.');
            } catch (\Exception $e) {
                $this->error('❌ Reservation failed: ' . $e->getMessage());
                $this->warn('Check logs/laravel.log for detailed error trace.');
                return 1;
            }
        }
        
        return 0;
    }
}
