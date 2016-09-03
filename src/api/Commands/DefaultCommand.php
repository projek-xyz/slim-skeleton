<?php
namespace App\Commands;

use Projek\Slim\Action;
use Slim\Http\Request;
use Slim\Http\Response;

class DefaultCommand extends Action
{
    /**
     * @api  GET  /
     * @param  Request  $req
     * @param  Response $res
     * @param  array    $args
     * @return mixed
     */
    public function __invoke(Request $req, Response $res, $args)
    {
        return 'hallo'.PHP_EOL;
    }
}
