<?php
namespace App\Contracts;

use Projek\Slim\Plates;

interface ShouldRenderView
{
    /**
     * @param  Plates  $view
     */
    public function setView(Plates $view);
}
