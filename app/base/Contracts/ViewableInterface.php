<?php
namespace Base\Contracts;

use Projek\Slim\Plates;

interface ViewableInterface
{
    /**
     * @param  Plates  $view
     */
    public function setView(Plates $view);
}
