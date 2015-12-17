<?php
namespace App\Utils;

use Projek\Slim\Monolog;

/**
 * Utilities used by error handlers.
 */
trait LoggableAware
{
    private $logger = null;

    public function setLogger(Monolog $logger)
    {
        $this->logger = $logger;
    }
}
