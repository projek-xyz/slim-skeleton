<?php
namespace App\Actions;

class HomeAction extends Base
{
    public function index($req, $res, $args)
    {
        throw new \Exception('Error bro');

        if (isset($args['name'])) {
            return $this->view->render('hello', [
                'name' => $args['name'],
                'desc' => 'Welcome to world',
            ]);
        }

        return $this->view->render('home');
    }
}
