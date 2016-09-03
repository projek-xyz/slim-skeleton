<?php
namespace Projek\Slim\Utils;

use Projek\Slim\View;

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
    public function setView(View $view)
    {
        $this->view = $view->getPlates();
    }
}
