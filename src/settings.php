<?php

use App\Providers;
use Projek\Slim\Providers as ProjekProviders;

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
        // 'directory' => APP_DIR.'langs', // Not implemented yet.
        'default'   => 'id',
    ],

    // Database settings
    'db' => [
        'dsn'     => getenv('DB_DSN')    ?: '',
        'driver'  => getenv('DB_DRIVER') ?: 'mysql',
        'host'    => getenv('DB_HOST')   ?: 'localhost',
        'user'    => getenv('DB_USER')   ?: 'root',
        'pass'    => getenv('DB_PASS')   ?: '',
        'name'    => getenv('DB_NAME')   ?: '',
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
        'directory'           => RES_DIR.'views',
        'assetPath'           => ROOT_DIR.'www',
        'fileExtension'       => 'tpl',
        'timestampInFilename' => false,
    ],

    // List of Pimple Service Providers
    'providers' => [
        ProjekProviders\AppProvider::class,
        ProjekProviders\ErrorHandlersProvider::class,
        ProjekProviders\DatabaseProvider::class,
        ProjekProviders\NegotiatorProvider::class,
        ProjekProviders\LoggerProvider::class,
        Providers\ViewProvider::class,
        ProjekProviders\EmailProvider::class,
    ]
];
