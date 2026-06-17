<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AffiliateEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'type',
        'start_date',
        'end_date',
        'rules',
        'rewards',
        'status',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'rules' => 'array',
        'rewards' => 'array',
    ];

    public function participants(): HasMany
    {
        return $this->hasMany(AffiliateEventParticipant::class);
    }

    public function rewardsClaimed(): HasMany
    {
        return $this->hasMany(AffiliateEventReward::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'active'
            && $this->start_date->isPast()
            && $this->end_date->isFuture();
    }
}
