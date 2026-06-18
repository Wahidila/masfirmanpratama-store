<?php

namespace Database\Factories;

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
            'scope' => 'global',
            'rate_percent' => 10.00,
            'min_payout' => 50000.00,
        ];
    }
}
