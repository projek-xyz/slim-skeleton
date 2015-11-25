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

$app->add(Middleware::TrailingSlash(true));
$app->add(Middleware::FormatNegotiator());

/**
 * Log every request
 */
$app->add(function (Request $req, Response $res, Callable $next) {
    $res = $next($req, $res);

    $this->get('logger')->debug($req->getMethod().' '.$req->getUri()->getPath());

    return $res;
});

/**
 * Middleware to add or remove the trailing slash.
 */
$app->add(function (Request $req, Response $res, Callable $next) {
    $uri = $req->getUri();
    $path = $uri->getPath();
    $is_true = true;

    if ($is_true) {
        if (strlen($path) > 1 && substr($path, -1) !== '/' && !pathinfo($path, PATHINFO_EXTENSION)) {
            $path .= '/';
        }
    } else {
        if (strlen($path) > 1 && substr($path, -1) === '/') {
            $path = substr($path, 0, -1);
        }
    }

    //Ensure the path has one "/"
    if (empty($path) || $path === $this->basePath) {
        $path .= '/';
    }

    //redirect
    if (is_int($this->redirectStatus) && ($uri->getPath() !== $path)) {
        return self::getRedirectResponse($this->redirectStatus, $uri->withPath($path), $res);
    }

    return $next($req->withUri($uri->withPath($path)), $res);
});

/**
 * Middleware to calculate the response time duration.
 */
$app->add(function (Request $req, Response $res, Callable $next) {
    $server = $req->getServerParams();

    if (!isset($server['REQUEST_TIME_FLOAT'])) {
        $server['REQUEST_TIME_FLOAT'] = microtime(true);
    }

    $res = $next($req, $res);
    $time = (microtime(true) - $server['REQUEST_TIME_FLOAT']) * 1000;

    return $res->withHeader('X-Response-Time', sprintf('%2.3fms', $time));
});

/**
 * Middleware to block robots search engine.
 */
$app->add(function (Request $req, Response $res, Callable $next) {
    if ($req->getUri()->getPath() === '/robots.txt') {
        $body = new Body(fopen('php://temp', 'r+'));
        $body->write("User-Agent: *\nDisallow: /");

        return $res->withBody($body);
    }

    $res = $next($req, $res);

    return $res->withHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
});
