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
        'order_id',
        'amount',
        'rate',
        'status',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'rate' => 'decimal:2',
            'approved_at' => 'datetime',
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
     * @return BelongsTo<ReferralOrder, $this>
     */
    public function referralOrder(): BelongsTo
    {
        return $this->belongsTo(ReferralOrder::class);
    }

    /**
     * @return BelongsTo<Order, $this>
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
