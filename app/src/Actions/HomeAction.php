<?php
namespace App\Actions;

use App\Actions;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeAction extends Actions
{
    /**
     * @api  GET  /
     * 
     * @param  Request  $req
     * @param  Response  $res
     * @param  array  $args
     *
     * @return Response
     */
    public function index(Request $req, Response $res, $args)
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
