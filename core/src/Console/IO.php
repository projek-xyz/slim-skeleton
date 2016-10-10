<?php
namespace Projek\Slim\Console;

use League\CLImate\CLImate;
use Projek\Slim\Console;

class IO
{
    /**
     * @var  CLImate
     */
    protected $climate;

    /**
     * @var  Console
     */
    protected $console;

    /**
     * @param  Console $console
     */
    public function __construct(Console $console)
    {
        $this->console = $console;
        $this->climate = $console->getClimate();
    }

    /**
     * Check if STTY available
     *
     * @return bool
     */
    public function hasSttyAvailable()
    {
        exec('stty 2>&1', $output, $exitcode);

        return $exitcode === 0;
    }
}
