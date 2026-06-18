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
        'clicks_count',
    ];

    protected function casts(): array
    {
        return [
            'clicks_count' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Affiliator, $this>
     */
    public function affiliator(): BelongsTo
    {
        return $this->belongsTo(Affiliator::class);
    }

    /**
     * @return HasMany<ReferralClick, $this>
     */
    public function referralClicks(): HasMany
    {
        return $this->hasMany(ReferralClick::class);
    }

    /**
     * @return HasMany<ReferralOrder, $this>
     */
    public function referralOrders(): HasMany
    {
        return $this->hasMany(ReferralOrder::class);
    }
}
