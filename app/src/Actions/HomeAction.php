<?php
namespace App\Actions;

class HomeAction extends Base
{
    public function index($req, $res, $args)
    {
        $name = isset($args['name']) ? $args['name'] : 'world';

        return $this->view->render($res, 'home', compact('name'));
    }
}
