<?php

return [
    // Application title, email and description
    'app' => [
        'title' => 'Slim Skeleton',
        'description' => 'PHP Application Skeleton for Slim v3 Microframework',
        'email' => getenv('APP_EMAIL') ?: 'admin@example.com',
    ],

    // Application basename
    'basename' => 'Slim-App',

    // Application baseurl
    'baseurl' => getenv('APP_URL') ?: '',

    // Application TimeZone
    'timezone' => getenv('APP_TIMEZONE') ?: 'UTC',

    // Application Mode
    'mode' => getenv('APP_ENV') ?: 'production',

    // Language settings
    'lang' => [
        // 'directory' => RES_DIR.'langs', // Not implemented yet.
        'default'   => 'id',
    ],

    // Database settings
    'db' => [
        'dsn'    => getenv('DB_DSN')    ?: '',
        'driver' => getenv('DB_DRIVER') ?: 'mysql',
        'host'   => getenv('DB_HOST')   ?: 'localhost',
        'user'   => getenv('DB_USER')   ?: 'root',
        'pass'   => getenv('DB_PASS')   ?: '',
        'name'   => getenv('DB_NAME')   ?: '',
    ],

    // Mailer settings
    'mailer' => [
        'host'     => getenv('EMAIL_HOST') ?: '',
        'port'     => getenv('EMAIL_PORT') ?: '',
        'username' => getenv('EMAIL_USER') ?: '',
        'password' => getenv('EMAIL_PASS') ?: '',
    ],

    // Loggin settings
    'logger' => [
        'rotate' => true,
        'level'  => 'DEBUG'
    ],

    // View settings
    'view' => [
        'fileExtension' => 'tpl',
    ],

    // Uploader settings
    'upload' => [
        'extensions' => ['image/jpeg', 'image/png']
    ],

    'commands' => [
        App\Commands\GreetingCommand::class
    ]
];
