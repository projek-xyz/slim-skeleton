<?php
namespace App\Tests;

use App\Controllers\HomeController;
use Projek\Slim\Http\Controllers;

class ControllerTest extends TestCase
{
    public function test_shuold_has_default_controller()
    {
        $this->assertTrue(class_exists(HomeController::class));
        $this->assertInstanceOf(Controllers::class, new HomeController($this->container));
    }
}
