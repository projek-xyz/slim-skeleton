<?php
namespace App\Controllers;

use Projek\Slim\Action;
use Projek\Slim\Response;
use Slim\Http\Request;

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
            return $res->withView('hello', [
                'name' => $args['name'],
                'desc' => 'Welcome to world',
            ]);
        }

        return $res->withView('home');
    }
}
