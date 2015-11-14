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

// $container['errorHandler'] = function (Container $c) {
//     return;
// }

// $container['notFoundHandler'] = function (Container $c) {
//     return;
// }

/**
 * Enable flash message using native PHP Session
 */
$container['flash'] = function () {
    return new Slim\Flash\Messages;
};

/**
 * Setup debugbar instance
 */
$container['debugbar'] = function () {
    return new DebugBar\StandardDebugBar;
};

/**
 * Setup Validator
 */
$container['validator'] = function (Container $c) {
    $params = $c->get('request')->getParams();
    $lang = $c->get('settings')['lang'];

    return new Valitron\Validator($params, [], $lang['default']);
};
