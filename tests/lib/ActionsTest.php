<?php

namespace Projek\Slim\Tests;

use Projek\Slim\Action;
use Projek\Slim\Container;
use Slim\Collection;

class ActionsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var  Action
     */
    private $action;

    public function setUp()
    {
        $container = new Container([]);
        $this->action = new SampleAction($container);
    }

    public function test_should_access_container()
    {
        $this->assertInstanceOf(Collection::class, $this->action->settings);
        $this->assertTrue(is_callable([$this->action, 'data']));
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function test_should_thrown_exception_if_no_callable_container()
    {
        $this->action->fooBar();
    }
}

class SampleAction extends Action
{
    // .
}
