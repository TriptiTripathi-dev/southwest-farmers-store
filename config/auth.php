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
        'customer' => [
            'driver' => 'session',
            'provider' => 'store_customers',
        ],
    ],

    'providers' => [
        'store_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\StoreUser::class,
        ],
        'store_customers' => [
            'driver' => 'eloquent',
            'model' => App\Models\StoreCustomer::class,
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
