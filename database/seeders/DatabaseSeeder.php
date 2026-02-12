<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            \Database\Seeders\RolesTableSeeder::class,
            \Database\Seeders\PermissionsTableSeeder::class,
            \Database\Seeders\UsersTableSeeder::class,
            \Database\Seeders\ModelHasRolesTableSeeder::class,
            \Database\Seeders\RoleHasPermissionsTableSeeder::class,
            \Database\Seeders\ShippingCompaniesTableSeeder::class,
            \Database\Seeders\DeliveryAgentsTableSeeder::class,
            \Database\Seeders\ShipmentStatusesTableSeeder::class,
            \Database\Seeders\ProductsTableSeeder::class,
            \Database\Seeders\ShipmentsTableSeeder::class,
            \Database\Seeders\ShipmentProductTableSeeder::class,
            \Database\Seeders\CollectionsTableSeeder::class,
            \Database\Seeders\ExpensesTableSeeder::class,
            // \Database\Seeders\ProductPricesTableSeeder::class,
            \Database\Seeders\AppSettingsSeeder::class,
            // Add other seeders if needed
        ]);

    }
}
