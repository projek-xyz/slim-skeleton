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
        $this->container = new Container(['settings' => $this->settings], FIXTURES_DIR);
    }

    protected function invokeMethod($obj, $name, $arg = [])
    {
        if (!is_object($obj)) {
            $this->fail('First argument should be an object');
        }

        $class = get_class($obj);
        $mock = $this->getMock($class);

        $mock->expects($this->once())->method($name);

        return $this->makeMethodInvokable($class, $name)->invokeArgs($obj, $arg);
    }

    protected function makeMethodInvokable($class, $name)
    {
        $method = new \ReflectionMethod($class, $name);

        if (!$method->isPublic()) {
            $method->setAccessible(true);
        }

        return $method;
    }
}
