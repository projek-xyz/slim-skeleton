# Slim Framework Application Skeleton

[![LICENSE](https://img.shields.io/packagist/l/projek-xyz/slim-skeleton.svg?style=flat-square)](LICENSE.md)
[![VERSION](https://img.shields.io/packagist/v/projek-xyz/slim-skeleton.svg?style=flat-square)](https://github.com/projek-xyz/slim-skeleton/releases)
[![Build Status](https://img.shields.io/travis/projek-xyz/slim-skeleton/master.svg?style=flat-square)](https://travis-ci.org/projek-xyz/slim-skeleton)
[![SensioLabs Insight](https://img.shields.io/sensiolabs/i/59c39221-cc85-467f-9e00-c7e0dcbdc9ee.svg?style=flat-square)](https://insight.sensiolabs.com/projects/59c39221-cc85-467f-9e00-c7e0dcbdc9ee)

## Requirements

- PHP 5.5.x or newer, since Slim v3.x depends on it.
- HTTP Server, e.g. NginX or Apache either.
- MySQL Server 5.x or newer for main database.

## Install

I've make this package available to install via `composer create-project` so make sure you've already have [composer](https://getcomposer.org/download/) installed globally in your system.

```bash
$ composer create-project -n -s dev projek-xyz/slim-skeleton my-app --prefer-dist
```

then enter `my-app` directory you just create and start the server.

```bash
$ cd my-app
$ php -S localhost:8888 -t public
```

Now, you should open [http://localhost:8888](http://localhost:8888) in your favorite web browser.

## Directories and Files structure

```
├── app/                  // Root for application codes
│   ├── data/             // Database directory
│   ├── src/              // Application directory, all classes under 'App' namespace
│   ├── views/            // View templates directory for Plates
│   ├── .env.sample       // Sample .env file for phpdotenv
│   ├── bootstrap.php     // Application bootstraper file
│   ├── dependencies.php  // Services for Pimple
│   ├── middlewares.php   // Middlewares declaration
│   ├── routers.php       // Application routes
│   └── settings.php      // Application settings
├── asset/                // Assets source directory
├── public/               // Webroot directory
│   ├── images/           // Static images directory
│   ├── styles/           // Stylesheets directory
│   └── index.php         // Entry point to application
├── storage/              // Assets directory
│   ├── cache/            // cache directory
│   └── logs/             // Log directory for Monolog
└── tests/                // Testing directory
    ├── specs/            // Integration test directory
    ├── units/            // Functional unit test directory
    └── phpunit.xml       // Sample phpunit configuration file
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
