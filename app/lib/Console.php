<?php
namespace Projek\Slim;

use League\CLImate\CLImate;
use League\Flysystem\Exception;

class Console
{
    const EXIT_SUCCESS = 0;
    const EXIT_ERROR = 1;

    /**
     * CLImate instance
     *
     * @var CLImate
     */
    protected $climate;

    /**
     * All available commands
     *
     * @var array
     */
    protected $commands = [];

    /**
     * @var  Container
     */
    protected $container;

    /**
     * @param  Container $container
     */
    public function __construct(Container $container)
    {
        $this->climate = new CLImate();
        $this->container = $container;

        $this->climate->setArgumentManager(new Console\Arguments\Manager());
        $this->climate->extend(Console\Extensions\Tab::class, 'tab');

        foreach ($container->get(Console\Commands::class) as $command) {
            $this->add($command);
        }
    }

    /**
     * Get CLImate instance
     *
     * @return CLImate
     */
    public function getClimate()
    {
        return $this->climate;
    }

    /**
     * Returns Argument manager
     *
     * @return Console\Arguments\Manager|\League\CLImate\Argument\Manager
     */
    public function getArgumentManager()
    {
        return $this->climate->arguments;
    }

    /**
     * Register new command
     *
     * @param  string|Console\Commands $command Command instances
     *
     * @return static
     */
    public function add($command)
    {
        if (is_string($command)) {
            $command = new $command($this->container);
        }

        if (!$command instanceof Console\Commands) {
            throw new \InvalidArgumentException(
                sprintf(
                    'Argument 1 passed to %s must be an instance of %s, %s given',
                    __FUNCTION__,
                    Console\Commands::class,
                    get_class($command)
                )
            );
        }

        if (array_key_exists($command->name(), $this->commands)) {
            throw new \RuntimeException(
                sprintf('Command %s already registered', $command->name())
            );
        }

        $this->commands[$command->name()] = $command;

        return $this;
    }

    /**
     * Listen for $argv and run console app
     *
     * @param  array $argv Arguments
     * @return mixed
     */
    public function listen(array $argv = [])
    {
        $this->climate->arguments->description(config('app.description'));
        $this->climate->arguments->add([
            'help' => [
                'prefix' => 'h',
                'longPrefix' => 'help',
                'description' => 'Show help',
                'noValue' => true
            ]
        ]);

        if (empty($argv)) {
            return $this->usage();
        }

        $cmd = array_shift($argv);
        if (isset($this->commands[$cmd])) {
            $command = $this->commands[$cmd];

            return $this->execute($command, $argv);
        }

        return $this->usage($argv);
    }

    protected function execute(Console\Commands $command, $argv)
    {
        $args = $this->getArgumentManager();

        $args->parse($argv);

        $args->description($command->description());

        foreach ($command->arguments() as $name => $options) {
            $args->add($name, $options);
        }

        if ($args->defined('help')) {
            return $this->usage($argv, $command->name());
        }

        try {
            return $command(
                new Console\Input($this),
                new Console\Output($this),
                new Console\Arguments($args)
            );
        } catch (\Exception $e) {
            $this->climate
                ->out(sprintf('Error: [%s] <red>%s</red>', $e->getCode(), $e->getMessage()))
                ->tab()->out(sprintf('%s (%d)', $e->getFile(), (int) $e->getLine()));
        }

        return self::EXIT_ERROR;
    }

    /**
     * Toggle ANSI support on or off
     *
     * @param  bool $enable
     *
     * @return static
     */
    public function forceAnsi($enable = true)
    {
        if ($enable) {
            $this->climate->forceAnsiOn();
        } else {
            $this->climate->forceAnsiOff();
        }

        return $this;
    }

    /**
     * Print usage
     *
     * @param  array|null $args
     * @param  string|null $command
     *
     * @return mixed
     */
    protected function usage(array $args = [], $command = null)
    {
        array_unshift($args, $command ?: '[command]');

        $this->climate->arguments->usage($this->climate, $args);

        if (empty($this->commands) || $command) {
            if (!$command) {
                $this->climate->br()->out('No command available');
            }

            return self::EXIT_SUCCESS;
        }

        $this->climate->br()->out(
            sprintf('<yellow>%s</yellow>:', 'Available commands')
        );

        $len = [];
        foreach ($this->commands as $name => $cmd) {
            $len[] = strlen($name);
        }

        foreach ($this->commands as $name => $cmd) {
            $spc = max($len) + 2 - strlen($name);
            $this->climate->tab()->out(
                sprintf('<green>%s</green>%s%s', $cmd->name(), str_repeat(' ', $spc), $cmd->description())
            );
        }

        return self::EXIT_SUCCESS;
    }
}
