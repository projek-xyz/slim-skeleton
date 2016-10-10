<?php
namespace App\Tests;

use App\Models\Sample;
use Projek\Slim\Database\Models;

class ModelTest extends TestCase
{
    public function test_shuold_has_controller()
    {
        $this->assertTrue(class_exists(Sample::class));
        $this->assertInstanceOf(Models::class, new Sample());
    }
}
