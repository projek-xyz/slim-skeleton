<?php
namespace Projek\Slim\Tests;

use Projek\Slim\Container;

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  Container
     */
    protected $container;

    protected $settings = [];

    public function setUp()
    {
        $this->container = new Container(['settings' => $this->settings], ROOT_DIR);
    }

    protected function invokeMethod($obj, $name, $arg = [])
    {
        if (!is_object($obj)) {
            throw new \PHPUnit_Framework_Exception('First argument should be an object');
        }

        $method = new \ReflectionMethod(get_class($obj), $name);

        if (!$method->isPublic()) {
            $method->setAccessible(true);
        }

        return $method->invokeArgs($obj, $arg);
    }
}
