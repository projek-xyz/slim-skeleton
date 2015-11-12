<?php

return [
    // Application basename
    'basename' => 'Slim-App',

    // Application title
    'title' => 'Slim Framework 3',

    // Application Mode
    'mode' => getenv('APP_ENV') ?: 'development',

    // Language settings
    'lang' => [
        'directory' => APP_DIR.'langs',
        'default'   => 'id',
    ],

    // Database settings
    'db' => [
        'default' => [
            'dsn'     => getenv('APP_DB_DSN')    ?: '',
            'driver'  => getenv('APP_DB_DRIVER') ?: 'mysql',
            'host'    => getenv('APP_DB_HOST')   ?: 'localhost',
            'user'    => getenv('APP_DB_USER')   ?: 'root',
            'pass'    => getenv('APP_DB_PASS')   ?: '',
            'name'    => getenv('APP_DB_NAME')   ?: '',
            'charset' => 'utf8',
        ]
    ],

    // Loggin settings
    'logger' => [
        'directory' => APP_DIR.'logs',
        'filename'  => date('Y-m-d').'.log',
        'level'     => 'debug'
    ],

    // View settings
    'view' => [
        'directory'     => APP_DIR.'views',
        'assetPath'     => ASSET_DIR.'asset',
        'fileExtension' => 'tpl',
    ],

    // List of Pimple Service Providers
    'providers' => [
        // App\Providers\DatabaseProvider::class,
        Projek\Slim\MonologProvider::class,
        Projek\Slim\PlatesProvider::class,
    ]
];
