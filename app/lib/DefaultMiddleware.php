<?php
namespace Projek\Slim;

use Slim\Http\Response;
use Slim\Http\Request;
use Psr\Http\Message\UriInterface;

/**
 * Middleware to create basic http authentication.
 */
class DefaultMiddleware
{
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
        $server = $req->getServerParams();

        if (!isset($server['REQUEST_TIME_FLOAT'])) {
            $server['REQUEST_TIME_FLOAT'] = microtime(true);
        }

        $req = $this->filterRequestMethod($req);

        /** @var Response $res */
        $res = $next($req, $res);

        // Only provide response calculation time in non-production env, tho.
        if (setting('mode') !== 'production') {
            $time = (microtime(true) - $server['REQUEST_TIME_FLOAT']) * 1000;
            $res = $res->withHeader('X-Response-Time', sprintf('%2.3fms', $time));
        }

        return $res;
    }

    /**
     * Provide filter to trim trailing slashes in URI path
     *
     * @deprecated
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

        if ($method != 'POST') {
            return $req;
        }

        $params = $req->getParsedBody();

        if (isset($params['_method'])) {
            $req = $req->withMethod($params['_method']);
        }

        return $req;
    }
}
