<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            AffiliatorTypeSeeder::class,
            WithdrawalMethodSeeder::class,
            CommissionSettingSeeder::class,
        ]);
    }
}
