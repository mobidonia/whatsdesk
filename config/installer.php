<?php

return [
    'icon' => '/default/logo.png',

    'background' => '/default/bg.jpg',

    'support_url' => 'https://help.mobidonia.com/',

    'server' => [
        'php' => [
            'name' => 'PHP Version',
            'version' => '>= 8.1.0',
            'check' => [
                'type' => 'php',
                'value' => 80100
            ]
        ],
        'pdo' => [
            'name' => 'PDO',
            'check' => [
                'type' => 'extension',
                'value' => 'pdo_mysql'
            ]
        ],
        'mbstring' => [
            'name' => 'Mbstring extension',
            'check' => [
                'type' => 'extension',
                'value' => 'mbstring'
            ]
        ],
        'fileinfo' => [
            'name' => 'Fileinfo extension',
            'check' => [
                'type' => 'extension',
                'value' => 'fileinfo'
            ]
        ],
        'openssl' => [
            'name' => 'OpenSSL extension',
            'check' => [
                'type' => 'extension',
                'value' => 'openssl'
            ]
        ],
        'tokenizer' => [
            'name' => 'Tokenizer extension',
            'check' => [
                'type' => 'extension',
                'value' => 'tokenizer'
            ]
        ],
        'json' => [
            'name' => 'Json extension',
            'check' => [
                'type' => 'extension',
                'value' => 'json'
            ]
        ],
        'curl' => [
            'name' => 'Curl extension',
            'check' => [
                'type' => 'extension',
                'value' => 'curl'
            ]
        ]
    ],

    'folders' => [
        'storage.framework' => [
            'name' => '/storage/framework',
            'check' => [
                'type' => 'directory',
                'value' => '../storage/framework'
            ]
        ],
        'storage.logs' => [
            'name' => '/storage/logs',
            'check' => [
                'type' => 'directory',
                'value' => '../storage/logs'
            ],
        ],
        'storage.cache' => [
            'name' => '/bootstrap/cache',
            'check' => [
                'type' => 'directory',
                'value' => '../bootstrap/cache'
            ]
        ],
        'storage.uploads' => [
            'name' => '/public/uploads',
            'check' => [
                'type' => 'directory',
                'value' => '../public/uploads'
            ]
        ]
    ],

    'database' => [
        'seeders' => true
    ],

    'commands' => [
        'app:migrrate-modules'
    ],

    'admin_area' => [
        'user' => [
            'email' => 'admin@example.com',
            'password' => 'secret'
        ]
    ],

    'login' => '/login'
];
