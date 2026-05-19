<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $password = env('ADMIN_SEED_PASSWORD', 'admin123');

        Admin::updateOrCreate(
            ['email' => 'admin@masfirmanpratama.com'],
            [
                'name' => 'Admin Firman Pratama',
                'password' => $password,
            ],
        );
    }
}
