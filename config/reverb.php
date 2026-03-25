<?php

return [
    'default' => env('REVERB_BROADCAST_DRIVER', 'reverb'),

    'servers' => [
        'reverb' => [
            'host' => env('REVERB_SERVER_HOST', '0.0.0.0'),
            'port' => env('REVERB_SERVER_PORT', 6001), // Changed to 6001
            'hostname' => env('REVERB_HOST'),
            'options' => [
                'tls' => env('APP_ENV') === 'production' ? [
                    'local_cert' => env('REVERB_TLS_CERT'),
                    'local_pk' => env('REVERB_TLS_KEY'),
                    'allow_self_signed' => false,
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                ] : [
                    'local_cert' => env('REVERB_TLS_CERT'),
                    'local_pk' => env('REVERB_TLS_KEY'),
                    'allow_self_signed' => true,
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                ],
            ],
            'max_request_size' => 10_000,
            'max_message_size' => 10_000,
            'scaling' => [
                'enabled' => env('REVERB_SCALING_ENABLED', false),
                'channel' => env('REVERB_SCALING_CHANNEL', 'reverb'),
                'server' => [
                    'url' => env('REDIS_URL'),
                    'host' => env('REDIS_HOST', '127.0.0.1'),
                    'port' => env('REDIS_PORT', 6379),
                    'username' => env('REDIS_USERNAME'),
                    'password' => env('REDIS_PASSWORD'),
                    'database' => env('REDIS_DB', 0),
                ],
            ],
            'pulse_ingest_interval' => 15,
            'telescope_ingest_interval' => 15,
        ],
    ],

    'apps' => [
        'provider' => 'config',
        'apps' => [
            [
                'key' => env('REVERB_APP_KEY'),
                'secret' => env('REVERB_APP_SECRET'),
                'app_id' => env('REVERB_APP_ID'),
                'options' => [
                    'host' => env('REVERB_HOST'),
                    'port' => env('REVERB_PORT', 6001), // Changed to 6001
                    'scheme' => env('REVERB_SCHEME', 'https'),
                ],
                'allowed_origins' => ['*'],
                'ping_interval' => env('REVERB_APP_PING_INTERVAL', 60),
                'activity_timeout' => env('REVERB_APP_ACTIVITY_TIMEOUT', 30),
                'max_message_size' => 10_000,
            ],
        ],
    ],
];
