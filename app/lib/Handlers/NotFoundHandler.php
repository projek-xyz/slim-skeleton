<?php
namespace Projek\Slim\Handlers;

use Projek\Slim\View;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LogLevel;
use Slim\Handlers\NotFound;

class NotFoundHandler extends NotFound
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        logger(LogLevel::WARNING, 'Page Not Found', [
            $request->getMethod() => (string) $request->getUri()
        ]);

        return parent::__invoke($request, $response);
    }

    /**
     * {@inheritdoc}
     */
    protected function renderHtmlNotFoundOutput(ServerRequestInterface $request)
    {
        $homeUrl = (string) $request->getUri()->withPath('')->withQuery('')->withFragment('');
        $title = 'Page Not Found';
        $desc = implode('<br>', [
            'The page you are looking for could not be found. Please ensure your URL is spelled correctly.',
            'If all else fails, you can visit our home page at the link below.'
        ]);

        return app(View::class)->render('error::404', compact('title', 'desc', 'homeUrl'));
    }
}
