<?php
/**
 * Application Middlewares
 */

// use Slim\Http\Stream;
use Psr7Middlewares\Middleware;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

Middleware::setStreamFactory(function () use ($container) {
    return $container->get('request')->getBody();
});

$app->add(new Middleware\Robots());
$app->add(new Middleware\ResponseTime());
$app->add(new Middleware\TrailingSlash(true));
$app->add(new Middleware\FormatNegotiator());
$app->add(new Middleware\LanguageNegotiator(['id', 'en']));

// $app->add(
//     Middleware::When()
//         ->condition($settings['mode'] === 'development')
//         ->middleware(Middleware::debugBar()->from($container, 'debugbar'))
// );

/**
 * Log every request
 */
$app->add(function (Request $req, Response $res, Callable $next) {
    $lang = Middleware\LanguageNegotiator::getLanguage($req);

    $res = $next($req, $res);

    $this->get('logger')->debug($req->getMethod().' '.$req->getUri()->getPath().' '.$lang);

    return $res;
});
