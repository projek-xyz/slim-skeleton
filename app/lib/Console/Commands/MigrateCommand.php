<?php
namespace Projek\Slim\Console\Commands;

use Projek\Slim\Console;
use Projek\Slim\Console\Commands;
use Projek\Slim\Console\Input;
use Projek\Slim\Console\Output;
use Projek\Slim\Database\Migrator;

class MigrateCommand extends Commands
{
    /**
     * {@inheritedoc}
     */
    protected $name = 'migrate';

    /**
     * {@inheritedoc}
     */
    protected $description = 'Execute migration data';

    /**
     * {@inheritedoc}
     */
    protected $arguments = [
        'up' => [
            'prefix' => 'u',
            'longPrefix' => 'up',
            'description' => 'Migrate Up',
            'noValue' => true
        ],
        'down' => [
            'prefix' => 'd',
            'longPrefix' => 'down',
            'description' => 'Migrate Down',
            'noValue' => true
        ]
    ];

    public function __invoke(Input $input, Output $output, $args)
    {
        $action = $args->has('down') ? 'down' : 'up';
        /** @var  Migrator $migrator */
        $migrator = app(Migrator::class);

        throw new \InvalidArgumentException('Error Bro', 200);

        if ($migrate = $migrator->migrate($action)) {
            $output->out('<green>Migration success</green>');

            return Console::EXIT_SUCCESS;
        }

        if (null === $migrate) {
            $output->out('<green>No Migration executed</green>');

            return Console::EXIT_SUCCESS;
        }

        $output->error('Migration Failed');

        return Console::EXIT_ERROR;
    }
}
