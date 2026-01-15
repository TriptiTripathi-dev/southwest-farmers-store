<?php

return [

    'defaults' => [
        'guard' => 'store',
        'passwords' => 'store_users',
    ],

    'guards' => [
        'store' => [
            'driver' => 'session',
            'provider' => 'store_users',
        ],
    ],

    'providers' => [
        'store_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\StoreUser::class,
        ],
    ],

    'passwords' => [
        'store_users' => [
            'provider' => 'store_users',
            'table' => 'store_password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,
];
