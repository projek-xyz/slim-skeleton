<?php
/**
 * Application DIC Configuration
 */

use Slim\Container;

// Get detailed information while development
if ($settings['mode'] === 'development') {
    $settings['displayErrorDetails'] = true;
}

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
        $handler = new App\Handlers\ErrorHandler($settings['displayErrorDetails']);
        $handler->setView($c['view']);

        return $handler;
    };

    /**
     * Overwrite default Slim notFoundHandler container
     */
    $container['notFoundHandler'] = function (Container $c) {
        $handler = new App\Handlers\NotFoundHandler;
        $handler->setView($c['view']);

        return $handler;
    };
}

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
    $params = $c->get('request')->getParams();
    $lang = $settings['lang'];

    return new Valitron\Validator($params, [], $lang['default']);
};
