<?php
namespace App\Providers;

use App\Utils;
use League\Plates\Engine;
use League\Plates\Extension\Asset;
use League\Plates\Extension\ExtensionInterface;
use Psr\Http\Message\ResponseInterface;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use InvalidArgumentException;

class ViewProvider implements ServiceProviderInterface
{
    /**
     * @var array
     */
    private $settings = [
        'directory'     => null,
        'assetPath'     => null,
        'fileExtension' => 'php',
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
     * Register this plates view provider with a Pimple container
     *
     * @param \Pimple\Container $container
     */
    public function register(Container $container)
    {
        $settings = $container->get('settings');

        if (!isset($settings['view'])) {
            throw new InvalidArgumentException('Template configuration not found');
        }

        $this->settings = array_merge($this->settings, $settings['view']);
        $this->plates = new Engine($this->settings['directory'], $this->settings['fileExtension']);

        if (null !== $this->settings['assetPath']) {
            $this->setAssetPath($this->settings['assetPath']);
        }

        $this->loadExtension(
            new Utils\ViewExtension(
                $container->get('router'),
                $container->get('request')->getUri()
            )
        );

        $this->response = $container->get('response');

        $container['view'] = $this;
    }

    /**
     * Set Asset path from Plates Asset Extension
     *
     * @param string $assetPath
     */
    public function setAssetPath($assetPath)
    {
        return $this->plates->loadExtension(new Asset($assetPath));
    }

    /**
     * Set Asset path from Plates Asset Extension
     *
     * @param  \Psr\Http\Message\ResponseInterface $extension
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
     */
    public function addFolder($name, $directory, $fallback = false)
    {
        return $this->plates->addFolder($name, $directory, $fallback);
    }

    /**
     * Add preassigned template data.
     *
     * @param  array             $data
     * @param  null|string|array $templates
     * @return \League\Plates\Engine
     */
    public function addData(array $data, $templates = null)
    {
        return $this->plates->addData($data, $templates);
    }

    /**
     * Register a new template function.
     *
     * @param  string   $name
     * @param  callback $callback
     * @return \League\Plates\Engine
     */
    public function registerFunction($name, $callback)
    {
        return $this->plates->registerFunction($name, $callback);
    }

    /**
     * Render the template
     *
     * @param  string $name
     * @param  array  $data
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function render($name, array $data = [])
    {
        return $this->response->write($this->plates->render($name, $data));
    }
}
