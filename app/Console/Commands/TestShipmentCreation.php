<?php

namespace App\Console\Commands;

use App\Models\Shipment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Console\Command;

class TestShipmentCreation extends Command
{
    protected $signature = 'inventory:test-shipment-creation';
    protected $description = 'Test creating a shipment and verify stock reservation';

    public function handle()
    {
        $this->info('🧪 Testing Shipment Creation with Stock Reservation (Pivot Logic)');
        $this->newLine();
        
        // Find a product with variants
        $variant = ProductVariant::with('product')->first();
        
        if (!$variant) {
            $this->error('❌ No ProductVariants found! Create at least one variant first.');
            return 1;
        }
        
        $this->info("Found variant: {$variant->full_name}");
        $this->info("Current stock: {$variant->stock_quantity}, Reserved: {$variant->reserved_quantity}, Available: {$variant->available_quantity}");
        $this->newLine();
        
        // Get settings
        $settings = Setting::first();
        if (!$settings) {
            $this->error('❌ Settings not found!');
            return 1;
        }
        
        $defaultCompanyId = $settings->default_shipping_company_id;
        $externalCompanyId = \App\Models\ShippingCompany::where('id', '!=', $defaultCompanyId)->first()?->id;
        
        if (!$externalCompanyId) {
            $this->error('❌ No external shipping company found!');
            return 1;
        }
        
        $this->info("Default Company ID: {$defaultCompanyId}");
        $this->info("External Company ID: {$externalCompanyId}");
        $this->newLine();
        
        if (!$this->confirm('Create a test shipment?', true)) {
            return 0;
        }
        
        // Create test shipment with external company
        $this->info('Creating shipment...');
        
        try {
            $shipment = Shipment::create([
                'tracking_number' => 'TEST-' . time(),
                'customer_name' => 'Test Customer',
                'customer_phone' => '01234567890',
                'customer_address' => 'Test Address',
                'customer_city' => 'Cairo',
                'product_name' => $variant->product->name, // Required field
                'shipping_company_id' => $externalCompanyId, // External company
                'status_id' => $settings->default_status_id ?? 1,
                'total_amount' => 100,
                'quantity' => 2,
            ]);
            
            $this->info("✅ Shipment #{$shipment->id} created with external company");
            
            $this->info('Attaching product...');
            // Attach product - This should trigger ShipmentProduct::created
            $shipment->products()->attach($variant->product_id, [
                'quantity' => 2,
                'color' => $variant->color,
                'size' => $variant->size,
                'price' => $variant->product->price ?? 100,
            ]);
            
            $this->info('Product attached.');
            
        } catch (\Exception $e) {
            $this->error("❌ Creation Failed: " . $e->getMessage());
            return 1;
        }
        
        $this->newLine();
        
        // Refresh variant to get updated values
        $variant->refresh();
        
        $this->info('📊 Stock Status After Attach:');
        $this->line("  Stock Quantity: {$variant->stock_quantity}");
        $this->line("  Reserved: {$variant->reserved_quantity}");
        $this->line("  Available: {$variant->available_quantity}");
        $this->newLine();
        
        // Check stock movements
        $movements = \App\Models\StockMovement::where('shipment_id', $shipment->id)->get();
        
        if ($movements->count() > 0) {
            $this->info("✅ {$movements->count()} stock movement(s) created:");
            foreach ($movements as $movement) {
                $this->line("  - Type: {$movement->movement_type}, Qty: {$movement->quantity_change}, Reason: {$movement->reason}");
            }
        } else {
            $this->error('❌ NO stock movements created! Pivot Observer failed.');
        }
        
        $this->newLine();
        
        if ($this->confirm('Delete test shipment?', true)) {
            $shipment->delete();
            $variant->refresh();
            
            $this->info('🗑️  Shipment deleted');
            $this->info("Stock after deletion: {$variant->stock_quantity}, Reserved: {$variant->reserved_quantity}");
        }
        
        return 0;
    }
}
