<?php

namespace Database\Factories;

use App\Models\AffiliateEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AffiliateEvent>
 */
class AffiliateEventFactory extends Factory
{
    protected $model = AffiliateEvent::class;

    public function definition(): array
    {
        $start = fake()->dateTimeBetween('+1 week', '+1 month');
        $end = fake()->dateTimeBetween($start, '+2 months');

        return [
            'title' => fake('id_ID')->sentence(3),
            'description' => fake('id_ID')->paragraph(3),
            'starts_at' => $start,
            'ends_at' => $end,
            'reward_note' => 'Hadiah utama: voucher belanja Rp 1.000.000',
            'status' => 'draft',
        ];
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'starts_at' => now()->subDay(),
            'ends_at' => now()->addMonth(),
        ]);
    }
}
