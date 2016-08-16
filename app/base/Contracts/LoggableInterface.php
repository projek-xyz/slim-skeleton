<?php
namespace Base\Contracts;

use Projek\Slim\Monolog;

interface LoggableInterface
{
    /**
     * @param  Monolog  $logger
     */
    public function setLogger(Monolog $logger);
}
