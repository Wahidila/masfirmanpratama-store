<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliatorType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'benefits',
        'default_commission_rate',
        'is_active',
    ];

    protected $casts = [
        'benefits' => 'array',
        'default_commission_rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function affiliators(): HasMany
    {
        return $this->hasMany(Affiliator::class);
    }

    public function commissionSettings(): HasMany
    {
        return $this->hasMany(CommissionSetting::class);
    }
}
