<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliator_type_id',
        'product_type',
        'rate',
        'min_amount',
        'cooling_days',
        'is_active',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function affiliatorType(): BelongsTo
    {
        return $this->belongsTo(AffiliatorType::class);
    }
}
