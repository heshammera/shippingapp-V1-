<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class TestInventoryMigration extends Command
{
    protected $signature = 'test:inventory-migration';
    protected $description = 'Test inventory migration step by step';

    public function handle()
    {
        try {
            // Test 1: Check if products table exists
            $this->info('Test 1: Checking products table...');
            $count = DB::table('products')->count();
            $this->info("✓ Products table exists with {$count} records");

            // Test 2: Try adding a simple nullable column
            $this->info(PHP_EOL . 'Test 2: Testing ALTER TABLE capability...');
            if (!Schema::hasColumn('products', 'test_migration_col')) {
                Schema::table('products', function ($table) {
                    $table->string('test_migration_col')->nullable();
                });
                $this->info('✓ Added test column successfully');
                
                // Cleanup
                Schema::table('products', function ($table) {
                    $table->dropColumn('test_migration_col');
                });
                $this->info('✓ Dropped test column successfully');
            }

            // Test 3: Check if SKU column already exists
            $this->info(PHP_EOL . 'Test 3: Checking for SKU column...');
            if (Schema::hasColumn('products', 'sku')) {
                $this->warn('! SKU column already exists - this might be causing the migration to fail');
                return;
            } else {
                $this->info('✓ SKU column does not exist');
            }

            // Test 4: Try adding SKU column
            $this->info(PHP_EOL . 'Test 4: Attempting to add SKU column...');
            Schema::table('products', function ($table) {
                $table->string('sku', 50)->nullable()->unique()->after('id');
            });
            $this->info('✓ SKU column added successfully!');
            
            $this->info(PHP_EOL . '✅ All tests passed! Migrations should work now.');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            $this->error('File: ' . $e->getFile() . ':' . $e->getLine());
            return 1;
        }
    }
}
