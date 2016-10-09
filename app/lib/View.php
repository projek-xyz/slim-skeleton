<?php
namespace Projek\Slim;

use League\Plates\Engine;
use League\Plates\Extension\ExtensionInterface;

class View
{
    /**
     * @var string[]
     */
    private $settings = [
        'directory' => null,
        'fileExtension' => 'tpl',
    ];

    /**
     * @var Engine
     */
    private $plates;

    /**
     * @param  string[] $settings
     */
    public function __construct(array $settings)
    {
        $this->settings = array_merge($this->settings, $settings);
        $directory = $this->settings['directory'] ?: directory('app.views');
        $this->plates = new Engine($directory, $this->settings['fileExtension']);
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
     * Render the template
     *
     * @param  string   $name
     * @param  string[] $data
     * @return string
     * @throws \LogicException
     */
    public function render($name, array $data = [])
    {
        return $this->plates->render($name, $data);
    }
}
