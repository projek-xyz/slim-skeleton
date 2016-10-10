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
    public function __construct(array $value = [], $rootDir = null)
    {
        // Let's set default timezone
        if (isset($value['settings']['timezone'])) {
            date_default_timezone_set($value['settings']['timezone'] ?: 'UTC');
        }

        // Set default application mode
        if (!array_key_exists('mode', $value['settings'])) {
            $value['settings']['mode'] = getenv('APP_ENV') ?: 'production';
        }

        $this->initializeAppDirectories($rootDir, $value['settings']);

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

    protected function initializeAppDirectories($rootDir, &$settings)
    {
        $settings['directories'] = [];
        if (null !== $rootDir) {
            $rootDir = rtrim($rootDir, DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR;
            $settings['directories']['root'] = $rootDir;

            foreach (['app', 'assets', 'public', 'storage'] as $dir) {
                $settings['directories'][$dir] = $rootDir.$dir.DIRECTORY_SEPARATOR;
            }
        }
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
