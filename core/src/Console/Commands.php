<?php
namespace Projek\Slim\Console;

use Projek\Slim\ContainerAwareTrait;

abstract class Commands
{
    use ContainerAwareTrait;

    /**
     * Command name
     *
     * @var string
     */
    protected $name = null;

    /**
     * Command description
     *
     * @var string
     */
    protected $description = null;

    /**
     * Command arguments
     *
     * @var  array
     */
    protected $arguments = [];

    /**
     * Get command name
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * Get command description
     *
     * @return string
     */
    public function description()
    {
        return $this->description;
    }

    /**
     * Get command description
     *
     * @return array
     */
    public function arguments()
    {
        return $this->arguments;
    }

    /**
     * @param  Input $input
     * @param  Output $output
     * @param  Arguments $args
     *
     * @return int
     */
    abstract public function __invoke(Input $input, Output $output, Arguments $args);
}
