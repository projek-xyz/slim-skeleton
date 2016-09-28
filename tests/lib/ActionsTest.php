<?php
namespace Projek\Slim\Tests;

use Projek\Slim\Action;
use Slim\Collection;

class ActionsTest extends TestCase
{
    /**
     * @var  Action
     */
    private $action;

    public function setUp()
    {
        parent::setUp();

        $this->action = new SampleAction($this->container);
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
