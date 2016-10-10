<?php
namespace App\Tests;

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
}
