<?php

namespace Projek\Slim;

use Slim\Container as SlimContainer;

class Container extends SlimContainer
{
    static $instance = null;

    public function __construct(array $value = [])
    {
        parent::__construct($value);

        $settings = $this->get('settings');

        $this->registerProviders($settings['providers']);

        // Let's just use PHP Native sesion
        if (!isset($_SESSION)) {
            session_name($settings['basename']);
            session_start();
        }

        static::$instance = $this;
    }

    public static function instance()
    {
        return static::$instance;
    }

    private function registerProviders(array $providers)
    {
        foreach ($providers as $provider) {
            $this->register(new $provider);
        }
    }
}