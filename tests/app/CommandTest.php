<?php
namespace App\Tests;

use App\Commands\GreetingCommand;
use Projek\Slim\Console\Commands;

class CommandTest extends TestCase
{
    public function test_shuold_has_default_command()
    {
        $this->assertTrue(class_exists(GreetingCommand::class));
        $this->assertInstanceOf(Commands::class, new GreetingCommand($this->container));
    }
}
