<?php
namespace App\Utils;

use Projek\Slim\Plates;

/**
 * Utilities used by error handlers.
 */
trait ViewableAware
{
    private $view = null;

    public function setView(Plates $view)
    {
        $this->view = $view->getPlates();
    }
}
