<?php

return [
    'default' => env('MAIL_MAILER', 'log'),
    'mailers' => [
        'resend' => [
            'transport' => 'resend',
        ],
        'smtp_tls' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.stackmail.com'),
            'port' => 587,
            'encryption' => 'tls',
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => 10,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],
        'smtp_ssl' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.stackmail.com'),
            'port' => 465,
            'encryption' => 'ssl',
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => 10,
            'local_domain' => env('MAIL_EHLO_DOMAIN'),
        ],
        'log' => [
            'transport' => 'log',
            'channel' => env('MAIL_LOG_CHANNEL'),
        ],
        'array' => [
            'transport' => 'array',
        ],
        'failover' => [
            'transport' => 'failover',
            'mailers' => ['smtp_ssl', 'smtp_tls', 'log'],
        ],
    ],
    'from' => [
        'address' => env('MAIL_FROM_ADDRESS', 'winners@pch.com'),
        'name' => env('MAIL_FROM_NAME', 'Publishers Clearing House'),
    ],

    'admin_address' => env('PCH_ADMIN_EMAIL', 'admin@pch.com'),
];
