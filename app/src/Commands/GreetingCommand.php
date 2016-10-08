<?php
namespace App\Commands;

use Projek\Slim\Console;

class GreetingCommand extends Console\Commands
{
    /**
     * {@inheritedoc}
     */
    protected $name = 'greeting';

    /**
     * {@inheritedoc}
     */
    protected $description = 'Say hello to the world';

    /**
     * {@inheritdoc}
     */
    public function __invoke($input, $output, $args)
    {
        $output->out('Hallo, world!');

        return Console::EXIT_SUCCESS;
    }
}
