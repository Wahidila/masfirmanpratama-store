<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $fillable = [
        'event_type',
        'payload',
        'signature',
        'status',
        'error_message',
        'source_ip',
    ];

    protected $casts = [
        'payload' => 'array',
    ];
}
