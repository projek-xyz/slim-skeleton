<?php
/**
 * Application DIC Configuration
 */

use Slim\Container;

/**
 * Registering all defined providers
 */
foreach ($settings['providers'] as $provider) {
    $container->register(new $provider);
}

/**
 * Overwrite default Slim errorHandler container
 */
$container['errorHandler'] = function () use ($settings) {
    // Get detailed information while development
    if ($settings['mode'] === 'development') {
        $settings['displayErrorDetails'] = true;
    }
    return new App\Handlers\ErrorHandler($settings['displayErrorDetails']);
};

/**
 * Overwrite default Slim notFoundHandler container
 */
$container['notFoundHandler'] = function () {
    return new App\Handlers\NotFoundHandler;
};

/**
 * Enable flash message using native PHP Session
 */
$container['flash'] = function () {
    return new Slim\Flash\Messages;
};

/**
 * Setup debugbar instance
 */
$container['debugbar'] = function () use ($settings) {
    // Only initiate debugbar when development
    if ($settings['mode'] === 'development') {
        return new DebugBar\StandardDebugBar;
    }
    return null;
};

/**
 * Setup Validator
 */
$container['validator'] = function (Container $c) use ($settings) {
    $params = $c->get('request')->getParams();
    $lang = $settings['lang'];

    return new Valitron\Validator($params, [], $lang['default']);
};
