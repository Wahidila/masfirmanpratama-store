<?php

namespace Database\Seeders;

use App\Models\AffiliateEvent;
use App\Models\Affiliator;
use App\Models\CommissionSetting;
use App\Models\Material;
use App\Models\ReferralCode;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AffiliateSeeder extends Seeder
{
    /**
     * Seed data demo affiliate system.
     *
     * Membuat: 1 commission_settings global, 3 affiliator demo (1 tiap tipe),
     * 1 referral_code per affiliator, 3 materials, 1 event aktif.
     */
    public function run(): void
    {
        // Commission setting global 10%
        CommissionSetting::updateOrCreate(
            ['scope' => 'global'],
            ['rate_percent' => 10.00, 'min_payout' => 50000.00]
        );

        // 3 Affiliator demo — 1 tiap tipe
        $affiliators = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@demo.test',
                'password' => Hash::make('password'),
                'phone' => '081234567890',
                'type' => 'alumni',
                'status' => 'active',
                'bank_name' => 'BCA',
                'bank_account' => '1234567890',
                'bank_holder' => 'Budi Santoso',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Siti Rahayu',
                'email' => 'siti@demo.test',
                'password' => Hash::make('password'),
                'phone' => '081298765432',
                'type' => 'non_alumni',
                'status' => 'active',
                'bank_name' => 'BNI',
                'bank_account' => '0987654321',
                'bank_holder' => 'Siti Rahayu',
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Andi Pratama',
                'email' => 'andi@demo.test',
                'password' => Hash::make('password'),
                'phone' => '081355566677',
                'type' => 'peserta',
                'status' => 'active',
                'bank_name' => 'BRI',
                'bank_account' => '1122334455',
                'bank_holder' => 'Andi Pratama',
                'email_verified_at' => now(),
            ],
        ];

        foreach ($affiliators as $data) {
            $affiliator = Affiliator::updateOrCreate(
                ['email' => $data['email']],
                $data
            );

            // 1 referral code per affiliator
            ReferralCode::updateOrCreate(
                ['affiliator_id' => $affiliator->id],
                ['code' => strtoupper(substr($affiliator->name, 0, 4)).$affiliator->id]
            );
        }

        // Materi marketing demo
        $materials = [
            ['title' => 'Banner Promosi Kelas AMC', 'description' => 'Banner ukuran 1200x628 untuk media sosial', 'file_path' => 'materials/banner-kelas-amc.png', 'type' => 'banner'],
            ['title' => 'Template WhatsApp Referral', 'description' => 'Template pesan WA untuk mengajak teman bergabung', 'file_path' => 'materials/template-wa-referral.txt', 'type' => 'template_wa'],
            ['title' => 'Brosur Program Affiliate', 'description' => 'Brosur PDF penjelasan program affiliate dan benefit', 'file_path' => 'materials/brosur-affiliate.pdf', 'type' => 'brosur'],
        ];

        foreach ($materials as $material) {
            Material::updateOrCreate(
                ['title' => $material['title']],
                $material
            );
        }

        // Event aktif demo
        AffiliateEvent::updateOrCreate(
            ['title' => 'Tantangan Referral Juni 2026'],
            [
                'description' => 'Ajak teman sebanyak-banyaknya selama bulan Juni. Top 3 referrer dapat hadiah spesial!',
                'starts_at' => now()->startOfMonth(),
                'ends_at' => now()->endOfMonth(),
                'reward_note' => 'Juara 1: Rp 1.000.000 | Juara 2: Rp 500.000 | Juara 3: Rp 250.000',
                'status' => 'active',
            ]
        );
    }
}
