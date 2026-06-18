<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AffiliateEventParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliate_event_id',
        'affiliator_id',
        'score',
        'rank',
        'progress',
    ];

    protected $casts = [
        'progress' => 'array',
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
