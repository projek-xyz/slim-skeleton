<?php
namespace App\Providers;

use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use League\Flysystem\Adapter;
use League\Flysystem\ZipArchive\ZipArchiveAdapter;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class FilesystemProvider implements ServiceProviderInterface
{
    /**
     * Filesystem instance
     *
     * @var \League\Flysystem\Filesystem
     */
    protected $fs;

    /**
     * MountManager instance
     *
     * @var \League\Flysystem\MountManager
     */
    protected $mounts;

    /**
     * Register this database provider with a Pimple container
     *
     * @param \Pimple\Container $container
     */
    public function register(Container $container)
    {
        $settings = $container->get('settings');

        if (!isset($settings['fs'])) {
            throw new InvalidArgumentException('Database configuration not found');
        }

        $fs = $settings['fs'];
        $mounts = [];

        if (isset($fs['local'])) {
            $mounts['local'] = new Filesystem(new Adapter\Local($path));
        }

        $this->mounts = new MountManager($mounts);

        $container['fs'] = $this;
    }

    public function __get($prefix)
    {
        $this->fs = $this->mounts->getFilesystem($prefix);

        return $this;
    }

    public function __destruct()
    {
        if ($this->fs && $this->fs instanceof ZipArchiveAdapter) {
            $this->fs->getAdapter()->getArchive()->close();
        }
    }

    public function mountLocal($path)
    {
        $this->fs = new Filesystem(new Adapter\Local($path));

        return $this;
    }

    public function mountArchive($path)
    {
        $this->fs = new Filesystem(new ZipArchiveAdapter($path));

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function write($path, $contents, array $config = [])
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->write($path, $contents, $config);
    }

    /**
     * @inheritdoc
     */
    public function writeStream($path, $resource, array $config = [])
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->writeStream($path, $contents, $config);
    }

    /**
     * @inheritdoc
     */
    public function put($path, $contents, array $config = [])
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->put($path, $contents, $config);
    }

    /**
     * @inheritdoc
     */
    public function putStream($path, $resource, array $config = [])
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->put($path, $contents, $config);
    }

    /**
     * @inheritdoc
     */
    public function update($path, $contents, array $config = [])
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->update($path, $contents, $config);
    }

    /**
     * @inheritdoc
     */
    public function updateStream($path, $resource, array $config = [])
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->updateStream($path, $contents, $config);
    }

    /**
     * @inheritdoc
     */
    public function read($path)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->read($path);
    }

    /**
     * @inheritdoc
     */
    public function readStream($path)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->readStream($path);
    }

    /**
     * @inheritdoc
     */
    public function rename($path, $newpath)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->rename($path, $newpath);
    }

    /**
     * @inheritdoc
     */
    public function copy($path, $newpath)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->copy($path, $newpath);
    }

    /**
     * @inheritdoc
     */
    public function delete($path)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->delete($path);
    }

    /**
     * @inheritdoc
     */
    public function deleteDir($dirname)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->deleteDir($dirname);
    }

    /**
     * @inheritdoc
     */
    public function createDir($dirname, array $config = [])
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->createDir($dirname, $config);
    }

    /**
     * @inheritdoc
     */
    public function listContents($directory = '', $recursive = false)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->createDir($directory, $recursive);
    }

    /**
     * @inheritdoc
     */
    public function getMimetype($path)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->getMimetype($path);
    }

    /**
     * @inheritdoc
     */
    public function getTimestamp($path)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->getTimestamp($path);
    }

    /**
     * @inheritdoc
     */
    public function getVisibility($path)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->getVisibility($path);
    }

    /**
     * @inheritdoc
     */
    public function getSize($path)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->getSize($path);
    }

    /**
     * @inheritdoc
     */
    public function setVisibility($path, $visibility)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->setVisibility($path, $visibility);
    }

    /**
     * @inheritdoc
     */
    public function getMetadata($path)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->getMetadata($path);
    }
}
