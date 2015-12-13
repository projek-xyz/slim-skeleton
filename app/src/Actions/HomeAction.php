<?php
namespace App\Actions;

class HomeAction extends Base
{
    public function index($req, $res, $args)
    {
        if (isset($args['name'])) {
            return $this->view->render($res, 'hello', [
                'name' => $args['name'],
                'desc' => 'Welcome to world',
            ]);
        }

        return $this->view->render($res, 'home');
    }
}
