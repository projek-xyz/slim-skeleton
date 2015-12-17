<?php

use App\Providers;

return [
    // Application basename
    'basename' => 'Slim-App',
    // Application baseurl
    'baseurl' => getenv('APP_BASEURL') ?: '',

    // Application title and description
    'title' => 'Slim Skeleton',
    'description' => 'PHP Application Skeleton for Slim v3 Microframework',

    // Application TimeZone
    'timezone' => 'Asia/Jakarta',

    // Application Mode
    'mode' => getenv('APP_ENV') ?: 'development',

    // Language settings
    'lang' => [
        // 'directory' => APP_DIR.'langs',
        'default'   => 'id',
    ],

    // Database settings
    'db' => [
        'dsn'     => getenv('APP_DB_DSN')    ?: '',
        'driver'  => getenv('APP_DB_DRIVER') ?: 'mysql',
        'host'    => getenv('APP_DB_HOST')   ?: 'localhost',
        'user'    => getenv('APP_DB_USER')   ?: 'root',
        'pass'    => getenv('APP_DB_PASS')   ?: '',
        'name'    => getenv('APP_DB_NAME')   ?: '',
        'charset' => 'utf8',
    ],

    // Database settings
    'fs' => [
        'local' => [
            'path' => ASSET_DIR,
        ]
    ],

    // Loggin settings
    'logger' => [
        'directory' => APP_DIR.'logs',
        'filename'  => date('Y-m-d').'.log',
        'level'     => 'DEBUG'
    ],

    // View settings
    'view' => [
        'directory'           => APP_DIR.'views',
        'assetPath'           => ROOT_DIR.'public',
        'fileExtension'       => 'tpl',
        'timestampInFilename' => false,
    ],

    // List of Pimple Service Providers
    'providers' => [
        // Providers\DatabaseProvider::class,
        Providers\NegotiatorProvider::class,
        Projek\Slim\FlysystemProvider::class,
        Projek\Slim\MonologProvider::class,
        Projek\Slim\PlatesProvider::class,
    ]
];
