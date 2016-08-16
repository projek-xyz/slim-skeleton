<?php
namespace App\Tests\Actions;

use App\Controllers\HomeController;

class HomeActionTest extends \PHPUnit_Framework_TestCase
{
    public function testHomeActionIsExists()
    {
        $this->assertTrue(class_exists(HomeController::class));
    }
}
