<?php

namespace Database\Factories;

use App\Models\AffiliatorType;
use Illuminate\Database\Eloquent\Factories\Factory;

class AffiliatorTypeFactory extends Factory
{
    protected $model = AffiliatorType::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'slug' => fake()->unique()->slug(2),
            'description' => fake()->sentence(),
            'benefits' => ['Benefit 1', 'Benefit 2'],
            'default_commission_rate' => fake()->randomFloat(2, 5, 20),
            'is_active' => true,
        ];
    }
}
