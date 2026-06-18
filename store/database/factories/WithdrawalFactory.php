<?php

namespace Database\Factories;

use App\Models\Affiliator;
use App\Models\Withdrawal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Withdrawal>
 */
class WithdrawalFactory extends Factory
{
    protected $model = Withdrawal::class;

    public function definition(): array
    {
        $affiliator = Affiliator::factory();

        return [
            'affiliator_id' => $affiliator,
            'amount' => fake()->randomFloat(2, 50000, 2000000),
            'status' => 'requested',
            'bank_name' => fake()->randomElement(['BCA', 'BNI', 'BRI', 'Mandiri', 'BSI']),
            'bank_account' => fake()->numerify('##########'),
            'bank_holder' => fake('id_ID')->name(),
            'note' => null,
            'requested_at' => now(),
            'processed_at' => null,
        ];
    }

    public function approved(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'approved',
            'processed_at' => now(),
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'processed_at' => now(),
        ]);
    }
}
