<?php
namespace Projek\Slim;

use Slim\Container as SlimContainer;

/**
 * @property-read callable data
 * @property-read \Slim\PDO\Database db
 * @property-read \League\Flysystem\Filesystem filesystem
 * @property-read Logger logger
 * @property-read Mailer mailer
 * @property-read \Slim\Collection settings
 * @property-read callable upload
 * @property-read callable validator
 * @property-read View view
 */
class Container extends SlimContainer
{
    private static $instance = null;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $value = [], $root_dir = null)
    {
        if (defined('ROOT_DIR')) {
            $value['settings']['directories'] = [
                'app' => $root_dir.'app/',
                'resources' => $root_dir.'resources/',
                'storage' => $root_dir.'storage/',
                'public' => $root_dir.'public/',
            ];
        }

        if (!array_key_exists('mode', $value['settings'])) {
            $value['settings']['mode'] = getenv('APP_ENV') ?: 'production';
        }

        // Get detailed information while not in production
        if ($value['settings']['mode'] !== 'production') {
            $value['settings']['displayErrorDetails'] = true;
        }

        parent::__construct($value);

        $settings = $this->get('settings');

        $this->register(new DefaultServicesProvider);

        if (isset($settings['providers'])) {
            $this->registerServicesProviders($settings['providers']);
        }

        static::$instance = $this;
    }

    /**
     * Retrieve static instane of the Container
     *
     * @return static
     */
    public static function instance()
    {
        return static::$instance;
    }

    /**
     * Register multiple ServiceProviders
     *
     * @param  array $providers
     */
    private function registerServicesProviders(array $providers)
    {
        foreach ($providers as $provider) {
            $this->register(new $provider);
        }
    }
}
