<?php
namespace Projek\Slim\Console\Commands;

use Projek\Slim\Console;
use Projek\Slim\Database\Migrator;

class MigrateCommand extends Console\Commands
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

    /**
     * {@inheritdoc}
     */
    public function __invoke(Console\Input$input, Console\Output $output, Console\Arguments $args)
    {
        $action = $args->has('down') ? 'down' : 'up';
        /** @var  Migrator $migrator */
        $migrator = app(Migrator::class);

        $migrator->setOutput($output);

        if ($migrator->migrate($action)) {
            return Console::EXIT_SUCCESS;
        }

        return Console::EXIT_ERROR;
    }
}
