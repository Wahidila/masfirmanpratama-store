<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'direction',
        'event',
        'payload',
        'status',
        'response_code',
        'attempt',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'response_code' => 'integer',
            'attempt' => 'integer',
        ];
    }
}
