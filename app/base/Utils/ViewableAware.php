<?php
namespace Base\Utils;

use Projek\Slim\Plates;

/**
 * Utilities used by error handlers.
 */
trait ViewableAware
{
    /**
     * @var \League\Plates\Engine
     */
    private $view = null;

    /**
     * @param \Projek\Slim\Plates
     */
    public function setView(Plates $view)
    {
        $this->view = $view->getPlates();
    }
}
