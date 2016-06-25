<?php
/**
 * Application Middlewares
 *
 * @var  array  $settings
 */

use Slim\Http\Response;
use Slim\Http\Request;

/**
 * Middleware that commonly used
 */
$app->add(new App\Middlewares\CommonMiddleware([
    'mode' => $settings['mode'],
    'baseurl' => $settings['baseurl'],
]));

/**
 * Middleware to log every request
 */
$app->add(function (Request $req, Response $res, Callable $next) {
    $res = $next($req, $res);

    /** @var \App\Providers\NegotiatorProvider $negotiator */
    $negotiator = $this->get('negotiator');
    $context = [
        $req->getMethod() => (string) $req->getUri(),
        'lang' => $negotiator->getLanguage($req),
        'format' => $negotiator->getFormat($req),
    ];

    if ($params = $req->getParams()) {
        $context['params'] = $params;
    }

    $this->logger->debug($req->getUri()->getPath(), $context);

    return $res;
});
