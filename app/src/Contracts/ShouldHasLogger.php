<?php
namespace App\Contracts;

use Projek\Slim\Monolog;

interface ShouldHasLogger
{
    /**
     * @param  Monolog  $logger
     */
    public function setLogger(Monolog $logger);
}