<?php

return [
    'default' => [
        'app_id' => env('REVERB_APP_ID'),
        'app_key' => env('REVERB_APP_KEY'),
        'app_secret' => env('REVERB_APP_SECRET'),
        'app_cert' => env('REVERB_APP_CERT'),
        'options' => [
            'host' => env('REVERB_HOST', 'localhost'),
            'port' => env('REVERB_PORT', 8080),
            'scheme' => env('REVERB_SCHEME', 'https'),
            'useTLS' => env('REVERB_SCHEME', 'https') === 'https',
        ],
        'apps' => [
            [
                'app_id' => env('REVERB_APP_ID'),
                'app_key' => env('REVERB_APP_KEY'),
                'app_secret' => env('REVERB_APP_SECRET'),
                'app_cert' => env('REVERB_APP_CERT'),
                'allowed_origins' => ['*'],
                'ping_interval' => 10,
                'max_message_size' => 10240,
            ],
        ],
    ],
];
