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
            $root_dir = rtrim($root_dir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
            $value['settings']['directories'] = [
                'root' => $root_dir,
                'app' => $root_dir.'app'.DIRECTORY_SEPARATOR,
                'public' => $root_dir.'public'.DIRECTORY_SEPARATOR,
                'resources' => $root_dir.'resources'.DIRECTORY_SEPARATOR,
                'storage' => $root_dir.'storage'.DIRECTORY_SEPARATOR,
            ];
        }

        // Let's set default timezone
        if (isset($settings['timezone'])) {
            date_default_timezone_set($settings['timezone'] ?: 'UTC');
        }

        // Set default application mode
        if (!array_key_exists('mode', $value['settings'])) {
            $value['settings']['mode'] = getenv('APP_ENV') ?: 'production';
        }

        // Get detailed information while not in production
        if ($value['settings']['mode'] !== 'production') {
            $value['settings']['displayErrorDetails'] = getenv('APP_DEBUG') ?: true;
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
