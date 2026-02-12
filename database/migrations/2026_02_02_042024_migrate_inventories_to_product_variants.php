<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // TODO: Data migration temporarily disabled for debugging
        // Will be run manually via artisan command instead
        // See: php artisan tinker
        // Use: app(App\Services\InventoryMigrationService::class)->migrateData();
        
        /*
        $inventories = DB::table('inventories')->whereNull('deleted_at')->get();
        
        foreach ($inventories as $inventory) {
            $product = DB::table('products')->find($inventory->product_id);
            if (!$product) continue;
            
            $sku = $this->generateSKU($product->name, $inventory->color, $inventory->size);
            
            DB::table('product_variants')->insert([
                'product_id' => $inventory->product_id,
                'sku' => $sku,
                'color' => $inventory->color,
                'size' => $inventory->size,
                'stock_quantity' => $inventory->quantity ?? 0,
                'reserved_quantity' => 0,
                'low_stock_threshold' => $inventory->low_stock_alert ?? 5,
                'is_unlimited' => $inventory->is_unlimited ?? false,
                'created_at' => $inventory->created_at ?? now(),
                'updated_at' => $inventory->updated_at ?? now(),
            ]);
        }
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear product_variants to restore from backup if needed
        DB::table('product_variants')->truncate();
    }
    
    /**
     * Generate SKU for variant
     */
    private function generateSKU($productName, $color, $size): string
    {
        // For Arabic products, use a simpler approach
        // Format: PROD{product_id}-{COLOR_ABBREV}-{SIZE_ABBREV}-{TIMESTAMP}
        static $counter = 0;
        $counter++;
        
        // Extract first letters or use fallback
        $productPart = 'PROD' . $counter;
        
        // Handle color
        $colorPart = 'NOC';
        if ($color) {
            // Try to get first 3 alphanumeric chars, fallback to hash
            $cleaned = preg_replace('/[^A-Za-z0-9]/', '', $color);
            $colorPart = $cleaned ? strtoupper(substr($cleaned, 0, 3)) : 'COL' . abs(crc32($color) % 1000);
        }
        
        // Handle size  
        $sizePart = 'NOS';
        if ($size) {
            $cleaned = preg_replace('/[^A-Za-z0-9]/', '', $size);
            $sizePart = $cleaned ? strtoupper(substr($cleaned, 0, 3)) : 'SIZ' . abs(crc32($size) % 1000);
        }
        
        return $productPart . '-' . $colorPart . '-' . $sizePart . '-' . time() . mt_rand(100, 999);
    }
};
