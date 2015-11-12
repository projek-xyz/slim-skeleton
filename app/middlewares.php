<?php
/**
 * Application Middlewares
 */

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Log every request
 */
$app->add(function (Request $req, Response $res, Callable $next) {
    $res = $next($req, $res);

    $this->get('logger')->info($req->getMethod().' '.$req->getUri()->getPath());

    return $res;
});
