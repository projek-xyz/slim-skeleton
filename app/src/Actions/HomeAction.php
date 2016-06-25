<?php
namespace App\Actions;

class HomeAction extends AbstractAction
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
