<?php
namespace Projek\Slim\Contracts;

use Projek\Slim\View;

interface ViewableInterface
{
    /**
     * @param  View  $view
     */
    public function setView(View $view);
}
