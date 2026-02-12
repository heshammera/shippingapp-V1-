<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateInventoryTablesManually extends Command
{
    protected $signature = 'inventory:create-tables';
    protected $description = 'Manually create inventory tables (workaround for migration issues)';

    public function handle()
    {
        try {
            $this->info('Creating inventory tables manually...');
            
            // 1. Add columns to products table
            $this->info('Step 1: Adding columns to products table...');
            DB::statement("ALTER TABLE products ADD COLUMN IF NOT EXISTS sku VARCHAR(50) UNIQUE");
            DB::statement("ALTER TABLE products ADD COLUMN IF NOT EXISTS description TEXT");
            DB::statement("ALTER TABLE products ADD COLUMN IF NOT EXISTS track_inventory BOOLEAN DEFAULT TRUE");
            DB::statement("ALTER TABLE products ADD COLUMN IF NOT EXISTS category_id BIGINT");
            DB::statement("ALTER TABLE products ADD COLUMN IF NOT EXISTS is_active BOOLEAN DEFAULT TRUE");
            $this->info('✓ Products table updated');
            
            // 2. Create product_variants table
            $this->info(PHP_EOL . 'Step 2: Creating product_variants table...');
            DB::statement("
                CREATE TABLE IF NOT EXISTS product_variants (
                    id BIGSERIAL PRIMARY KEY,
                    product_id BIGINT NOT NULL,
                    sku VARCHAR(50) UNIQUE NOT NULL,
                    color VARCHAR(50),
                    size VARCHAR(50),
                    stock_quantity INTEGER DEFAULT 0,
                    reserved_quantity INTEGER DEFAULT 0,
                    low_stock_threshold INTEGER DEFAULT 5,
                    is_unlimited BOOLEAN DEFAULT FALSE,
                    barcode VARCHAR(50),
                    created_at TIMESTAMP,
                    updated_at TIMESTAMP,
                    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
                    UNIQUE (product_id, color, size)
                );
            ");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_product_variants_product ON product_variants(product_id, color, size)");
            $this->info('✓ product_variants table created');
            
            // 3. Create stock_movements table
            $this->info(PHP_EOL . 'Step 3: Creating stock_movements table...');
            DB::statement("
                CREATE TABLE IF NOT EXISTS stock_movements (
                    id BIGSERIAL PRIMARY KEY,
                    variant_id BIGINT NOT NULL,
                    shipment_id BIGINT,
                    movement_type VARCHAR(20) NOT NULL,
                    quantity_change INTEGER NOT NULL,
                    quantity_before INTEGER NOT NULL,
                    quantity_after INTEGER NOT NULL,
                    reason TEXT,
                    reference_number VARCHAR(50),
                    user_id BIGINT,
                    ip_address VARCHAR(45),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (variant_id) REFERENCES product_variants(id) ON DELETE CASCADE,
                    FOREIGN KEY (shipment_id) REFERENCES shipments(id) ON DELETE CASCADE,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
                );
            ");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_stock_movements_variant ON stock_movements(variant_id, created_at)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_stock_movements_type ON stock_movements(movement_type, created_at)");
            DB::statement("CREATE INDEX IF NOT EXISTS idx_stock_movements_shipment ON stock_movements(shipment_id)");
            $this->info('✓ stock_movements table created');
            
            // 4. Mark migrations as run in migrations table
            $this->info(PHP_EOL . 'Step 4: Updating migrations table...');
            $timestamp = date('Y_m_d_His');
            DB::table('migrations')->insert([
                ['migration' => '2026_02_02_041554_add_inventory_fields_to_products_table', 'batch' => DB::table('migrations')->max('batch') + 1],
                ['migration' => '2026_02_02_041600_create_product_variants_table', 'batch' => DB::table('migrations')->max('batch') + 1],
                ['migration' => '2026_02_02_041606_create_stock_movements_table', 'batch' => DB::table('migrations')->max('batch') + 1],
                ['migration' => '2026_02_02_042024_migrate_inventories_to_product_variants', 'batch' => DB::table('migrations')->max('batch') + 1],
            ]);
            $this->info('✓ Migrations table updated');
            
            $this->info(PHP_EOL . '✅ All tables created successfully!');
            $this->info('You can now test the inventory system.');
            
        } catch (\Exception $e) {
            $this->error('❌ Error: ' . $e->getMessage());
            return 1;
        }
    }
}
