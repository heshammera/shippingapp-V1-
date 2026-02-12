<?php

namespace App\Console\Commands;

use App\Models\Shipment;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateMissingVariants extends Command
{
    protected $signature = 'inventory:create-missing-variants {--dry-run : Show what would be created without actually creating}';
    protected $description = 'Create missing product variants based on existing shipments';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
        }
        
        $this->info('Scanning shipments for missing variants...');
        
        $shipments = Shipment::with('products')->get();
        $missingVariants = [];
        $created = 0;
        $skipped = 0;
        
        foreach ($shipments as $shipment) {
            foreach ($shipment->products as $product) {
                $color = $product->pivot->color;
                $size = $product->pivot->size;
                
                // Check if variant exists
                $exists = ProductVariant::where('product_id', $product->id)
                    ->where('color', $color)
                    ->where('size', $size)
                    ->exists();
                
                if (!$exists) {
                    $key = "{$product->id}|{$color}|{$size}";
                    
                    if (!isset($missingVariants[$key])) {
                        $missingVariants[$key] = [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'color' => $color,
                            'size' => $size,
                        ];
                    }
                }
            }
        }
        
        if (empty($missingVariants)) {
            $this->info('✅ No missing variants found! All shipments have corresponding variants.');
            return 0;
        }
        
        $this->warn('Found ' . count($missingVariants) . ' missing variants:');
        $this->newLine();
        
        foreach ($missingVariants as $data) {
            $this->line("  📦 {$data['product_name']} | Color: {$data['color']} | Size: {$data['size']}");
            
            if (!$dryRun) {
                try {
                    $sku = $this->generateSKU($data['product_id'], $data['product_name'], $data['color'], $data['size']);
                    
                    ProductVariant::create([
                        'product_id' => $data['product_id'],
                        'sku' => $sku,
                        'color' => $data['color'],
                        'size' => $data['size'],
                        'stock_quantity' => 0, // Start with 0, admin will adjust
                        'reserved_quantity' => 0,
                        'low_stock_threshold' => 5,
                        'is_unlimited' => false,
                    ]);
                    
                    $this->info("     ✓ Created with SKU: {$sku}");
                    $created++;
                } catch (\Exception $e) {
                    $this->error("     ✗ Failed: " . $e->getMessage());
                    $skipped++;
                }
            }
        }
        
        $this->newLine();
        
        if ($dryRun) {
            $this->info('💡 Run without --dry-run to create these variants');
            $this->warn('⚠️ Note: All variants will be created with stock_quantity = 0');
            $this->warn('    You need to manually adjust stock levels in the admin panel');
        } else {
            $this->info("✅ Created {$created} variants");
            if ($skipped > 0) {
                $this->warn("⚠️ Skipped {$skipped} variants due to errors");
            }
            $this->newLine();
            $this->info('Next steps:');
            $this->line('1. Go to admin panel → المنتجات والمخزون');
            $this->line('2. For each product → Variants tab');
            $this->line('3. Update stock quantities manually');
        }
        
        return 0;
    }
    
    private function generateSKU($productId, $productName, $color, $size): string
    {
        // Clean and shorten product name
        $productPart = substr(preg_replace('/[^A-Za-z0-9]/', '', $productName), 0, 4);
        if (empty($productPart)) {
            $productPart = 'P' . $productId;
        }
        
        // Clean color and size
        $colorPart = substr(preg_replace('/[^A-Za-z0-9]/', '', $color), 0, 3);
        $sizePart = substr(preg_replace('/[^A-Za-z0-9]/', '', $size), 0, 3);
        
        // Fallback to hash if Arabic
        if (empty($colorPart)) {
            $colorPart = 'C' . abs(crc32($color) % 100);
        }
        if (empty($sizePart)) {
            $sizePart = 'S' . abs(crc32($size) % 100);
        }
        
        return strtoupper($productPart . '-' . $colorPart . '-' . $sizePart . '-' . substr(uniqid(), -4));
    }
}
