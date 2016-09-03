<?php
namespace App\Controllers;

use Projek\Slim\Action;
use Slim\Http\Request;
use Slim\Http\Response;

class HomeController extends Action
{
    /**
     * @api  GET  /
     * @param  Request  $req
     * @param  Response $res
     * @param  array    $args
     * @return Response
     */
    public function __invoke(Request $req, Response $res, $args)
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
