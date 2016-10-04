<?php

use Projek\Slim\Container;

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

if (!function_exists('directory')) {
    /**
     * @param  string $path
     *
     * @return string
     */
    function directory($path)
    {
        if (empty($path)) {
            return ROOT_DIR;
        }

        $paths = explode('.', $path);
        $dir = array_shift($paths);

        return setting('directories.'.$dir).($paths ? implode('/', $paths).'/' : '');
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
        return array_get(app('settings'), $name, $default);
    }
}

if (!function_exists('dump')) {
    function dump()
    {
        array_map(function ($params) {
            var_dump($params);
        }, func_get_args());

        exit;
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
        return app('logger')->log($level, $message, $context);
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

if (!function_exists('array_get')) {
    /**
     * Get an item from array using 'dot' notation
     *
     * @aee https://github.com/laravel/framework/blob/5.3/src/Illuminate/Support/Arr.php#L238-L269
     * @param  array|\Slim\Collection $array
     * @param  string $key
     * @param  mixed $default
     *
     * @return mixed
     */
    function array_get($array, $key, $default = null)
    {
        if ($array instanceof \Slim\Collection) {
            $array = $array->all();
        }

        if (null === $key) {
            return $default;
        }

        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        if (strpos($key, '.') !== false) {
            foreach (explode('.', $key) as $segment) {
                if (array_key_exists($segment, $array)) {
                    $array = $array[$segment];
                }
            }

            return $array;
        }

        return $default;
    }
}

if (!function_exists('array_devide')) {
    /**
     * Divide an array into two arrays. One with keys and the other with values.
     *
     * @see https://github.com/laravel/framework/blob/5.3/src/Illuminate/Support/Arr.php#L63-L72
     * @param  array  $array
     * @return array
     */
    function array_devide(array $array)
    {
        return [array_keys($array), array_values($array)];
    }
}

if (!function_exists('sizes_to_bites')) {
    function sizes_to_bites($size)
    {
        $size = trim($size);
        $last = strtolower($size[strlen($size)-1]);

        switch ($last) {
            case 'g':
                $size *= 1024; // fall-through
            case 'm':
                $size *= 1024; // fall-through
            case 'k':
                $size *= 1024; // fall-through
        }

        return $size;
    }
}
