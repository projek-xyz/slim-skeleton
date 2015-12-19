<?php
namespace App;

use App\Utils;
use Slim\Handlers\NotFound;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class NotFoundHandler extends NotFound
{
    use Utils\ViewableAware;
    use Utils\LoggableAware;

    /**
     * {inheritdoc}
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response)
    {
        $this->logger->warning('Not found', [
            $request->getMethod() => (string) $request->getUri()
        ]);

        return parent::__invoke($request, $response);
    }

    /**
     * {inheritdoc}
     */
    protected function renderHtmlNotFoundOutput(ServerRequestInterface $request, ResponseInterface $response)
    {
        if (is_null($this->view)) {
            return parent::renderHtmlNotFoundOutput($request, $response);
        }

        $title = 'Page Not Found';
        $desc = 'The page you are looking for could not be found. Check the address bar to ensure your URL is spelled correctly. If all else fails, you can visit our home page at the link below.';
        $homeUrl = (string)($request->getUri()->withPath('')->withQuery('')->withFragment(''));

        $this->view->addData(compact('title', 'desc', 'homeUrl'));

        return $this->view->render('error-404');
    }
}
