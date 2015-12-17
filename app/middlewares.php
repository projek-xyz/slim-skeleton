<?php
/**
 * Application Middlewares
 */

use Slim\Http\Response;
use Slim\Http\Request;

/**
 * Middleware that commonly used
 */
$app->add(new App\Middlewares\CommonMiddleware([
    'mode' => $settings['mode']
]));

/**
 * Middleware to log every request
 */
$app->add(function (Request $req, Response $res, Callable $next) {
    $res = $next($req, $res);
    $negotiator = $this->get('negotiator');

    $this->get('logger')->debug($req->getUri()->getPath(), [
        'lang' => $negotiator->getLanguage($req),
        'format' => $negotiator->getFormat($req),
        'target' => $req->getRequestTarget(),
        'status' => $res->getStatusCode(),
        'method' => $req->getMethod(),
        'params' => $req->getParams(),
    ]);

    return $res;
});
