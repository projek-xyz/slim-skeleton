<?php
namespace Projek\Slim;

/**
 * @property-read callable data
 * @property-read \Slim\PDO\Database db
 * @property-read \League\Flysystem\Filesystem filesystem
 * @property-read Logger logger
 * @property-read Mailer mailer
 * @property-read \Slim\Collection settings
 * @property-read Uploader upload
 * @property-read callable validator
 * @method Database\Models data(string $modelClass)
 * @method void upload(\Psr\Http\Message\UploadedFileInterface $file)
 * @method bool logger(integer $level, string $message, array $context = [])
 * @method \Valitron\Validator validator(array|\Psr\Http\Message\ServerRequestInterface $data, array $rules)
 */
trait ContainerAwareTrait
{
    /**
     * Slim\Container instance
     *
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Get \Slim\Container name
     *
     * @param  string $name Container Name
     *
     * @return mixed
     * @throws \Slim\Exception\ContainerValueNotFoundException
     */
    public function __get($name)
    {
        return $this->container->get($name);
    }

    /**
     * Call \Slim\Container callable name
     *
     * @param  string $method Container Name
     * @param  array  $params Parameters
     *
     * @return mixed
     * @throws BadMethodCallException
     */
    public function __call($method, $params)
    {
        if ($this->container->has($method)) {
            $obj = $this->container->get($method);

            if (is_callable($obj)) {
                return call_user_func_array($obj, $params);
            }
        }

        throw new \BadMethodCallException(
            sprintf('Undefined method %s in %s.', $method, static::class)
        );
    }
}
