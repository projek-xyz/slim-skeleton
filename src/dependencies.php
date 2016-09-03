<?php
/**
 * Application DIC Configuration
 *
 * @var  Container  $container
 * @var  array  $settings
 */

use Slim\Container;

/**
 * Enable flash message using native PHP Session
 */
$container['flash'] = function () {
    return new Slim\Flash\Messages;
};

/**
 * Setup Validator
 *
 * @uses   $settings
 * @param  Container  $c
 *
 * @return \Valitron\Validator
 */
$container['validator'] = function (Container $c) use ($settings) {
    $params = $c['request']->getParams();
    $lang = $settings['lang'];

    return new Valitron\Validator($params, [], $lang['default']);
};
