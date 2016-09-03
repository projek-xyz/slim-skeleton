<?php
namespace Projek\Slim\Contracts;

use Projek\Slim\Logger;

interface LoggableInterface
{
    /**
     * @param  Logger  $logger
     */
    public function setLogger(Logger $logger);
}
