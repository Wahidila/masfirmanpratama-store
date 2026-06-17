<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReferralCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliator_id',
        'code',
        'label',
        'target_url',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function affiliator(): BelongsTo
    {
        return $this->belongsTo(Affiliator::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(ReferralClick::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(ReferralOrder::class);
    }

    public function totalClicks(): int
    {
        return $this->clicks()->count();
    }

    public function conversionRate(): float
    {
        $clicks = $this->totalClicks();
        if ($clicks === 0) return 0;
        return ($this->orders()->count() / $clicks) * 100;
    }
}
