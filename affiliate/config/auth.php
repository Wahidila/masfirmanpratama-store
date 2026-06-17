<?php

use App\Models\Affiliator;

return [

    'defaults' => [
        'guard' => 'affiliator',
        'passwords' => 'affiliators',
    ],

    'guards' => [
        'affiliator' => [
            'driver' => 'session',
            'provider' => 'affiliators',
        ],
    ],

    'providers' => [
        'affiliators' => [
            'driver' => 'eloquent',
            'model' => Affiliator::class,
        ],
    ],

    'passwords' => [
        'affiliators' => [
            'provider' => 'affiliators',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
