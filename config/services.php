<?php

return [
    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],
    'jwt' => [
        'secret' => env('JWT_SECRET'),
        'ttl' => env('JWT_TTL', 10080),
    ],
];
