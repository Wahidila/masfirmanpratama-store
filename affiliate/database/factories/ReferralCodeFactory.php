<?php

namespace Database\Factories;

use App\Models\Affiliator;
use App\Models\ReferralCode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<ReferralCode>
 */
class ReferralCodeFactory extends Factory
{
    protected $model = ReferralCode::class;

    public function definition(): array
    {
        return [
            'affiliator_id' => Affiliator::factory(),
            'code' => Str::upper(fake()->unique()->bothify('REF-####??')),
            'label' => fake()->words(2, true),
            'target_url' => fake()->url(),
            'is_active' => true,
        ];
    }

    /**
     * Referral code yang non-aktif.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
