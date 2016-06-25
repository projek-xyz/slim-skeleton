<?php
namespace App\Actions;

use App\Actions;

class HomeAction extends Actions
{
    public function index($req, $res, $args)
    {
        if (isset($args['name'])) {
            return $this->view->render('hello', [
                'name' => $args['name'],
                'desc' => 'Welcome to world',
            ]);
        }

        return $this->view->render('home');
    }
}
