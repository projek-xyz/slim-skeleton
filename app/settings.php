<?php

return [
    // Application basename
    'basename' => 'Slim-App',

    // Application title and description
    'title' => 'Slim 3 Skeleton',
    'description' => 'PHP Application Skeleton for SLIM 3 Microframework',

    // Application Mode
    'mode' => getenv('APP_ENV') ?: 'development',

    // Language settings
    'lang' => [
        // 'directory' => APP_DIR.'langs',
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
        App\Providers\MonologProvider::class,
        App\Providers\PlatesProvider::class,
    ]
];
