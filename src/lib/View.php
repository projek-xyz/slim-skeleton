<?php
namespace Projek\Slim;

use League\Plates\Engine;
use League\Plates\Extension\Asset;
use League\Plates\Extension\ExtensionInterface;
use Psr\Http\Message\ResponseInterface;

class View
{
    /**
     * @var string[]
     */
    private $settings = [
        'directory' => null,
        'assetPath' => null,
        'fileExtension' => 'php',
        'timestampInFilename' => false,
    ];

    /**
     * @var \League\Plates\Engine
     */
    private $plates;

    /**
     * @var \Psr\Http\Message\ResponseInterface
     */
    private $response;

    /**
     * Create new Projek\Slim\Plates instance
     *
     * @param string[]                                 $settings
     * @param null|\Psr\Http\Message\ResponseInterface $response
     */
    public function __construct(array $settings, ResponseInterface $response = null)
    {
        $this->settings = array_merge($this->settings, $settings);
        $this->plates = new Engine($this->settings['directory'], $this->settings['fileExtension']);
        $this->response = $response;

        if (null !== $this->settings['assetPath']) {
            $this->setAssetPath($this->settings['assetPath']);
        }
    }

    /**
     * Get the Plate Engine
     *
     * @return \League\Plates\Engine
     */
    public function getPlates()
    {
        return $this->plates;
    }

    /**
     * Set Asset path from Plates Asset Extension
     *
     * @param  string $assetPath
     * @return \League\Plates\Engine
     */
    public function setAssetPath($assetPath)
    {
        return $this->plates->loadExtension(
            new Asset($assetPath, $this->settings['timestampInFilename'])
        );
    }

    /**
     * Get view directory
     *
     * @param  string|null $path
     *
     * @return string
     */
    public function directory($path = null)
    {
        if (null === $path) {
            return $this->plates->getDirectory();
        }

        return $this->plates->getDirectory().'/'.$path;
    }

    /**
     * Set Asset path from Plates Asset Extension
     *
     * @param  \League\Plates\Extension\ExtensionInterface $extension
     * @return \League\Plates\Engine
     */
    public function loadExtension(ExtensionInterface $extension)
    {
        $extension->register($this->plates);

        return $this->plates;
    }

    /**
     * Add a new template folder for grouping templates under different namespaces.
     *
     * @param  string  $name
     * @param  string  $directory
     * @param  boolean $fallback
     * @return \League\Plates\Engine
     * @throws \LogicException
     */
    public function addFolder($name, $directory, $fallback = false)
    {
        return $this->plates->addFolder($name, $directory, $fallback);
    }

    /**
     * Add preassigned template data.
     *
     * @param  array         $data
     * @param  null|string[] $templates
     * @return \League\Plates\Engine
     * @throws \LogicException
     */
    public function addData(array $data, $templates = null)
    {
        return $this->plates->addData($data, $templates);
    }

    /**
     * Register a new template function.
     *
     * @param  string   $name
     * @param  callable $callback
     * @return \League\Plates\Engine
     * @throws \LogicException
     */
    public function registerFunction($name, $callback)
    {
            return $this->plates->registerFunction($name, $callback);
    }

    /**
     * Set response
     *
     * @param  \Psr\Http\Message\ResponseInterface $response
     * @return self
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Render the template
     *
     * @param  string   $name
     * @param  string[] $data
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \LogicException
     */
    public function render($name, array $data = [])
    {
        if (! isset($this->response)) {
            throw new \LogicException(
                sprintf('Invalid %s object instance', ResponseInterface::class)
            );
        }

        return $this->response->write($this->plates->render($name, $data));
    }
}
