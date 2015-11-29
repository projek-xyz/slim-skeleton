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
     * Mounting settings
     *
     * @var array
     */
    protected $settings = [];

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

        $this->settings = array_merge($this->settings, $settings['fs']);
        $mounts = [];

        if (isset($this->settings['local'])) {
            $mounts['local'] = new Filesystem(new Adapter\Local($this->settings['local']['path']));
        }

        $this->mounts = new MountManager($mounts);

        $container['fs'] = $this;
    }

    /**
     * Magic method to connect to filesystem for convenience
     *
     * @param  string $prefix Filesystem name
     * @return $this
     */
    public function __get($prefix)
    {
        $this->fs = $this->mounts->getFilesystem($prefix);

        return $this;
    }

    /**
     * Mound local adapter with given $path
     *
     * @param  string $path
     * @return $this
     */
    public function mountLocal($path)
    {
        if (isset($this->settings[$path]['path'])) {
            $this->fs = $this->mounts->getFilesystem($path);
        } else {
            $this->fs = new Filesystem(new Adapter\Local($path));
        }

        return $this;
    }

    /**
     * Mount Archive
     *
     * @param  string $host
     * @return $this
     */
    public function mountFtp($host, $username = '', $password = '', array $opt = [])
    {
        if (isset($this->settings[$host]['host'])) {
            $this->fs = $this->mounts->getFilesystem($host);
        } else {
            $opts = [
                'host' => $host,
                'username' => $username,
                'password' => $password,
            ];

            if ($opt) {
                $opts = array_merge($opt, $opts);
            }

            $this->fs = new Filesystem(new Adapter\Ftp($opts));
        }

        return $this;
    }

    /**
     * Mount Archive
     *
     * @param  string $path
     * @return $this
     */
    public function mountArchive($path)
    {
        $this->fs = new Filesystem(new ZipArchiveAdapter($path));

        return $this;
    }

    /**
     * Write a new file.
     *
     * @param  string $path     The path of the new file.
     * @param  string $contents The file contents.
     * @param  array  $config   An optional configuration array.
     * @throws FileExistsException
     * @return bool True on success, false on failure.
     */
    public function write($path, $contents, array $config = [])
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->write($path, $contents, $config);
    }

    /**
     * Write a new file using a stream.
     *
     * @param  string   $path     The path of the new file.
     * @param  resource $resource The file handle.
     * @param  array    $config   An optional configuration array.
     * @throws InvalidArgumentException If $resource is not a file handle.
     * @throws FileExistsException
     * @return bool True on success, false on failure.
     */
    public function writeStream($path, $resource, array $config = [])
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->writeStream($path, $contents, $config);
    }

    /**
     * Create a file or update if exists.
     *
     * @param  string $path     The path to the file.
     * @param  string $contents The file contents.
     * @param  array  $config   An optional configuration array.
     * @return bool True on success, false on failure.
     */
    public function put($path, $contents, array $config = [])
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->put($path, $contents, $config);
    }

    /**
     * Create a file or update if exists.
     *
     * @param  string   $path     The path to the file.
     * @param  resource $resource The file handle.
     * @param  array    $config   An optional configuration array.
     * @throws InvalidArgumentException Thrown if $resource is not a resource.
     * @return bool True on success, false on failure.
     */
    public function putStream($path, $resource, array $config = [])
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->put($path, $contents, $config);
    }

    /**
     * Update an existing file.
     *
     * @param  string $path     The path of the existing file.
     * @param  string $contents The file contents.
     * @param  array  $config   An optional configuration array.
     * @throws FileNotFoundException
     * @return bool True on success, false on failure.
     */
    public function update($path, $contents, array $config = [])
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->update($path, $contents, $config);
    }

    /**
     * Update an existing file using a stream.
     *
     * @param  string   $path     The path of the existing file.
     * @param  resource $resource The file handle.
     * @param  array    $config   An optional configuration array.
     * @throws InvalidArgumentException If $resource is not a file handle.
     * @throws FileNotFoundException
     * @return bool True on success, false on failure.
     */
    public function updateStream($path, $resource, array $config = [])
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->updateStream($path, $contents, $config);
    }

    /**
     * Read a file.
     *
     * @param  string $path The path to the file.
     * @throws FileNotFoundException
     * @return string|false The file contents or false on failure.
     */
    public function read($path)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->read($path);
    }

    /**
     * Retrieves a read-stream for a path.
     *
     * @param  string $path The path to the file.
     * @throws FileNotFoundException
     * @return resource|false The path resource or false on failure.
     */
    public function readStream($path)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->readStream($path);
    }

    /**
     * Rename a file.
     *
     * @param  string $path    Path to the existing file.
     * @param  string $newpath The new path of the file.
     * @throws FileExistsException   Thrown if $newpath exists.
     * @throws FileNotFoundException Thrown if $path does not exist.
     * @return bool True on success, false on failure.
     */
    public function rename($path, $newpath)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->rename($path, $newpath);
    }

    /**
     * Copy a file.
     *
     * @param  string $path    Path to the existing file.
     * @param  string $newpath The new path of the file.
     * @throws FileExistsException   Thrown if $newpath exists.
     * @throws FileNotFoundException Thrown if $path does not exist.
     * @return bool True on success, false on failure.
     */
    public function copy($path, $newpath)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->copy($path, $newpath);
    }

    /**
     * Delete a file.
     *
     * @param  string $path
     * @throws FileNotFoundException
     * @return bool True on success, false on failure.
     */
    public function delete($path)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->delete($path);
    }

    /**
     * Delete a directory.
     *
     * @param  string $dirname
     * @throws RootViolationException Thrown if $dirname is empty.
     * @return bool True on success, false on failure.
     */
    public function deleteDir($dirname)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->deleteDir($dirname);
    }

    /**
     * Create a directory.
     *
     * @param  string $dirname The name of the new directory.
     * @param  array  $config  An optional configuration array.
     * @return bool True on success, false on failure.
     */
    public function createDir($dirname, array $config = [])
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->createDir($dirname, $config);
    }

    /**
     * List contents of a directory.
     *
     * @param  string $directory The directory to list.
     * @param  bool   $recursive Whether to list recursively.
     * @return array A list of file metadata.
     */
    public function listContents($directory = '', $recursive = false)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->createDir($directory, $recursive);
    }

    /**
     * Get a file's mime-type.
     *
     * @param  string $path The path to the file.
     * @throws FileNotFoundException
     * @return string|false The file mime-type or false on failure.
     */
    public function getMimetype($path)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->getMimetype($path);
    }

    /**
     * Get a file's timestamp.
     *
     * @param  string $path The path to the file.
     * @throws FileNotFoundException
     * @return string|false The timestamp or false on failure.
     */
    public function getTimestamp($path)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->getTimestamp($path);
    }

    /**
     * Get a file's visibility.
     *
     * @param  string $path The path to the file.
     * @throws FileNotFoundException
     * @return string|false The visibility (public|private) or false on failure.
     */
    public function getVisibility($path)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->getVisibility($path);
    }

    /**
     * Get a file's size.
     *
     * @param  string $path The path to the file.
     * @return int|false The file size or false on failure.
     */
    public function getSize($path)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->getSize($path);
    }

    /**
     * Set the visibility for a file.
     *
     * @param  string $path       The path to the file.
     * @param  string $visibility One of 'public' or 'private'.
     * @return bool True on success, false on failure.
     */
    public function setVisibility($path, $visibility)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->setVisibility($path, $visibility);
    }

    /**
     * Get a file's metadata.
     *
     * @param  string $path The path to the file.
     * @throws FileNotFoundException
     * @return array|false The file metadata or false on failure.
     */
    public function getMetadata($path)
    {
        $filesystem = $this->fs ?: $this->mounts;

        return $filesystem->getMetadata($path);
    }
}
