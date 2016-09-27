<?php
namespace Projek\Slim\Utils;

use Projek\Slim\Logger;

/**
 * Utilities used by error handlers.
 */
trait LoggableAware
{
    /**
     * @var \Projek\Slim\Logger
     */
    private $logger = null;

    /**
     * @param \Projek\Slim\Monolog
     */
    public function setLogger(Logger $logger)
    {
        $this->logger = $logger;
    }
}
