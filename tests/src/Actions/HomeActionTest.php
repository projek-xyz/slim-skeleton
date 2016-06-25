<?php
namespace App\Tests\Actions;

class HomeActionTest extends \PHPUnit_Framework_TestCase
{
    public function testHomeActionIsExists()
    {
        $this->assertTrue(class_exists('App\Actions\HomeAction'));
    }
}
