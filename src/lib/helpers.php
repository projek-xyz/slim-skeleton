<?php

use Projek\Slim\Container;

if (!function_exists('dd')) {
    function dd()
    {
        array_map(function ($params) {
            var_dump($params);
        }, func_get_args());
    }
}

if (!function_exists('app')) {
    /**
     * @param  string|null  $name  Container key name
     * @return mixed        Containers instance
     */
    function app($name = null)
    {
        /** @var Container $app */
        $app = Container::instance();

        if (null !== $name && $app->has($name)) {
            return $app->get($name);
        }

        return $app;
    }
}

if (!function_exists('setting')) {
    /**
     * @param  string       $name    Setting key name
     * @param  string|null  $default Default value
     * @return mixed        Setting value
     */
    function setting($name, $default = null)
    {
        $settings = app('settings');

        return isset($settings[$name]) ? $settings[$name] : $default;
    }
}

if (!function_exists('log')) {
    /**
     * @param  integer $level   The logging level
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return bool    Whether the record has been processed
     */
    function log($level, $message, array $context = [])
    {
        $log = app('logger');

        return $log->log($level, $message, $context);
    }
}