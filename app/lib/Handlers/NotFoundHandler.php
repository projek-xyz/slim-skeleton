<?php

namespace Projek\Slim\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LogLevel;
use Slim\Handlers\NotFound;

class NotFoundHandler extends NotFound
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        logger(LogLevel::WARNING, 'Page Not Found', [
            $request->getMethod() => (string) $request->getUri()->withPath('')->withQuery('')->withFragment('')
        ]);

        return parent::__invoke($request, $response);
    }

    protected function renderHtmlNotFoundOutput(ServerRequestInterface $request)
    {
        $homeUrl = (string) $request->getUri()->withPath('')->withQuery('')->withFragment('');
        $title = 'Page Not Found';
        $desc = implode('<br>', [
            'The page you are looking for could not be found. Check the address bar to ensure your URL is spelled correctly.',
            'If all else fails, you can visit our home page at the link below.'
        ]);

        return app('view')->render('error::404', compact('title', 'desc', 'homeUrl'));
    }
}
