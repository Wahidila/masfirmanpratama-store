<?php

namespace Database\Seeders;

use App\Models\AffiliatorType;
use App\Models\CommissionSetting;
use Illuminate\Database\Seeder;

class CommissionSettingSeeder extends Seeder
{
    public function run(): void
    {
        $alumni = AffiliatorType::where('slug', 'alumni')->first();
        $nonAlumni = AffiliatorType::where('slug', 'non-alumni')->first();
        $peserta = AffiliatorType::where('slug', 'peserta')->first();

        $settings = [
            // Alumni rates
            ['affiliator_type_id' => $alumni->id, 'product_type' => 'course', 'rate' => 15.00, 'cooling_days' => 7],
            ['affiliator_type_id' => $alumni->id, 'product_type' => 'book', 'rate' => 12.00, 'cooling_days' => 7],
            // Non-alumni rates
            ['affiliator_type_id' => $nonAlumni->id, 'product_type' => 'course', 'rate' => 10.00, 'cooling_days' => 7],
            ['affiliator_type_id' => $nonAlumni->id, 'product_type' => 'book', 'rate' => 8.00, 'cooling_days' => 7],
            // Peserta rates
            ['affiliator_type_id' => $peserta->id, 'product_type' => 'course', 'rate' => 12.00, 'cooling_days' => 7],
            ['affiliator_type_id' => $peserta->id, 'product_type' => 'book', 'rate' => 10.00, 'cooling_days' => 7],
        ];

        foreach ($settings as $setting) {
            CommissionSetting::create($setting);
        }
    }
}
