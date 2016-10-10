<?php

use Projek\Slim\Container;

if (!function_exists('app')) {
    /**
     * @param  string|null $name
     *
     * @return mixed
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

if (!function_exists('dump')) {
    /**
     *  Dump data
     */
    function dump()
    {
        array_map(function ($params) {
            var_dump($params);
        }, func_get_args());

        exit;
    }
}

if (!function_exists('directory')) {
    /**
     * @param  string $path
     * @param  string|null $relative
     *
     * @return string
     */
    function directory($path, $relative = null)
    {
        if (empty($path)) {
            return config('directories.root');
        }

        $paths = explode('.', $path);
        $first = array_shift($paths);
        $dir = config('directories.'.$first);

        if (is_string($relative) && !empty($relative)) {
            $dir = str_replace(directory($relative), '', $dir);
        }

        return $dir.($paths ? implode(DIRECTORY_SEPARATOR, $paths).DIRECTORY_SEPARATOR : '');
    }
}

if (!function_exists('config')) {
    /**
     * @param  string $name
     * @param  string|null $default
     *
     * @return mixed Setting
     */
    function config($name, $default = null)
    {
        /** @var  \Slim\Collection $settings */
        $settings = app('settings');

        if (strpos($name, '.') === false) {
            return $settings->get($name, $default);
        }

        return array_get($settings, $name, $default);
    }
}

if (!function_exists('logger')) {
    /**
     * @param  integer $level
     * @param  string $message
     * @param  array $context
     *
     * @return bool Whether
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
