<?php
namespace App\Commands;

use Projek\Slim\Console;

class GreetingCommand extends Console\Commands
{
    /**
     * @inheritdoc
     */
    protected $name = 'greeting';

    /**
     * @inheritdoc
     */
    protected $description = 'Say hello to the world';

    /**
     * @inheritdoc
     */
    public function __invoke(Console\Input $input, Console\Output $output, Console\Arguments $args)
    {
        $output->out('Hallo, world!');

        return Console::EXIT_SUCCESS;
    }
}
