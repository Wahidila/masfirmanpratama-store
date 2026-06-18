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
            'code' => strtoupper(Str::random(8)),
            'clicks_count' => fake()->numberBetween(0, 500),
        ];
    }
}
