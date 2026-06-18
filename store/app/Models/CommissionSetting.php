<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommissionSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'scope',
        'rate_percent',
        'min_payout',
    ];

    protected function casts(): array
    {
        return [
            'rate_percent' => 'decimal:2',
            'min_payout' => 'decimal:2',
        ];
    }
}
