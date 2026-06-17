<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commission extends Model
{
    use HasFactory;

    protected $fillable = [
        'affiliator_id',
        'referral_order_id',
        'amount',
        'rate_applied',
        'status',
        'available_at',
        'withdrawn_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'rate_applied' => 'decimal:2',
        'available_at' => 'datetime',
        'withdrawn_at' => 'datetime',
    ];

    public function affiliator(): BelongsTo
    {
        return $this->belongsTo(Affiliator::class);
    }

    public function referralOrder(): BelongsTo
    {
        return $this->belongsTo(ReferralOrder::class);
    }

    public function isCooling(): bool
    {
        return $this->status === 'cooling' && $this->available_at->isFuture();
    }

    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}
