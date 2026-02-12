<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CleanInventoryTables extends Command
{
    protected $signature = 'inventory:clean';
    protected $description = 'Clean up inventory-related tables for fresh migration';

    public function handle()
    {
        $this->info('Cleaning inventory tables...');
        
        try {
            // Drop tables if they exist
            if (Schema::hasTable('stock_movements')) {
                Schema::dropIfExists('stock_movements');
                $this->info('✓ Dropped stock_movements table');
            }
            
            if (Schema::hasTable('product_variants')) {
                Schema::dropIfExists('product_variants');
                $this->info('✓ Dropped product_variants table');
            }
            
            // Clean products table columns
            if (Schema::hasColumn('products', 'sku')) {
                Schema::table('products', function ($table) {
                    $table->dropColumn(['sku']);
                });
                $this->info('✓ Dropped sku from products');
            }
            
            if (Schema::hasColumn('products', 'description')) {
                Schema::table('products', function ($table) {
                    $table->dropColumn(['description']);
                });
                $this->info('✓ Dropped description from products');
            }
            
            if (Schema::hasColumn('products', 'track_inventory')) {
                Schema::table('products', function ($table) {
                    $table->dropColumn(['track_inventory']);
                });
                $this->info('✓ Dropped track_inventory from products');
            }
            
            if (Schema::hasColumn('products', 'category_id')) {
                Schema::table('products', function ($table) {
                    $table->dropColumn(['category_id']);
                });
                $this->info('✓ Dropped category_id from products');
            }
            
            if (Schema::hasColumn('products', 'is_active')) {
                Schema::table('products', function ($table) {
                    $table->dropColumn(['is_active']);
                });
                $this->info('✓ Dropped is_active from products');
            }
            
            $this->info(PHP_EOL . '✅ Database cleaned successfully! Ready for fresh migrations.');
            $this->info('Run: php artisan migrate');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
