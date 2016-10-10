<?php
namespace Projek\Slim\Console;

class Arguments
{
    /**
     * @var  Arguments\Manager|\League\CLImate\Argument\Manager
     */
    protected $argument;

    /**
     * @param  Arguments\Manager|\League\CLImate\Argument\Manager $argument
     */
    public function __construct(Arguments\Manager $argument)
    {
        $this->argument = $argument;
    }

    /**
     * Determine if an argument has been defined on the command line and get the value.
     *
     * @param string $name
     * @param array $argv
     *
     * @return bool|null
     */
    public function get($name)
    {
        if ($this->argument->defined($name)) {
            return $this->argument->get($name);
        }
        return null;
    }

    /**
     * Determine if an argument exists.
     *
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->argument->defined($name);
    }
}
