<?php

use Projek\Slim\Container;

function dd()
{
    array_map(function ($params) {
        var_dump($params);
    }, func_get_args());
}

function app($name = null)
{
    /** @var Container $app */
    $app = Container::instance();

    if (null !== $name && $app->has($name)) {
        return $app->get($name);
    }

    return $app;
}

function setting($name, $default = null)
{
    $settings = app('settings');

    return isset($settings[$name]) ? $settings[$name] : $default;
}