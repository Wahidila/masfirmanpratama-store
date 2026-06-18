<?php

namespace Database\Factories;

use App\Models\AffiliatorType;
use App\Models\CommissionSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CommissionSetting>
 */
class CommissionSettingFactory extends Factory
{
    protected $model = CommissionSetting::class;

    public function definition(): array
    {
        return [
            'affiliator_type_id' => null,
            'product_type' => null,
            'rate' => fake()->randomFloat(2, 5, 20),
            'min_amount' => 0,
            'cooling_days' => 7,
            'is_active' => true,
        ];
    }

    /**
     * Setting untuk affiliator type tertentu.
     */
    public function forType(AffiliatorType $type): static
    {
        return $this->state(fn (array $attributes) => [
            'affiliator_type_id' => $type->id,
        ]);
    }

    /**
     * Setting untuk product type tertentu.
     */
    public function forProduct(string $productType): static
    {
        return $this->state(fn (array $attributes) => [
            'product_type' => $productType,
        ]);
    }

    /**
     * Setting global (fallback).
     */
    public function global(): static
    {
        return $this->state(fn (array $attributes) => [
            'affiliator_type_id' => null,
            'product_type' => null,
        ]);
    }
}
