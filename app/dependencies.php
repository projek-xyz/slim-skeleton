<?php
/**
 * Application DIC Configuration
 *
 * @uses \Slim\Container $container
 */

use Slim\Container;

/**
 * Registering all defined providers
 */
foreach ($settings['providers'] as $provider) {
    $container->register(new $provider);
}

if ($settings['mode'] !== 'development') {
    /**
     * Overwrite default Slim errorHandler container
     */
    $container['errorHandler'] = function (Container $c) use ($settings) {
        $handler = new App\ErrorHandler($settings['displayErrorDetails']);
        $handler->setView($c['view']);
        $handler->setLogger($c['logger']);

        return $handler;
    };
}

/**
 * Overwrite default Slim notFoundHandler container
 */
$container['notFoundHandler'] = function (Container $c) {
    $handler = new App\NotFoundHandler;
    $handler->setView($c['view']);
    $handler->setLogger($c['logger']);

    return $handler;
};

/**
 * Enable flash message using native PHP Session
 */
$container['flash'] = function () {
    return new Slim\Flash\Messages;
};

/**
 * Setup Validator
 */
$container['validator'] = function (Container $c) use ($settings) {
    $params = $c['request']->getParams();
    $lang = $settings['lang'];

    return new Valitron\Validator($params, [], $lang['default']);
};
