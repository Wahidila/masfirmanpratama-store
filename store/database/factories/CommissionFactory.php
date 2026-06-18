<?php

namespace Database\Factories;

use App\Models\Affiliator;
use App\Models\Commission;
use App\Models\Order;
use App\Models\ReferralOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Commission>
 */
class CommissionFactory extends Factory
{
    protected $model = Commission::class;

    public function definition(): array
    {
        return [
            'affiliator_id' => Affiliator::factory(),
            'referral_order_id' => ReferralOrder::factory(),
            'order_id' => Order::factory(),
            'amount' => fake()->randomFloat(2, 10000, 500000),
            'rate' => 10.00,
            'status' => 'pending',
            'approved_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'approved_at' => now(),
        ]);
    }
}
