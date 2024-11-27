<?php

return [
    'default' => 'database',
    'loggers' => [
        'email' => [
            'type' => 'email',
            'recipient' => env('LOG_EMAIL_RECIPIENT', 'admin@example.com'),
        ],
        'database' => [
            'type' => 'database',
        ],
        'file' => [
            'type' => 'file',
            'file_path' => storage_path('logs/custom.log'),
        ],
    ],
];
