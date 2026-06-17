<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'affiliator_id',
        'type',
        'title',
        'message',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public function affiliator(): BelongsTo
    {
        return $this->belongsTo(Affiliator::class);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }
}
