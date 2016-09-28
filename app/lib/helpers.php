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

if (!function_exists('logger')) {
    /**
     * @param  integer $level   The logging level
     * @param  string  $message The log message
     * @param  array   $context The log context
     * @return bool    Whether the record has been processed
     */
    function logger($level, $message, array $context = [])
    {
        $log = app('logger');

        return $log->log($level, $message, $context);
    }
}

if (!function_exists('base_url')) {
    /**
     * Get application base url
     *
     * @param  string $permalink
     *
     * @return string
     */
    function base_url($permalink = '')
    {
        /** @var \Slim\Http\Uri $uri */
        $uri = app('request')->getUri();

        return $uri->getBaseUrl().'/'.ltrim($permalink, '/');
    }
}

if (!function_exists('path_for')) {
    /**
     * @param string $name
     * @param array  $data
     * @param array  $queryParams
     *
     * @return string
     */
    function path_for($name, array $data = [], array $queryParams = [])
    {
        return app('router')->pathFor($name, $data, $queryParams);
    }
}
