<?php
namespace App\Controllers;

use Projek\Slim\Http\Controllers;
use Projek\Slim\Http\Response;
use Slim\Http\Request;

class HomeControllers extends Controllers
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
