<?php

use App\Providers;

return [
    // Application basename
    'basename' => 'Slim-App',

    // Application title, email and description
    'app' => [
        'title' => 'Slim Skeleton',
        'description' => 'PHP Application Skeleton for Slim v3 Microframework',
        'email' => getenv('APP_EMAIL') ?: 'admin@example.com',
    ],

    // Application baseurl
    'baseurl' => getenv('APP_URL') ?: '',

    // Application TimeZone
    'timezone' => getenv('APP_TIMEZONE') ?: 'UTC',

    // Application Mode
    'mode' => getenv('APP_ENV') ?: 'production',

    // Language settings
    'lang' => [
        // 'directory' => APP_DIR.'langs', // Not implemented yet.
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

    // Mailer settings
    'mailer' => [
        'host'      => getenv('EMAIL_HOST') ?: '',
        'port'      => getenv('EMAIL_PORT') ?: '',
        'username'  => getenv('EMAIL_USER') ?: '',
        'password'  => getenv('EMAIL_PASS') ?: '',
    ],

    // Loggin settings
    'logger' => [
        'directory' => STORAGE_DIR.'logs',
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
        Providers\ErrorHandlersProvider::class,
        // Providers\DatabaseProvider::class,
        Providers\NegotiatorProvider::class,
        Projek\Slim\MonologProvider::class,
        Projek\Slim\PlatesProvider::class,
        Providers\EmailProvider::class,
        Providers\AppProvider::class,
    ]
];
