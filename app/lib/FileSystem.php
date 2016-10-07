<?php
namespace Projek\Slim;

use League\Flysystem\Adapter\Local;
use League\Flysystem\AdapterInterface;
use League\Flysystem\MountManager;
use League\Flysystem\Filesystem as Fs;

class FileSystem
{
    /**
     * @var  array
     */
    protected $settings = [
        'default' => 'local',
        'local' => [
            'directory' => null
        ]
    ];

    /**
     * @var  MountManager
     */
    protected $manager;

    /**
     * @var  \League\Flysystem\FilesystemInterface
     */
    protected $current = null;

    /**
     * @param  array $settings
     */
    public function __construct(array $settings = [])
    {
        $this->settings = array_merge($this->settings, $settings);

        $default = $this->settings['default'];

        if (!isset($this->settings[$default])) {
            throw new \LogicException('Default file system configuration not found');
        }

        $this->manager = new MountManager;

        if ($default === 'local') {
            $directory = $this->settings[$default]['directory'] ?: directory('storage');
            $this->mountFilesystem($default, new Local($directory));
        }
    }

    /**
     * @param  string $prefix
     * @param  AdapterInterface $adapter
     *
     * @return MountManager
     */
    public function mountFilesystem($prefix, AdapterInterface $adapter)
    {
        return $this->manager->mountFilesystem($prefix, new Fs($adapter));
    }

    public function __get($param)
    {
        $this->current = $this->manager->getFilesystem($param);

        return $this;
    }

    public function __call($method, $params)
    {
        if (null !== $this->current) {
            return call_user_func_array([$this->current, $method], $params);
        }

        list($prefix, $params) = $this->manager->filterPrefix($params);

        return $this->manager->invokePluginOnFilesystem($method, $params, $prefix);
    }
}
