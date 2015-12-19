<?php
namespace App\Utils;

use Projek\Slim\Monolog;

/**
 * Utilities used by error handlers.
 */
trait LoggableAware
{
    /**
     * @var \Projek\Slim\Monolog
     */
    private $logger = null;

    /**
     * @param \Projek\Slim\Monolog
     */
    public function setLogger(Monolog $logger)
    {
        $this->logger = $logger;
    }
}
