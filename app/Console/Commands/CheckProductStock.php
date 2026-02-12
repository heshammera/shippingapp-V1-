<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;

class CheckProductStock extends Command
{
    protected $signature = 'inventory:check-product {name?}';
    protected $description = 'Check stock details for a product';

    public function handle()
    {
        $name = $this->argument('name') ?? 'منتج تجربة1';
        $this->info("🔍 Searching for product: $name");
        
        $product = Product::where('name', 'LIKE', "%$name%")->with('variants')->first();
        
        if (!$product) {
            $this->error("❌ Product not found!");
            return;
        }
        
        $this->info("📦 Product: {$product->name} (ID: {$product->id})");
        $this->newLine();
        
        $headers = ['Variant', 'Stock (Physical)', 'Reserved (Held)', 'Available (Free)'];
        $data = [];
        
        foreach ($product->variants as $variant) {
            $data[] = [
                $variant->full_name,
                $variant->stock_quantity,
                $variant->reserved_quantity,
                $variant->available_quantity,
            ];
        }
        
        $this->table($headers, $data);
        
        $this->newLine();
        $this->info("∑ Product Totals (Calculated from Accessors):");
        $this->line("  - Total Stock (Physical): " . $product->total_stock);
        $this->line("  - Reserved Stock: " . $product->reserved_stock);
        $this->line("  - Available Stock: " . $product->available_stock);
        
        $this->newLine();
        $this->info("🧮 Math Check:");
        $calcAvailable = $product->total_stock - $product->reserved_stock;
        $this->line("  {$product->total_stock} (Total) - {$product->reserved_stock} (Reserved) = {$calcAvailable} (Should be Available)");
        
        if ($calcAvailable === $product->available_stock) {
            $this->info("  ✅ Math matches.");
        } else {
            $this->error("  ❌ Math MISMATCH!");
        }
    }
}
