<?php
namespace Projek\Slim\Tests;

use Projek\Slim\Action;
use Projek\Slim\Console\Arguments;
use Projek\Slim\Console\Commands;
use Projek\Slim\Console\Input;
use Projek\Slim\Console\Output;
use Projek\Slim\Http\Controllers;
use Slim\Collection;

class ActionsTest extends TestCase
{
    /**
     * @var  Controllers
     */
    private $controller;

    /**
     * @var  Commands
     */
    private $command;

    public function setUp()
    {
        parent::setUp();

        $this->controller = new DummyControllers($this->container);
        $this->command = new DummyCommand($this->container);
    }

    /**
     * @dataProvider provideClassName
     */
    public function test_should_access_container($action)
    {
        $this->assertInstanceOf(Collection::class, $this->$action->settings);
        $this->assertTrue(is_callable([$this->$action, 'data']));
    }

    /**
     * @dataProvider provideClassName
     * @expectedException \BadMethodCallException
     */
    public function test_should_thrown_exception_if_no_callable_container($action)
    {
        $this->$action->fooBar();
    }

    public function provideClassName()
    {
        return [
            ['controller'],
            ['command']
        ];
    }
}

class DummyControllers extends Controllers
{
    // Do nothing.
}

class DummyCommand extends Commands
{
    public function __invoke(Input $input, Output $output, Arguments $arguments)
    {
        // Do nothing.
    }
}
