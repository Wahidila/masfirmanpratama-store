<?php

namespace Database\Factories;

use App\Models\Affiliator;
use App\Models\AffiliatorType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AffiliatorFactory extends Factory
{
    protected $model = Affiliator::class;

    public function definition(): array
    {
        return [
            'affiliator_type_id' => AffiliatorType::first()?->id ?? AffiliatorType::factory(),
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => 'password',
            'phone' => fake()->phoneNumber(),
            'bank_name' => fake()->randomElement(['BCA', 'BNI', 'Mandiri', 'BRI']),
            'bank_account_number' => fake()->numerify('##########'),
            'bank_account_name' => fake()->name(),
            'status' => 'active',
            'remember_token' => Str::random(10),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
            'approved_at' => null,
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'suspended',
        ]);
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
