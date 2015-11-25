<?php
/**
 * Application Middlewares
 */

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

/**
 * Middleware to log every request
 */
$app->add(function (Request $req, Response $res, Callable $next) {
    $res = $next($req, $res);

    $this->get('logger')->debug($req->getMethod().' '.$req->getUri()->getPath());

    return $res;
});

/**
 * Middleware to remove the trailing slash.
 */
$app->add(function (Request $req, Response $res, Callable $next) {
    $uri = $req->getUri();
    $path = $uri->getPath();

    if (strlen($path) > 1 && substr($path, -1) === '/') {
        $path = substr($path, 0, -1);
    }

    // Ensure the path has one "/"
    if (empty($path) || $path === $uri->getBasePath()) {
        $path .= '/';
    }

    // redirect
    if ($uri->getPath() !== $path) {
        return $res->withStatus(301)
            ->withHeader('Location', $path)
            ->withBody($req->getBody());
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
