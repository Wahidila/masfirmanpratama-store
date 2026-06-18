<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\ReferralCode;
use App\Models\ReferralOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReferralOrder>
 */
class ReferralOrderFactory extends Factory
{
    protected $model = ReferralOrder::class;

    public function definition(): array
    {
        return [
            'referral_code_id' => ReferralCode::factory(),
            'order_id' => Order::factory(),
            'status' => 'pending',
        ];
    }

    public function credited(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'credited',
        ]);
    }
}
