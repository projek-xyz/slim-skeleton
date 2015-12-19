<?php

namespace App\Middlewares;

use Slim\Http\Uri;
use Slim\Http\Response;
use Slim\Http\Request;
use Psr\Http\Message\UriInterface;

/**
 * Middleware to create basic http authentication.
 */
class CommonMiddleware
{
    /**
     * @var array
     */
    private $settings = [
        'mode' => 'development',
        'privateRoutes' => null,
        'baseurl' => '',
    ];

    /**
     * @param array $settings User settings
     */
    public function __construct(array $settings)
    {
        $this->settings = array_merge($this->settings, $settings);
    }

    /**
     * Execute the middleware.
     *
     * @param  \Slim\Http\Request  $req
     * @param  \Slim\Http\Response $res
     * @param  callable            $next
     * @return \Slim\Http\Response
     */
    public function __invoke(Request $req, Response $res, callable $next)
    {
        $uri = $req->getUri();
        $path = $this->filterTrailingSlash($uri);

        if ($uri->getPath() !== $path) {
            return $res->withStatus(301)
                ->withHeader('Location', $path)
                ->withBody($req->getBody());
        }

        if ($this->filterBaseurl($uri)) {
            return $res->withStatus(301)
                ->withHeader('Location', (string) $uri)
                ->withBody($req->getBody());
        }

        $server = $req->getServerParams();

        if (!isset($server['REQUEST_TIME_FLOAT'])) {
            $server['REQUEST_TIME_FLOAT'] = microtime(true);
        }

        $uri = $uri->withPath($path);
        $req = $this->filterRequestMethod($req->withUri($uri));

        $res = $next($req, $res);

        $res = $this->filterPrivateRoutes($uri, $res);

        // Only provide response calculation time in non-production env, tho.
        if ($this->settings['mode'] !== 'production') {
            $time = (microtime(true) - $server['REQUEST_TIME_FLOAT']) * 1000;
            $res = $res->withHeader('X-Response-Time', sprintf('%2.3fms', $time));
        }

        return $res;
    }

    /**
     * @param  \Psr\Http\Message\UriInterface $uri
     * @return string
     */
    protected function filterBaseurl(UriInterface $uri)
    {
        if ($baseUrl = $this->settings['baseurl']) {
            $reqUri = $uri->getScheme().'://'.$uri->getHost();

            if ($port = $uri->getPort()) {
                $reqUri .= ':'.$port;
            }

            $url = parse_url($baseUrl);
            $uri = $uri->withScheme($url['scheme'])->withHost($url['host']);

            // var_dump($reqUri);
            if ($port || isset($url['port'])) {
                $port = $port == $url['port'] ? $port : $url['port'];
                $uri = $uri->withPort($port);
            }

            // var_dump($reqUri);
            return $reqUri !== rtrim($baseUrl, '/');
        }

        return false;
    }

    /**
     * Provide filter to trim trailing slashes in URI path
     *
     * @param  \Psr\Http\Message\UriInterface $uri
     * @return string
     */
    protected function filterTrailingSlash(UriInterface $uri)
    {
        $path = $uri->getPath();

        if (strlen($path) > 1 && substr($path, -1) === '/') {
            $path = substr($path, 0, -1);
        }

        return $path;
    }

    /**
     * This provide a method-overwrite for GET and POST request
     *
     * @param  \Slim\Http\Request $req
     * @return \Slim\Http\Request
     */
    protected function filterRequestMethod(Request $req)
    {
        $method = strtoupper($req->getMethod());
        $params = [];

        if ($method == 'GET') {
            $params = $req->getQueryParams();
        } elseif ($method == 'POST') {
            $params = $req->getParsedBody();
        }

        if (isset($params['_method'])) {
            $req = $req->withMethod($params['_method']);
        }

        return $req;
    }

    /**
     * Provide private routes to be exposes by search engine
     *
     * @param  \Psr\Http\Message\UriInterface $uri
     * @param  \Slim\Http\Response            $res
     * @return \Slim\Http\Response
     */
    protected function filterPrivateRoutes(UriInterface $uri, Response $res)
    {
        $privates = $this->settings['privateRoutes'];
        $path = $uri->getPath();

        if (is_string($privates) && $privates !== '') {
            $privates = [$privates];
        }

        if (
            is_null($privates) ||
            (is_array($privates) && in_array($path, $privates))
        ) {
            $res = $res->withHeader('X-Robots-Tag', 'noindex, nofollow, noarchive');
        }

        return $res;
    }
}
