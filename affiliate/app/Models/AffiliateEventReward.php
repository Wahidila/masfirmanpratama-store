<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateEventReward extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_event_id',
        'affiliator_id',
        'reward_type',
        'reward_value',
        'description',
        'is_claimed',
        'claimed_at',
    ];

    protected $casts = [
        'reward_value' => 'decimal:2',
        'is_claimed' => 'boolean',
        'claimed_at' => 'datetime',
    ];

    public function event(): BelongsTo
    {
        return $this->belongsTo(AffiliateEvent::class, 'affiliate_event_id');
    }

    public function affiliator(): BelongsTo
    {
        return $this->belongsTo(Affiliator::class);
    }
}
