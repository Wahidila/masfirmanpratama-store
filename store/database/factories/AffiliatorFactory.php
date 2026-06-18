<?php

namespace Database\Factories;

use App\Models\Affiliator;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Affiliator>
 */
class AffiliatorFactory extends Factory
{
    protected $model = Affiliator::class;

    public function definition(): array
    {
        $namaDepan = fake('id_ID')->firstName();
        $namaBelakang = fake('id_ID')->lastName();

        return [
            'name' => $namaDepan.' '.$namaBelakang,
            'email' => fake('id_ID')->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'phone' => fake('id_ID')->phoneNumber(),
            'type' => fake()->randomElement(['alumni', 'non_alumni', 'peserta']),
            'status' => 'active',
            'bank_name' => fake()->randomElement(['BCA', 'BNI', 'BRI', 'Mandiri', 'BSI']),
            'bank_account' => fake()->numerify('##########'),
            'bank_holder' => $namaDepan.' '.$namaBelakang,
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'email_verified_at' => null,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
        ]);
    }

    public function alumni(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'alumni',
        ]);
    }

    public function nonAlumni(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'non_alumni',
        ]);
    }

    public function peserta(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'peserta',
        ]);
    }
}
