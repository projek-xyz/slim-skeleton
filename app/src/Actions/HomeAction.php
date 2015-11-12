<?php
namespace App\Actions;

use App\BaseAction;

class HomeAction extends BaseAction
{
    public function index($req, $res, $args)
    {
        $name = isset($args['name']) ? $args['name'] : 'world';

        return $this->view->render($res, 'home', compact('name'));
    }
}
