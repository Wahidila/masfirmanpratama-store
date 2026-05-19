<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Order penting:
     * 1. AdminSeeder            — referenced by OrderPayment.verified_by (FK)
     * 2. SettingsSeeder         — k/v store, tidak punya FK
     * 3. ProductSeeder          — referenced by OrderItem.product_id + InstallmentScheme.product_id
     * 4. InstallmentSchemeSeeder — depend on Product (skema 12x untuk kelas reguler)
     * 5. OrderSeeder            — depend on Product, ngerangkai Order + OrderItem + OrderPayment
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            SettingsSeeder::class,
            ProductSeeder::class,
            InstallmentSchemeSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
